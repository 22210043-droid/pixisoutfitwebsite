<?php
include 'conf/conf.php';

$slug = isset($_GET['slug']) ? trim($_GET['slug']) : '';
if ($slug === '') {
    die('<h3>Slug tidak valid.</h3>');
}

$stmtUpload = $conn->prepare("SELECT * FROM uploads WHERE slug = ? LIMIT 1");
$stmtUpload->bind_param("s", $slug);
$stmtUpload->execute();
$upload = $stmtUpload->get_result()->fetch_assoc();
$stmtUpload->close();

if (!$upload) {
    die('<h3>Data tidak ditemukan.</h3>');
}

$id = (int) $upload['id'];
$skinToneRaw = strtolower(trim($upload['skin_tone'] ?? $upload['skin_tone_label'] ?? 'neutral'));
$bodyShapeRaw = strtolower(trim($upload['body_shape'] ?? $upload['body_shape_label'] ?? 'rectangle'));

$allowedSkinTones = ['warm', 'cool', 'neutral', 'neutral-warm', 'neutral-cool'];
$skinToneGroup = in_array($skinToneRaw, $allowedSkinTones, true) ? $skinToneRaw : 'neutral';

// Normalisasi data lama agar tampilan hasil baru hanya memakai 4 kategori penelitian.
$bodyAliases = [
    'pear' => 'triangle',
    'triangle' => 'triangle',
    'inverted-triangle' => 'inverted triangle',
    'inverted triangle' => 'inverted triangle',
    'rectangle' => 'rectangle',
    'hourglass' => 'hourglass',
    'upper-body' => 'inverted triangle',
    'apple' => 'rectangle'
];
$bodyShape = $bodyAliases[$bodyShapeRaw] ?? 'rectangle';

function findResultRule(mysqli $conn, string $skinTone, string $bodyShape): ?array
{
    $legacyAliases = [
        'triangle' => ['triangle', 'pear'],
        'inverted triangle' => ['inverted triangle', 'inverted-triangle', 'upper-body'],
        'rectangle' => ['rectangle'],
        'hourglass' => ['hourglass']
    ];

    foreach ($legacyAliases[$bodyShape] ?? [$bodyShape] as $candidate) {
        $stmt = $conn->prepare("SELECT * FROM outfit_rules WHERE skin_tone_group = ? AND body_shape = ? LIMIT 1");
        $stmt->bind_param("ss", $skinTone, $candidate);
        $stmt->execute();
        $rule = $stmt->get_result()->fetch_assoc();
        $stmt->close();
        if ($rule) return $rule;
    }

    return null;
}

$rule = findResultRule($conn, $skinToneGroup, $bodyShape);
$rec = null;

if (!$rule) {
    $stmtRec = $conn->prepare("SELECT * FROM recommendations WHERE upload_id = ? ORDER BY id DESC LIMIT 1");
    $stmtRec->bind_param("i", $id);
    $stmtRec->execute();
    $rec = $stmtRec->get_result()->fetch_assoc();
    $stmtRec->close();
}

if (!$rule && !$rec) {
    $rec = [
        'title' => 'Basic Style Suggestion',
        'items_json' => json_encode([
            'recommended_colors' => ['#d6d3d1', '#a8a29e', '#78716c'],
            'tips' => [
                'Pilih warna netral yang mudah dipadukan.',
                'Gunakan potongan pakaian yang rapi dan proporsional.',
                'Tambahkan aksesori seperlunya agar tampilan tetap seimbang.'
            ]
        ], JSON_UNESCAPED_UNICODE)
    ];
}

function formatLabel($text)
{
    return ucwords(str_replace(['-', '_'], ' ', $text));
}

$imagePath = !empty($upload['file_path']) ? $upload['file_path'] : 'assets/img/no-image.png';
$heightCm = isset($upload['height_cm']) ? (float) $upload['height_cm'] : 0;
$weightKg = isset($upload['weight_kg']) ? (float) $upload['weight_kg'] : 0;
$bmi = ($heightCm > 0 && $weightKg > 0) ? $weightKg / pow($heightCm / 100, 2) : 0;
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Style Coach Result</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/cssresult.css">
    <link rel="shortcut icon" href="assets/favi.png" type="image/x-icon">
</head>

<body>
    <div class="card">
        <div class="text-center mb-4">
            <h2>👗 Style Coach Result</h2>
            <p class="text-muted" style="color:#ccc !important;">
                Hasil rekomendasi outfit berdasarkan analisis warna kulit dan proporsi tubuh
            </p>
        </div>

        <div class="row align-items-center g-4">
            <div class="col-md-5 text-center">
                <img src="<?= htmlspecialchars($imagePath) ?>" alt="Foto pengguna" class="img-fluid rounded shadow">
            </div>

            <div class="col-md-7">
                <h5>
                    <b>Skin Tone:</b>
                    <span class="tone-badge tone-<?= htmlspecialchars($skinToneGroup) ?>">
                        <?= htmlspecialchars(formatLabel($skinToneGroup)) ?>
                    </span>
                </h5>

                <h5><b>Body Shape:</b> <?= htmlspecialchars(formatLabel($bodyShape)) ?></h5>

                <?php if ($heightCm > 0 && $weightKg > 0): ?>
                <div class="row g-2 mt-2 mb-3">
                    <div class="col-sm-4"><b>Tinggi:</b> <?= htmlspecialchars(number_format($heightCm, 1)) ?> cm</div>
                    <div class="col-sm-4"><b>Berat:</b> <?= htmlspecialchars(number_format($weightKg, 1)) ?> kg</div>
                    <div class="col-sm-4"><b>BMI:</b> <?= htmlspecialchars(number_format($bmi, 1)) ?></div>
                </div>
                <?php endif; ?>

                <hr>

                <?php if ($rule): ?>
                    <h6><b>🎨 Palet Warna Cocok:</b></h6>
                    <div class="mb-3">
                        <?php
                        $colors = array_filter(array_map('trim', explode(',', $rule['color_palette'] ?? '')));
                        foreach ($colors as $color):
                        ?>
                        <span class="palette-box" style="background-color:<?= htmlspecialchars($color) ?>;"></span>
                        <?php endforeach; ?>
                    </div>

                    <h6><b>✅ Disarankan:</b></h6>
                    <p><?= nl2br(htmlspecialchars($rule['do_list'] ?? '-')) ?></p>

                    <h6><b>🚫 Hindari:</b></h6>
                    <p><?= nl2br(htmlspecialchars($rule['dont_list'] ?? '-')) ?></p>
                <?php else: ?>
                    <?php
                    $data = json_decode($rec['items_json'] ?? '{}', true) ?: [];
                    $recommendedColors = $data['recommended_colors'] ?? [];
                    $tips = $data['tips'] ?? [];
                    ?>

                    <h6><b>🎨 Rekomendasi Warna:</b></h6>
                    <div class="mb-3">
                        <?php foreach ($recommendedColors as $color): ?>
                        <span class="palette-box" style="background-color:<?= htmlspecialchars(trim($color)) ?>;"></span>
                        <?php endforeach; ?>
                    </div>

                    <h6><b>✅ Tips Outfit:</b></h6>
                    <ul>
                        <?php foreach ($tips as $tip): ?>
                        <li><?= htmlspecialchars($tip) ?></li>
                        <?php endforeach; ?>
                    </ul>
                <?php endif; ?>
            </div>
        </div>

        <div class="text-center mt-4">
            <a href="upload.php" class="btn btn-primary px-4 py-2 rounded-pill">🔁 Analisis Ulang</a>
        </div>
    </div>
</body>

</html>
