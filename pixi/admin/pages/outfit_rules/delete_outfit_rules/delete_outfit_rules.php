<?php
if (isset($_GET['q']) && $_GET['q'] === 'delete_outfit_rules' && isset($_GET['id'])) {
    $id = (int) $_GET['id'];

    // Cek dulu apakah data ada
    $check = $conn->prepare("SELECT id FROM outfit_rules WHERE id = ? LIMIT 1");
    $check->bind_param("i", $id);
    $check->execute();
    $result = $check->get_result();
    $row = $result->fetch_assoc();
    $check->close();

    if (!$row) {
        echo "<script>
            Swal.fire({
                icon: 'warning',
                title: 'Data tidak ditemukan!',
                text: 'Rule yang ingin dihapus tidak tersedia.'
            }).then(() => {
                window.location.href='?q=outfit_rules';
            });
        </script>";
        exit;
    }

    $stmt = $conn->prepare("DELETE FROM outfit_rules WHERE id = ?");
    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
        echo "<script>
            Swal.fire({
                icon: 'success',
                title: 'Rule berhasil dihapus!',
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
                title: 'Gagal menghapus!',
                text: '" . addslashes($stmt->error) . "'
            }).then(() => {
                window.history.back();
            });
        </script>";
    }

    $stmt->close();
}
