<?php
include 'conf/conf.php';
header('Content-Type: application/json; charset=utf-8');

function jsonError($message, $status = 400)
{
    http_response_code($status);
    echo json_encode(['success' => false, 'message' => $message], JSON_UNESCAPED_UNICODE);
    exit;
}

function hasColumn(mysqli $conn, string $table, string $column): bool
{
    $safeColumn = $conn->real_escape_string($column);
    $result = $conn->query("SHOW COLUMNS FROM `$table` LIKE '$safeColumn'");
    return $result && $result->num_rows > 0;
}

function findOutfitRule(mysqli $conn, string $skinTone, string $bodyShape): ?array
{
    // Alias lama dipertahankan hanya untuk kompatibilitas dengan data rule yang sudah ada.
    // Hasil analisis baru tetap menggunakan 4 kategori akademik.
    $legacyAliases = [
        'triangle' => ['triangle', 'pear'],
        'inverted triangle' => ['inverted triangle', 'inverted-triangle', 'upper-body'],
        'rectangle' => ['rectangle'],
        'hourglass' => ['hourglass']
    ];

    $candidates = $legacyAliases[$bodyShape] ?? [$bodyShape];
    foreach ($candidates as $candidate) {
        $stmt = $conn->prepare("SELECT color_palette, do_list, dont_list FROM outfit_rules WHERE skin_tone_group = ? AND body_shape = ? LIMIT 1");
        $stmt->bind_param("ss", $skinTone, $candidate);
        $stmt->execute();
        $rule = $stmt->get_result()->fetch_assoc();
        $stmt->close();
        if ($rule) {
            return $rule;
        }
    }

    return null;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    jsonError('Invalid request', 405);
}

$user_id = 1;
$skin_tone = strtolower(trim($_POST['skin_tone'] ?? ''));
$body_shape = strtolower(trim($_POST['body_shape'] ?? ''));
$skin_tone_label = strtolower(trim($_POST['skin_tone_label'] ?? $skin_tone));
$body_shape_label = strtolower(trim($_POST['body_shape_label'] ?? $body_shape));
$dataset_type = strtolower(trim($_POST['dataset_type'] ?? 'testing'));
$height_cm = (float) ($_POST['height_cm'] ?? 0);
$weight_kg = (float) ($_POST['weight_kg'] ?? 0);

$allowedSkinTones = ['warm', 'cool', 'neutral', 'neutral-warm', 'neutral-cool'];
$allowedBodyShapes = ['rectangle', 'triangle', 'inverted triangle', 'hourglass'];
$allowedDatasetTypes = ['training', 'testing'];

if (!in_array($skin_tone, $allowedSkinTones, true)) {
    jsonError('Kategori skin tone tidak valid.');
}
if (!in_array($body_shape, $allowedBodyShapes, true)) {
    jsonError('Kategori body shape tidak valid.');
}
if (!in_array($dataset_type, $allowedDatasetTypes, true)) {
    $dataset_type = 'testing';
}
if ($height_cm < 120 || $height_cm > 230) {
    jsonError('Tinggi badan harus berada pada rentang 120–230 cm.');
}
if ($weight_kg < 30 || $weight_kg > 250) {
    jsonError('Berat badan harus berada pada rentang 30–250 kg.');
}

if (!hasColumn($conn, 'uploads', 'height_cm') || !hasColumn($conn, 'uploads', 'weight_kg')) {
    jsonError('Database belum memiliki kolom tinggi dan berat badan. Jalankan file database_update_height_weight.sql satu kali melalui phpMyAdmin.', 500);
}

if (!isset($_FILES['photo']) || $_FILES['photo']['error'] !== UPLOAD_ERR_OK) {
    jsonError('File foto tidak ditemukan.');
}

if ($_FILES['photo']['size'] > 8 * 1024 * 1024) {
    jsonError('Ukuran foto maksimal 8 MB.');
}

$finfo = new finfo(FILEINFO_MIME_TYPE);
$mime = $finfo->file($_FILES['photo']['tmp_name']);
$allowedMime = [
    'image/jpeg' => 'jpg',
    'image/png' => 'png',
    'image/webp' => 'webp'
];

if (!isset($allowedMime[$mime])) {
    jsonError('Format foto harus JPG, PNG, atau WebP.');
}

$upload_dir = __DIR__ . '/uploads/';
if (!is_dir($upload_dir) && !mkdir($upload_dir, 0777, true)) {
    jsonError('Folder upload tidak dapat dibuat.', 500);
}

$safeName = preg_replace('/[^A-Za-z0-9_-]/', '_', pathinfo($_FILES['photo']['name'], PATHINFO_FILENAME));
$safeName = trim($safeName, '_');
if ($safeName === '') {
    $safeName = 'photo';
}

$filename = time() . '_' . substr($safeName, 0, 60) . '.' . $allowedMime[$mime];
$absolutePath = $upload_dir . $filename;
$file_path = 'uploads/' . $filename;

if (!move_uploaded_file($_FILES['photo']['tmp_name'], $absolutePath)) {
    jsonError('Gagal menyimpan file foto.', 500);
}

$stmt = $conn->prepare("\n    INSERT INTO uploads\n    (user_id, file_path, height_cm, weight_kg, skin_tone, body_shape, skin_tone_label, body_shape_label, dataset_type, created_at)\n    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())\n");

if (!$stmt) {
    @unlink($absolutePath);
    jsonError('Query upload gagal dipersiapkan: ' . $conn->error, 500);
}

$stmt->bind_param(
    "isddsssss",
    $user_id,
    $file_path,
    $height_cm,
    $weight_kg,
    $skin_tone,
    $body_shape,
    $skin_tone_label,
    $body_shape_label,
    $dataset_type
);

if (!$stmt->execute()) {
    @unlink($absolutePath);
    $message = $stmt->error;
    $stmt->close();
    jsonError('Gagal menyimpan data upload: ' . $message, 500);
}

$upload_id = $stmt->insert_id;
$stmt->close();

$slug = substr(bin2hex(random_bytes(8)), 0, 10);
$stmtSlug = $conn->prepare("UPDATE uploads SET slug = ? WHERE id = ?");
$stmtSlug->bind_param("si", $slug, $upload_id);
$stmtSlug->execute();
$stmtSlug->close();

$rule = findOutfitRule($conn, $skin_tone, $body_shape);

if ($rule) {
    $colors = array_values(array_filter(array_map('trim', explode(',', $rule['color_palette']))));
    $items = [
        'recommended_colors' => $colors,
        'tips' => [
            $rule['do_list'],
            '⚠️ Hindari: ' . $rule['dont_list']
        ]
    ];

    $title = ucwords(str_replace('-', ' ', $skin_tone)) . ' - ' . ucwords($body_shape) . ' recommendation';
    $items_json = json_encode($items, JSON_UNESCAPED_UNICODE);

    $stmtRec = $conn->prepare("\n        INSERT INTO recommendations (upload_id, title, items_json, skin_tone, body_shape, created_at)\n        VALUES (?, ?, ?, ?, ?, NOW())\n    ");

    if ($stmtRec) {
        $stmtRec->bind_param("issss", $upload_id, $title, $items_json, $skin_tone, $body_shape);
        $stmtRec->execute();
        $stmtRec->close();
    }
}

echo json_encode([
    'success' => true,
    'id' => $upload_id,
    'slug' => $slug,
    'skin_tone' => $skin_tone,
    'body_shape' => $body_shape,
    'height_cm' => $height_cm,
    'weight_kg' => $weight_kg
], JSON_UNESCAPED_UNICODE);
