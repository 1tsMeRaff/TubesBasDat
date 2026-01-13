<?php
require_once __DIR__ . "/../middleware/admin_auth.php";
require_once __DIR__ . "/../../config/database.php";

$id = $_GET['id'];
$q = mysqli_query($conn, "
    SELECT * FROM produk_varian WHERE Kode_SKU='$id'
");
$data = mysqli_fetch_assoc($q);

include __DIR__ . "/../templates/header.php";
?>

<h2>Edit Harga Produk</h2>

<form method="POST" action="simpan_edit.php">
    <input type="hidden" name="kode_sku" value="<?= $data['Kode_SKU'] ?>">

    <label>Harga Jual</label><br>
    <input type="number" name="harga_jual" value="<?= $data['Harga_Jual'] ?>" required>
    <br><br>

    <button type="submit">Simpan</button>
</form>

<?php include __DIR__ . "/../templates/footer.php"; ?>
