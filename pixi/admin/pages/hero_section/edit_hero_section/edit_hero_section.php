<?php
if ($_GET['q'] === 'edit_hero_section') {
    $id = $_POST['id'];
    $title = $_POST['title'];
    $subtitle = $_POST['subtitle'];
    $btn1t = $_POST['btn_start_text'];
    $btn1l = $_POST['btn_start_link'];
    $btn2t = $_POST['btn_video_text'];
    $btn2l = $_POST['btn_video_link'];

    // Folder upload (sama seperti add)
    $folder = __DIR__ . '/../../../uploads/';
    if (!is_dir($folder)) mkdir($folder, 0777, true);

    // Ambil data lama untuk mengetahui path gambar sebelumnya
    $result = $conn->query("SELECT image_path FROM hero_section WHERE id='$id'");
    $oldData = $result->fetch_assoc();
    $oldImage = $oldData['image_path'];

    $image = $oldImage; // default tetap gambar lama
    if (!empty($_FILES['image_path']['name'])) {
        $filename = time() . '_' . basename($_FILES['image_path']['name']);
        $tmp = $_FILES['image_path']['tmp_name'];
        $target = $folder . $filename;

        if (move_uploaded_file($tmp, $target)) {
            $image = 'uploads/' . $filename;

            // Hapus gambar lama jika ada dan berbeda
            if (!empty($oldImage) && file_exists(__DIR__ . '/../../../' . $oldImage)) {
                unlink(__DIR__ . '/../../../' . $oldImage);
            }
        }
    }

    $sql = "UPDATE hero_section 
            SET title='$title',
                subtitle='$subtitle',
                btn_start_text='$btn1t',
                btn_start_link='$btn1l',
                btn_video_text='$btn2t',
                btn_video_link='$btn2l',
                image_path='$image',
                updated_at=NOW()
            WHERE id='$id'";

    if ($conn->query($sql)) {
        echo "<script>
            Swal.fire({
                icon:'success',
                title:'Data hero berhasil diperbarui!'
            }).then(()=>{window.location.href='?q=hero_section';});
        </script>";
    } else {
        echo "<script>
            Swal.fire({
                icon:'error',
                title:'Gagal memperbarui data!'
            });
        </script>";
    }
}
