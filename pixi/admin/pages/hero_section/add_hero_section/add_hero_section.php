<?php
if ($_GET['q'] === 'add_hero_section') {
    $title = $_POST['title'];
    $subtitle = $_POST['subtitle'];
    $btn1t = $_POST['btn_start_text'];
    $btn1l = $_POST['btn_start_link'];
    $btn2t = $_POST['btn_video_text'];
    $btn2l = $_POST['btn_video_link'];

    $folder = __DIR__ . '/../../../uploads/';
    if (!is_dir($folder)) mkdir($folder, 0777, true);

    $image = '';
    if (!empty($_FILES['image_path']['name'])) {
        $filename = time() . '_' . basename($_FILES['image_path']['name']);
        $tmp = $_FILES['image_path']['tmp_name'];
        $target = $folder . $filename;
        if (move_uploaded_file($tmp, $target)) $image = 'uploads/' . $filename;
    }

    $sql = "INSERT INTO hero_section (title, subtitle, btn_start_text, btn_start_link, btn_video_text, btn_video_link, image_path, created_at)
            VALUES ('$title','$subtitle','$btn1t','$btn1l','$btn2t','$btn2l','$image',NOW())";

    if ($conn->query($sql)) {
        echo "<script>Swal.fire({icon:'success',title:'Hero berhasil ditambah!'}).then(()=>{window.location.href='?q=hero_section';});</script>";
    } else {
        echo "<script>Swal.fire({icon:'error',title:'Gagal menambah data!'});</script>";
    }
}
