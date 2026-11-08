<?php
if (isset($_GET['q']) && $_GET['q'] === 'delete_uploads' && isset($_GET['id'])) {
    $id = (int) $_GET['id'];

    // Ambil data upload dulu
    $stmt = $conn->prepare("SELECT file_path FROM uploads WHERE id = ? LIMIT 1");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    $stmt->close();

    if (!$row) {
        echo "<script>
            Swal.fire({
                icon: 'warning',
                title: 'Data tidak ditemukan!',
                text: 'Upload yang ingin dihapus tidak tersedia.'
            }).then(() => {
                window.location.href = '?q=uploads';
            });
        </script>";
        exit;
    }

    // Hapus file gambar jika ada
    if (!empty($row['file_path'])) {
        $fullPath = $_SERVER['DOCUMENT_ROOT'] . '/putriti/' . $row['file_path'];

        if (file_exists($fullPath) && is_file($fullPath)) {
            unlink($fullPath);
        }
    }

    // Hapus rekomendasi yang terkait jika ada
    $stmtRec = $conn->prepare("DELETE FROM recommendations WHERE upload_id = ?");
    $stmtRec->bind_param("i", $id);
    $stmtRec->execute();
    $stmtRec->close();

    // Hapus data upload
    $stmtDelete = $conn->prepare("DELETE FROM uploads WHERE id = ?");
    $stmtDelete->bind_param("i", $id);

    if ($stmtDelete->execute()) {
        echo "<script>
            Swal.fire({
                icon: 'success',
                title: 'Data upload berhasil dihapus!',
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
                title: 'Gagal menghapus data!',
                text: '" . addslashes($stmtDelete->error) . "'
            }).then(() => {
                window.history.back();
            });
        </script>";
    }

    $stmtDelete->close();
}
