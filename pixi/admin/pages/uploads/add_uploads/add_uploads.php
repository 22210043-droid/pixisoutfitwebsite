<?php
if (isset($_GET['q']) && $_GET['q'] === 'add_uploads') {
    $user_id = 1; // nanti bisa diganti dengan $_SESSION['user_id']

    $dataset_type     = mysqli_real_escape_string($conn, trim($_POST['dataset_type'] ?? 'training'));
    $skin_tone_label  = mysqli_real_escape_string($conn, trim($_POST['skin_tone_label'] ?? ''));
    $body_shape_label = mysqli_real_escape_string($conn, trim($_POST['body_shape_label'] ?? ''));
    $skin_tone        = mysqli_real_escape_string($conn, trim($_POST['skin_tone'] ?? ''));
    $body_shape       = mysqli_real_escape_string($conn, trim($_POST['body_shape'] ?? ''));
    $slug             = substr(md5(uniqid(mt_rand(), true)), 0, 8);

    // validasi sederhana
    if ($dataset_type === '') {
        $dataset_type = 'training';
    }

    if ($skin_tone_label === '' || $body_shape_label === '') {
        echo "<script>
            Swal.fire({
                icon: 'warning',
                title: 'Data belum lengkap!',
                text: 'Skin tone label dan body shape label wajib diisi.'
            }).then(() => {
                window.history.back();
            });
        </script>";
        exit;
    }

    // Folder upload (samakan dengan update_profile)
    $folder = __DIR__ . '/../../../uploads/';
    if (!is_dir($folder)) mkdir($folder, 0777, true);

    $file_path = '';

    if (!empty($_FILES['file_path']['name'])) {

        $filename = time() . '_' . basename($_FILES['file_path']['name']);
        $tmp      = $_FILES['file_path']['tmp_name'];
        $target   = $folder . $filename;

        if (move_uploaded_file($tmp, $target)) {
            $file_path = 'uploads/' . $filename;
        } else {
            echo "<script>
            Swal.fire({
                icon: 'error',
                title: 'Upload gagal!',
                text: 'File tidak berhasil disimpan.'
            }).then(() => {
                window.history.back();
            });
        </script>";
            exit;
        }
    } else {
        echo "<script>
        Swal.fire({
            icon: 'warning',
            title: 'File wajib diisi!',
            text: 'Silakan pilih gambar terlebih dahulu.'
        }).then(() => {
            window.history.back();
        });
    </script>";
        exit;
    }

    $sql = "INSERT INTO uploads 
            (user_id, file_path, dataset_type, skin_tone_label, body_shape_label, skin_tone, body_shape, slug, created_at)
            VALUES 
            ('$user_id', '$file_path', '$dataset_type', '$skin_tone_label', '$body_shape_label', '$skin_tone', '$body_shape', '$slug', NOW())";

    if (mysqli_query($conn, $sql)) {
        echo "<script>
            Swal.fire({
                icon: 'success',
                title: 'Upload berhasil ditambahkan!',
                showConfirmButton: false,
                timer: 1500
            }).then(() => {
                window.location.href = '?q=uploads';
            });
        </script>";
    } else {
        echo "<script>
            Swal.fire({
                icon: 'error',
                title: 'Gagal menambahkan upload!',
                text: '" . addslashes(mysqli_error($conn)) . "'
            }).then(() => {
                window.history.back();
            });
        </script>";
    }
}
