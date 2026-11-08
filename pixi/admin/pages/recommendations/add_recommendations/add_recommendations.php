<?php
if (isset($_GET['q']) && $_GET['q'] === 'add_recommendations') {
    $upload_id   = trim($_POST['upload_id'] ?? '');
    $title       = mysqli_real_escape_string($conn, trim($_POST['title'] ?? ''));
    $items_json  = trim($_POST['items_json'] ?? '');
    $skin_tone   = mysqli_real_escape_string($conn, trim($_POST['skin_tone'] ?? ''));
    $body_shape  = mysqli_real_escape_string($conn, trim($_POST['body_shape'] ?? ''));

    if ($title === '' || $items_json === '') {
        echo "<script>
            Swal.fire({
                icon: 'warning',
                title: 'Data belum lengkap!',
                text: 'Title dan Items JSON wajib diisi.'
            }).then(() => {
                window.history.back();
            });
        </script>";
        exit;
    }

    $allowedSkin = ['', 'warm', 'cool', 'neutral', 'neutral-warm', 'neutral-cool'];
    $allowedShape = ['', 'rectangle', 'triangle', 'inverted triangle', 'hourglass'];

    if (!in_array($skin_tone, $allowedSkin, true) || !in_array($body_shape, $allowedShape, true)) {
        echo "<script>
            Swal.fire({
                icon: 'error',
                title: 'Data tidak valid!',
                text: 'Skin tone atau body shape tidak sesuai pilihan yang tersedia.'
            }).then(() => {
                window.history.back();
            });
        </script>";
        exit;
    }

    // Validasi JSON
    json_decode($items_json, true);
    if (json_last_error() !== JSON_ERROR_NONE) {
        echo "<script>
            Swal.fire({
                icon: 'error',
                title: 'Format JSON tidak valid!',
                text: 'Periksa kembali struktur items_json kamu.'
            }).then(() => {
                window.history.back();
            });
        </script>";
        exit;
    }

    // upload_id opsional
    $upload_id_value = null;
    if ($upload_id !== '') {
        $upload_id_value = (int) $upload_id;

        $checkUpload = $conn->prepare("SELECT id FROM uploads WHERE id = ? LIMIT 1");
        $checkUpload->bind_param("i", $upload_id_value);
        $checkUpload->execute();
        $checkUpload->store_result();

        if ($checkUpload->num_rows === 0) {
            $checkUpload->close();

            echo "<script>
                Swal.fire({
                    icon: 'error',
                    title: 'Upload tidak ditemukan!',
                    text: 'Upload yang dipilih tidak tersedia.'
                }).then(() => {
                    window.history.back();
                });
            </script>";
            exit;
        }
        $checkUpload->close();
    }

    $stmt = $conn->prepare("
        INSERT INTO recommendations (upload_id, title, items_json, created_at, skin_tone, body_shape)
        VALUES (?, ?, ?, NOW(), ?, ?)
    ");
    $stmt->bind_param("issss", $upload_id_value, $title, $items_json, $skin_tone, $body_shape);

    if ($stmt->execute()) {
        echo "<script>
            Swal.fire({
                icon: 'success',
                title: 'Rekomendasi berhasil ditambahkan!',
                showConfirmButton: false,
                timer: 1500
            }).then(() => {
                window.location.href = '?q=recommendations';
            });
        </script>";
    } else {
        echo "<script>
            Swal.fire({
                icon: 'error',
                title: 'Gagal menambahkan rekomendasi!',
                text: '" . addslashes($stmt->error) . "'
            }).then(() => {
                window.history.back();
            });
        </script>";
    }

    $stmt->close();
}
