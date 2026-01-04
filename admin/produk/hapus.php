<?php
require_once __DIR__ . "/../middleware/admin_auth.php";
require_once __DIR__ . "/../../config/database.php";

$id = $_GET['id'];

mysqli_query($conn, "DELETE FROM produk_induk WHERE id_produk='$id'");

header("Location: index.php");
