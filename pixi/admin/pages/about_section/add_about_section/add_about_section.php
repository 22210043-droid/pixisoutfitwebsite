<?php
if ($_GET['q'] === 'add_about_section') {
    $title = $_POST['title'];
    $subtitle = $_POST['subtitle'];
    $description = $_POST['description'];
    $btn_primary_text = $_POST['btn_primary_text'];
    $btn_primary_link = $_POST['btn_primary_link'];
    $btn_secondary_text = $_POST['btn_secondary_text'];
    $btn_secondary_link = $_POST['btn_secondary_link'];

    // Folder upload khusus untuk about section
    $folder = __DIR__ . '/../../../uploads/about/';
    if (!is_dir($folder)) mkdir($folder, 0777, true);

    $image_main = $image_secondary = '';

    // Upload image_main
    if (!empty($_FILES['image_main']['name'])) {
        $filename = time() . '_main_' . basename($_FILES['image_main']['name']);
        $target = $folder . $filename;
        if (move_uploaded_file($_FILES['image_main']['tmp_name'], $target)) {
            $image_main = 'uploads/about/' . $filename;
        }
    }

    // Upload image_secondary
    if (!empty($_FILES['image_secondary']['name'])) {
        $filename = time() . '_secondary_' . basename($_FILES['image_secondary']['name']);
        $target = $folder . $filename;
        if (move_uploaded_file($_FILES['image_secondary']['tmp_name'], $target)) {
            $image_secondary = 'uploads/about/' . $filename;
        }
    }

    // Query sesuai struktur tabel kamu
    $sql = "INSERT INTO about_section 
        (title, subtitle, description, btn_primary_text, btn_primary_link, btn_secondary_text, btn_secondary_link, image_main, image_secondary, created_at) 
        VALUES 
        ('$title','$subtitle','$description','$btn_primary_text','$btn_primary_link','$btn_secondary_text','$btn_secondary_link','$image_main','$image_secondary',NOW())";

    if ($conn->query($sql)) {
        echo "<script>
            Swal.fire({icon:'success',title:'About Section berhasil ditambahkan!'})
            .then(()=>{window.location.href='?q=about_section';});
        </script>";
    } else {
        echo "<script>
            Swal.fire({icon:'error',title:'Gagal menambah data!'});
        </script>";
    }
}
