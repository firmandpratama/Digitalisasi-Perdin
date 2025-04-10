<?php

$server = "localhost";
$user = "root";
$password = "";
$database = "e-perdin";

$koneksi = new mysqli($server, $user, $password, $database);
if ($koneksi->connect_errno) {
    echo "Gagal Konek Database: " . $koneksi->connect_errno;
    exit();
}
