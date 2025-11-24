<?php
session_start();
require_once 'database/koneksi.php';

// Ambil data pelanggan
 $pelanggan_query = mysqli_query($koneksi, "SELECT * FROM kasir_pelanggan ORDER BY NamaPelanggan ASC");

// Inisialisasi variabel untuk form edit
 $edit_data = null;
if (isset($_GET['edit'])) {
    $edit_id = (int)$_GET['edit'];
    $edit_query = mysqli_query($koneksi, "SELECT * FROM kasir_pelanggan WHERE PelangganID = $edit_id");
    $edit_data = mysqli_fetch_assoc($edit_query);
}

 $page_title = "Manajemen Pelanggan";
include 'includes/header.php';
?>

<main class="container">
    <h1>Manajemen Pelanggan</h1>

    <?php if (isset($_SESSION['pesan'])): ?>
        <div class="alert success"><?php echo $_SESSION['pesan']; unset($_SESSION['pesan']); ?></div>
    <?php endif; ?>
    <?php if (isset($_SESSION['error'])): ?>
        <div class="alert error"><?php echo $_SESSION['error']; unset($_SESSION['error']); ?></div>
    <?php endif; ?>

    <div class="form-container">
        <h2><?= $edit_data ? 'Edit Pelanggan' : 'Tambah Pelanggan Baru' ?></h2>
        <form action="proses_pelanggan.php" method="POST" class="form-manajemen">
            <input type="hidden" name="aksi" value="<?= $edit_data ? 'edit' : 'tambah' ?>">
            <?php if ($edit_data): ?>
                <input type="hidden" name="id" value="<?= $edit_data['PelangganID'] ?>">
            <?php endif; ?>

            <div class="form-group">
                <label for="nama">Nama Lengkap</label>
                <input type="text" name="nama" id="nama" value="<?= $edit_data['NamaPelanggan'] ?? '' ?>" required>
            </div>
            <div class="form-group">
                <label for="alamat">Alamat</label>
                <textarea name="alamat" id="alamat" rows="3"><?= $edit_data['Alamat'] ?? '' ?></textarea>
            </div>
            <div class="form-group">
                <label for="telepon">Nomor Telepon</label>
                <input type="tel" name="telepon" id="telepon" value="<?= $edit_data['NomorTelepon'] ?? '' ?>">
            </div>
            <button type="submit" class="btn-submit"><?= $edit_data ? 'Update Pelanggan' : 'Simpan Pelanggan' ?></button>
            <?php if ($edit_data): ?>
                <a href="kelola-pelanggan.php" class="btn-batal">Batal</a>
            <?php endif; ?>
        </form>
    </div>

    <div class="table-container">
        <h2>Daftar Pelanggan</h2>
        <table>
            <thead>
                <tr>
                    <th>Nama Pelanggan</th>
                    <th>Alamat</th>
                    <th>Nomor Telepon</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php while($pelanggan = mysqli_fetch_assoc($pelanggan_query)): ?>
                <tr>
                    <td><?= htmlspecialchars($pelanggan['NamaPelanggan']) ?></td>
                    <td><?= htmlspecialchars($pelanggan['Alamat']) ?></td>
                    <td><?= htmlspecialchars($pelanggan['NomorTelepon']) ?></td>
                    <td class="aksi-column">
                        <a href="?edit=<?= $pelanggan['PelangganID'] ?>" class="btn-edit">Edit</a>
                        <form action="proses_pelanggan.php" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus pelanggan ini?');">
                            <input type="hidden" name="aksi" value="hapus">
                            <input type="hidden" name="id" value="<?= $pelanggan['PelangganID'] ?>">
                            <button type="submit" class="btn-hapus">Hapus</button>
                        </form>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</main>

<?php include 'includes/footer.php'; ?>