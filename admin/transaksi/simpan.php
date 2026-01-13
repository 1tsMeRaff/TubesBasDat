<?php
session_start();
require_once __DIR__ . "/../../config/database.php";

$kodeSKU = $_POST['kode_sku'];
$qty = (int) $_POST['qty'];
$admin = $_SESSION['user']['name'];

$noTransaksi = "TRX-" . time();

// Ambil harga & stok
$q = mysqli_query($conn,
    "SELECT Harga_Jual, Stok FROM produk_varian WHERE Kode_SKU='$kodeSKU'"
);
$p = mysqli_fetch_assoc($q);

$harga = $p['Harga_Jual'];
$stokLama = $p['Stok'];

if ($stokLama < $qty) {
    die("Stok tidak cukup");
}

$subtotal = $harga * $qty;
$stokBaru = $stokLama - $qty;

// === SIMPAN TRANSAKSI ===
mysqli_query($conn, "
    INSERT INTO transaksi (No_Transaksi, Total, Dibuat_Oleh)
    VALUES ('$noTransaksi', '$subtotal', '$admin')
");

// === DETAIL ===
mysqli_query($conn, "
    INSERT INTO transaksi_detail
    (No_Transaksi, Kode_SKU, Qty, Harga, Subtotal)
    VALUES
    ('$noTransaksi', '$kodeSKU', '$qty', '$harga', '$subtotal')
");

// === UPDATE STOK ===
mysqli_query($conn, "
    UPDATE produk_varian
    SET Stok='$stokBaru'
    WHERE Kode_SKU='$kodeSKU'
");

// === LOG STOK ===
mysqli_query($conn, "
    INSERT INTO log_stok_changes
    (Kode_SKU, Perubahan, Jumlah, No_Transaksi)
    VALUES
    ('$kodeSKU', 'DECREASE', '$qty', '$noTransaksi')
");

header("Location: index.php");
exit;
