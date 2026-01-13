<?php
require_once __DIR__ . "/../middleware/admin_auth.php";
require_once __DIR__ . "/../../config/database.php";
include __DIR__ . "/../templates/header.php";

$q = mysqli_query($conn,
    "SELECT * FROM audit_stock_changes ORDER BY Changed_At DESC"
);
?>

<h2>Audit Stok</h2>

<table>
<tr>
    <th>SKU</th>
    <th>Stok Lama</th>
    <th>Stok Baru</th>
    <th>Admin</th>
    <th>Waktu</th>
</tr>

<?php while ($r = mysqli_fetch_assoc($q)): ?>
<tr>
    <td><?= $r['Kode_SKU'] ?></td>
    <td><?= $r['Old_Stock'] ?></td>
    <td><?= $r['New_Stock'] ?></td>
    <td><?= $r['Changed_By'] ?></td>
    <td><?= $r['Changed_At'] ?></td>
</tr>
<?php endwhile; ?>
</table>

<?php include __DIR__ . "/../templates/footer.php"; ?>
