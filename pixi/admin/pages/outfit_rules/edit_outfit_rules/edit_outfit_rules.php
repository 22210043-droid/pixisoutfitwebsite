<?php
if (isset($_GET['q']) && $_GET['q'] === 'edit_outfit_rules' && isset($_GET['id'])) {
    $id    = (int) $_GET['id'];
    $skin  = mysqli_real_escape_string($conn, trim($_POST['skin_tone_group'] ?? ''));
    $shape = mysqli_real_escape_string($conn, trim($_POST['body_shape'] ?? ''));
    $color = mysqli_real_escape_string($conn, trim($_POST['color_palette'] ?? ''));
    $do    = mysqli_real_escape_string($conn, trim($_POST['do_list'] ?? ''));
    $dont  = mysqli_real_escape_string($conn, trim($_POST['dont_list'] ?? ''));

    if ($skin === '' || $shape === '' || $color === '' || $do === '' || $dont === '') {
        echo "<script>
            Swal.fire({
                icon: 'warning',
                title: 'Data belum lengkap!',
                text: 'Semua field wajib diisi.'
            }).then(() => {
                window.history.back();
            });
        </script>";
        exit;
    }

    $allowedSkin = ['warm', 'cool', 'neutral', 'neutral-warm', 'neutral-cool'];
    $allowedShape = ['rectangle', 'triangle', 'inverted triangle', 'hourglass'];

    if (!in_array($skin, $allowedSkin, true) || !in_array($shape, $allowedShape, true)) {
        echo "<script>
            Swal.fire({
                icon: 'error',
                title: 'Data tidak valid!',
                text: 'Skin tone group atau body shape tidak sesuai pilihan yang tersedia.'
            }).then(() => {
                window.history.back();
            });
        </script>";
        exit;
    }

    // Cegah duplikat dengan rule lain
    $check = $conn->prepare("
        SELECT id 
        FROM outfit_rules 
        WHERE skin_tone_group = ? AND body_shape = ? AND id != ?
        LIMIT 1
    ");
    $check->bind_param("ssi", $skin, $shape, $id);
    $check->execute();
    $check->store_result();

    if ($check->num_rows > 0) {
        $check->close();

        echo "<script>
            Swal.fire({
                icon: 'warning',
                title: 'Rule sudah ada!',
                text: 'Kombinasi skin tone group dan body shape ini sudah dipakai oleh data lain.'
            }).then(() => {
                window.location.href='?q=outfit_rules';
            });
        </script>";
        exit;
    }
    $check->close();

    $stmt = $conn->prepare("
        UPDATE outfit_rules
        SET skin_tone_group = ?, 
            body_shape = ?, 
            color_palette = ?, 
            do_list = ?, 
            dont_list = ?
        WHERE id = ?
    ");
    $stmt->bind_param("sssssi", $skin, $shape, $color, $do, $dont, $id);

    if ($stmt->execute()) {
        echo "<script>
            Swal.fire({
                icon: 'success',
                title: 'Rule berhasil diperbarui!',
                showConfirmButton: false,
                timer: 1500
            }).then(() => {
                window.location.href='?q=outfit_rules';
            });
        </script>";
    } else {
        echo "<script>
            Swal.fire({
                icon: 'error',
                title: 'Gagal memperbarui!',
                text: '" . addslashes($stmt->error) . "'
            }).then(() => {
                window.history.back();
            });
        </script>";
    }

    $stmt->close();
}
