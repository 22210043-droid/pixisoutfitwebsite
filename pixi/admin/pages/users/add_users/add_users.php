<?php
if (isset($_GET['q']) && $_GET['q'] === 'add_users') {
    $name     = mysqli_real_escape_string($conn, $_POST['name']);
    $email    = mysqli_real_escape_string($conn, $_POST['email']);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $role     = mysqli_real_escape_string($conn, $_POST['role']);

    $sql = "INSERT INTO users (name, email, password, role, created_at)
            VALUES ('$name', '$email', '$password', '$role', NOW())";

    if (mysqli_query($conn, $sql)) {
        echo "<script>
            Swal.fire({ icon:'success', title:'User berhasil ditambahkan!', showConfirmButton:false, timer:1500 })
                .then(() => window.location.href='?q=users');
        </script>";
    } else {
        echo "<script>
            Swal.fire({ icon:'error', title:'Gagal menambahkan user!', text:'" . mysqli_error($conn) . "' });
        </script>";
    }
}
