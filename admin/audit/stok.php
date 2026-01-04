<?php
require_once __DIR__ . "/../middleware/admin_auth.php";
require_once __DIR__ . "/../../config/database.php";
include __DIR__ . "/../templates/header.php";

$query = mysqli_query($conn, "
    SELECT 
        a.Changed_At,
        pi.Nama_Produk,
        a.Kode_SKU,
        a.Old_Stock,
        a.New_Stock,
        (a.New_Stock - a.Old_Stock) AS Selisih,
        a.Reason,
        a.Changed_By
    FROM audit_stock_changes a
    JOIN produk_varian pv ON a.Kode_SKU = pv.Kode_SKU
    JOIN produk_induk pi ON pv.ID_Induk = pi.ID_Induk
    ORDER BY a.Changed_At DESC
");
?>

<h1>Audit Perubahan Stok</h1>

<table border="1" cellpadding="10" cellspacing="0">
<tr>
    <th>Waktu</th>
    <th>Produk</th>
    <th>SKU</th>
    <th>Stok Lama</th>
    <th>Stok Baru</th>
    <th>Selisih</th>
    <th>Alasan</th>
    <th>Diubah Oleh</th>
</tr>

<?php while($row = mysqli_fetch_assoc($query)): ?>
<tr>
    <td><?= $row['Changed_At'] ?></td>
    <td><?= $row['Nama_Produk'] ?></td>
    <td><?= $row['Kode_SKU'] ?></td>
    <td><?= $row['Old_Stock'] ?></td>
    <td><?= $row['New_Stock'] ?></td>
    <td><?= $row['Selisih'] ?></td>
    <td><?= $row['Reason'] ?></td>
    <td><?= $row['Changed_By'] ?></td>
</tr>
<?php endwhile; ?>
</table>

<?php include __DIR__ . "/../templates/footer.php"; ?>
