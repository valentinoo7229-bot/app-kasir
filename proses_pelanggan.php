<?php
require_once 'database/koneksi.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $aksi = $_POST['aksi'];

    if ($aksi == 'tambah') {
        $nama = $_POST['nama'];
        $alamat = $_POST['alamat'];
        $telepon = $_POST['telepon'];

        $query = "INSERT INTO kasir_pelanggan (NamaPelanggan, Alamat, NomorTelepon) VALUES (?, ?, ?)";
        $stmt = mysqli_prepare($koneksi, $query);
        mysqli_stmt_bind_param($stmt, "sss", $nama, $alamat, $telepon);
        
        if (mysqli_stmt_execute($stmt)) {
            $_SESSION['pesan'] = "Pelanggan berhasil ditambahkan!";
        } else {
            $_SESSION['error'] = "Gagal menambahkan pelanggan: " . mysqli_error($koneksi);
        }

    } elseif ($aksi == 'edit') {
        $id = $_POST['id'];
        $nama = $_POST['nama'];
        $alamat = $_POST['alamat'];
        $telepon = $_POST['telepon'];

        $query = "UPDATE kasir_pelanggan SET NamaPelanggan = ?, Alamat = ?, NomorTelepon = ? WHERE PelangganID = ?";
        $stmt = mysqli_prepare($koneksi, $query);
        mysqli_stmt_bind_param($stmt, "sssi", $nama, $alamat, $telepon, $id);

        if (mysqli_stmt_execute($stmt)) {
            $_SESSION['pesan'] = "Data pelanggan berhasil diperbarui!";
        } else {
            $_SESSION['error'] = "Gagal memperbarui pelanggan: " . mysqli_error($koneksi);
        }

    } elseif ($aksi == 'hapus') {
        $id = $_POST['id'];
        
        // Cek apakah pelanggan ada di penjualan
        $cek_query = "SELECT COUNT(*) as count FROM kasir_penjualan WHERE PelangganID = ?";
        $stmt_cek = mysqli_prepare($koneksi, $cek_query);
        mysqli_stmt_bind_param($stmt_cek, "i", $id);
        mysqli_stmt_execute($stmt_cek);
        $result = mysqli_stmt_get_result($stmt_cek);
        $data = mysqli_fetch_assoc($result);

        if ($data['count'] > 0) {
            $_SESSION['error'] = "Pelanggan tidak dapat dihapus karena memiliki riwayat transaksi.";
        } else {
            $query = "DELETE FROM kasir_pelanggan WHERE PelangganID = ?";
            $stmt = mysqli_prepare($koneksi, $query);
            mysqli_stmt_bind_param($stmt, "i", $id);

            if (mysqli_stmt_execute($stmt)) {
                $_SESSION['pesan'] = "Pelanggan berhasil dihapus!";
            } else {
                $_SESSION['error'] = "Gagal menghapus pelanggan: " . mysqli_error($koneksi);
            }
        }
    }

    header("Location: kelola-pelanggan.php");
    exit();
}
?>