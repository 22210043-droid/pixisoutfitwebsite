<?php
if ($_GET['q'] === 'add_features') {
    $tab_key = $_POST['tab_key'];
    $tab_title = $_POST['tab_title'];
    $description = $_POST['description'];

    // Folder upload khusus untuk features
    $folder = __DIR__ . '/../../../uploads/features/';
    if (!is_dir($folder)) mkdir($folder, 0777, true);

    $image_before = $image_reference = $image_after = '';

    // Fungsi untuk membersihkan nama file
    function sanitize_filename($name)
    {
        // Ganti spasi, tanda kurung, dan karakter aneh dengan underscore
        $name = preg_replace('/[^\w.\-]/', '_', $name);
        return $name;
    }

    // Upload image_before
    if (!empty($_FILES['image_before']['name'])) {
        $filename = time() . '_before_' . sanitize_filename($_FILES['image_before']['name']);
        $target = $folder . $filename;
        if (move_uploaded_file($_FILES['image_before']['tmp_name'], $target)) {
            $image_before = 'uploads/features/' . $filename;
        }
    }

    // Upload image_reference
    if (!empty($_FILES['image_reference']['name'])) {
        $filename = time() . '_ref_' . sanitize_filename($_FILES['image_reference']['name']);
        $target = $folder . $filename;
        if (move_uploaded_file($_FILES['image_reference']['tmp_name'], $target)) {
            $image_reference = 'uploads/features/' . $filename;
        }
    }

    // Upload image_after
    if (!empty($_FILES['image_after']['name'])) {
        $filename = time() . '_after_' . sanitize_filename($_FILES['image_after']['name']);
        $target = $folder . $filename;
        if (move_uploaded_file($_FILES['image_after']['tmp_name'], $target)) {
            $image_after = 'uploads/features/' . $filename;
        }
    }

    // Simpan ke database
    $sql = "INSERT INTO features_section (tab_key, tab_title, description, image_before, image_reference, image_after, created_at)
            VALUES ('$tab_key','$tab_title','$description','$image_before','$image_reference','$image_after',NOW())";

    if ($conn->query($sql)) {
        echo "<script>
            Swal.fire({icon:'success',title:'Feature berhasil ditambahkan!'})
            .then(()=>{window.location.href='?q=features';});
        </script>";
    } else {
        echo "<script>
            Swal.fire({icon:'error',title:'Gagal menambah data!'});
        </script>";
    }
}
