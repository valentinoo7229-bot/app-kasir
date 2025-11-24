<?php
require_once 'database/koneksi.php';

if (!isset($_GET['id'])) {
    header("Location: index.php");
    exit();
}

 $penjualan_id = $_GET['id'];

// Ambil data penjualan
 $query_penjualan = "SELECT p.*, pl.NamaPelanggan FROM kasir_penjualan p LEFT JOIN kasir_pelanggan pl ON p.PelangganID = pl.PelangganID WHERE p.PenjualanID = ?";
 $stmt_penjualan = mysqli_prepare($koneksi, $query_penjualan);
mysqli_stmt_bind_param($stmt_penjualan, "i", $penjualan_id);
mysqli_stmt_execute($stmt_penjualan);
 $result_penjualan = mysqli_stmt_get_result($stmt_penjualan);
 $penjualan = mysqli_fetch_assoc($result_penjualan);

if (!$penjualan) {
    echo "Nota tidak ditemukan.";
    exit();
}

// Ambil detail penjualan
 $query_detail = "SELECT dp.NamaProduk, dp.JumlahProduk, dp.Harga, dp.Subtotal 
                 FROM (
                     SELECT dp.PenjualanID, pr.NamaProduk, dp.JumlahProduk, pr.Harga, dp.Subtotal
                     FROM kasir_detailpenjualan dp
                     JOIN kasir_produk pr ON dp.ProdukID = pr.ProdukID
                 ) AS dp
                 WHERE dp.PenjualanID = ?";
 $stmt_detail = mysqli_prepare($koneksi, $query_detail);
mysqli_stmt_bind_param($stmt_detail, "i", $penjualan_id);
mysqli_stmt_execute($stmt_detail);
 $result_detail = mysqli_stmt_get_result($stmt_detail);

 $page_title = "Cetak Nota";
// Kita tidak perlu include header/footer agar tampilan print bersih
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $page_title ?></title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;700&family=Courier+Prime:wght@400;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>

    <div class="nota-wrapper">
        <div class="nota-container">
            <div class="nota-header">
                <div class="logo-area">
                    <i class="fas fa-mug-hot"></i>
                    <h1>Kopi Kenangan Senja</h1>
                </div>
                <p class="tagline">"Setiap Tegukan Adalah Cerita"</p>
                <hr>
                <p><i class="fas fa-map-marker-alt"></i> Jl. Coffeeshop No. 1, Jakarta</p>
                <p><i class="fas fa-phone"></i> 021-12345678</p>
            </div>
            
            <div class="nota-body">
                <div class="nota-info">
                    <table>
                        <tr>
                            <td>No. Nota:</td>
                            <td>: #<?= str_pad($penjualan['PenjualanID'], 5, '0', STR_PAD_LEFT) ?></td>
                        </tr>
                        <tr>
                            <td>Kasir:</td>
                            <td>: Admin</td>
                        </tr>
                        <tr>
                            <td>Tanggal:</td>
                            <td>: <?= date('d M Y, H:i', strtotime($penjualan['TanggalPenjualan'])) ?></td>
                        </tr>
                        <tr>
                            <td>Pelanggan:</td>
                            <td>: <?= htmlspecialchars($penjualan['NamaPelanggan'] ?? 'Umum') ?></td>
                        </tr>
                    </table>
                </div>

                <hr class="dashed">

                <div class="nota-items">
                    <table>
                        <thead>
                            <tr>
                                <th colspan="3">ITEM</th>
                                <th class="text-right">TOTAL</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                            $no = 1;
                            while($item = mysqli_fetch_assoc($result_detail)): 
                            ?>
                                <tr>
                                    <td colspan="3">
                                        <?= $item['NamaProduk'] ?><br>
                                        <small><?= number_format($item['Harga'], 0, ',', '.') ?> x <?= $item['JumlahProduk'] ?></small>
                                    </td>
                                    <td class="text-right"><?= number_format($item['Subtotal'], 0, ',', '.') ?></td>
                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>

                <hr class="dashed">

                <div class="nota-summary">
                    <table>
                        <tr>
                            <td>Subtotal:</td>
                            <td class="text-right"><?= number_format($penjualan['TotalHarga'], 0, ',', '.') ?></td>
                        </tr>
                        <tr>
                            <td>Tunai:</td>
                            <td class="text-right"><?= number_format($penjualan['UangBayar'], 0, ',', '.') ?></td>
                        </tr>
                        <tr class="grand-total">
                            <td>Kembali:</td>
                            <td class="text-right"><?= number_format($penjualan['UangKembali'], 0, ',', '.') ?></td>
                        </tr>
                    </table>
                </div>
            </div>
            
            <div class="nota-footer">
                <hr>
                <p class="thank-you">Terima Kasih</p>
                <p class="visit-again">dan selamat menikmati</p>
                <br>
                <p class="social-info">www.kopikenangansenja.id</p>
            </div>
        </div>
        
        <!-- ... kode lainnya di cetak-nota.php ... -->
        
        <div class="action-buttons">
            <?php if (isset($_GET['view'])): ?>
                <!-- Tampilan saat dibuka dari Riwayat Transaksi -->
                <a href="riwayat-transaksi.php" class="btn btn-secondary"><i class="fas fa-arrow-left"></i> Kembali</a>
                <button class="btn btn-primary" onclick="window.print()"><i class="fas fa-print"></i> Cetak Nota</button>
            <?php else: ?>
                <!-- Tampilan normal setelah transaksi -->
                <button class="btn btn-primary" onclick="window.print()"><i class="fas fa-print"></i> Cetak Nota</button>
                <a href="index.php" class="btn btn-success"><i class="fas fa-cash-register"></i> Transaksi Baru</a>
            <?php endif; ?>
        </div>
    </div>

</body>
</html>