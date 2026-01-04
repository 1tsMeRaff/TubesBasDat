<?php
require_once __DIR__ . "/../middleware/admin_auth.php";

// BASE URL admin
$base_admin = "/TubesBasDat/admin";
?>

<!DOCTYPE html>
<html>
<head>
    <title>Admin Panel - Sakinah Style</title>
    <link rel="stylesheet" href="<?= $base_admin ?>/assets/css/admin.css">
</head>
<body>

<div class="admin-wrapper">
    <aside class="sidebar">
        <h2>Sakinah Style</h2>
        <ul>
            <li><a href="<?= $base_admin ?>/">Dashboard</a></li>
            <li><a href="<?= $base_admin ?>/produk/">Manajemen Produk</a></li>
            <li><a href="<?= $base_admin ?>/stok/">Stok</a></li>
            <li><a href="<?= $base_admin ?>/transaksi/">Transaksi</a></li>
            <li><a href="<?= $base_admin ?>/audit/">Audit</a></li>
            <li><a href="<?= $base_admin ?>/logout.php">Logout</a></li>
        </ul>
    </aside>

    <main class="content">
