<?php
require_once __DIR__ . "/../middleware/admin_auth.php";
require_once __DIR__ . "/../../config/database.php";
include __DIR__ . "/../templates/header.php";

$query = mysqli_query($conn, "
    SELECT 
        pi.Nama_Produk,
        pv.Kode_SKU,
        pv.Stok,
        pv.Harga_Jual
    FROM produk_varian pv
    JOIN produk_induk pi 
        ON pv.ID_Induk = pi.ID_Induk
    WHERE pv.Is_Active = 1
");
?>

<h1>Manajemen Stok</h1>

<table border="1" cellpadding="10">
    <tr>
        <th>No</th>
        <th>Nama Produk</th>
        <th>SKU</th>
        <th>Stok</th>
        <th>Harga Jual</th>
    </tr>

<?php $no=1; while($s = mysqli_fetch_assoc($query)): ?>
<tr>
    <td><?= $no++ ?></td>
    <td><?= $s['Nama_Produk'] ?></td>
    <td><?= $s['Kode_SKU'] ?></td>
    <td><?= $s['Stok'] ?></td>
    <td>Rp <?= number_format($s['Harga_Jual']) ?></td>
</tr>
<?php endwhile; ?>
</table>

<?php include __DIR__ . "/../templates/footer.php"; ?>
