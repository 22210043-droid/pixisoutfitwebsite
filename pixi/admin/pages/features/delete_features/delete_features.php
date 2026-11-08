<?php
if ($_GET['q'] === 'delete_features') {
    $id = $_GET['id'];
    $result = $conn->query("SELECT * FROM features_section WHERE id='$id'");
    $row = $result->fetch_assoc();

    // Hapus gambar jika ada
    $images = ['image_before', 'image_reference', 'image_after'];
    foreach ($images as $img) {
        if (!empty($row[$img]) && file_exists(__DIR__ . '/../../../' . $row[$img])) {
            unlink(__DIR__ . '/../../../' . $row[$img]);
        }
    }

    $sql = "DELETE FROM features_section WHERE id='$id'";
    if ($conn->query($sql)) {
        echo "<script>
            Swal.fire({icon:'success',title:'Feature berhasil dihapus!'})
            .then(()=>{window.location.href='?q=features';});
        </script>";
    } else {
        echo "<script>
            Swal.fire({icon:'error',title:'Gagal menghapus data!'});
        </script>";
    }
}
