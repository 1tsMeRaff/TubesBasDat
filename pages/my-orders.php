<?php
/**
 * Order History Page
 * Sakinah Style - Customer Order History
 */

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../functions.php';

// Check if user is logged in
if (!isset($_SESSION['user'])) {
    header('Location: login.php?redirect=my-orders.php');
    exit;
}

$page_title = "Riwayat Pesanan - Sakinah Style";
$page_description = "Lihat riwayat pesanan Anda";

$user_id = $_SESSION['user']['id'];

// Get orders
$pdo = getDBConnection();
try {
    $sql = "SELECT * FROM Transaksi 
            WHERE ID_Pelanggan = :id 
            ORDER BY Tanggal_Transaksi DESC";
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute([':id' => $user_id]);
    $orders = $stmt->fetchAll();
} catch (PDOException $e) {
    error_log("Error fetching orders: " . $e->getMessage());
    $orders = [];
}

// Get order details for modal
$order_details = [];
if (isset($_GET['order_id'])) {
    $order_id = $_GET['order_id'];
    try {
        $sql = "SELECT dt.*, pv.Kode_SKU, pv.Foto_Produk, pi.Nama_Produk, mw.Nama_Warna
                FROM Detail_Transaksi dt
                INNER JOIN Produk_Varian pv ON dt.Kode_SKU = pv.Kode_SKU
                INNER JOIN Produk_Induk pi ON pv.ID_Induk = pi.ID_Induk
                INNER JOIN Master_Warna mw ON pv.ID_Warna = mw.ID_Warna
                WHERE dt.No_Transaksi = :order_id";
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute([':order_id' => $order_id]);
        $order_details = $stmt->fetchAll();
    } catch (PDOException $e) {
        error_log("Error fetching order details: " . $e->getMessage());
    }
}

/**
 * Get status badge HTML
 */
function getStatusBadge($status) {
    $badges = [
        'Pending' => '<span class="badge bg-warning text-dark">Pending</span>',
        'Paid' => '<span class="badge bg-info">Paid</span>',
        'Sent' => '<span class="badge bg-success">Sent</span>',
        'Cancelled' => '<span class="badge bg-danger">Cancelled</span>'
    ];
    
    return $badges[$status] ?? '<span class="badge bg-secondary">' . htmlspecialchars($status) . '</span>';
}

include __DIR__ . '/../includes/header.php';
?>

