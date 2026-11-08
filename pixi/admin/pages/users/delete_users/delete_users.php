<?php
if (isset($_GET['q']) && $_GET['q'] === 'delete_users' && isset($_GET['id'])) {
    $id = (int) $_GET['id'];

    $sql = "DELETE FROM users WHERE id='$id'";
    if (mysqli_query($conn, $sql)) {
        echo "<script>
            Swal.fire({ icon:'success', title:'User berhasil dihapus!', showConfirmButton:false, timer:1500 })
                .then(() => window.location.href='?q=users');
        </script>";
    } else {
        echo "<script>
            Swal.fire({ icon:'error', title:'Gagal menghapus user!', text:'" . mysqli_error($conn) . "' });
        </script>";
    }
}
