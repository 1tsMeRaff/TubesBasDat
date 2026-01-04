<?php
require_once __DIR__ . "/../middleware/admin_auth.php";
require_once __DIR__ . "/../../config/database.php";
include __DIR__ . "/../templates/header.php";

$query = mysqli_query($conn, "
    SELECT 
        l.Created_At,
        pi.Nama_Produk,
        l.Kode_SKU,
        l.Perubahan,
        l.Jumlah,
        l.No_Transaksi
    FROM log_stok_changes l
    JOIN produk_varian pv ON l.Kode_SKU = pv.Kode_SKU
    JOIN produk_induk pi ON pv.ID_Induk = pi.ID_Induk
    ORDER BY l.Created_At DESC
");
?>

<h1>Log Stok dari Transaksi</h1>

<table border="1" cellpadding="10">
<tr>
    <th>Waktu</th>
    <th>Produk</th>
    <th>SKU</th>
    <th>Perubahan</th>
    <th>Jumlah</th>
    <th>No Transaksi</th>
</tr>

<?php while($row = mysqli_fetch_assoc($query)): ?>
<tr>
    <td><?= $row['Created_At'] ?></td>
    <td><?= $row['Nama_Produk'] ?></td>
    <td><?= $row['Kode_SKU'] ?></td>
    <td><?= $row['Perubahan'] ?></td>
    <td><?= $row['Jumlah'] ?></td>
    <td><?= $row['No_Transaksi'] ?></td>
</tr>
<?php endwhile; ?>
</table>

<?php include __DIR__ . "/../templates/footer.php"; ?>
