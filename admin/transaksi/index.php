<?php
require_once __DIR__ . "/../middleware/admin_auth.php";
require_once __DIR__ . "/../../config/database.php";
include __DIR__ . "/../templates/header.php";

$data = mysqli_query($conn, "
    SELECT * FROM log_stok_changes
    ORDER BY Created_At DESC
");
?>

<h1>Riwayat Transaksi</h1>

<table border="1" cellpadding="8">
<tr>
    <th>Kode SKU</th>
    <th>Perubahan</th>
    <th>Jumlah</th>
    <th>Waktu</th>
</tr>

<?php while($d = mysqli_fetch_assoc($data)): ?>
<tr>
    <td><?= $d['Kode_SKU'] ?></td>
    <td><?= $d['Perubahan'] ?></td>
    <td><?= $d['Jumlah'] ?></td>
    <td><?= $d['Created_At'] ?></td>
</tr>
<?php endwhile; ?>
</table>

<?php include __DIR__ . "/../templates/footer.php"; ?>
