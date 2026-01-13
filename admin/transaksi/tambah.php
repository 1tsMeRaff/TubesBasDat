<?php
require_once __DIR__ . "/../middleware/admin_auth.php";
require_once __DIR__ . "/../../config/database.php";
include __DIR__ . "/../templates/header.php";

$produk = mysqli_query($conn,
    "SELECT Kode_SKU, Harga_Jual FROM produk_varian WHERE Is_Active=1"
);
?>

<h2>Transaksi Baru</h2>

<form method="POST" action="simpan.php">
    <label>Produk</label><br>
    <select name="kode_sku">
        <?php while($p = mysqli_fetch_assoc($produk)): ?>
            <option value="<?= $p['Kode_SKU'] ?>">
                <?= $p['Kode_SKU'] ?> - Rp<?= number_format($p['Harga_Jual']) ?>
            </option>
        <?php endwhile; ?>
    </select><br><br>

    <label>Qty</label><br>
    <input type="number" name="qty" min="1" required><br><br>

    <button type="submit">Simpan Transaksi</button>
</form>

<?php include __DIR__ . "/../templates/footer.php"; ?>
