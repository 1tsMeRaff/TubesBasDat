<?php
/**
 * Order Success Page
 * Sakinah Style - Order Confirmation
 */

require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/functions.php';

$page_title = "Pesanan Berhasil - Sakinah Style";

// Get order details from URL
$no_transaksi = isset($_GET['order']) ? trim($_GET['order']) : null;
$nama = isset($_GET['nama']) ? trim($_GET['nama']) : '';
$whatsapp = isset($_GET['whatsapp']) ? trim($_GET['whatsapp']) : '';

// Clear cart session
$_SESSION['cart'] = [];

// If no order ID, redirect to shop
if (!$no_transaksi) {
    header('Location: shop.php');
    exit;
}

// WhatsApp admin number (update this with your actual number)
$admin_whatsapp = '6281234567890'; // Format: 62 + country code + number without 0

// Generate WhatsApp message
$whatsapp_message = urlencode("Halo admin, saya sudah order dengan nomor transaksi: " . $no_transaksi);
$whatsapp_url = "https://wa.me/{$admin_whatsapp}?text={$whatsapp_message}";

include __DIR__ . '/includes/header.php';
?>

<div class="container my-5">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card shadow-lg border-0">
                <div class="card-body text-center py-5">
                    <!-- Success Icon -->
                    <div class="mb-4">
                        <div class="success-icon mx-auto mb-3">
                            <i class="bi bi-check-circle-fill" style="font-size: 5rem; color: #27AE60;"></i>
                        </div>
                    </div>
                    
                    <!-- Success Message -->
                    <h2 class="mb-3">
                        Terima Kasih <?php echo htmlspecialchars($nama); ?>!
                    </h2>
                    <p class="lead text-muted mb-4">
                        Pesanan Anda berhasil dibuat.
                    </p>
                    
                    <!-- Order ID Card -->
                    <div class="bg-light p-4 rounded mb-4">
                        <p class="text-muted mb-2">Nomor Transaksi</p>
                        <h3 class="text-primary fw-bold mb-0"><?php echo htmlspecialchars($no_transaksi); ?></h3>
                        <small class="text-muted">
                            <i class="bi bi-info-circle"></i> Simpan nomor transaksi ini untuk tracking pesanan Anda
                        </small>
                    </div>
                    
                    <!-- Next Steps -->
                    <div class="alert alert-info text-start mb-4">
                        <h6 class="alert-heading"><i class="bi bi-list-check"></i> Langkah Selanjutnya:</h6>
                        <ol class="mb-0">
                            <li>Lakukan pembayaran sesuai total yang tertera</li>
                            <li>Konfirmasi pembayaran via WhatsApp dengan mengklik tombol di bawah</li>
                            <li>Tunggu konfirmasi dari admin</li>
                            <li>Pesanan akan dikirim setelah pembayaran dikonfirmasi</li>
                        </ol>
                    </div>
                    
                    <!-- Action Buttons -->
                    <div class="d-grid gap-2 d-md-flex justify-content-md-center">
                        <a href="<?php echo $whatsapp_url; ?>" 
                           target="_blank" 
                           class="btn btn-success btn-lg">
                            <i class="bi bi-whatsapp"></i> Konfirmasi via WhatsApp
                        </a>
                        <a href="shop.php" class="btn btn-outline-primary btn-lg">
                            <i class="bi bi-bag"></i> Lanjutkan Belanja
                        </a>
                    </div>
                    
                    <?php if (isset($_SESSION['user'])): ?>
                        <div class="mt-4">
                            <a href="<?php echo SITE_URL; ?>/pages/my-orders.php" class="text-decoration-none">
                                <i class="bi bi-bag-check"></i> Lihat Riwayat Pesanan
                            </a>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.success-icon {
    animation: scaleIn 0.5s ease;
}

@keyframes scaleIn {
    from {
        transform: scale(0);
        opacity: 0;
    }
    to {
        transform: scale(1);
        opacity: 1;
    }
}

.card {
    animation: fadeInUp 0.6s ease;
}

@keyframes fadeInUp {
    from {
        opacity: 0;
        transform: translateY(30px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}
</style>

<?php include __DIR__ . '/includes/footer.php'; ?>

