<?php
if (isset($_GET['q']) && $_GET['q'] === 'update_profile') {
    $id = (int) $_POST['id'];
    $name = mysqli_real_escape_string($conn, $_POST['name']);

    $folder = __DIR__ . '/../../../uploads/profile/';
    if (!is_dir($folder)) mkdir($folder, 0777, true);

    $update_photo = "";
    if (!empty($_FILES['path_photo']['name'])) {
        $filename = time() . '_' . basename($_FILES['path_photo']['name']);
        $tmp = $_FILES['path_photo']['tmp_name'];
        $target = $folder . $filename;

        if (move_uploaded_file($tmp, $target)) {
            $path_photo = 'uploads/profile/' . $filename;
            $update_photo = ", path_photo = '$path_photo'";
        }
    }

    $sql = "UPDATE users SET name = '$name' $update_photo WHERE id = '$id'";
    if (mysqli_query($conn, $sql)) {
        echo "<script>
            Swal.fire({
                icon: 'success',
                title: 'Profil berhasil diperbarui!',
                showConfirmButton: false,
                timer: 1500
            }).then(() => {
                window.location.href = '?q=myprofile';
            });
        </script>";
    } else {
        echo "<script>
            Swal.fire({
                icon: 'error',
                title: 'Gagal memperbarui profil!',
                text: '" . mysqli_error($conn) . "'
            }).then(() => {
                window.history.back();
            });
        </script>";
    }
}
