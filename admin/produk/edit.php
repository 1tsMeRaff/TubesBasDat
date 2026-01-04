<?php
require_once __DIR__ . "/../middleware/admin_auth.php";
require_once __DIR__ . "/../../config/database.php";

$id = $_GET['id'];
$data = mysqli_fetch_assoc(
    mysqli_query($conn, "SELECT * FROM produk_induk WHERE id_produk='$id'")
);

if (isset($_POST['update'])) {
    $nama = $_POST['nama_produk'];
    $kategori = $_POST['kategori'];

    mysqli_query($conn, "UPDATE produk_induk SET 
        nama_produk='$nama',
        kategori='$kategori'
        WHERE id_produk='$id'");

    header("Location: index.php");
}

include __DIR__ . "/../templates/header.php";
?>

<h1>Edit Produk</h1>

<form method="post">
    <label>Nama Produk</label><br>
    <input type="text" name="nama_produk" value="<?= $data['nama_produk'] ?>" required><br><br>

    <label>Kategori</label><br>
    <input type="text" name="kategori" value="<?= $data['kategori'] ?>" required><br><br>

    <button name="update">Update</button>
</form>

<?php include __DIR__ . "/../templates/footer.php"; ?>
