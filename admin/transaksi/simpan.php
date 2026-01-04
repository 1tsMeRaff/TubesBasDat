<?php
require_once __DIR__ . "/../middleware/admin_auth.php";
require_once __DIR__ . "/../../config/database.php";

$kode_sku = $_POST['kode_sku'];
$jumlah   = (int) $_POST['jumlah'];
$admin    = $_SESSION['admin_nama'];

// Ambil stok lama
$q = mysqli_query($conn, "SELECT Stok FROM produk_varian WHERE Kode_SKU='$kode_sku'");
$data = mysqli_fetch_assoc($q);

$stok_lama = $data['Stok'];

if ($jumlah > $stok_lama) {
    die("Stok tidak mencukupi");
}

$stok_baru = $stok_lama - $jumlah;

// ================= UPDATE STOK =================
mysqli_query($conn, "
    UPDATE produk_varian 
    SET Stok = $stok_baru 
    WHERE Kode_SKU = '$kode_sku'
");

// ================= AUDIT STOCK =================
mysqli_query($conn, "
    INSERT INTO audit_stock_changes
    (Kode_SKU, Old_Stock, New_Stock, Reason, Changed_By)
    VALUES
    ('$kode_sku', $stok_lama, $stok_baru, 'Penjualan', '$admin')
");

// ================= LOG STOCK =================
mysqli_query($conn, "
    INSERT INTO log_stok_changes
    (Kode_SKU, Perubahan, Jumlah)
    VALUES
    ('$kode_sku', 'DECREASE', $jumlah)
");

header("Location: index.php");
exit;
