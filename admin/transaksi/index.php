<?php
require_once __DIR__ . "/../middleware/admin_auth.php";
require_once __DIR__ . "/../../config/database.php";


$query = mysqli_query($conn, "
    SELECT * FROM transaksi
    ORDER BY Tanggal_Transaksi DESC
");
?>

<?php include __DIR__ . "/../templates/header.php"; ?>

<div class="main-content">
    <h2 class="page-title">Riwayat Transaksi</h2>

    <div class="table-container">
        <table>
            <thead>
                <tr>
                    <th>No</th>
                    <th>No Transaksi</th>
                    <th>ID Pelanggan</th>
                    <th>Tanggal</th>
                    <th>Status</th>
                    <th>Total Bayar</th>
                </tr>
            </thead>
            <tbody>

            <?php if (mysqli_num_rows($query) > 0): ?>
                <?php $no = 1; ?>
                <?php while ($row = mysqli_fetch_assoc($query)): ?>
                    <tr>
                        <td><?= $no++; ?></td>
                        <td><?= $row['No_Transaksi']; ?></td>
                        <td><?= $row['ID_Pelanggan']; ?></td>
                        <td><?= date('d-m-Y H:i', strtotime($row['Tanggal_Transaksi'])); ?></td>
                        <td>
                            <span class="badge <?= strtolower($row['Status_Transaksi']); ?>">
                                <?= $row['Status_Transaksi']; ?>
                            </span>
                        </td>
                        <td class="harga">
                            Rp <?= number_format($row['Total_Bayar'], 0, ',', '.'); ?>
                        </td>
                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr class="empty-row">
                    <td colspan="6">
                        <div class="empty-state">
                            📦 <br>
                            <strong>Belum ada transaksi</strong><br>
                            Data transaksi akan muncul setelah ada pembelian.
                        </div>
                    </td>
                </tr>
            <?php endif; ?>

            </tbody>
        </table>
    </div>
</div>

<?php include __DIR__ . "/../templates/footer.php"; ?>
