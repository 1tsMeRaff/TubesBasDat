<?php
require_once __DIR__ . "/../middleware/admin_auth.php";
include __DIR__ . "/../templates/header.php";
?>

<h1>Audit Sistem</h1>

<ul>
    <li><a href="stok.php">Audit Perubahan Stok</a></li>
    <li><a href="harga.php">Audit Perubahan Harga</a></li>
    <li><a href="log_transaksi.php">Log Stok Transaksi</a></li>
</ul>

<?php include __DIR__ . "/../templates/footer.php"; ?>
