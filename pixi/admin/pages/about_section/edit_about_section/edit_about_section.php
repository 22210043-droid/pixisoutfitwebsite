<?php
if ($_GET['q'] === 'edit_about_section') {
    $id = $_POST['id'];
    $title = $_POST['title'];
    $subtitle = $_POST['subtitle'];
    $description = $_POST['description'];
    $btn_primary_text = $_POST['btn_primary_text'];
    $btn_primary_link = $_POST['btn_primary_link'];
    $btn_secondary_text = $_POST['btn_secondary_text'];
    $btn_secondary_link = $_POST['btn_secondary_link'];

    $folder = __DIR__ . '/../../../uploads/about/';
    if (!is_dir($folder)) mkdir($folder, 0777, true);

    // Ambil data lama
    $result = $conn->query("SELECT * FROM about_section WHERE id='$id'");
    $old = $result->fetch_assoc();

    $image_main = $old['image_main'];
    $image_secondary = $old['image_secondary'];

    // Update image_main
    if (!empty($_FILES['image_main']['name'])) {
        $filename = time() . '_main_' . basename($_FILES['image_main']['name']);
        $target = $folder . $filename;
        if (move_uploaded_file($_FILES['image_main']['tmp_name'], $target)) {
            if (!empty($image_main) && file_exists(__DIR__ . '/../../../' . $image_main)) {
                unlink(__DIR__ . '/../../../' . $image_main);
            }
            $image_main = 'uploads/about/' . $filename;
        }
    }

    // Update image_secondary
    if (!empty($_FILES['image_secondary']['name'])) {
        $filename = time() . '_secondary_' . basename($_FILES['image_secondary']['name']);
        $target = $folder . $filename;
        if (move_uploaded_file($_FILES['image_secondary']['tmp_name'], $target)) {
            if (!empty($image_secondary) && file_exists(__DIR__ . '/../../../' . $image_secondary)) {
                unlink(__DIR__ . '/../../../' . $image_secondary);
            }
            $image_secondary = 'uploads/about/' . $filename;
        }
    }

    // Update data ke database
    $sql = "UPDATE about_section 
            SET title='$title',
                subtitle='$subtitle',
                description='$description',
                btn_primary_text='$btn_primary_text',
                btn_primary_link='$btn_primary_link',
                btn_secondary_text='$btn_secondary_text',
                btn_secondary_link='$btn_secondary_link',
                image_main='$image_main',
                image_secondary='$image_secondary',
                updated_at=NOW()
            WHERE id='$id'";

    if ($conn->query($sql)) {
        echo "<script>
            Swal.fire({icon:'success',title:'About Section berhasil diperbarui!'})
            .then(()=>{window.location.href='?q=about_section';});
        </script>";
    } else {
        echo "<script>
            Swal.fire({icon:'error',title:'Gagal memperbarui data!'});
        </script>";
    }
}
