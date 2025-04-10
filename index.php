<?php
session_name('e_perdin');
session_start();
include 'lib/koneksi.php';

if (isset($_POST['submit'])) {
    $_SESSION['old'] = $_POST;
    $email = mysqli_real_escape_string($koneksi, $_POST['email']);
    $password = $_POST['password'];
    $query = mysqli_query($koneksi, "SELECT * FROM tbl_user WHERE BINARY email='$email' AND status_aktif='Y'");

    if (mysqli_num_rows($query) === 1) {
        $row = mysqli_fetch_assoc($query);

        if (password_verify($password, $row['password_hash'])) {
            $_SESSION['nama_lengkap'] = $row['nama_lengkap'];
            $_SESSION['email'] = $row['email'];
            $_SESSION['jabatan'] = $row['jabatan'];
            $_SESSION['divisi'] = $row['divisi'];
            $_SESSION['department'] = $row['department'];
            $_SESSION['password_hash'] = $row['password_hash'];
            $_SESSION['penempatan'] = $row['penempatan'];
            $_SESSION['kode_cabang'] = $row['kode_cabang'];
            $_SESSION['status_aktif'] = $row['status_aktif'];
            $_SESSION['no_hp'] = $row['no_hp'];
            $_SESSION['status_level'] = $row['status_level'];
            $_SESSION['region_area'] = $row['region_area'];
            $_SESSION['jabatan_singkat'] = $row['jabatan_singkat'];
            $_SESSION['nama_singkat'] = $row['nama_singkat'];
            $_SESSION['nik'] = $row['nik'];
            $_SESSION['submit'] = true;

            unset($_SESSION['old']);

            if ($_SESSION['divisi'] == 'IT') {
                header("Location: home.php");
                exit();
            } else {
                header("Location: home.php");
                exit();
            }
        } else {
            $error = 'Password salah!';
        }
    } else {
        $error = 'Email tidak ditemukan atau akun tidak aktif!';
    }
}
if (isset($_SESSION['submit'])) {
    // Jika sudah login, arahkan ke halaman sesuai divisi
    if ($_SESSION['divisi'] == 'IT') {
        header("Location: home.php");
        exit();
    } else {
        header("Location: home.php");
        exit();
    }
}

?>

<!doctype html>
<html lang="en" data-layout="vertical" data-topbar="light" data-sidebar="dark" data-sidebar-size="lg" data-sidebar-image="none" data-preloader="disable">

<head>

    <meta charset="utf-8" />
    <title>Login | E-Perdin</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta content="Premium Multipurpose Admin & Dashboard Template" name="description" />
    <meta content="Themesbrand" name="author" />
    <!-- App favicon -->
    <link rel="shortcut icon" href="assets/images/favicon.ico">

    <!-- Layout config Js -->
    <script src="assets/js/layout.js"></script>
    <!-- Bootstrap Css -->
    <link href="assets/css/bootstrap.min.css" rel="stylesheet" type="text/css" />
    <!-- Icons Css -->
    <link href="assets/css/icons.min.css" rel="stylesheet" type="text/css" />
    <!-- App Css-->
    <link href="assets/css/app.min.css" rel="stylesheet" type="text/css" />
    <!-- custom Css-->
    <link href="assets/css/custom.min.css" rel="stylesheet" type="text/css" />

</head>

