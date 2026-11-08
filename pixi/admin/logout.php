<?php
session_start();
session_unset();
session_destroy();
?>
<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Logout | AI Style Coach</title>
    <link rel="shortcut icon" type="image/png" href="assets/images/logos/favicon.png" />
    <link rel="stylesheet" href="assets/css/styles.min.css" />
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>

<body class="bg-light d-flex align-items-center justify-content-center" style="height:100vh;">
    <script>
        Swal.fire({
            icon: 'info',
            title: 'Logout Berhasil',
            text: 'Kamu telah keluar dengan aman dari sistem.',
            showConfirmButton: false,
            timer: 1800,
            timerProgressBar: true,
            backdrop: true
        }).then(() => {
            window.location.href = './';
        });
    </script>
</body>

</html>