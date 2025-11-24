<?php
session_start();
require_once 'database/koneksi.php';

// --- Query untuk Ringkasan Dashboard ---

// 1. Pendapatan & Transaksi Hari Ini
 $query_hari_ini = "SELECT 
                    COUNT(*) as total_transaksi, 
                    SUM(TotalHarga) as total_pendapatan 
                   FROM kasir_penjualan 
                   WHERE DATE(TanggalPenjualan) = CURDATE()";

 $result_hari_ini = mysqli_query($koneksi, $query_hari_ini);
 $data_hari_ini = mysqli_fetch_assoc($result_hari_ini);
 $pendapatan_hari_ini = $data_hari_ini['total_pendapatan'] ?? 0;
 $transaksi_hari_ini = $data_hari_ini['total_transaksi'] ?? 0;

// 2. Pendapatan & Transaksi Bulan Ini
 $query_bulan_ini = "SELECT 
                     COUNT(*) as total_transaksi, 
                     SUM(TotalHarga) as total_pendapatan 
                    FROM kasir_penjualan 
                    WHERE MONTH(TanggalPenjualan) = MONTH(CURDATE()) 
                    AND YEAR(TanggalPenjualan) = YEAR(CURDATE())";

 $result_bulan_ini = mysqli_query($koneksi, $query_bulan_ini);
 $data_bulan_ini = mysqli_fetch_assoc($result_bulan_ini);
 $pendapatan_bulan_ini = $data_bulan_ini['total_pendapatan'] ?? 0;
 $transaksi_bulan_ini = $data_bulan_ini['total_transaksi'] ?? 0;

// 3. Transaksi Terbaru (5 terakhir)
 $query_terbaru = "SELECT 
                    p.PenjualanID, 
                    p.TanggalPenjualan, 
                    p.TotalHarga, 
                    pl.NamaPelanggan 
                  FROM kasir_penjualan p
                  LEFT JOIN kasir_pelanggan pl ON p.PelangganID = pl.PelangganID
                  ORDER BY p.TanggalPenjualan DESC 
                  LIMIT 5";

 $result_terbaru = mysqli_query($koneksi, $query_terbaru);

 $page_title = "Dashboard";
include 'includes/header.php';
?>

<main class="container">
    <h1>Dashboard</h1>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <section class="dashboard-summary">
        <div class="summary-card">
            <div class="card-icon" style="background-color: #e3f2fd;">
                <i class="fas fa-money-bill-wave" style="color: #1976d2;"></i>
            </div>
            <div class="card-info">
                <h3>Pendapatan Hari Ini</h3>
                <p class="card-value">Rp. <?= number_format($pendapatan_hari_ini, 0, ',', '.') ?></p>
            </div>
        </div>

        <div class="summary-card">
            <div class="card-icon" style="background-color: #e8f5e9;">
                <i class="fas fa-shopping-cart" style="color: #388e3c;"></i>
            </div>
            <div class="card-info">
                <h3>Transaksi Hari Ini</h3>
                <p class="card-value"><?= number_format($transaksi_hari_ini, 0) ?></p>
            </div>
        </div>

        <div class="summary-card">
            <div class="card-icon" style="background-color: #fff3e0;">
                <i class="fas fa-calendar-alt" style="color: #f57c00;"></i>
            </div>
            <div class="card-info">
                <h3>Pendapatan Bulan Ini</h3>
                <p class="card-value">Rp. <?= number_format($pendapatan_bulan_ini, 0, ',', '.') ?></p>
            </div>
        </div>

        <div class="summary-card">
            <div class="card-icon" style="background-color: #fce4ec;">
                <i class="fas fa-chart-line" style="color: #c2185b;"></i>
            </div>
            <div class="card-info">
                <h3>Transaksi Bulan Ini</h3>
                <p class="card-value"><?= number_format($transaksi_bulan_ini, 0) ?></p>
            </div>
        </div>
    </section>

    <section class="dashboard-latest">
        <div class="table-container">
            <h2>Transaksi Terbaru</h2>
            <table>
                <thead>
                    <tr>
                        <th>No. Nota</th>
                        <th>Tanggal & Waktu</th>
                        <th>Pelanggan</th>
                        <th>Total</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (mysqli_num_rows($result_terbaru) > 0): ?>
                        <?php while($transaksi = mysqli_fetch_assoc($result_terbaru)): ?>
                        <tr>
                            <td>#<?= str_pad($transaksi['PenjualanID'], 5, '0', STR_PAD_LEFT) ?></td>
                            <td><?= date('d M Y, H:i', strtotime($transaksi['TanggalPenjualan'])) ?></td>
                            <td><?= htmlspecialchars($transaksi['NamaPelanggan'] ?? 'Pelanggan Umum') ?></td>
                            <td>Rp. <?= number_format($transaksi['TotalHarga'], 0, ',', '.') ?></td>
                            <td>
                                <a href="cetak-nota.php?id=<?= $transaksi['PenjualanID'] ?>&view=true" class="btn btn-info">
                                    <i class="fas fa-eye"></i> Lihat
                                </a>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="5" style="text-align: center;">Belum ada transaksi.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </section>
</main>

<?php include 'includes/footer.php'; ?>