<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>AI Style Coach | Sign Up</title>
    <link rel="shortcut icon" type="image/png" href="assets/images/favi.png" />
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
                            <img src="assets/logo.png" alt="AI Style Coach" width="150" class="mb-3">
                            <h4 class="fw-bold mb-1">Join <span class="text-primary">AI Style Coach</span></h4>
                            <p class="text-muted">Create your account and start your personalized fashion journey</p>
                        </div>

                        <form action="regis_proses" method="POST">
                            <div class="mb-3">
                                <label class="form-label fw-semibold">Full Name</label>
                                <input type="text" name="name" class="form-control form-control-lg"
                                    placeholder="Your full name" required>
                            </div>

                            <div class="mb-3">
                                <label class="form-label fw-semibold">Email Address</label>
                                <input type="email" name="email" class="form-control form-control-lg"
                                    placeholder="you@example.com" required>
                            </div>

                            <div class="mb-4">
                                <label class="form-label fw-semibold">Password</label>
                                <input type="password" name="password" class="form-control form-control-lg"
                                    placeholder="Create a strong password" required>
                            </div>

                            <button type="submit" class="btn btn-primary w-100 py-3 fs-5 mb-3">Sign Up</button>

                            <div class="text-center">
                                <p class="fs-6 mb-0 fw-medium">Already have an account?</p>
                                <a href="./" class="fw-bold text-primary">Sign In</a>
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
        // (Opsional) SweetAlert untuk notifikasi registrasi sukses
        const urlParams = new URLSearchParams(window.location.search);
        if (urlParams.get('success') === '1') {
            Swal.fire({
                icon: 'success',
                title: 'Registration Successful!',
                text: 'Your account has been created successfully. You can now log in.',
                confirmButtonColor: '#3085d6',
                confirmButtonText: 'OK'
            });
        }
    </script>
</body>

</html>