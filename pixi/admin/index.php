<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: login');
    exit;
}

include '../conf/conf.php';
$id = $_SESSION['user_id'];
?>

<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Admin Pixis</title>
    <link rel="shortcut icon" type="image/png" href="assets/images/fav.png" />
    <link rel="stylesheet" href="assets/css/styles.min.css" />


    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>

<body>
    <!--  Body Wrapper -->
    <div class="page-wrapper" id="main-wrapper" data-layout="vertical" data-navbarbg="skin6" data-sidebartype="full"
        data-sidebar-position="fixed" data-header-position="fixed">
        <!--  App Topstrip -->
        <div class="app-topstrip bg-dark py-3 px-3 w-100 d-flex align-items-center justify-content-center">
            <marquee behavior="scroll" direction="left" scrollamount="6" class="text-white fw-semibold">
                🌟 Selamat datang di <span class="text-primary">AI Style Coach</span> Dashboard |
                Nikmati kemudahan analisis warna, rekomendasi outfit, dan personalisasi gaya dengan AI! 💃🕺
            </marquee>
        </div>

        <!-- Sidebar Start -->
        <aside class="left-sidebar">
            <div>
                <!-- Logo -->
                <div class="brand-logo d-flex align-items-center justify-content-between">
                    <a href="index" class="text-nowrap logo-img">
                        <img src="assets/log.png" alt="" width="200" />
                    </a>
                    <div class="close-btn d-xl-none d-block sidebartoggler cursor-pointer" id="sidebarCollapse">
                        <i class="ti ti-x fs-8"></i>
                    </div>
                </div>

                <!-- Sidebar Navigation -->
                <nav class="sidebar-nav scroll-sidebar" data-simplebar="">
                    <ul id="sidebarnav">
                        <!-- HOME -->
                        <li class="nav-small-cap">
                            <iconify-icon icon="solar:menu-dots-linear" class="nav-small-cap-icon fs-4"></iconify-icon>
                            <span class="hide-menu">Main Menu</span>
                        </li>

                        <!-- DASHBOARD -->
                        <li class="sidebar-item">
                            <a class="sidebar-link primary-hover-bg" href="index" aria-expanded="false">
                                <iconify-icon icon="solar:atom-line-duotone"></iconify-icon>
                                <span class="hide-menu">Dashboard</span>
                            </a>
                        </li>

                        <!-- UPLOADS -->
                        <li class="sidebar-item">
                            <a class="sidebar-link primary-hover-bg" href="?q=uploads" aria-expanded="false">
                                <iconify-icon icon="solar:cloud-upload-line-duotone"></iconify-icon>
                                <span class="hide-menu">Uploads</span>
                            </a>
                        </li>

                        <!-- RECOMMENDATIONS -->
                        <li class="sidebar-item">
                            <a class="sidebar-link primary-hover-bg" href="?q=recommendations" aria-expanded="false">
                                <iconify-icon icon="solar:magic-stick-line-duotone"></iconify-icon>
                                <span class="hide-menu">Recommendations</span>
                            </a>
                        </li>

                        <!-- OUTFIT RULES -->
                        <li class="sidebar-item">
                            <a class="sidebar-link primary-hover-bg" href="?q=outfit_rules" aria-expanded="false">
                                <iconify-icon icon="solar:t-shirt-line-duotone"></iconify-icon>
                                <span class="hide-menu">Outfit Rules</span>
                            </a>
                        </li>

                        <!-- USERS -->
                        <li class="sidebar-item">
                            <a class="sidebar-link primary-hover-bg" href="?q=users" aria-expanded="false">
                                <iconify-icon icon="solar:user-id-line-duotone"></iconify-icon>
                                <span class="hide-menu">Users</span>
                            </a>
                        </li>

                        <li class="sidebar-item">
                            <a class="sidebar-link primary-hover-bg" href="?q=hero_section" aria-expanded="false">
                                <iconify-icon icon="solar:star-fall-line-duotone"></iconify-icon>
                                <span class="hide-menu">Hero Section</span>
                            </a>
                        </li>

                        <!-- FEATURES SECTION (baru) -->
                        <li class="sidebar-item">
                            <a class="sidebar-link primary-hover-bg" href="?q=features" aria-expanded="false">
                                <iconify-icon icon="solar:layers-line-duotone"></iconify-icon>
                                <span class="hide-menu">Features Section</span>
                            </a>
                        </li>

                        <!-- ABOUT SECTION (baru) -->
                        <li class="sidebar-item">
                            <a class="sidebar-link primary-hover-bg" href="?q=about_section" aria-expanded="false">
                                <iconify-icon icon="solar:info-circle-line-duotone"></iconify-icon>
                                <span class="hide-menu">About Section</span>
                            </a>
                        </li>

                        <!-- LOGOUT -->
                        <li class="sidebar-item mt-4">
                            <a class="sidebar-link text-danger" href="logout" aria-expanded="false">
                                <iconify-icon icon="solar:logout-2-line-duotone"></iconify-icon>
                                <span class="hide-menu fw-semibold">Logout</span>
                            </a>
                        </li>
                    </ul>
                </nav>
                <!-- End Sidebar Navigation -->
            </div>
        </aside>
        <!-- Sidebar End -->


        <!--  Main wrapper -->
        <div class="body-wrapper">

            <div class="body-wrapper-inner">
                <div class="container-fluid">
                    <!--  Header Start -->
                    <header class="app-header">
                        <nav class="navbar navbar-expand-lg navbar-light">
                            <ul class="navbar-nav d-flex align-items-center">
                                <!-- Tampilkan jam langsung -->
                                <li class="nav-item d-flex align-items-center ms-3">
                                    <iconify-icon icon="solar:clock-circle-line-duotone" class="fs-5 text-primary me-2">
                                    </iconify-icon>
                                    <div class="text-start">
                                        <div id="clock" class="fw-bold text-dark fs-6"></div>
                                        <div id="date" class="text-muted small"></div>
                                    </div>
                                </li>
                            </ul>

                            <!-- Script waktu -->
                            <script>
                            function updateClock() {
                                const now = new Date();

                                const days = [
                                    "Minggu", "Senin", "Selasa", "Rabu",
                                    "Kamis", "Jumat", "Sabtu"
                                ];
                                const months = [
                                    "Januari", "Februari", "Maret", "April",
                                    "Mei", "Juni", "Juli", "Agustus",
                                    "September", "Oktober", "November", "Desember"
                                ];

                                const dayName = days[now.getDay()];
                                const day = now.getDate();
                                const month = months[now.getMonth()];
                                const year = now.getFullYear();

                                let hours = now.getHours();
                                let minutes = now.getMinutes();
                                let seconds = now.getSeconds();

                                hours = hours < 10 ? "0" + hours : hours;
                                minutes = minutes < 10 ? "0" + minutes : minutes;
                                seconds = seconds < 10 ? "0" + seconds : seconds;

                                document.getElementById("clock").textContent = `${hours}:${minutes}:${seconds}`;
                                document.getElementById("date").textContent = `${dayName}, ${day} ${month} ${year}`;
                            }

                            setInterval(updateClock, 1000);
                            updateClock();
                            </script>

                            <div class="navbar-collapse justify-content-end px-0" id="navbarNav">
                                <ul class="navbar-nav flex-row ms-auto align-items-center justify-content-end">
                                    <li class="nav-item dropdown">
                                        <a class="nav-link" href="#" id="drop2" data-bs-toggle="dropdown"
                                            aria-expanded="false">
                                            <?php
                                            $photo = !empty($_SESSION['path_photo'])
                                                ? './' . htmlspecialchars($_SESSION['path_photo'])
                                                : 'assets/images/profile/user-1.jpg';
                                            ?>
                                            <img src="<?= $photo; ?>" alt="Profile" width="35" height="35"
                                                class="rounded-circle" style="object-fit: cover;">
                                        </a>

                                        <ul class="dropdown-menu dropdown-menu-end dropdown-menu-animate-up"
                                            aria-labelledby="drop2">
                                            <li>
                                                <a href="?q=myprofile"
                                                    class="dropdown-item d-flex align-items-center gap-2">
                                                    <i class="ti ti-user fs-6"></i>
                                                    <span>My Profile</span>
                                                </a>
                                            </li>
                                            <li>
                                                <hr class="dropdown-divider">
                                            </li>
                                            <li>
                                                <a href="logout"
                                                    class="btn btn-outline-primary mx-3 mt-2 d-block">Logout</a>
                                            </li>
                                        </ul>
                                    </li>
                                </ul>

                            </div>
                        </nav>
                    </header>
                    <!--  Header End -->

                    <?php
                    include 'link.php';
                    ?>

                </div>
                <div class="py-6 px-6 text-center">
                    <p class="mb-0 fs-4">
                        © 2025 <span class="text-primary fw-semibold">AI Style Coach</span> —
                        Designed & Developed by <a href="#" class="text-decoration-underline text-primary">Pixis Dev
                            Team</a>
                    </p>
                </div>

            </div>
        </div>
    </div>


    <script src="assets/libs/jquery/dist/jquery.min.js"></script>
    <script src="assets/libs/bootstrap/dist/js/bootstrap.bundle.min.js"></script>
    <script src="assets/js/sidebarmenu.js"></script>
    <script src="assets/js/app.min.js"></script>
    <script src="assets/libs/apexcharts/dist/apexcharts.min.js"></script>
    <script src="assets/libs/simplebar/dist/simplebar.js"></script>
    <script src="assets/js/dashboard.js"></script>



    <!-- DataTables (load sekali di layout kalau belum) -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css" />
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>

    <!-- solar icons -->
    <script src="https://cdn.jsdelivr.net/npm/iconify-icon@1.0.8/dist/iconify-icon.min.js"></script>




    <script>
    // === Chart 1: Line Trend (Uploads & Recommendations) ===
    var optionsTrend = {
        chart: {
            type: 'line',
            height: 320,
            toolbar: {
                show: false
            }
        },
        series: [{
                name: 'Uploads',
                data: [
                    <?php
                        $res = $conn->query("SELECT DATE(created_at) AS tgl, COUNT(*) AS jml FROM uploads GROUP BY DATE(created_at)");
                        while ($r = $res->fetch_assoc()) echo "{x: '{$r['tgl']}', y: {$r['jml']}},";
                        ?>
                ]
            },
            {
                name: 'Recommendations',
                data: [
                    <?php
                        $res = $conn->query("SELECT DATE(created_at) AS tgl, COUNT(*) AS jml FROM recommendations GROUP BY DATE(created_at)");
                        while ($r = $res->fetch_assoc()) echo "{x: '{$r['tgl']}', y: {$r['jml']}},";
                        ?>
                ]
            }
        ],
        colors: ['#5D87FF', '#13DEB9'],
        stroke: {
            curve: 'smooth',
            width: 3
        },
        dataLabels: {
            enabled: false
        },
        xaxis: {
            type: 'category',
            labels: {
                style: {
                    colors: '#888'
                }
            }
        },
        yaxis: {
            labels: {
                style: {
                    colors: '#888'
                }
            }
        },
        legend: {
            position: 'top'
        }
    };
    new ApexCharts(document.querySelector("#chart-trend"), optionsTrend).render();

    // === Chart 2: Pie Chart (Data Composition) ===
    var optionsPie = {
        chart: {
            type: 'donut',
            height: 320
        },
        labels: ['Uploads', 'Recommendations', 'Outfit Rules', 'Users'],
        series: [<?= $uploads ?>, <?= $recom ?>, <?= $rules ?>, <?= $users ?>],
        colors: ['#5D87FF', '#13DEB9', '#FFC107', '#FA896B'],
        legend: {
            position: 'bottom'
        },
        dataLabels: {
            enabled: true
        }
    };
    new ApexCharts(document.querySelector("#chart-pie"), optionsPie).render();
    </script>


    <script>
    $(function() {
        $('#uploadsTable').DataTable({
            pageLength: 10,
            lengthMenu: [5, 10, 20, 50],
            language: {
                search: "Cari:",
                lengthMenu: "Tampilkan _MENU_ data"
            }
        });
    });
    </script>

    <script>
    $(document).ready(function() {
        $('#recommendationsTable').DataTable({
            pageLength: 10,
            order: [
                [0, 'asc']
            ],
            language: {
                search: "Cari:",
                lengthMenu: "Tampilkan _MENU_ data",
                info: "Menampilkan _START_–_END_ dari _TOTAL_ data",
                paginate: {
                    previous: "Sebelumnya",
                    next: "Berikutnya"
                }
            }
        });
    });
    </script>

    <script>
    // DataTables
    $(document).ready(function() {
        $('#outfitRulesTable').DataTable({
            pageLength: 10,
            order: [
                [0, 'asc']
            ],
            language: {
                search: "Cari:",
                lengthMenu: "Tampilkan _MENU_ data",
                info: "Menampilkan _START_–_END_ dari _TOTAL_ data",
                paginate: {
                    previous: "Sebelumnya",
                    next: "Berikutnya"
                }
            }
        });
    });
    </script>

    <script>
    // DataTables
    $(document).ready(function() {
        $('#usersTable').DataTable({
            pageLength: 10,
            order: [
                [0, 'asc']
            ],
            language: {
                search: "Cari:",
                lengthMenu: "Tampilkan _MENU_ data",
                info: "Menampilkan _START_–_END_ dari _TOTAL_ data",
                paginate: {
                    previous: "Sebelumnya",
                    next: "Berikutnya"
                }
            }
        });
    });
    </script>
</body>

</html>