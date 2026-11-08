<?php
if (isset($_GET['q']) && $_GET['q'] === 'add_outfit_rules') {
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

    // Cegah duplikat kombinasi skin_tone_group + body_shape
    $check = $conn->prepare("SELECT id FROM outfit_rules WHERE skin_tone_group = ? AND body_shape = ? LIMIT 1");
    $check->bind_param("ss", $skin, $shape);
    $check->execute();
    $check->store_result();

    if ($check->num_rows > 0) {
        $check->close();

        echo "<script>
            Swal.fire({
                icon: 'warning',
                title: 'Rule sudah ada!',
                text: 'Kombinasi skin tone group dan body shape ini sudah tersimpan.'
            }).then(() => {
                window.location.href='?q=outfit_rules';
            });
        </script>";
        exit;
    }
    $check->close();

    $stmt = $conn->prepare("
        INSERT INTO outfit_rules (skin_tone_group, body_shape, color_palette, do_list, dont_list)
        VALUES (?, ?, ?, ?, ?)
    ");
    $stmt->bind_param("sssss", $skin, $shape, $color, $do, $dont);

    if ($stmt->execute()) {
        echo "<script>
            Swal.fire({
                icon: 'success',
                title: 'Rule berhasil ditambahkan!',
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
                title: 'Gagal menambahkan!',
                text: '" . addslashes($stmt->error) . "'
            }).then(() => {
                window.history.back();
            });
        </script>";
    }

    $stmt->close();
}
