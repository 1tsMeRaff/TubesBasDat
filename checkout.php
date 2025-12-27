<?php
/**
 * Checkout Page
 * Sakinah Style - Transaction Processing with Two-Column Layout
 */

require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/functions.php';

$page_title = "Checkout - Sakinah Style";

// Check if cart is empty
if (empty($_SESSION['cart'])) {
    header('Location: cart.php');
    exit;
}

// Get cart items with product details
$cart_items = [];
$subtotal = 0;

foreach ($_SESSION['cart'] as $item) {
    $variant = getVariantBySKU($item['kode_sku']);
    if ($variant) {
        // Check stock availability
        if ($variant['Stok'] < $item['qty']) {
            $_SESSION['error'] = "Stok tidak mencukupi untuk produk: " . $variant['Nama_Produk'];
            header('Location: cart.php');
            exit;
        }
        
        $item_subtotal = $variant['Harga_Jual'] * $item['qty'];
        $cart_items[] = [
            'variant' => $variant,
            'qty' => $item['qty'],
            'subtotal' => $item_subtotal
        ];
        $subtotal += $item_subtotal;
    }
}

// Shipping cost (fixed for now)
$shipping_cost = 10000;
$grand_total = $subtotal + $shipping_cost;

// Handle form submission
$success = false;
$error = null;
$no_transaksi = null;
$order_nama = null;

// Pre-fill form if user is logged in
$is_logged_in = isset($_SESSION['user']);
$prefill_data = [
    'nama' => $is_logged_in ? ($_SESSION['user']['name'] ?? '') : '',
    'alamat' => $is_logged_in ? ($_SESSION['user']['address'] ?? '') : '',
    'whatsapp' => $is_logged_in ? ($_SESSION['user']['phone'] ?? '') : '',
    'email' => $is_logged_in ? ($_SESSION['user']['email'] ?? '') : ''
];

