<?php
require_once __DIR__ . "/../middleware/admin_auth.php";
require_once __DIR__ . "/../../config/database.php";

if (isset($_POST['simpan'])) {
    $nama = $_POST['nama_produk'];
    $kategori = $_POST['kategori'];

    mysqli_query($conn, "INSERT INTO produk_induk 
        (nama_produk, kategori) 
        VALUES ('$nama', '$kategori')");

    header("Location: index.php");
}
include __DIR__ . "/../templates/header.php";
?>

<h1>Tambah Produk</h1>

<form method="post">
    <label>Nama Produk</label><br>
    <input type="text" name="nama_produk" required><br><br>

    <label>Kategori</label><br>
    <input type="text" name="kategori" required><br><br>

    <button name="simpan">Simpan</button>
</form>

<?php include __DIR__ . "/../templates/footer.php"; ?>
