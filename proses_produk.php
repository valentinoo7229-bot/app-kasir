<?php
require_once 'database/koneksi.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $aksi = $_POST['aksi'];

    if ($aksi == 'tambah') {
        $nama = $_POST['nama'];
        $harga = $_POST['harga'];
        $stok = $_POST['stok'];

        $query = "INSERT INTO kasir_produk (NamaProduk, Harga, Stok) VALUES (?, ?, ?)";
        $stmt = mysqli_prepare($koneksi, $query);
        mysqli_stmt_bind_param($stmt, "sdi", $nama, $harga, $stok);
        
        if (mysqli_stmt_execute($stmt)) {
            $_SESSION['pesan'] = "Produk berhasil ditambahkan!";
        } else {
            $_SESSION['error'] = "Gagal menambahkan produk: " . mysqli_error($koneksi);
        }

    } elseif ($aksi == 'edit') {
        $id = $_POST['id'];
        $nama = $_POST['nama'];
        $harga = $_POST['harga'];
        $stok = $_POST['stok'];

        $query = "UPDATE kasir_produk SET NamaProduk = ?, Harga = ?, Stok = ? WHERE ProdukID = ?";
        $stmt = mysqli_prepare($koneksi, $query);
        mysqli_stmt_bind_param($stmt, "sdii", $nama, $harga, $stok, $id);

        if (mysqli_stmt_execute($stmt)) {
            $_SESSION['pesan'] = "Produk berhasil diperbarui!";
        } else {
            $_SESSION['error'] = "Gagal memperbarui produk: " . mysqli_error($koneksi);
        }

    } elseif ($aksi == 'hapus') {
        $id = $_POST['id'];
        
        // Cek apakah produk ada di detail penjualan
        $cek_query = "SELECT COUNT(*) as count FROM kasir_detailpenjualan WHERE ProdukID = ?";
        $stmt_cek = mysqli_prepare($koneksi, $cek_query);
        mysqli_stmt_bind_param($stmt_cek, "i", $id);
        mysqli_stmt_execute($stmt_cek);
        $result = mysqli_stmt_get_result($stmt_cek);
        $data = mysqli_fetch_assoc($result);

        if ($data['count'] > 0) {
            $_SESSION['error'] = "Produk tidak dapat dihapus karena sudah ada dalam transaksi penjualan.";
        } else {
            $query = "DELETE FROM kasir_produk WHERE ProdukID = ?";
            $stmt = mysqli_prepare($koneksi, $query);
            mysqli_stmt_bind_param($stmt, "i", $id);

            if (mysqli_stmt_execute($stmt)) {
                $_SESSION['pesan'] = "Produk berhasil dihapus!";
            } else {
                $_SESSION['error'] = "Gagal menghapus produk: " . mysqli_error($koneksi);
            }
        }
    }

    header("Location: kelola-stok.php");
    exit();
}
?>