<body>

    <div class="auth-page-wrapper pt-5">
        <!-- auth page bg -->
        <div class="auth-one-bg-position auth-one-bg" id="auth-particles">
            <div class="bg-overlay"></div>

            <div class="shape">
                <svg xmlns="http://www.w3.org/2000/svg" version="1.1" xmlns:xlink="http://www.w3.org/1999/xlink" viewBox="0 0 1440 120">
                    <path d="M 0,36 C 144,53.6 432,123.2 720,124 C 1008,124.8 1296,56.8 1440,40L1440 140L0 140z"></path>
                </svg>
            </div>
        </div>

        <!-- auth page content -->
        <div class="auth-page-content">
            <div class="container">
                <div class="row">
                    <div class="col-lg-12">
                        <div class="text-center mt-sm-5 mb-4 text-white-50">
                            <div>
                                <a href="index.php" class="d-inline-block auth-logo">
                                    <img src="assets/images/Importa Logo Primary - White.png" alt="" height="50">
                                </a>
                            </div>
                            <p class="mt-3 fs-15 fw-medium"></p>
                        </div>
                    </div>
                </div>
                <!-- end row -->

                <div class="row justify-content-center">
                    <div class="col-md-8 col-lg-6 col-xl-5">
                        <div class="card mt-4">

                            <div class="card-body p-4">
                                <div class="text-center mt-2">
                                    <h3 class="text-primary">E-Digitalisasi Perjalanan Dinas</h3>
                                    <marquee behavior="scroll" direction="left" class="text-muted">
                                        Sistem ini membantu anda untuk proses pengajuan Perjalanan Dinas antar cabang.
                                    </marquee>
                                </div>
                                <div class="p-2 mt-4">
                                    <?php if (isset($error)) {
                                        echo "<script>alert('Periksa username & Password');</script>";
                                    } ?>
                                    <form action="" method="POST" enctype="multipart/form-data">

                                        <div class="mb-3">
                                            <label for="username" class="form-label">Email Karyawan</label>
                                            <input type="text" class="form-control" id="username" name="email" value="<?php echo isset($_SESSION['old']['email']) ? htmlspecialchars($_SESSION['old']['email']) : ""; ?>" placeholder="Enter your email.." required>
                                        </div>

                                        <div class="mb-3">
                                            <label class="form-label" for="password-input">Password</label>
                                            <div class="position-relative auth-pass-inputgroup mb-3">
                                                <input type="password" class="form-control pe-5 password-input" value="<?php echo isset($_SESSION['old']['password']) ? htmlspecialchars($_SESSION['old']['password']) : ""; ?>" placeholder="Enter password" id="password-input" name="password">
                                                <button class="btn btn-link position-absolute end-0 top-0 text-decoration-none text-muted password-addon" type="button" id="password-addon"><i class="ri-eye-fill align-middle"></i></button>
                                            </div>
                                        </div>

                                        <div class="mt-4">
                                            <button class="btn btn-success w-100" type="submit" name="submit">Login</button>
                                        </div>


                                    </form>
                                </div>
                            </div>
                            <!-- end card body -->
                        </div>
                        <!-- end card -->


                    </div>
                </div>
                <!-- end row -->
            </div>
            <!-- end container -->
        </div>
        <!-- end auth page content -->

        <!-- footer -->
        <footer class="footer">
            <div class="container">
                <div class="row">
                    <div class="col-lg-12">
                        <div class="text-center">
                            <p class="mb-0 text-muted">&copy;
                                <script>
                                    document.write(new Date().getFullYear())
                                </script> IT Department. Crafted with <i class="mdi mdi-heart text-danger"></i> by PT Importa Jaya Abadi
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </footer>
        <!-- end Footer -->
    </div>
    <!-- end auth-page-wrapper -->

    <!-- JAVASCRIPT -->
    <script src="assets/libs/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="assets/libs/simplebar/simplebar.min.js"></script>
    <script src="assets/libs/node-waves/waves.min.js"></script>
    <script src="assets/libs/feather-icons/feather.min.js"></script>
    <script src="assets/js/pages/plugins/lord-icon-2.1.0.js"></script>
    <script src="assets/js/plugins.js"></script>

    <!-- particles js -->
    <script src="assets/libs/particles.js/particles.js"></script>
    <!-- particles app js -->
    <script src="assets/js/pages/particles.app.js"></script>
    <!-- password-addon init -->
    <script src="assets/js/pages/password-addon.init.js"></script>
</body>

</html>