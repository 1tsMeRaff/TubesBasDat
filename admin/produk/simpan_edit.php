<?php
session_start();
require_once __DIR__ . "/../../config/database.php";
require_once __DIR__ . "/../audit/audit_helper.php";

$kodeSKU = $_POST['kode_sku'];
$hargaBaru = $_POST['harga_jual'];
$admin = $_SESSION['user']['name'];

// Ambil harga lama
$qOld = mysqli_query($conn,
    "SELECT Harga_Jual FROM produk_varian WHERE Kode_SKU='$kodeSKU'"
);
$old = mysqli_fetch_assoc($qOld);
$hargaLama = $old['Harga_Jual'];

// Update harga
mysqli_query($conn, "
    UPDATE produk_varian
    SET Harga_Jual='$hargaBaru'
    WHERE Kode_SKU='$kodeSKU'
");

// Audit otomatis
auditLog($conn, $kodeSKU, $hargaLama, $hargaBaru, 'HARGA', $admin);

header("Location: index.php");
exit;
