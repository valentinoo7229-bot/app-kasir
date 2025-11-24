<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= isset($page_title) ? htmlspecialchars($page_title) : 'Kasir Coffeeshop' ?></title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;700&display=swap" rel="stylesheet">
</head>
<body>
    <header>
        <nav>
            <div class="logo">
                <img src="assets/logo.png" alt="Logo Kopi Kenangan Senja">
                <h1>Kopi Kenangan Senja</h1>
            </div>
            <ul>
                <li><a href="dashboard.php" class="active">Dashboard</a></li>
                <li><a href="index.php">Kasir</a></li>
                <li><a href="kelola-stok.php">Manajemen Stok</a></li>
                <li><a href="kelola-pelanggan.php">Manajemen Pelanggan</a></li>
                <li><a href="riwayat-transaksi.php">Riwayat Transaksi</a></li>
            </ul>
        </nav>
    </header>