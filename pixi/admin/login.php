<?php
session_start();
if (isset($_SESSION['user_id'])) {
  header('Location: index');
  exit;
}
?>

<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>AI Style Coach | Sign In</title>
    <link rel="shortcut icon" type="image/png" href="assets/images/fav.png" />
    <link rel="stylesheet" href="assets/css/styles.min.css" />
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>

<body class="bg-light">
    <div class="page-wrapper" id="main-wrapper" data-layout="vertical" data-navbarbg="skin6" data-sidebartype="full"
        data-sidebar-position="fixed" data-header-position="fixed">

        <div class="min-vh-100 d-flex align-items-center justify-content-center">
            <div class="col-md-8 col-lg-5 col-xl-4">
                <div class="card shadow-lg border-0 rounded-4">
                    <div class="card-body p-5">
                        <div class="text-center mb-4">
                            <img src="assets/log.png" alt="AI Style Coach" width="150" class="mb-3">
                            <h4 class="fw-bold mb-1">Welcome to <span class="text-primary">AI Style Coach</span></h4>
                            <p class="text-muted">Log in to your premium AI fashion experience</p>
                        </div>

                        <form action="login_proses" method="POST">
                            <div class="mb-3">
                                <label class="form-label fw-semibold">Email</label>
                                <input type="email" name="email" class="form-control form-control-lg"
                                    placeholder="you@example.com" required>
                            </div>

                            <div class="mb-2">
                                <label class="form-label fw-semibold">Password</label>
                                <input type="password" name="password" class="form-control form-control-lg"
                                    placeholder="Enter your password" required>
                            </div>

                            <!-- Info pengingat login -->
                            <small class="text-muted d-block mb-4">
                                Your credentials are securely encrypted.
                            </small>

                            <div class="d-flex align-items-center justify-content-between mb-4">
                                <span></span>
                                <a href="#" id="forgotBtn" class="text-primary fw-semibold">Forgot Password?</a>
                            </div>

                            <button type="submit" class="btn btn-primary w-100 py-3 fs-5 mb-3">Sign In</button>

                            <div class="text-center">
                                <p class="fs-6 mb-0 fw-medium">New to <span class="text-primary">AI Style Coach</span>?
                                </p>
                                <a href="regis" class="fw-bold text-primary">Create an account</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="assets/libs/jquery/dist/jquery.min.js"></script>
    <script src="assets/libs/bootstrap/dist/js/bootstrap.bundle.min.js"></script>
    <script>
    // SweetAlert untuk tombol "Forgot Password"
    document.getElementById("forgotBtn").addEventListener("click", function(e) {
        e.preventDefault();
        Swal.fire({
            icon: "info",
            title: "Segera Hadir!",
            text: "Fitur pemulihan kata sandi akan segera tersedia.",
            confirmButtonColor: "#3085d6",
            confirmButtonText: "Mengerti"
        });
    });
    </script>
</body>

</html>