<div class="container my-5">
    <div class="row">
        <!-- Sidebar Menu -->
        <div class="col-lg-3 mb-4">
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="bi bi-person-circle"></i> Menu</h5>
                </div>
                <ul class="list-group list-group-flush">
                    <li class="list-group-item">
                        <a href="profile.php" class="text-decoration-none text-dark">
                            <i class="bi bi-person"></i> Profil Saya
                        </a>
                    </li>
                    <li class="list-group-item active">
                        <i class="bi bi-bag-check"></i> Riwayat Pesanan
                    </li>
                    <li class="list-group-item">
                        <a href="../cart.php" class="text-decoration-none text-dark">
                            <i class="bi bi-cart"></i> Keranjang
                        </a>
                    </li>
                    <li class="list-group-item">
                        <a href="logout.php" class="text-decoration-none text-danger">
                            <i class="bi bi-box-arrow-right"></i> Keluar
                        </a>
                    </li>
                </ul>
            </div>
        </div>
        
        <!-- Main Content -->
        <div class="col-lg-9">
            <h2 class="mb-4">Riwayat Pesanan</h2>
            
            <?php if (empty($orders)): ?>
                <div class="alert alert-info text-center py-5">
                    <i class="bi bi-inbox" style="font-size: 3rem;"></i>
                    <h4 class="mt-3">Belum Ada Pesanan</h4>
                    <p class="text-muted">Mulai berbelanja untuk melihat riwayat pesanan di sini.</p>
                    <a href="../shop.php" class="btn btn-primary mt-3">
                        <i class="bi bi-bag"></i> Mulai Belanja
                    </a>
                </div>
            <?php else: ?>
                <div class="card shadow-sm">
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>Tanggal</th>
                                        <th>No. Pesanan</th>
                                        <th>Total</th>
                                        <th>Status</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($orders as $order): ?>
                                        <tr>
                                            <td>
                                                <?php echo date('d/m/Y H:i', strtotime($order['Tanggal_Transaksi'])); ?>
                                            </td>
                                            <td>
                                                <strong><?php echo htmlspecialchars($order['No_Transaksi']); ?></strong>
                                            </td>
                                            <td>
                                                <strong><?php echo formatRupiah($order['Total_Bayar']); ?></strong>
                                            </td>
                                            <td>
                                                <?php echo getStatusBadge($order['Status_Transaksi']); ?>
                                            </td>
                                            <td>
                                                <button type="button" 
                                                        class="btn btn-sm btn-outline-primary" 
                                                        data-bs-toggle="modal" 
                                                        data-bs-target="#orderModal<?php echo str_replace('-', '', $order['No_Transaksi']); ?>">
                                                    <i class="bi bi-eye"></i> Lihat Detail
                                                </button>
                                            </td>
                                        </tr>
                                        
                                        <!-- Order Detail Modal -->
                                        <div class="modal fade" id="orderModal<?php echo str_replace('-', '', $order['No_Transaksi']); ?>" tabindex="-1">
                                            <div class="modal-dialog modal-lg">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title">
                                                            Detail Pesanan: <?php echo htmlspecialchars($order['No_Transaksi']); ?>
                                                        </h5>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                    </div>
                                                    <div class="modal-body">
                                                        <div class="row mb-3">
                                                            <div class="col-md-6">
                                                                <strong>Tanggal:</strong><br>
                                                                <?php echo date('d F Y H:i', strtotime($order['Tanggal_Transaksi'])); ?>
                                                            </div>
                                                            <div class="col-md-6">
                                                                <strong>Status:</strong><br>
                                                                <?php echo getStatusBadge($order['Status_Transaksi']); ?>
                                                            </div>
                                                        </div>
                                                        
                                                        <div class="mb-3">
                                                            <strong>Alamat Pengiriman:</strong><br>
                                                            <?php echo nl2br(htmlspecialchars($order['Alamat_Pengiriman'] ?? '-')); ?>
                                                        </div>
                                                        
                                                        <hr>
                                                        
                                                        <h6 class="mb-3">Item Pesanan:</h6>
                                                        <?php
                                                        // Get details for this order
                                                        try {
                                                            $detailSql = "SELECT dt.*, pv.Foto_Produk, pi.Nama_Produk, mw.Nama_Warna
                                                                         FROM Detail_Transaksi dt
                                                                         INNER JOIN Produk_Varian pv ON dt.Kode_SKU = pv.Kode_SKU
                                                                         INNER JOIN Produk_Induk pi ON pv.ID_Induk = pi.ID_Induk
                                                                         INNER JOIN Master_Warna mw ON pv.ID_Warna = mw.ID_Warna
                                                                         WHERE dt.No_Transaksi = :order_id";
                                                            
                                                            $detailStmt = $pdo->prepare($detailSql);
                                                            $detailStmt->execute([':order_id' => $order['No_Transaksi']]);
                                                            $details = $detailStmt->fetchAll();
                                                        } catch (PDOException $e) {
                                                            $details = [];
                                                        }
                                                        ?>
                                                        
                                                        <div class="table-responsive">
                                                            <table class="table table-sm">
                                                                <thead>
                                                                    <tr>
                                                                        <th>Produk</th>
                                                                        <th>Warna</th>
                                                                        <th class="text-center">Qty</th>
                                                                        <th class="text-end">Harga</th>
                                                                        <th class="text-end">Subtotal</th>
                                                                    </tr>
                                                                </thead>
                                                                <tbody>
                                                                    <?php foreach ($details as $detail): ?>
                                                                        <tr>
                                                                            <td>
                                                                                <div class="d-flex align-items-center">
                                                                                    <?php if ($detail['Foto_Produk']): ?>
                                                                                        <img src="<?php echo SITE_URL; ?>/assets/images/products/<?php echo htmlspecialchars($detail['Foto_Produk']); ?>" 
                                                                                             alt="<?php echo htmlspecialchars($detail['Nama_Produk']); ?>"
                                                                                             style="width: 50px; height: 50px; object-fit: cover; border-radius: 4px; margin-right: 10px;">
                                                                                    <?php endif; ?>
                                                                                    <div>
                                                                                        <strong><?php echo htmlspecialchars($detail['Nama_Produk']); ?></strong><br>
                                                                                        <small class="text-muted"><?php echo htmlspecialchars($detail['Kode_SKU']); ?></small>
                                                                                    </div>
                                                                                </div>
                                                                            </td>
                                                                            <td><?php echo htmlspecialchars($detail['Nama_Warna']); ?></td>
                                                                            <td class="text-center"><?php echo $detail['Jumlah_Beli']; ?></td>
                                                                            <td class="text-end"><?php echo formatRupiah($detail['Harga_Satuan_Snapshot']); ?></td>
                                                                            <td class="text-end"><strong><?php echo formatRupiah($detail['Subtotal']); ?></strong></td>
                                                                        </tr>
                                                                    <?php endforeach; ?>
                                                                </tbody>
                                                                <tfoot>
                                                                    <tr>
                                                                        <th colspan="4" class="text-end">Total:</th>
                                                                        <th class="text-end"><?php echo formatRupiah($order['Total_Bayar']); ?></th>
                                                                    </tr>
                                                                </tfoot>
                                                            </table>
                                                        </div>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>