// Get user data from database if logged in
if ($is_logged_in) {
    $pdo = getDBConnection();
    try {
        $userSql = "SELECT * FROM Pelanggan WHERE ID_Pelanggan = :id";
        $userStmt = $pdo->prepare($userSql);
        $userStmt->execute([':id' => $_SESSION['user']['id']]);
        $user_data = $userStmt->fetch();
        
        if ($user_data) {
            $prefill_data['nama'] = $user_data['Nama_Pelanggan'] ?? $prefill_data['nama'];
            $prefill_data['alamat'] = $user_data['Alamat_Utama'] ?? $prefill_data['alamat'];
            $prefill_data['whatsapp'] = $user_data['No_HP'] ?? $prefill_data['whatsapp'];
            $prefill_data['email'] = $user_data['Email'] ?? $prefill_data['email'];
        }
    } catch (PDOException $e) {
        error_log("Error fetching user data: " . $e->getMessage());
    }
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nama = trim($_POST['nama'] ?? '');
    $alamat = trim($_POST['alamat'] ?? '');
    $whatsapp = trim($_POST['whatsapp'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $user_id = $is_logged_in ? $_SESSION['user']['id'] : null;
    
    // Validation
    if (empty($nama) || empty($alamat)) {
        $error = "Nama dan alamat wajib diisi!";
    } else {
        // Prepare cart items for checkout
        $checkout_items = [];
        foreach ($_SESSION['cart'] as $item) {
            $checkout_items[] = [
                'kode_sku' => $item['kode_sku'],
                'qty' => $item['qty']
            ];
        }
        
        // Process checkout
        $result = processCheckout([
            'nama' => $nama,
            'alamat' => $alamat,
            'whatsapp' => $whatsapp,
            'email' => $email,
            'user_id' => $user_id,
            'cart_items' => $checkout_items,
            'shipping_cost' => $shipping_cost
        ]);
        
        if ($result['success']) {
            // Redirect to success page
            header('Location: success.php?order=' . urlencode($result['no_transaksi']) . '&nama=' . urlencode($result['nama']) . '&whatsapp=' . urlencode($result['whatsapp'] ?? ''));
            exit;
        } else {
            $error = $result['message'];
        }
    }
}

include __DIR__ . '/includes/header.php';
?>

<div class="container my-5">
    <h2 class="mb-4">
        <i class="bi bi-credit-card"></i> Checkout
    </h2>
    
    <?php if ($error): ?>
        <div class="alert alert-danger">
            <i class="bi bi-exclamation-triangle"></i> <?php echo htmlspecialchars($error); ?>
        </div>
    <?php endif; ?>
    
    <div class="row">
        <!-- Left Column: Shipping Form -->
        <div class="col-lg-8">
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="bi bi-truck"></i> Informasi Pengiriman</h5>
                </div>
                <div class="card-body">
                    <?php if ($is_logged_in): ?>
                        <div class="alert alert-info mb-3">
                            <i class="bi bi-info-circle"></i> 
                            Anda masuk sebagai <strong><?php echo htmlspecialchars($_SESSION['user']['name']); ?></strong>. 
                            Data akan diisi otomatis dari profil Anda.
                        </div>
                    <?php else: ?>
                        <div class="alert alert-warning mb-3">
                            <i class="bi bi-exclamation-triangle"></i> 
                            <a href="<?php echo SITE_URL; ?>/pages/login.php?redirect=<?php echo urlencode('checkout.php'); ?>" class="alert-link">
                                Masuk ke akun
                            </a> untuk pengalaman checkout yang lebih cepat.
                        </div>
                    <?php endif; ?>
                    
                    <form method="POST" action="checkout.php" id="checkoutForm">
                        <div class="mb-3">
                            <label for="nama" class="form-label">Nama Penerima <span class="text-danger">*</span></label>
                            <input type="text" 
                                   class="form-control" 
                                   id="nama" 
                                   name="nama" 
                                   value="<?php echo isset($_POST['nama']) ? htmlspecialchars($_POST['nama']) : $prefill_data['nama']; ?>"
                                   required>
                        </div>
                        
                        <?php if (!$is_logged_in): ?>
                            <div class="mb-3">
                                <label for="email" class="form-label">Email</label>
                                <input type="email" 
                                       class="form-control" 
                                       id="email" 
                                       name="email" 
                                       value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : $prefill_data['email']; ?>"
                                       placeholder="email@example.com">
                                <small class="text-muted">Untuk konfirmasi pesanan</small>
                            </div>
                        <?php endif; ?>
                        
                        <div class="mb-3">
                            <label for="alamat" class="form-label">Alamat Pengiriman <span class="text-danger">*</span></label>
                            <textarea class="form-control" 
                                      id="alamat" 
                                      name="alamat" 
                                      rows="4" 
                                      required><?php echo isset($_POST['alamat']) ? htmlspecialchars($_POST['alamat']) : $prefill_data['alamat']; ?></textarea>
                        </div>
                        
                        <div class="mb-3">
                            <label for="whatsapp" class="form-label">Nomor WhatsApp <span class="text-danger">*</span></label>
                            <input type="text" 
                                   class="form-control" 
                                   id="whatsapp" 
                                   name="whatsapp" 
                                   value="<?php echo isset($_POST['whatsapp']) ? htmlspecialchars($_POST['whatsapp']) : $prefill_data['whatsapp']; ?>"
                                   placeholder="08xxxxxxxxxx"
                                   required>
                            <small class="text-muted">Untuk konfirmasi pesanan dan tracking</small>
                        </div>
                        
                        <div class="alert alert-info">
                            <i class="bi bi-info-circle"></i> 
                            <strong>Catatan:</strong> Setelah checkout, Anda akan menerima nomor transaksi. 
                            Silakan lakukan pembayaran sesuai instruksi yang akan dikirimkan via WhatsApp.
                        </div>
                        
                        <button type="submit" class="btn btn-primary btn-lg w-100">
                            <i class="bi bi-check-circle"></i> Proses Pesanan
                        </button>
                    </form>
                </div>
            </div>
        </div>
        
        <!-- Right Column: Order Summary -->
        <div class="col-lg-4">
            <div class="card shadow-sm sticky-top" style="top: 100px;">
                <div class="card-header bg-secondary text-white">
                    <h5 class="mb-0"><i class="bi bi-receipt"></i> Ringkasan Pesanan</h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <?php foreach ($cart_items as $item): ?>
                            <div class="d-flex justify-content-between mb-2 pb-2 border-bottom">
                                <div style="flex: 1;">
                                    <small class="fw-bold"><?php echo htmlspecialchars($item['variant']['Nama_Produk']); ?></small><br>
                                    <small class="text-muted">
                                        <?php echo htmlspecialchars($item['variant']['Nama_Warna']); ?> x <?php echo $item['qty']; ?>
                                    </small>
                                </div>
                                <small class="fw-bold ms-2"><?php echo formatRupiah($item['subtotal']); ?></small>
                            </div>
                        <?php endforeach; ?>
                    </div>
                    
                    <div class="summary-row">
                        <span>Subtotal</span>
                        <span><?php echo formatRupiah($subtotal); ?></span>
                    </div>
                    <div class="summary-row">
                        <span>Ongkir</span>
                        <span><?php echo formatRupiah($shipping_cost); ?></span>
                    </div>
                    <div class="summary-row total">
                        <span>Grand Total</span>
                        <span><?php echo formatRupiah($grand_total); ?></span>
                    </div>
                    
                    <a href="cart.php" class="btn btn-outline-primary w-100 mt-3">
                        <i class="bi bi-arrow-left"></i> Kembali ke Keranjang
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include __DIR__ . '/includes/footer.php'; ?>
