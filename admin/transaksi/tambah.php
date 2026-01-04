<?php
require_once __DIR__ . "/../middleware/admin_auth.php";
require_once __DIR__ . "/../../config/database.php";
include __DIR__ . "/../templates/header.php";

$produk = mysqli_query($conn, "
    SELECT pv.Kode_SKU, pi.Nama_Produk, pv.Stok, pv.Harga_Jual
    FROM produk_varian pv
    JOIN produk_induk pi ON pv.ID_Induk = pi.ID_Induk
");
?>

<h1>Tambah Transaksi</h1>

<form action="simpan.php" method="POST">
    <label>Produk</label><br>
    <select name="kode_sku" required>
        <?php while($p = mysqli_fetch_assoc($produk)): ?>
            <option value="<?= $p['Kode_SKU'] ?>">
                <?= $p['Nama_Produk'] ?> | Stok: <?= $p['Stok'] ?>
            </option>
        <?php endwhile; ?>
    </select><br><br>

    <label>Jumlah</label><br>
    <input type="number" name="jumlah" min="1" required><br><br>

    <button type="submit">Simpan Transaksi</button>
</form>

<?php include __DIR__ . "/../templates/footer.php"; ?>
