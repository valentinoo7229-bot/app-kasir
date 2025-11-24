<?php
 $host = "localhost";
 $user = "root"; // Ganti dengan username database Anda
 $pass = ""; // Ganti dengan password database Anda
 $db = "db_kasir"; // Nama database yang Anda buat

 $koneksi = mysqli_connect($host, $user, $pass, $db);

if (!$koneksi) {
    die("Koneksi gagal: " . mysqli_connect_error());
}
?>