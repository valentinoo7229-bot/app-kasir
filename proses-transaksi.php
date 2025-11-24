<?php
require_once 'database/koneksi.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $pelanggan_id = isset($_POST['pelanggan_id']) && !empty($_POST['pelanggan_id']) ? (int)$_POST['pelanggan_id'] : NULL;
    $total_harga = (float)$_POST['total_bayar'];
    $uang_bayar = (float)$_POST['uang_bayar'];
    $uang_kembali = (float)$_POST['uang_kembali'];
    $cart_data = json_decode($_POST['cart_data'], true);

    // Validasi sederhana
    if ($uang_bayar < $total_harga) {
        die("Error: Uang bayar tidak mencukupi.");
    }
    
    if (empty($cart_data)) {
        die("Error: Keranjang belanja kosong.");
    }

    // Mulai transaksi
    mysqli_begin_transaction($koneksi);

    try {
        // 1. Insert ke tabel penjualan
        $query_penjualan = "INSERT INTO kasir_penjualan (PelangganID, TotalHarga, UangBayar, UangKembali) VALUES (?, ?, ?, ?)";
        $stmt_penjualan = mysqli_prepare($koneksi, $query_penjualan);
        mysqli_stmt_bind_param($stmt_penjualan, "iddd", $pelanggan_id, $total_harga, $uang_bayar, $uang_kembali);
        mysqli_stmt_execute($stmt_penjualan);
        $penjualan_id = mysqli_insert_id($koneksi);

        // 2. Insert ke tabel detail penjualan dan update stok
        foreach ($cart_data as $item) {
            $produk_id = (int)$item['id'];
            $jumlah = (int)$item['qty'];
            $subtotal = (float)$item['subtotal'];

            // Insert detail
            $query_detail = "INSERT INTO kasir_detailpenjualan (PenjualanID, ProdukID, JumlahProduk, Subtotal) VALUES (?, ?, ?, ?)";
            $stmt_detail = mysqli_prepare($koneksi, $query_detail);
            mysqli_stmt_bind_param($stmt_detail, "iiid", $penjualan_id, $produk_id, $jumlah, $subtotal);
            mysqli_stmt_execute($stmt_detail);

            // Update stok produk
            $query_stok = "UPDATE kasir_produk SET Stok = Stok - ? WHERE ProdukID = ?";
            $stmt_stok = mysqli_prepare($koneksi, $query_stok);
            mysqli_stmt_bind_param($stmt_stok, "ii", $jumlah, $produk_id);
            mysqli_stmt_execute($stmt_stok);
        }

        // Jika semua berhasil, commit transaksi
        mysqli_commit($koneksi);

        // Redirect ke halaman cetak nota
        header("Location: cetak-nota.php?id=" . $penjualan_id);
        exit();

    } catch (Exception $e) {
        // Jika ada error, rollback transaksi
        mysqli_rollback($koneksi);
        die("Transaksi gagal: " . $e->getMessage());
    }

} else {
    header("Location: index.php");
    exit();
}
?>