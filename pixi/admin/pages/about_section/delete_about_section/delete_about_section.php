<?php
if ($_GET['q'] === 'delete_about_section' && isset($_GET['id'])) {
    $id = $_GET['id'];

    $result = $conn->query("SELECT * FROM about_section WHERE id='$id'");
    $row = $result->fetch_assoc();

    if ($row) {
        if (!empty($row['image_main']) && file_exists(__DIR__ . '/../../../' . $row['image_main'])) unlink(__DIR__ . '/../../../' . $row['image_main']);
        if (!empty($row['image_secondary']) && file_exists(__DIR__ . '/../../../' . $row['image_secondary'])) unlink(__DIR__ . '/../../../' . $row['image_secondary']);

        $delete = $conn->query("DELETE FROM about_section WHERE id='$id'");
        if ($delete) {
            echo "<script>
                Swal.fire({icon:'success',title:'Data berhasil dihapus!'})
                .then(()=>{window.location.href='?q=about_section';});
            </script>";
        } else {
            echo "<script>
                Swal.fire({icon:'error',title:'Gagal menghapus data!'});
            </script>";
        }
    } else {
        echo "<script>
            Swal.fire({icon:'error',title:'Data tidak ditemukan!'});
        </script>";
    }
}
