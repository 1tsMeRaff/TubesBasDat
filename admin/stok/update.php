<?php
session_start();
require_once __DIR__ . "/../../config/database.php";
require_once __DIR__ . "/../audit/audit_helper.php";

$kodeSKU = $_POST['kode_sku'];
$stokBaru = $_POST['stok'];
$admin = $_SESSION['user']['name'];

// Ambil stok lama
$qOld = mysqli_query($conn,
    "SELECT Stok FROM produk_varian WHERE Kode_SKU='$kodeSKU'"
);
$old = mysqli_fetch_assoc($qOld);
$stokLama = $old['Stok'];

// Update stok
mysqli_query($conn, "
    UPDATE produk_varian
    SET Stok='$stokBaru'
    WHERE Kode_SKU='$kodeSKU'
");

// Audit otomatis
auditLog($conn, $kodeSKU, $stokLama, $stokBaru, 'STOK', $admin);

header("Location: index.php");
exit;
