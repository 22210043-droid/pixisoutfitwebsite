<?php
session_start();
include '../conf/conf.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
  header('Location: login');
  exit;
}

$email = trim($_POST['email']);
$password = trim($_POST['password']);
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <title>AI Style Coach | Login Process</title>
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>

<body>
  <?php
  if (empty($email) || empty($password)) {
    echo "<script>
    document.addEventListener('DOMContentLoaded', () => {
      Swal.fire({
        icon: 'warning',
        title: 'Data Tidak Lengkap',
        text: 'Harap isi email dan password!',
        confirmButtonColor: '#3085d6'
      }).then(() => {
        window.location.href = 'login';
      });
    });
  </script>";
    exit;
  }

  // ✅ Tambahkan kolom path_photo ke SELECT
  $stmt = $conn->prepare("SELECT id, name, email, password, role, path_photo FROM users WHERE email = ?");
  $stmt->bind_param("s", $email);
  $stmt->execute();
  $result = $stmt->get_result();

  if ($result->num_rows === 0) {
    echo "<script>
    document.addEventListener('DOMContentLoaded', () => {
      Swal.fire({
        icon: 'error',
        title: 'Email Tidak Ditemukan',
        text: 'Periksa kembali email kamu atau daftar terlebih dahulu.',
        confirmButtonColor: '#3085d6'
      }).then(() => {
        window.location.href = 'login';
      });
    });
  </script>";
    exit;
  }

  $user = $result->fetch_assoc();

  // Verifikasi password
  if (password_verify($password, $user['password'])) {
    // ✅ Simpan semua data penting ke session
    $_SESSION['user_id']    = $user['id'];
    $_SESSION['user_name']  = $user['name'];
    $_SESSION['user_role']  = $user['role'];
    $_SESSION['path_photo'] = $user['path_photo']; // ✅ foto profil

    echo "<script>
    document.addEventListener('DOMContentLoaded', () => {
      Swal.fire({
        icon: 'success',
        title: 'Login Berhasil!',
        text: 'Selamat datang, {$user['name']} 👋',
        confirmButtonColor: '#3085d6',
        timer: 1500,
        showConfirmButton: false
      }).then(() => {
        window.location.href = 'index';
      });
    });
  </script>";
  } else {
    echo "<script>
    document.addEventListener('DOMContentLoaded', () => {
      Swal.fire({
        icon: 'error',
        title: 'Password Salah',
        text: 'Periksa kembali password kamu!',
        confirmButtonColor: '#3085d6'
      }).then(() => {
        window.location.href = 'login';
      });
    });
  </script>";
  }

  $stmt->close();
  $conn->close();
  ?>
</body>

</html>