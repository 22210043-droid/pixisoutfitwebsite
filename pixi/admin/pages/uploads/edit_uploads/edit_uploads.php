<?php
if (isset($_GET['q']) && $_GET['q'] === 'edit_uploads' && isset($_GET['id'])) {

    $id = (int) $_GET['id'];

    $dataset_type     = mysqli_real_escape_string($conn, trim($_POST['dataset_type'] ?? 'training'));
    $skin_tone_label  = mysqli_real_escape_string($conn, trim($_POST['skin_tone_label'] ?? ''));
    $body_shape_label = mysqli_real_escape_string($conn, trim($_POST['body_shape_label'] ?? ''));

    if ($dataset_type === '') {
        $dataset_type = 'training';
    }

    if ($skin_tone_label === '' || $body_shape_label === '') {
        echo "<script>
            Swal.fire({
                icon: 'warning',
                title: 'Label wajib diisi!',
                text: 'Skin tone label dan body shape label tidak boleh kosong.'
            }).then(() => {
                window.history.back();
            });
        </script>";
        exit;
    }

    // Folder upload, disamakan dengan update_profile
    $folder = __DIR__ . '/../../../uploads/';
    if (!is_dir($folder)) {
        mkdir($folder, 0777, true);
    }

    $update_file = "";

    if (!empty($_FILES['file_path']['name'])) {

        $filename = time() . '_' . preg_replace('/\s+/', '_', basename($_FILES['file_path']['name']));
        $tmp      = $_FILES['file_path']['tmp_name'];
        $target   = $folder . $filename;

        if (move_uploaded_file($tmp, $target)) {
            $file_path = 'uploads/' . $filename;
            $update_file = ", file_path = '$file_path'";
        } else {
            echo "<script>
                Swal.fire({
                    icon: 'error',
                    title: 'Upload gagal!',
                    text: 'File gambar tidak berhasil disimpan.'
                }).then(() => {
                    window.history.back();
                });
            </script>";
            exit;
        }
    }

    $sql = "UPDATE uploads 
            SET 
                dataset_type = '$dataset_type',
                skin_tone_label = '$skin_tone_label',
                body_shape_label = '$body_shape_label'
                $update_file
            WHERE id = '$id'";

    if (mysqli_query($conn, $sql)) {
        echo "<script>
            Swal.fire({
                icon: 'success',
                title: 'Data berhasil diperbarui!',
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
                title: 'Gagal update!',
                text: '" . addslashes(mysqli_error($conn)) . "'
            }).then(() => {
                window.history.back();
            });
        </script>";
    }
}
