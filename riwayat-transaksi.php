<?php
session_start();
require_once 'database/koneksi.php';

// Query untuk mengambil data penjualan beserta nama pelanggan, diurutkan dari yang terbaru
 $query = "SELECT 
            p.PenjualanID, 
            p.TanggalPenjualan, 
            p.TotalHarga, 
            pl.NamaPelanggan 
          FROM kasir_penjualan p
          LEFT JOIN kasir_pelanggan pl ON p.PelangganID = pl.PelangganID
          ORDER BY p.TanggalPenjualan DESC";

 $result = mysqli_query($koneksi, $query);

 $page_title = "Riwayat Transaksi";
include 'includes/header.php';
?>

<main class="container">
    <h1>Riwayat Transaksi</h1>

    <div class="table-container">
        <table>
            <thead>
                <tr>
                    <th>No. Nota</th>
                    <th>Tanggal & Waktu</th>
                    <th>Pelanggan</th>
                    <th>Total Harga</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php if (mysqli_num_rows($result) > 0): ?>
                    <?php while($transaksi = mysqli_fetch_assoc($result)): ?>
                    <tr>
                        <td>#<?= str_pad($transaksi['PenjualanID'], 5, '0', STR_PAD_LEFT) ?></td>
                        <td><?= date('d M Y, H:i', strtotime($transaksi['TanggalPenjualan'])) ?></td>
                        <td><?= htmlspecialchars($transaksi['NamaPelanggan'] ?? 'Pelanggan Umum') ?></td>
                        <td>Rp. <?= number_format($transaksi['TotalHarga'], 0, ',', '.') ?></td>
                        <td class="aksi-column">
                            <a href="cetak-nota.php?id=<?= $transaksi['PenjualanID'] ?>&view=true" class="btn btn-info">
                                <i class="fas fa-eye"></i> Lihat Detail
                            </a>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="5" style="text-align: center; padding: 20px;">Belum ada riwayat transaksi.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</main>

<?php include 'includes/footer.php'; ?>