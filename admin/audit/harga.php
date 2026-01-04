<?php
require_once __DIR__ . "/../middleware/admin_auth.php";
require_once __DIR__ . "/../../config/database.php";
include __DIR__ . "/../templates/header.php";

$query = mysqli_query($conn, "
    SELECT 
        a.Changed_At,
        pi.Nama_Produk,
        a.Kode_SKU,
        a.Old_Price,
        a.New_Price,
        a.Percentage_Change,
        a.Changed_By
    FROM audit_price_changes a
    JOIN produk_varian pv ON a.Kode_SKU = pv.Kode_SKU
    JOIN produk_induk pi ON pv.ID_Induk = pi.ID_Induk
    ORDER BY a.Changed_At DESC
");
?>

<h1>Audit Perubahan Harga</h1>

<table border="1" cellpadding="10" cellspacing="0">
<tr>
    <th>Waktu</th>
    <th>Produk</th>
    <th>SKU</th>
    <th>Harga Lama</th>
    <th>Harga Baru</th>
    <th>Persentase</th>
    <th>Diubah Oleh</th>
</tr>

<?php while($row = mysqli_fetch_assoc($query)): ?>
<tr>
    <td><?= $row['Changed_At'] ?></td>
    <td><?= $row['Nama_Produk'] ?></td>
    <td><?= $row['Kode_SKU'] ?></td>
    <td>Rp <?= number_format($row['Old_Price']) ?></td>
    <td>Rp <?= number_format($row['New_Price']) ?></td>
    <td><?= $row['Percentage_Change'] ?>%</td>
    <td><?= $row['Changed_By'] ?></td>
</tr>
<?php endwhile; ?>
</table>

<?php include __DIR__ . "/../templates/footer.php"; ?>
