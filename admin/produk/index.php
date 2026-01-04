<?php
require_once __DIR__ . "/../middleware/admin_auth.php";
require_once __DIR__ . "/../../config/database.php";
include __DIR__ . "/../templates/header.php";

$query = mysqli_query($conn, "SELECT ID_Induk, Nama_Produk, ID_Kategori FROM produk_induk");
?>

<h1>Manajemen Produk</h1>
<a href="tambah.php">+ Tambah Produk</a>

<table border="1" cellpadding="10">
    <tr>
        <th>No</th>
        <th>Nama Produk</th>
        <th>Kategori</th>
        <th>Aksi</th>
    </tr>

<?php $no=1; while($p = mysqli_fetch_assoc($query)): ?>
<tr>
    <td><?= $no++ ?></td>
    <td><?= $p['Nama_Produk'] ?></td>
    <td><?= $p['ID_Kategori'] ?></td>
    <td>
        <a href="edit.php?id=<?= $p['id_produk'] ?>">Edit</a> |
        <a href="hapus.php?id=<?= $p['id_produk'] ?>" 
           onclick="return confirm('Hapus produk ini?')">Hapus</a>
    </td>
</tr>
<?php endwhile; ?>
</table>

<?php include __DIR__ . "/../templates/footer.php"; ?>
