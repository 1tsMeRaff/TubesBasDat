<?php
// Middleware & database (PAKAI __DIR__)
require_once __DIR__ . "/middleware/admin_auth.php";
require_once __DIR__ . "/../config/database.php";

// ================== QUERY STATISTIK ==================

// Total produk induk
$qProduk = mysqli_query($conn, "SELECT COUNT(*) AS total FROM produk_induk");
$produk = mysqli_fetch_assoc($qProduk);

// Total varian
$qVarian = mysqli_query($conn, "SELECT COUNT(*) AS total FROM produk_varian");
$varian = mysqli_fetch_assoc($qVarian);

// Total pelanggan
$qPelanggan = mysqli_query($conn, "SELECT COUNT(*) AS total FROM pelanggan");
$pelanggan = mysqli_fetch_assoc($qPelanggan);

// Total transaksi
$qTransaksi = mysqli_query($conn, "SELECT COUNT(*) AS total FROM transaksi");
$transaksi = mysqli_fetch_assoc($qTransaksi);

// Total stok
$qStok = mysqli_query($conn, "SELECT SUM(stok) AS total FROM produk_varian");
$stok = mysqli_fetch_assoc($qStok);
?>

<?php include __DIR__ . "/templates/header.php"; ?>

<h1>Dashboard Admin</h1>
<p>Selamat datang, <b><?= $_SESSION['admin_nama']; ?></b></p>

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
        <h2><?= $stok['total']; ?></h2>
    </div>
</div>

<?php include __DIR__ . "/templates/footer.php"; ?>
