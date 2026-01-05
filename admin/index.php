<?php
require_once __DIR__ . "/middleware/admin_auth.php";
require_once __DIR__ . "/../config/database.php";

// ================== QUERY STATISTIK ==================
$qProduk     = mysqli_query($conn, "SELECT COUNT(*) AS total FROM produk_induk");
$qVarian     = mysqli_query($conn, "SELECT COUNT(*) AS total FROM produk_varian");
$qPelanggan  = mysqli_query($conn, "SELECT COUNT(*) AS total FROM pelanggan");
$qTransaksi  = mysqli_query($conn, "SELECT COUNT(*) AS total FROM transaksi");
$qStok       = mysqli_query($conn, "SELECT SUM(stok) AS total FROM produk_varian");

$produk     = mysqli_fetch_assoc($qProduk);
$varian     = mysqli_fetch_assoc($qVarian);
$pelanggan  = mysqli_fetch_assoc($qPelanggan);
$transaksi  = mysqli_fetch_assoc($qTransaksi);
$stok       = mysqli_fetch_assoc($qStok);
?>

<?php include __DIR__ . "/templates/header.php"; ?>

<h1>Dashboard Admin</h1>
<p>
    Selamat datang, 
    <b><?= htmlspecialchars($_SESSION['user']['name']); ?></b>
</p>

<div class="stats">
    <div class="card">
        <p>Total Produk</p>
        <h2><?= $produk['total']; ?></h2>
    </div>

    <div class="card">
        <p>Total Varian</p>
        <h2><?= $varian['total']; ?></h2>
    </div>

    <div class="card">
        <p>Total Pelanggan</p>
        <h2><?= $pelanggan['total']; ?></h2>
    </div>

    <div class="card">
        <p>Total Transaksi</p>
        <h2><?= $transaksi['total']; ?></h2>
    </div>

    <div class="card">
        <p>Total Stok</p>
        <h2><?= $stok['total'] ?? 0; ?></h2>
    </div>
</div>

<?php include __DIR__ . "/templates/footer.php"; ?>