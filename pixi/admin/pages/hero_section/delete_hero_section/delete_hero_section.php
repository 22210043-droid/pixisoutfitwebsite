<?php
if ($_GET['q'] === 'delete_hero_section' && isset($_GET['id'])) {
    $id = (int) $_GET['id'];
    $data = $conn->query("SELECT image_path FROM hero_section WHERE id=$id")->fetch_assoc();

    if (!empty($data['image_path']) && file_exists(__DIR__ . '/../../../' . $data['image_path'])) {
        unlink(__DIR__ . '/../../../' . $data['image_path']);
    }

    if ($conn->query("DELETE FROM hero_section WHERE id=$id")) {
        echo "<script>Swal.fire({icon:'success',title:'Data hero dihapus!'}).then(()=>{window.location.href='?q=hero_section';});</script>";
    } else {
        echo "<script>Swal.fire({icon:'error',title:'Gagal hapus data!'});</script>";
    }
}
