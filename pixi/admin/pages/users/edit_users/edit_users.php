<?php
if (isset($_GET['q']) && $_GET['q'] === 'edit_users' && isset($_GET['id'])) {
    $id    = (int) $_GET['id'];
    $name  = mysqli_real_escape_string($conn, $_POST['name']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $role  = mysqli_real_escape_string($conn, $_POST['role']);

    $sql = "UPDATE users SET name='$name', email='$email', role='$role' WHERE id='$id'";
    if (mysqli_query($conn, $sql)) {
        echo "<script>
            Swal.fire({ icon:'success', title:'User berhasil diperbarui!', showConfirmButton:false, timer:1500 })
                .then(() => window.location.href='?q=users');
        </script>";
    } else {
        echo "<script>
            Swal.fire({ icon:'error', title:'Gagal memperbarui user!', text:'" . mysqli_error($conn) . "' });
        </script>";
    }
}
