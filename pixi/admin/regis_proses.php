<?php
include '../conf/conf.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: register');
    exit;
}

$name = trim($_POST['name']);
$email = trim($_POST['email']);
$password = trim($_POST['password']);

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>AI Style Coach | Register Process</title>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>

<body>
    <?php

    // Cek kolom kosong
    if (empty($name) || empty($email) || empty($password)) {
        echo "<script>
        document.addEventListener('DOMContentLoaded', () => {
            Swal.fire({
                icon: 'warning',
                title: 'Data Tidak Lengkap',
                text: 'Semua kolom wajib diisi!',
                confirmButtonColor: '#3085d6'
            }).then(() => {
                window.location.href = 'register';
            });
        });
    </script>";
        exit;
    }

    // Cek duplikasi email
    $check = $conn->prepare("SELECT id FROM users WHERE email = ?");
    $check->bind_param("s", $email);
    $check->execute();
    $check->store_result();

    if ($check->num_rows > 0) {
        echo "<script>
        document.addEventListener('DOMContentLoaded', () => {
            Swal.fire({
                icon: 'error',
                title: 'Email Sudah Terdaftar',
                text: 'Gunakan email lain atau login dengan akun ini.',
                confirmButtonColor: '#3085d6'
            }).then(() => {
                window.location.href = 'login';
            });
        });
    </script>";
        exit;
    }

    // Enkripsi password
    $hashed = password_hash($password, PASSWORD_DEFAULT);
    $role = 'user';

    // Simpan data user baru
    $stmt = $conn->prepare("INSERT INTO users (name, email, password, role, created_at) VALUES (?, ?, ?, ?, NOW())");
    $stmt->bind_param("ssss", $name, $email, $hashed, $role);

    if ($stmt->execute()) {
        echo "<script>
        document.addEventListener('DOMContentLoaded', () => {
            Swal.fire({
                icon: 'success',
                title: 'Registrasi Berhasil!',
                text: 'Akun kamu sudah dibuat. Silakan login sekarang.',
                confirmButtonColor: '#3085d6'
            }).then(() => {
                window.location.href = 'login';
            });
        });
    </script>";
    } else {
        echo "<script>
        document.addEventListener('DOMContentLoaded', () => {
            Swal.fire({
                icon: 'error',
                title: 'Gagal Menyimpan Data',
                text: 'Terjadi kesalahan saat menyimpan data. Coba lagi nanti.',
                confirmButtonColor: '#3085d6'
            }).then(() => {
                window.location.href = 'register';
            });
        });
    </script>";
    }

    $stmt->close();
    $conn->close();
    ?>
</body>

</html>