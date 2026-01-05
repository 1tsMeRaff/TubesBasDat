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

<section class="section admin-dashboard">
    <div class="container">

        <div class="admin-header mb-4">
            <h2 class="section-title">Dashboard Admin</h2>
            <p class="text-muted">
                Selamat datang, <strong><?= $_SESSION['admin_nama']; ?></strong>
            </p>
        </div>

        <div class="row g-4 stats">

            <div class="col-lg-3 col-md-4 col-sm-6">
                <div class="card stat-card">
                    <p>Total Produk</p>
                    <h2><?= $produk['total']; ?></h2>
                </div>
            </div>

            <div class="col-lg-3 col-md-4 col-sm-6">
                <div class="card stat-card">
                    <p>Total Varian</p>
                    <h2><?= $varian['total']; ?></h2>
                </div>
            </div>

            <div class="col-lg-3 col-md-4 col-sm-6">
                <div class="card stat-card">
                    <p>Total Pelanggan</p>
                    <h2><?= $pelanggan['total']; ?></h2>
                </div>
            </div>

            <div class="col-lg-3 col-md-4 col-sm-6">
                <div class="card stat-card">
                    <p>Total Transaksi</p>
                    <h2><?= $transaksi['total']; ?></h2>
                </div>
            </div>

            <div class="col-lg-3 col-md-4 col-sm-6">
                <div class="card stat-card">
                    <p>Total Stok</p>
                    <h2><?= $stok['total'] ?? 0; ?></h2>
                </div>
            </div>

        </div>
    </div>
</section>

<?php include __DIR__ . "/templates/footer.php"; ?>
