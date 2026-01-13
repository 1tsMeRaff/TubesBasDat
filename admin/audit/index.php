<?php
require_once __DIR__ . "/../middleware/admin_auth.php";
include __DIR__ . "/../templates/header.php";
?>

<h1>Audit Sistem</h1>

<div class="action-bar">
  <a href="stok.php" class="btn btn-warning">📦 Audit Stok</a>
  <a href="harga.php" class="btn btn-danger">💰 Audit Harga</a>
</div>


<?php include __DIR__ . "/../templates/footer.php"; ?>
