<?php
require_once __DIR__ . "/../middleware/admin_auth.php";
require_once __DIR__ . "/../../config/database.php";
include __DIR__ . "/../templates/header.php";

$no = $_GET['no'];

$q = mysqli_query($conn, "
    SELECT d.*, v.Kode_SKU
    FROM transaksi_detail d
    JOIN produk_varian v ON d.Kode_SKU = v.Kode_SKU
    WHERE d.No_Transaksi='$no'
");
?>

<h2>Detail Transaksi <?= $no ?></h2>

<table>
<tr>
    <th>SKU</th>
    <th>Qty</th>
    <th>Harga</th>
    <th>Subtotal</th>
</tr>

<?php while($d = mysqli_fetch_assoc($q)): ?>
<tr>
    <td><?= $d['Kode_SKU'] ?></td>
    <td><?= $d['Qty'] ?></td>
    <td>Rp<?= number_format($d['Harga']) ?></td>
    <td>Rp<?= number_format($d['Subtotal']) ?></td>
</tr>
<?php endwhile; ?>
</table>

<?php include __DIR__ . "/../templates/footer.php"; ?>
