<?php
if ($_GET['q'] === 'edit_features') {
    $id = $_POST['id'];
    $tab_key = $_POST['tab_key'];
    $tab_title = $_POST['tab_title'];
    $description = $_POST['description'];

    $folder = __DIR__ . '/../../../uploads/features/';
    if (!is_dir($folder)) mkdir($folder, 0777, true);

    // Ambil data lama
    $result = $conn->query("SELECT * FROM features_section WHERE id='$id'");
    $old = $result->fetch_assoc();

    $image_before = $old['image_before'];
    $image_reference = $old['image_reference'];
    $image_after = $old['image_after'];

    // Update image_before
    if (!empty($_FILES['image_before']['name'])) {
        $filename = time() . '_before_' . basename($_FILES['image_before']['name']);
        $target = $folder . $filename;
        if (move_uploaded_file($_FILES['image_before']['tmp_name'], $target)) {
            if (!empty($image_before) && file_exists(__DIR__ . '/../../../' . $image_before)) unlink(__DIR__ . '/../../../' . $image_before);
            $image_before = 'uploads/features/' . $filename;
        }
    }

    // Update image_reference
    if (!empty($_FILES['image_reference']['name'])) {
        $filename = time() . '_ref_' . basename($_FILES['image_reference']['name']);
        $target = $folder . $filename;
        if (move_uploaded_file($_FILES['image_reference']['tmp_name'], $target)) {
            if (!empty($image_reference) && file_exists(__DIR__ . '/../../../' . $image_reference)) unlink(__DIR__ . '/../../../' . $image_reference);
            $image_reference = 'uploads/features/' . $filename;
        }
    }

    // Update image_after
    if (!empty($_FILES['image_after']['name'])) {
        $filename = time() . '_after_' . basename($_FILES['image_after']['name']);
        $target = $folder . $filename;
        if (move_uploaded_file($_FILES['image_after']['tmp_name'], $target)) {
            if (!empty($image_after) && file_exists(__DIR__ . '/../../../' . $image_after)) unlink(__DIR__ . '/../../../' . $image_after);
            $image_after = 'uploads/features/' . $filename;
        }
    }

    $sql = "UPDATE features_section 
            SET tab_key='$tab_key',
                tab_title='$tab_title',
                description='$description',
                image_before='$image_before',
                image_reference='$image_reference',
                image_after='$image_after',
                updated_at=NOW()
            WHERE id='$id'";

    if ($conn->query($sql)) {
        echo "<script>
            Swal.fire({icon:'success',title:'Feature berhasil diperbarui!'})
            .then(()=>{window.location.href='?q=features';});
        </script>";
    } else {
        echo "<script>
            Swal.fire({icon:'error',title:'Gagal memperbarui data!'});
        </script>";
    }
}
