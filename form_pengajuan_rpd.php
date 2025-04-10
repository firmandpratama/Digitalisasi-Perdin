<?php
date_default_timezone_set("Asia/jakarta");

session_name('e_perdin');
session_start();
include 'lib/koneksi.php';

if ($_SESSION['status_aktif'] != 'Y') {
    header("Location: logout.php");
    exit();
} elseif ($_SESSION['status_aktif'] == 'Y') {

    $statusLevel = $_SESSION['status_level'] ?? null;
    $department = $_SESSION['department'] ?? null;
    $divisi = $_SESSION['divisi'] ?? null;
    $jabatan = $_SESSION['jabatan'] ?? null;
    $penempatan = $_SESSION['penempatan'] ?? null;
    $kodecabang = $_SESSION['kode_cabang'] ?? null;
    $region = $_SESSION['region_area'] ?? null;
    $no_hp = $_SESSION['no_hp'];
    $nowapr = preg_replace('/^0/', '62', $no_hp);

    $nik = $_SESSION['nik'];
    $querytalentaus = mysqli_query($koneksi, "SELECT * FROM tbl_master_class_grade WHERE nik='$nik'");
    $gettalenus = mysqli_fetch_assoc($querytalentaus);

    function sanitize_input($koneksi, $input)
    {
        if (is_array($input)) {
            return array_map(function ($item) use ($koneksi) {
                return mysqli_real_escape_string($koneksi, $item);
            }, $input);
        } else {
            return mysqli_real_escape_string($koneksi, $input);
        }
    }

    function sanitize_rupiah($value)
    {
        return (int) preg_replace('/[^\d]/', '', $value);
    }

    // query sendapproval
    if ($_SESSION['kode_cabang'] == '100-HO') {
        $querysend = mysqli_query($koneksi, "
        SELECT * 
        FROM tbl_user
        WHERE kode_cabang = '$kodecabang'
          AND department = '$department'
          AND status_level > $statusLevel 
          AND status_aktif = 'Y'");

        if ($querysend && mysqli_num_rows($querysend) > 0) {
            $getquerysend = mysqli_fetch_assoc($querysend);
            $sendEmail = $getquerysend['email'];
            $sendNama = $getquerysend['nama_lengkap'];
            $sendJabatan = $getquerysend['jabatan'];
            $sendPassword = $getquerysend['password_view'];
            $sendnoHP = $getquerysend['no_hp'];
            $nowa1 = preg_replace('/^0/', '62', $sendnoHP);
        } else {
            $querysend = mysqli_query($koneksi, "
            SELECT * 
            FROM tbl_user
            WHERE kode_cabang = '$kodecabang'
              AND divisi = '$divisi'
              AND status_level > $statusLevel
              AND status_aktif = 'Y'");

            if ($querysend && mysqli_num_rows($querysend) > 0) {
                $getquerysend = mysqli_fetch_assoc($querysend);
                $sendEmail = $getquerysend['email'];
                $sendNama = $getquerysend['nama_lengkap'];
                $sendJabatan = $getquerysend['jabatan'];
                $sendPassword = $getquerysend['password_view'];
                $sendnoHP = $getquerysend['no_hp'];
                $nowa1 = preg_replace('/^0/', '62', $sendnoHP);
            } else {
                $querysend = mysqli_query($koneksi, "
                SELECT * 
                FROM tbl_user
                WHERE kode_cabang = '$kodecabang'
                  AND divisi = 'Business Control'
                  AND email = 'yohana.ekarina@importa.co.id'
                  AND status_aktif = 'Y'");

                if ($querysend && mysqli_num_rows($querysend) > 0) {
                    $getquerysend = mysqli_fetch_assoc($querysend);
                    $sendEmail = $getquerysend['email'];
                    $sendNama = $getquerysend['nama_lengkap'];
                    $sendJabatan = $getquerysend['jabatan'];
                    $sendPassword = $getquerysend['password_view'];
                    $sendnoHP = $getquerysend['no_hp'];
                    $nowa1 = preg_replace('/^0/', '62', $sendnoHP);
                } else {
                    echo "Email Atasan tidak ditemukan";
                }
            }
            // else {
            //     $querysend = mysqli_query($koneksi, "
            //     SELECT * 
            //     FROM tbl_user
            //     WHERE region_area = '$region'
            //       AND jabatan = 'Regional Sales Manager'
            //       AND status_level = 5
            //       AND status_aktif = 'Y'");

            //     if ($querysend && mysqli_num_rows($querysend) > 0) {
            //         $getquerysend = mysqli_fetch_assoc($querysend);
            //         $sendEmail = $getquerysend['email'];
            //         $sendNama = $getquerysend['nama_lengkap'];
            //         $sendJabatan = $getquerysend['jabatan'];
            //         $sendPassword = $getquerysend['password_view'];
            //         $sendnoHP = $getquerysend['no_hp'];
            //         $nowa1 = preg_replace('/^0/', '62', $sendnoHP);
            //     } else {
            //         echo "Tidak ada data";
            //     }
            // }
        }
    }

    if (isset($_POST['submit'])) {

        // var_dump($_POST);

        $email_req = $_SESSION['email'];
        $nomor_tiket = mysqli_real_escape_string($koneksi, $_POST['nomor_tiket']);
        $tanggal_pengajuan = mysqli_real_escape_string($koneksi, $_POST['tanggal_pengajuan']);
        $nama_req = mysqli_real_escape_string($koneksi, $_POST['nama_requestor']);
        $dept_req = mysqli_real_escape_string($koneksi, $_POST['dept_requestor']);
        $jabatan_req = mysqli_real_escape_string($koneksi, $_POST['jabatan_requestor']);
        $classgrade_req = mysqli_real_escape_string($koneksi, $_POST['classgrade_requestor']);
        $lokasi_tujuan = mysqli_real_escape_string($koneksi, $_POST['lokasiTujuan']);

        $detail_tuj = '';
        if ($lokasi_tujuan == 'Dalam Negeri') {
            $detail_tuj = mysqli_real_escape_string($koneksi, $_POST['tujuanDalam']);
        } else {
            $detail_tuj = mysqli_real_escape_string($koneksi, $_POST['tujuanLuar']);
        }

        $agendakeperluan = mysqli_real_escape_string($koneksi, $_POST['agenda_keperluan']);
        $tgl_berangkat = mysqli_real_escape_string($koneksi, $_POST['tgl_berangkat']);
        $jam_berangkat = mysqli_real_escape_string($koneksi, $_POST['jam_berangkat']);
        $tgl_kembali = mysqli_real_escape_string($koneksi, $_POST['tgl_kembali']);
        $jam_tiba = mysqli_real_escape_string($koneksi, $_POST['jam_tiba']);
        $jenis_perjalanan = mysqli_real_escape_string($koneksi, $_POST['jenisPerjalanan']);
        $lainnya_text = '';
        if ($jenis_perjalanan == 'Lainnya') {
            $lainnya_text = mysqli_real_escape_string($koneksi, $_POST['lainnya_text']);
        } else {
            $lainnya_text = '';
        }

        $no_rek = mysqli_real_escape_string($koneksi, $_POST['no_rekening']);
        $nama_rek = mysqli_real_escape_string($koneksi, $_POST['nama_rekening']);

        // detail rincian anggaran
        $pesawat_jumlah1 =  $_POST['pesawat_jumlah_1'][0] ?? 0;
        $pesawat_jumlah2 =  $_POST['pesawat_jumlah_2'][0] ?? 0;
        $pesawat_nominal1 =  sanitize_rupiah($_POST['pesawat_nominal_1'][0] ?? 0);
        $pesawat_nominal2 =  sanitize_rupiah($_POST['pesawat_nominal_2'][0] ?? 0);
        $kereta_jumlah1 =  $_POST['kereta_jumlah_1'][0] ?? 0;
        $kereta_jumlah2 =  $_POST['kereta_jumlah_2'][0] ?? 0;
        $kereta_nominal1 =  sanitize_rupiah($_POST['kereta_nominal_1'][0] ?? 0);
        $kereta_nominal2 =  sanitize_rupiah($_POST['kereta_nominal_2'][0] ?? 0);
        $bbm_jumlah1 =  $_POST['bbm_jumlah_1'][0] ?? 0;
        $bbm_jumlah2 =  $_POST['bbm_jumlah_2'][0] ?? 0;
        $bbm_nominal1 =  sanitize_rupiah($_POST['bbm_nominal_1'][0] ?? 0);
        $bbm_nominal2 =  sanitize_rupiah($_POST['bbm_nominal_2'][0] ?? 0);
        $tol_jumlah1 =  $_POST['tol_jumlah_1'][0] ?? 0;
        $tol_jumlah2 =  $_POST['tol_jumlah_2'][0] ?? 0;
        $tol_nominal1 =  sanitize_rupiah($_POST['tol_nominal_1'][0] ?? 0);
        $tol_nominal2 =  sanitize_rupiah($_POST['tol_nominal_2'][0] ?? 0);
        $parkir_jumlah1 =  $_POST['parkir_jumlah_1'][0] ?? 0;
        $parkir_jumlah2 =  $_POST['parkir_jumlah_2'][0] ?? 0;
        $parkir_nominal1 =  sanitize_rupiah($_POST['parkir_nominal_1'][0] ?? 0);
        $parkir_nominal2 =  sanitize_rupiah($_POST['parkir_nominal_2'][0] ?? 0);
        $dalamkota_jumlah1 =  $_POST['dalamkota_jumlah_1'][0] ?? 0;
        $dalamkota_jumlah2 =  $_POST['dalamkota_jumlah_2'][0] ?? 0;
        $dalamkota_nominal1 =  sanitize_rupiah($_POST['dalamkota_nominal_1'][0] ?? 0);
        $dalamkota_nominal2 =  sanitize_rupiah($_POST['dalamkota_nominal_2'][0] ?? 0);
        $hotel_jumlah1 =  $_POST['hotel_jumlah_1'][0] ?? 0;
        $hotel_jumlah2 =  $_POST['hotel_jumlah_2'][0] ?? 0;
        $hotel_nominal1 =  sanitize_rupiah($_POST['hotel_nominal_1'][0] ?? 0);
        $hotel_nominal2 =  sanitize_rupiah($_POST['hotel_nominal_2'][0] ?? 0);
        $makan_jumlah1 =  $_POST['makan_jumlah_1'][0] ?? 0;
        $makan_jumlah2 =  $_POST['makan_jumlah_2'][0] ?? 0;
        $makan_nominal1 =  sanitize_rupiah($_POST['makan_nominal_1'][0] ?? 0);
        $makan_nominal2 =  sanitize_rupiah($_POST['makan_nominal_2'][0] ?? 0);
        $saku_jumlah1 =  $_POST['saku_jumlah_1'][0] ?? 0;
        $saku_jumlah2 =  $_POST['saku_jumlah_2'][0] ?? 0;
        $saku_nominal1 =  sanitize_rupiah($_POST['saku_nominal_1'][0] ?? 0);
        $saku_nominal2 =  sanitize_rupiah($_POST['saku_nominal_2'][0] ?? 0);
        $lain_jumlah1 =  $_POST['lain_jumlah_1'][0] ?? 0;
        $lain_jumlah2 =  $_POST['lain_jumlah_2'][0] ?? 0;
        $lain_nominal1 =  sanitize_rupiah($_POST['lain_nominal_1'][0] ?? 0);
        $lain_nominal2 =  sanitize_rupiah($_POST['lain_nominal_2'][0] ?? 0);
        $total_keseluruhan = preg_replace('/[Rp. ]/', '', $_POST['total_keseluruhan']);

        $email_tujuan = $sendEmail;
        $keperluan = 'Dibuat Oleh';
        $catatan = '';
        $tgl_approve = date('Y-m-d H:i:s');
        $status_approval = 'Menunggu Approval ' . $sendNama;
        $jabatan_user = $sendJabatan;

        // insert to tbl_pengajuan 
        $insert_rpd = "INSERT INTO tbl_pengajuan_rpd (nomor_tiket,tanggal_pengajuan,email_req,nama_req,jabatan_req,dept_div_req,class_grade_req,lokasi_tujuan,ket_lokasi_tujuan,agenda_keperluan,tanggal_berangkat,tanggal_kembali,jam_berangkat,jam_tiba,jenis_perjalanan,ket_jenis_perjalanan,no_rek,nama_rek,status_pengajuan) VALUES ('$nomor_tiket','$tanggal_pengajuan','$email_req','$nama_req','$jabatan_req','$dept_req','$classgrade_req','$lokasi_tujuan','$detail_tuj','$agendakeperluan','$tgl_berangkat','$tgl_kembali','$jam_berangkat','$jam_tiba','$jenis_perjalanan','$lainnya_text','$no_rek','$nama_rek','$status_approval')";
        $result_insert_rpd = mysqli_query($koneksi, $insert_rpd);
        if (!$result_insert_rpd) {
            echo "Gagal insert query error: " . mysqli_errno($koneksi) . " - " . mysqli_error($koneksi);
        } else {
            $data_rincian = [
                'Pesawat' => [$pesawat_jumlah1, $pesawat_jumlah2, $pesawat_nominal1, $pesawat_nominal2],
                'Kereta' => [$kereta_jumlah1, $kereta_jumlah2, $kereta_nominal1, $kereta_nominal2],
                'BBM' => [$bbm_jumlah1, $bbm_jumlah2, $bbm_nominal1, $bbm_nominal2],
                'Tol' => [$tol_jumlah1, $tol_jumlah2, $tol_nominal1, $tol_nominal2],
                'Parkir' => [$parkir_jumlah1, $parkir_jumlah2, $parkir_nominal1, $parkir_nominal2],
                'Dalam Kota' => [$dalamkota_jumlah1, $dalamkota_jumlah2, $dalamkota_nominal1, $dalamkota_nominal2],
                'Hotel/Penginapan' => [$hotel_jumlah1, $hotel_jumlah2, $hotel_nominal1, $hotel_nominal2],
                'Makan' => [$makan_jumlah1, $makan_jumlah2, $makan_nominal1, $makan_nominal2],
                'Uang Saku' => [$saku_jumlah1, $saku_jumlah2, $saku_nominal1, $saku_nominal2],
                'Lain-lain' => [$lain_jumlah1, $lain_jumlah2, $lain_nominal1, $lain_nominal2],
            ];

            foreach ($data_rincian as $jenis => [$qty, $hari, $nominal, $subtotal]) {
                $query = "INSERT INTO tbl_rincian_anggaran_rpd 
                    (nomor_tiket, jenis_kebutuhan, qty, hari, biaya_perunit, subtotal, total_anggaran) 
                    VALUES ('$nomor_tiket', '$jenis', '$qty', '$hari', '$nominal', '$subtotal', '$total_keseluruhan')";

                mysqli_query($koneksi, $query);
            }

            // insert to tbl_matrix
            $insert_matrix = "INSERT INTO tbl_matrix_approval_pengajuanrpd 
                (nomor_tiket,email_user,email_tujuan,keperluan,catatan,tgl_approve,status_approval,jabatan_user) VALUES ('$nomor_tiket','$email_req','$email_tujuan','$keperluan','$catatan','$tgl_approve','$status_approval','$jabatan_user')";
            $result_insert_matrix = mysqli_query($koneksi, $insert_matrix);
            if (!$result_insert_matrix) {
                echo "Gagal insert tbl_matrix, Error: " . mysqli_errno($koneksi) . " - " . mysqli_error($koneksi);
            } else {
                echo "<script>alert('Data Berhasil disimpan');</script>";
            }
        }
    }
}


?>

<!doctype html>
<html lang="en" data-layout="vertical" data-topbar="light" data-sidebar="dark" data-sidebar-size="lg" data-sidebar-image="none" data-preloader="disable">

<head>

    <meta charset="utf-8" />
    <title>Pengajauan RPD | E-Perdin</title>
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

    <!-- Begin page -->
    <div id="layout-wrapper">

        <?php include 'templates/header.php'; ?>

        <!-- removeNotificationModal -->
        <?php include 'templates/sidebar.php'; ?>
        <!-- Left Sidebar End -->
        <!-- Vertical Overlay-->
        <div class="vertical-overlay"></div>

        <!-- ============================================================== -->
        <!-- Start right Content here -->
        <!-- ============================================================== -->
        <div class="main-content">

            <div class="page-content">
                <div class="container-fluid">

                    <!-- start page title -->
                    <div class="row">
                        <div class="col-12">
                            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                                <h4 class="mb-sm-0"></h4>

                                <div class="page-title-right">
                                    <ol class="breadcrumb m-0">
                                        <li class="breadcrumb-item"><a href="javascript: void(0);">Pengajuan</a></li>
                                        <li class="breadcrumb-item active">RPD</li>
                                    </ol>
                                </div>

                            </div>
                        </div>
                    </div>
                    <!-- end page title -->


                    <?php
                    echo "send Email: " . $sendEmail;
                    echo "<br>send Nama: " . $sendNama;
                    echo "<br>send Jabatan: " . $sendJabatan;
                    echo "<br>send no_hp: " . $nowa1;
                    ?>

                    <div class="row">
                        <div class="col-xxl-12">
                            <div class="card">
                                <div class="card-header align-items-center d-flex">
                                    <h4 class="card-title mb-0 flex-grow-1">Silahkan isi detail pengajuan RDP disini!</h4>

                                </div><!-- end card header -->
                                <div class="card-body">
                                    <div class="live-preview">
                                        <form action="" method="post" enctype="multipart/form-data">
                                            <div class="row mb-3">
                                                <input type="hidden" name="nomor_tiket" value="<?= rand(); ?>">
                                                <input type="hidden" name="tanggal_pengajuan" value="<?= date('Y-m-d H:i:s'); ?>">
                                                <div class="col-lg-2">
                                                    <label for="nameInput" style="margin-top:6%;" class="form-label">Nama</label>
                                                </div>
                                                <div class="col mt-2" style="max-width: 2%;">:</div>
                                                <div class="col-lg-3">
                                                    <input type="text" name="nama_requestor" class="form-control" id="nameInput" placeholder="Enter your name" style="border-top: none;border-right:none;border-left:none;border-bottom:1px solid;border-radius:0;">
                                                </div>
                                                <div class="col" style="max-width: 2%;"></div>
                                                <div class="col-lg-2">
                                                    <label for="nameInput" class="form-label" style="margin-top:6%;">Dept/Division</label>
                                                </div>
                                                <div class="col mt-2" style="max-width: 2%;">:</div>
                                                <div class="col-lg-3">
                                                    <input type="text" name="dept_requestor" value="<?= $gettalenus['department']; ?>" readonly class="form-control" id="deptInput" placeholder="Enter your dept/division" style="border-top: none;border-right:none;border-left:none;border-bottom:1px solid;border-radius:0;background-color:#e9ecef;">
                                                </div>
                                            </div>
                                            <div class="row mb-3">
                                                <div class="col-lg-2">
                                                    <label for="nameInput" class="form-label" style="margin-top:6%;">Jabatan</label>
                                                </div>
                                                <div class="col mt-2" style="max-width: 2%;">:</div>
                                                <div class="col-lg-3">
                                                    <input type="text" name="jabatan_requestor" value="<?= $gettalenus['jabatan']; ?>" readonly class="form-control" id="nameInput" placeholder="Enter your jabatan" style="border-top: none;border-right:none;border-left:none;border-bottom:1px solid;border-radius:0;background-color:#e9ecef;">
                                                </div>
                                                <div class="col" style="max-width: 2%;"></div>
                                                <div class="col-lg-2">
                                                    <label for="nameInput" class="form-label" style="margin-top:6%;">Class/Grade</label>
                                                </div>
                                                <div class="col mt-2" style="max-width: 2%;">:</div>
                                                <div class="col-lg-3">
                                                    <input type="text" name="classgrade_requestor" value="<?= $gettalenus['class'] . '/' . $gettalenus['grade']; ?>" readonly class="form-control" id="deptInput" placeholder="Enter your class/grade" style="border-top: none;border-right:none;border-left:none;border-bottom:1px solid;border-radius:0;background-color:#e9ecef;">
                                                </div>
                                            </div>
                                            <div class="row mb-3">
                                                <div class="col-lg-2">
                                                    <label class="form-label" style="margin-top:6%;">Lokasi Tujuan</label>
                                                </div>
                                                <div class="col mt-2" style="max-width: 2%;">:</div>

                                                <!-- Dalam Negeri -->
                                                <div class="col-lg-4">
                                                    <div class="row">
                                                        <div class="col-4">
                                                            <div class="form-check" style="margin-top:6%;">
                                                                <input class="form-check-input" type="radio" name="lokasiTujuan" id="dalamNegeri" value="Dalam Negeri">
                                                                <label for="dalamNegeri" class="form-check-label">Dalam Negeri</label>
                                                            </div>
                                                        </div>
                                                        <div class="col-8">
                                                            <input type="text" class="form-control" id="inputDalam" name="tujuanDalam" placeholder="Isi lokasi dalam negeri" style="border-top: none;border-right:none;border-left:none;border-bottom:1px solid;border-radius:0;" readonly>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="col" style="max-width: 2%;"></div>

                                                <!-- Luar Negeri -->
                                                <div class="col-lg-4">
                                                    <div class="row">
                                                        <div class="col-4">
                                                            <div class="form-check" style="margin-top:6%;">
                                                                <input class="form-check-input" type="radio" name="lokasiTujuan" id="luarNegeri" value="Luar Negeri">
                                                                <label for="luarNegeri" class="form-check-label">Luar Negeri</label>
                                                            </div>
                                                        </div>
                                                        <div class="col-8">
                                                            <input type="text" class="form-control" id="inputLuar" name="tujuanLuar" placeholder="Isi lokasi luar negeri" style="border-top: none;border-right:none;border-left:none;border-bottom:1px solid;border-radius:0;" readonly>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="row mb-3">
                                                <div class="col-lg-2">
                                                    <label for="nameInput" class="form-label" style="margin-top:6%;">Agenda/Keperluan </label>
                                                </div>
                                                <div class="col mt-2" style="max-width: 2%;">:</div>
                                                <div class="col-lg-9">
                                                    <input type="text" class="form-control" id="nameInput" name="agenda_keperluan" placeholder="Enter your keperluan" style="border-top: none;border-right:none;border-left:none;border-bottom:1px solid;border-radius:0;max-width:94%;" required>
                                                </div>
                                            </div>

                                            <div class="row mb-3">
                                                <div class="col-lg-2">
                                                    <label for="dateInput" class="form-label" style="margin-top:6%;">Tanggal Berangkat</label>
                                                </div>
                                                <div class="col mt-2" style="max-width: 2%;">:</div>
                                                <div class="col-lg-3">
                                                    <input type="date" class="form-control" data-provider="flatpickr" id="dateInput" name="tgl_berangkat" style="border-top: none;border-right:none;border-left:none;border-bottom:1px solid;border-radius:0;" required>
                                                </div>
                                                <div class="col mt-2" style="max-width: 2%;"></div>
                                                <div class="col-lg-2">
                                                    <label for="timeInput" class="form-label" style="margin-top:6%;">Jam Berangkat </label>
                                                </div>
                                                <div class="col mt-2" style="max-width: 2%;">:</div>
                                                <div class="col-lg-3">
                                                    <input type="time" class="form-control" name="jam_berangkat" data-provider="timepickr" data-time-basic="true" id="timeInput" style="border-top: none;border-right:none;border-left:none;border-bottom:1px solid;border-radius:0;" required>
                                                </div>
                                            </div>
                                            <div class="row mb-3">
                                                <div class="col-lg-2">
                                                    <label for="dateInput" class="form-label" style="margin-top:6%;">Tanggal Kembali</label>
                                                </div>
                                                <div class="col mt-2" style="max-width: 2%;">:</div>
                                                <div class="col-lg-3">
                                                    <input type="date" name="tgl_kembali" class="form-control" data-provider="flatpickr" id="dateInput" style="border-top: none;border-right:none;border-left:none;border-bottom:1px solid;border-radius:0;" required>
                                                </div>
                                                <div class="col mt-2" style="max-width: 2%;"></div>
                                                <div class="col-lg-2">
                                                    <label for="timeInput" class="form-label" style="margin-top:6%;">Jam Tiba </label>
                                                </div>
                                                <div class="col mt-2" style="max-width: 2%;">:</div>
                                                <div class="col-lg-3">
                                                    <input type="time" class="form-control" name="jam_tiba" data-provider="timepickr" data-time-basic="true" id="timeInput" style="border-top: none;border-right:none;border-left:none;border-bottom:1px solid;border-radius:0;" required>
                                                </div>
                                            </div>
                                            <div class="row mb-3">
                                                <div class="col-lg-2">
                                                    <label class="form-label" style="margin-top:6%;">Jenis Perjalanan</label>
                                                </div>
                                                <div class="col mt-2" style="max-width: 2%;">:</div>

                                                <!-- Pilihan Dinas / Rekrutmen / Training -->
                                                <div class="col-lg-4">
                                                    <div class="row">
                                                        <div class="col-4">
                                                            <div class="form-check" style="margin-top:6%;">
                                                                <input class="form-check-input" type="radio" name="jenisPerjalanan" id="dinas" value="Dinas">
                                                                <label for="dinas" class="form-check-label">Dinas</label>
                                                            </div>
                                                        </div>
                                                        <div class="col-4">
                                                            <div class="form-check" style="margin-top:6%;">
                                                                <input class="form-check-input" type="radio" name="jenisPerjalanan" id="rekrutmen" value="Rekrutmen">
                                                                <label for="rekrutmen" class="form-check-label">Rekrutmen</label>
                                                            </div>
                                                        </div>
                                                        <div class="col-4">
                                                            <div class="form-check" style="margin-top:6%;">
                                                                <input class="form-check-input" type="radio" name="jenisPerjalanan" id="training" value="Training">
                                                                <label for="training" class="form-check-label">Training</label>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>

                                                <!-- Pilihan Lainnya + Input -->
                                                <div class="col-lg-5">
                                                    <div class="row">
                                                        <div class="col-3">
                                                            <div class="form-check" style="margin-top:6%;">
                                                                <input class="form-check-input" type="radio" name="jenisPerjalanan" id="lainnya" value="Lainnya">
                                                                <label for="lainnya" class="form-check-label">Lainnya</label>
                                                            </div>
                                                        </div>
                                                        <div class="col mt-2" style="max-width: 2%;">:</div>
                                                        <div class="col-8">
                                                            <input type="text" name="lainnya_text" class="form-control" id="inputLainnya" placeholder="Isi jenis perjalanan lainnya" style="border-top: none;border-right:none;border-left:none;border-bottom:1px solid;border-radius:0; max-width:89%;" readonly>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row mb-3">
                                                <div class="col-lg-2">
                                                    <label for="nameInput" class="form-label" style="margin-top:6%;">No. Rekening</label>
                                                </div>
                                                <div class="col mt-2" style="max-width: 2%;">:</div>
                                                <div class="col-lg-3">
                                                    <input type="number" class="form-control" name="no_rekening" placeholder="Enter your rekening number" style="border-top: none;border-right:none;border-left:none;border-bottom:1px solid;border-radius:0;" required>
                                                </div>
                                                <div class="col" style="max-width: 2%;"></div>
                                                <div class="col-lg-2">
                                                    <label for="nameInput" class="form-label" style="margin-top:6%;">Atas Nama </label>
                                                </div>
                                                <div class="col mt-2" style="max-width: 2%;">:</div>
                                                <div class="col-lg-3">
                                                    <input type="text" class="form-control" name="nama_rekening" placeholder="Enter your rekening name" style="border-top: none;border-right:none;border-left:none;border-bottom:1px solid;border-radius:0;" required>
                                                </div>
                                            </div>
                                            <!-- rincian anggaran -->
                                            <h5 class="box-title text-center" style="margin: 40px 0px 30px 0px;border-top:#000 1px solid;border-bottom:#000 1px solid;background-color:#F2F2F2;">RINCIAN ANGGARAN</h5>
                                            <div class="row">
                                                <div class="col-md-12">
                                                    <table class="table table-bordered">
                                                        <thead>
                                                            <tr>
                                                                <th style="width:5%; text-align:center; font-weight:bold;">No</th>
                                                                <th style="width:30%; text-align:center; font-weight:bold;">Jenis Kebutuhan</th>
                                                                <th style="width:8%; text-align:center; font-weight:bold;">Qty</th>
                                                                <th style="width:8%; text-align:center; font-weight:bold;">Hari</th>
                                                                <th style="width:14%; text-align:center; font-weight:bold;">Biaya/Unit (Rp)</th>
                                                                <th style="width:14%; text-align:center; font-weight:bold;">Subtotal (Rp)</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            <tr>
                                                                <td rowspan="6" style="text-align: center;">1</td>
                                                                <td colspan="5">Transportasi</td>
                                                            </tr>
                                                            <tr class="data-row">
                                                                <td>Pesawat</td>
                                                                <td><input type="number" class="form-control jumlah" name="pesawat_jumlah_1[]" style="border-top: none;border-right:none;border-left:none;border-bottom:1px solid;border-radius:0;"></td>
                                                                <td><input type="number" class="form-control hari" name="pesawat_jumlah_2[]" style="border-top: none;border-right:none;border-left:none;border-bottom:1px solid;border-radius:0;"></td>
                                                                <td><input type="text" class="form-control rupiah" name="pesawat_nominal_1[]" placeholder="Enter numeral" style="border-top: none;border-right:none;border-left:none;border-bottom:1px solid;border-radius:0;"></td>
                                                                <td><input type="text" class="form-control subtotal" readonly name="pesawat_nominal_2[]" style="border-top: none;border-right:none;border-left:none;border-bottom:1px solid;border-radius:0;"></td>
                                                            </tr>
                                                            <tr class="data-row">
                                                                <td>Kereta</td>
                                                                <td><input type="number" class="form-control jumlah" name="kereta_jumlah_1[]" style="border-top: none;border-right:none;border-left:none;border-bottom:1px solid;border-radius:0;"></td>
                                                                <td><input type="number" class="form-control hari" name="kereta_jumlah_2[]" style="border-top: none;border-right:none;border-left:none;border-bottom:1px solid;border-radius:0;"></td>
                                                                <td><input type="text" class="form-control rupiah" name="kereta_nominal_1[]" placeholder="Enter numeral" style="border-top: none;border-right:none;border-left:none;border-bottom:1px solid;border-radius:0;"></td>
                                                                <td><input type="text" class="form-control subtotal" readonly name="kereta_nominal_2[]" style="border-top: none;border-right:none;border-left:none;border-bottom:1px solid;border-radius:0;"></td>
                                                            </tr>
                                                            <tr class="data-row">
                                                                <td>BBM</td>
                                                                <td><input type="number" class="form-control jumlah" name="bbm_jumlah_1[]" style="border-top: none;border-right:none;border-left:none;border-bottom:1px solid;border-radius:0;"></td>
                                                                <td><input type="number" class="form-control hari" name="bbm_jumlah_2[]" style="border-top: none;border-right:none;border-left:none;border-bottom:1px solid;border-radius:0;"></td>
                                                                <td><input type="text" class="form-control rupiah" name="bbm_nominal_1[]" placeholder="Enter numeral" style="border-top: none;border-right:none;border-left:none;border-bottom:1px solid;border-radius:0;"></td>
                                                                <td><input type="text" class="form-control subtotal" readonly name="bbm_nominal_2[]" style="border-top: none;border-right:none;border-left:none;border-bottom:1px solid;border-radius:0;"></td>
                                                            </tr>
                                                            <tr class="data-row">
                                                                <td>TOL (COA:tol)</td>
                                                                <td><input type="number" class="form-control jumlah" name="tol_jumlah_1[]" style="border-top: none;border-right:none;border-left:none;border-bottom:1px solid;border-radius:0;"></td>
                                                                <td><input type="number" class="form-control hari" name="tol_jumlah_2[]" style="border-top: none;border-right:none;border-left:none;border-bottom:1px solid;border-radius:0;"></td>
                                                                <td><input type="text" class="form-control rupiah" name="tol_nominal_1[]" placeholder="Enter numeral" style="border-top: none;border-right:none;border-left:none;border-bottom:1px solid;border-radius:0;"></td>
                                                                <td><input type="text" class="form-control subtotal" readonly name="tol_nominal_2[]" style="border-top: none;border-right:none;border-left:none;border-bottom:1px solid;border-radius:0;"></td>
                                                            </tr>
                                                            <tr class="data-row">
                                                                <td>Parkir (COA: transportasi dalam kota)</td>
                                                                <td><input type="number" class="form-control jumlah" name="parkir_jumlah_1[]" style="border-top: none;border-right:none;border-left:none;border-bottom:1px solid;border-radius:0;"></td>
                                                                <td><input type="number" class="form-control hari" name="parkir_jumlah_2[]" style="border-top: none;border-right:none;border-left:none;border-bottom:1px solid;border-radius:0;"></td>
                                                                <td><input type="text" class="form-control rupiah" name="parkir_nominal_1[]" placeholder="Enter numeral" style="border-top: none;border-right:none;border-left:none;border-bottom:1px solid;border-radius:0;"></td>
                                                                <td><input type="text" class="form-control subtotal" readonly name="parkir_nominal_2[]" style="border-top: none;border-right:none;border-left:none;border-bottom:1px solid;border-radius:0;"></td>
                                                            </tr>

                                                            <!-- Lanjutan baris lainnya menggunakan pola yang sama -->
                                                            <tr class="data-row">
                                                                <td style="text-align: center;">2</td>
                                                                <td>Transportasi Dalam Kota</td>
                                                                <td><input type="number" class="form-control jumlah" name="dalamkota_jumlah_1[]" style="border-top: none;border-right:none;border-left:none;border-bottom:1px solid;border-radius:0;" required></td>
                                                                <td><input type="number" class="form-control hari" name="dalamkota_jumlah_2[]" style="border-top: none;border-right:none;border-left:none;border-bottom:1px solid;border-radius:0;" required></td>
                                                                <td><input type="text" class="form-control rupiah" name="dalamkota_nominal_1[]" style="border-top: none;border-right:none;border-left:none;border-bottom:1px solid;border-radius:0;" required></td>
                                                                <td><input type="text" class="form-control subtotal" readonly name="dalamkota_nominal_2[]" style="border-top: none;border-right:none;border-left:none;border-bottom:1px solid;border-radius:0;" required></td>
                                                            </tr>
                                                            <tr class="data-row">
                                                                <td style="text-align: center;">3</td>
                                                                <td>Hotel/Penginapan</td>
                                                                <td><input type="number" class="form-control jumlah" name="hotel_jumlah_1[]" style="border-top: none;border-right:none;border-left:none;border-bottom:1px solid;border-radius:0;" required></td>
                                                                <td><input type="number" class="form-control hari" name="hotel_jumlah_2[]" style="border-top: none;border-right:none;border-left:none;border-bottom:1px solid;border-radius:0;" required></td>
                                                                <td><input type="text" class="form-control rupiah" name="hotel_nominal_1[]" style="border-top: none;border-right:none;border-left:none;border-bottom:1px solid;border-radius:0;" required></td>
                                                                <td><input type="text" class="form-control subtotal" readonly name="hotel_nominal_2[]" style="border-top: none;border-right:none;border-left:none;border-bottom:1px solid;border-radius:0;" required></td>
                                                            </tr>
                                                            <tr class="data-row">
                                                                <td style="text-align: center;">4</td>
                                                                <td>Uang Makan</td>
                                                                <td><input type="number" class="form-control jumlah" name="makan_jumlah_1[]" style="border-top: none;border-right:none;border-left:none;border-bottom:1px solid;border-radius:0;" required></td>
                                                                <td><input type="number" class="form-control hari" name="makan_jumlah_2[]" style="border-top: none;border-right:none;border-left:none;border-bottom:1px solid;border-radius:0;" required></td>
                                                                <td><input type="text" class="form-control rupiah" name="makan_nominal_1[]" style="border-top: none;border-right:none;border-left:none;border-bottom:1px solid;border-radius:0;" required></td>
                                                                <td><input type="text" class="form-control subtotal" readonly name="makan_nominal_2[]" style="border-top: none;border-right:none;border-left:none;border-bottom:1px solid;border-radius:0;" required></td>
                                                            </tr>
                                                            <tr class="data-row">
                                                                <td style="text-align: center;">5</td>
                                                                <td>Uang Saku</td>
                                                                <td><input type="number" class="form-control jumlah" name="saku_jumlah_1[]" style="border-top: none;border-right:none;border-left:none;border-bottom:1px solid;border-radius:0;" required></td>
                                                                <td><input type="number" class="form-control hari" name="saku_jumlah_2[]" style="border-top: none;border-right:none;border-left:none;border-bottom:1px solid;border-radius:0;" required></td>
                                                                <td><input type="text" class="form-control rupiah" name="saku_nominal_1[]" style="border-top: none;border-right:none;border-left:none;border-bottom:1px solid;border-radius:0;" required></td>
                                                                <td><input type="text" class="form-control subtotal" readonly name="saku_nominal_2[]" style="border-top: none;border-right:none;border-left:none;border-bottom:1px solid;border-radius:0;" required></td>
                                                            </tr>
                                                            <tr class="data-row">
                                                                <td style="text-align: center;">6</td>
                                                                <td>Lain-Lain:</td>
                                                                <td><input type="number" class="form-control jumlah" name="lain_jumlah_1[]" style="border-top: none;border-right:none;border-left:none;border-bottom:1px solid;border-radius:0;"></td>
                                                                <td><input type="number" class="form-control hari" name="lain_jumlah_2[]" style="border-top: none;border-right:none;border-left:none;border-bottom:1px solid;border-radius:0;"></td>
                                                                <td><input type="text" class="form-control rupiah" name="lain_nominal_1[]" style="border-top: none;border-right:none;border-left:none;border-bottom:1px solid;border-radius:0;"></td>
                                                                <td><input type="text" class="form-control subtotal" readonly name="lain_nominal_2[]" style="border-top: none;border-right:none;border-left:none;border-bottom:1px solid;border-radius:0;"></td>
                                                            </tr>
                                                            <tr class="data-row">
                                                                <td style="text-align: center;"></td>
                                                                <td style="text-align: right;font-weight:bold;" colspan="4">Total Anggaran (Rp)</td>
                                                                <td>
                                                                    <input type="text" class="form-control total_keseluruhan" readonly name="total_keseluruhan"
                                                                        style="border-top: none;border-right:none;border-left:none;border-bottom:1px solid;border-radius:0;">
                                                                </td>
                                                            </tr>

                                                        </tbody>

                                                    </table>
                                                </div>
                                            </div>

                                            <div class="row mb-3 mt-3">
                                                <div class="col-lg-12">
                                                    <div class="text-end">
                                                        <button class="btn btn-primary" type="submit" name="submit">Submit</button>
                                                    </div>
                                                </div>
                                            </div>

                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!--end row-->

                </div> <!-- container-fluid -->
            </div>
            <!-- End Page-content -->


            <?php include 'templates/footer.php'; ?>
        </div>
        <!-- end main content-->

    </div>
    <!-- END layout-wrapper -->





    <!--start back-to-top-->
    <button onclick="topFunction()" class="btn btn-danger btn-icon" id="back-to-top">
        <i class="ri-arrow-up-line"></i>
    </button>
    <!--end back-to-top-->

    <!--preloader-->
    <div id="preloader">
        <div id="status">
            <div class="spinner-border text-primary avatar-sm" role="status">
                <span class="visually-hidden">Loading...</span>
            </div>
        </div>
    </div>


    <!-- JAVASCRIPT -->
    <script src="assets/libs/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="assets/libs/simplebar/simplebar.min.js"></script>
    <script src="assets/libs/node-waves/waves.min.js"></script>
    <script src="assets/libs/feather-icons/feather.min.js"></script>
    <script src="assets/js/pages/plugins/lord-icon-2.1.0.js"></script>
    <script src="assets/js/plugins.js"></script>

    <!-- prismjs plugin -->
    <script src="assets/libs/prismjs/prism.js"></script>

    <script src="assets/js/app.js"></script>

    <!-- cleave.js -->
    <script src="assets/libs/cleave.js/cleave.min.js"></script>
    <!-- form masks init -->
    <script src="assets/js/pages/form-masks.init.js"></script>

    <script>
        document.querySelectorAll('.rupiah').forEach(input => {
            new Cleave(input, {
                numeral: true,
                numeralThousandsGroupStyle: 'thousand',
                prefix: 'Rp ',
                noImmediatePrefix: false,
                rawValueTrimPrefix: true,
            });
        });
    </script>


    <script>
        const radioDalam = document.getElementById('dalamNegeri');
        const radioLuar = document.getElementById('luarNegeri');
        const inputDalam = document.getElementById('inputDalam');
        const inputLuar = document.getElementById('inputLuar');

        function updateInputStates() {
            if (radioDalam.checked) {
                inputDalam.readOnly = false;
                inputDalam.style.backgroundColor = '#ffffff';
                inputLuar.readOnly = true;
                inputLuar.value = '';
                inputLuar.style.backgroundColor = '#e9ecef';
            } else if (radioLuar.checked) {
                inputDalam.readOnly = true;
                inputDalam.value = '';
                inputDalam.style.backgroundColor = '#e9ecef';
                inputLuar.readOnly = false;
                inputLuar.style.backgroundColor = '#ffffff';
            }
        }

        radioDalam.addEventListener('change', updateInputStates);
        radioLuar.addEventListener('change', updateInputStates);
    </script>
    <script>
        const inputLainnya = document.getElementById('inputLainnya');
        const radioButtons = document.querySelectorAll('input[name="jenisPerjalanan"]');

        radioButtons.forEach(radio => {
            radio.addEventListener('change', () => {
                if (radio.value === 'Lainnya') {
                    inputLainnya.readOnly = false;
                    inputLainnya.focus();
                    inputLainnya.style.backgroundColor = '#ffffff';
                } else {
                    inputLainnya.readOnly = true;
                    inputLainnya.value = '';
                    inputLainnya.style.backgroundColor = '#e9ecef';
                }
            });
        });
    </script>

    <script>
        document.querySelectorAll('input[type="number"]').forEach(input => {
            input.addEventListener('input', function() {
                this.value = this.value.replace(/[^0-9]/g, '');
            });
        });
    </script>

    <!-- <script>
        $(document).ready(function() {
            function hitungSubtotal() {
                let totalKeseluruhan = 0;

                $('.data-row').each(function() {
                    const row = $(this);
                    const kategori = row.find('.jenis_form').val(); // pastikan ada select/input dengan class 'jenis_form'
                    const biaya = parseFloat(row.find('.biaya').val()) || 0;
                    const qty = parseFloat(row.find('.qty').val()) || 0;
                    const hari = parseFloat(row.find('.hari').val()) || 0;

                    let subtotal = 0;
                    if (kategori.toLowerCase() === 'hotel/penginapan') {
                        subtotal = biaya * qty;
                    } else {
                        subtotal = biaya * qty * hari;
                    }

                    row.find('.sub_total').val(subtotal.toLocaleString('id-ID'));
                    totalKeseluruhan += subtotal;
                });

                $('.total_keseluruhan').val(totalKeseluruhan.toLocaleString('id-ID'));
            }

            // Jalankan saat input berubah
            $(document).on('input change', '.biaya, .qty, .hari, .jenis_form', function() {
                hitungSubtotal();
            });

            // Hitung saat halaman load juga
            hitungSubtotal();
        });
    </script> -->

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const rows = document.querySelectorAll('.data-row');
            const totalKeseluruhanInput = document.querySelector('.total_keseluruhan');

            function formatRupiah(angka) {
                return new Intl.NumberFormat('id-ID', {
                    style: 'currency',
                    currency: 'IDR',
                    minimumFractionDigits: 0
                }).format(angka);
            }

            function parseNominal(nominalStr) {
                return parseFloat(nominalStr.replace(/[^\d]/g, '')) || 0;
            }

            function updateTotalKeseluruhan() {
                let total = 0;
                const subtotalInputs = document.querySelectorAll('.subtotal');

                subtotalInputs.forEach(function(input) {
                    const val = parseNominal(input.value || '0');
                    total += val;
                });

                if (totalKeseluruhanInput) {
                    totalKeseluruhanInput.value = formatRupiah(total);
                }
            }

            rows.forEach(function(row) {
                const jenis = row.querySelector('td:nth-child(2)')?.textContent?.trim().toLowerCase();
                const jumlahInputs = row.querySelectorAll('.jumlah');
                const hariInputs = row.querySelectorAll('.hari');
                const nominalInputs = row.querySelectorAll('.rupiah');
                const subtotalInput = row.querySelector('.subtotal');

                function updateSubtotal() {
                    let subtotal = 0;

                    if (jenis === 'hotel/penginapan') {
                        const jumlah = parseFloat(jumlahInputs[0]?.value) || 0;
                        const nominal = parseNominal(nominalInputs[0]?.value || '');
                        subtotal = jumlah * nominal;
                    } else {
                        jumlahInputs.forEach((jumlahInput, index) => {
                            const jumlah = parseFloat(jumlahInput.value) || 0;
                            const hari = parseFloat(hariInputs[index]?.value) || 0;
                            const nominal = parseNominal(nominalInputs[index]?.value || '');
                            subtotal += jumlah * hari * nominal;
                        });
                    }

                    if (subtotalInput) {
                        subtotalInput.value = formatRupiah(subtotal);
                    }

                    updateTotalKeseluruhan();
                }

                jumlahInputs.forEach(input => input.addEventListener('input', updateSubtotal));
                hariInputs.forEach(input => input.addEventListener('input', updateSubtotal));
                nominalInputs.forEach(input => input.addEventListener('input', updateSubtotal));

                updateSubtotal();
            });
        });
    </script>


</body>

</html>