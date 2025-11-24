<?php
session_start();
require_once 'database/koneksi.php';

// Ambil data produk
 $produk_query = mysqli_query($koneksi, "SELECT * FROM kasir_produk WHERE Stok > 0 ORDER BY NamaProduk ASC");
// Ambil data pelanggan
 $pelanggan_query = mysqli_query($koneksi, "SELECT * FROM kasir_pelanggan ORDER BY NamaPelanggan ASC");

 $page_title = "Halaman Kasir";
include 'includes/header.php';
?>

<main class="container">
    <h1>Desain Halaman Kasir</h1>
    <div class="kasir-container">
        <!-- Daftar Produk -->
        <section class="produk-list">
            <h2>Daftar Produk</h2>
            <div class="produk-grid">
                <?php while($produk = mysqli_fetch_assoc($produk_query)): ?>
                    <div class="produk-card" data-id="<?= $produk['ProdukID'] ?>" data-nama="<?= $produk['NamaProduk'] ?>" data-harga="<?= $produk['Harga'] ?>">
                        <h3><?= htmlspecialchars($produk['NamaProduk']) ?></h3>
                        <p>Harga: Rp. <?= number_format($produk['Harga'], 2, ',', '.') ?></p>
                        <p>Stok: <?= $produk['Stok'] ?></p>
                    </div>
                <?php endwhile; ?>
            </div>
        </section>

        <!-- Detail Pembayaran -->
        <section class="pembayaran">
            <h2>Detail Pembayaran</h2>
            
            <div class="pelanggan-section">
                <label for="pelanggan">Pelanggan:</label>
                <select name="pelanggan" id="pelanggan">
                    <option value="">-- Pilih Pelanggan --</option>
                    <?php while($pelanggan = mysqli_fetch_assoc($pelanggan_query)): ?>
                        <option value="<?= $pelanggan['PelangganID'] ?>"><?= htmlspecialchars($pelanggan['NamaPelanggan']) ?></option>
                    <?php endwhile; ?>
                </select>
            </div>

            <table class="cart-table">
                <thead>
                    <tr>
                        <th>Produk</th>
                        <th>Harga</th>
                        <th>Qty</th>
                        <th>Subtotal</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody id="cart-body">
                    <!-- Item keranjang akan ditambahkan di sini oleh JavaScript -->
                </tbody>
                <tfoot>
                    <tr>
                        <td colspan="3"><strong>Total:</strong></td>
                        <td id="total-harga">Rp. 0</td>
                        <td></td>
                    </tr>
                </tfoot>
            </table>

            <form id="form-pembayaran" action="proses-transaksi.php" method="POST">
                <div class="payment-section">
                    <div class="form-group">
                        <label for="uang-bayar">Uang Bayar:</label>
                        <input type="number" id="uang-bayar" name="uang_bayar" min="0" step="0.01" required>
                    </div>
                    <div class="form-group">
                        <label for="uang-kembali">Uang Kembali:</label>
                        <input type="text" id="uang-kembali" name="uang_kembali" value="Rp. 0" readonly>
                    </div>
                </div>
                
                <input type="hidden" name="pelanggan_id" id="pelanggan_id">
                <input type="hidden" name="total_bayar" id="total_bayar">
                <input type="hidden" name="cart_data" id="cart_data">
                
                <div class="form-actions">
                    <button type="submit" class="btn-bayar">Bayar</button>
                    <button type="button" class="btn-batal" id="btn-batal">Batal</button>
                </div>
            </form>
        </section>
    </div>
</main>

<?php include 'includes/footer.php'; ?>