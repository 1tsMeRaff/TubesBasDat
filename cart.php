<?php
/**
 * Shopping Cart Page
 * Sakinah Style - Session-based Cart Management with Ajax Updates
 */

require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/functions.php';

$page_title = "Keranjang Belanja - Sakinah Style";

// Handle Ajax requests
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
    header('Content-Type: application/json');
    
    $action = $_POST['action'] ?? '';
    $response = ['success' => false, 'message' => ''];
    
    if ($action == 'update') {
        $index = (int)($_POST['index'] ?? -1);
        $qty = (int)($_POST['qty'] ?? 1);
        
        if ($index >= 0 && $index < count($_SESSION['cart'])) {
            // Get variant to check stock
            $kode_sku = $_SESSION['cart'][$index]['kode_sku'];
            $variant = getVariantBySKU($kode_sku);
            
            if (!$variant) {
                $response['message'] = 'Produk tidak ditemukan';
                echo json_encode($response);
                exit;
            }
            
            if ($qty > $variant['Stok']) {
                $response['message'] = 'Stok tersedia hanya ' . $variant['Stok'] . ' pcs';
                echo json_encode($response);
                exit;
            }
            
            if ($qty > 0) {
                $_SESSION['cart'][$index]['qty'] = $qty;
            } else {
                unset($_SESSION['cart'][$index]);
                $_SESSION['cart'] = array_values($_SESSION['cart']);
            }
            
            // Recalculate totals
            $subtotal = $variant['Harga_Jual'] * $qty;
            $total = 0;
            foreach ($_SESSION['cart'] as $item) {
                $v = getVariantBySKU($item['kode_sku']);
                if ($v) {
                    $total += $v['Harga_Jual'] * $item['qty'];
                }
            }
            
            $response['success'] = true;
            $response['subtotal'] = formatRupiah($subtotal);
            $response['total'] = formatRupiah($total);
            $response['cart_count'] = count($_SESSION['cart']);
        } else {
            $response['message'] = 'Item tidak ditemukan';
        }
        
        echo json_encode($response);
        exit;
    }
    
    if ($action == 'remove') {
        $index = (int)($_POST['index'] ?? -1);
        
        if ($index >= 0 && $index < count($_SESSION['cart'])) {
            unset($_SESSION['cart'][$index]);
            $_SESSION['cart'] = array_values($_SESSION['cart']);
            
            // Recalculate total
            $total = 0;
            foreach ($_SESSION['cart'] as $item) {
                $variant = getVariantBySKU($item['kode_sku']);
                if ($variant) {
                    $total += $variant['Harga_Jual'] * $item['qty'];
                }
            }
            
            $response['success'] = true;
            $response['total'] = formatRupiah($total);
            $response['cart_count'] = count($_SESSION['cart']);
        }
        
        echo json_encode($response);
        exit;
    }
}

// Handle regular POST requests (non-Ajax)
if ($_SERVER['REQUEST_METHOD'] == 'POST' && !isset($_SERVER['HTTP_X_REQUESTED_WITH'])) {
    $action = $_POST['action'] ?? '';
    
    if ($action == 'add') {
        $kode_sku = $_POST['kode_sku'] ?? '';
        $qty = (int)($_POST['qty'] ?? 1);
        
        if ($kode_sku) {
            $found = false;
            foreach ($_SESSION['cart'] as &$item) {
                if ($item['kode_sku'] == $kode_sku) {
                    $item['qty'] += $qty;
                    $found = true;
                    break;
                }
            }
            
            if (!$found) {
                $_SESSION['cart'][] = [
                    'kode_sku' => $kode_sku,
                    'qty' => $qty
                ];
            }
            
            header('Location: cart.php?added=1');
            exit;
        }
    }
}

// Get cart items with product details
$cart_items = [];
$total = 0;

foreach ($_SESSION['cart'] as $item) {
    $variant = getVariantBySKU($item['kode_sku']);
    if ($variant) {
        $subtotal = $variant['Harga_Jual'] * $item['qty'];
        $cart_items[] = [
            'variant' => $variant,
            'qty' => $item['qty'],
            'subtotal' => $subtotal,
            'index' => count($cart_items)
        ];
        $total += $subtotal;
    }
}

include __DIR__ . '/includes/header.php';
?>

<div class="container my-5">
    <h2 class="mb-4">
        <i class="bi bi-cart3"></i> Keranjang Belanja
    </h2>
    
    <?php if (isset($_GET['added'])): ?>
        <div class="alert alert-success alert-dismissible fade show">
            <i class="bi bi-check-circle"></i> Produk berhasil ditambahkan ke keranjang!
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>
    
    <?php if (empty($cart_items)): ?>
        <!-- Empty Cart State -->
        <div class="text-center py-5">
            <div class="empty-cart-illustration mb-4">
                <svg width="200" height="200" viewBox="0 0 200 200" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <circle cx="100" cy="100" r="80" fill="#F5E6E0" opacity="0.5"/>
                    <path d="M70 80 L130 80 L125 140 L75 140 Z" fill="#DCAE96" opacity="0.3"/>
                    <circle cx="85" cy="90" r="8" fill="#DCAE96"/>
                    <circle cx="115" cy="90" r="8" fill="#DCAE96"/>
                    <path d="M70 80 Q100 60 130 80" stroke="#DCAE96" stroke-width="3" fill="none"/>
                </svg>
            </div>
            <h3 class="mb-3">Keranjang Anda Kosong</h3>
            <p class="text-muted mb-4">Mulai berbelanja untuk menambahkan produk ke keranjang.</p>
            <a href="shop.php" class="btn btn-primary btn-lg">
                <i class="bi bi-bag"></i> Mulai Belanja
            </a>
        </div>
    <?php else: ?>
        <div class="row">
            <!-- Cart Items -->
            <div class="col-lg-8">
                <div class="card shadow-sm">
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>Produk</th>
                                        <th>Harga</th>
                                        <th>Jumlah</th>
                                        <th>Subtotal</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody id="cartTableBody">
                                    <?php foreach ($cart_items as $item): 
                                        $variant = $item['variant'];
                                        $product = getProductDetail($variant['ID_Induk']);
                                    ?>
                                        <tr data-index="<?php echo $item['index']; ?>" data-sku="<?php echo htmlspecialchars($variant['Kode_SKU']); ?>">
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <?php if ($variant['Foto_Produk']): ?>
                                                        <img src="<?php echo SITE_URL; ?>/assets/images/products/<?php echo htmlspecialchars($variant['Foto_Produk']); ?>" 
                                                             alt="<?php echo htmlspecialchars($variant['Nama_Produk']); ?>"
                                                             style="width: 80px; height: 80px; object-fit: cover; border-radius: 8px; margin-right: 1rem;">
                                                    <?php else: ?>
                                                        <div style="width: 80px; height: 80px; background: #f0f0f0; border-radius: 8px; margin-right: 1rem; display: flex; align-items: center; justify-content: center;">
                                                            <i class="bi bi-image"></i>
                                                        </div>
                                                    <?php endif; ?>
                                                    <div>
                                                        <h6 class="mb-1"><?php echo htmlspecialchars($variant['Nama_Produk']); ?></h6>
                                                        <small class="text-muted">Warna: <?php echo htmlspecialchars($variant['Nama_Warna']); ?></small><br>
                                                        <small class="text-muted">SKU: <?php echo htmlspecialchars($variant['Kode_SKU']); ?></small>
                                                    </div>
                                                </div>
                                            </td>
                                            <td><?php echo formatRupiah($variant['Harga_Jual']); ?></td>
                                            <td>
                                                <div class="input-group" style="width: 120px;">
                                                    <button class="btn btn-outline-secondary btn-sm qty-decrease" type="button">-</button>
                                                    <input type="number" 
                                                           class="form-control form-control-sm text-center qty-input" 
                                                           value="<?php echo $item['qty']; ?>" 
                                                           min="1" 
                                                           max="<?php echo $variant['Stok']; ?>"
                                                           data-index="<?php echo $item['index']; ?>"
                                                           data-price="<?php echo $variant['Harga_Jual']; ?>">
                                                    <button class="btn btn-outline-secondary btn-sm qty-increase" type="button">+</button>
                                                </div>
                                                <?php if ($item['qty'] > $variant['Stok']): ?>
                                                    <small class="text-danger d-block mt-1">Stok tersedia: <?php echo $variant['Stok']; ?></small>
                                                <?php endif; ?>
                                            </td>
                                            <td class="fw-bold subtotal-cell" data-price="<?php echo $variant['Harga_Jual']; ?>">
                                                <?php echo formatRupiah($item['subtotal']); ?>
                                            </td>
                                            <td>
                                                <button type="button" 
                                                        class="btn btn-sm btn-outline-danger remove-item" 
                                                        data-index="<?php echo $item['index']; ?>">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                
                <div class="mt-3">
                    <a href="shop.php" class="btn btn-outline-primary">
                        <i class="bi bi-arrow-left"></i> Lanjutkan Belanja
                    </a>
                </div>
            </div>
            
            <!-- Cart Summary -->
            <div class="col-lg-4">
                <div class="card shadow-sm sticky-top" style="top: 100px;">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0"><i class="bi bi-receipt"></i> Ringkasan Belanja</h5>
                    </div>
                    <div class="card-body">
                        <div class="summary-row">
                            <span>Subtotal</span>
                            <span id="cartSubtotal"><?php echo formatRupiah($total); ?></span>
                        </div>
                        <div class="summary-row">
                            <span>Ongkir</span>
                            <span class="text-muted">Dihitung saat checkout</span>
                        </div>
                        <div class="summary-row total">
                            <span>Total</span>
                            <span id="cartTotal"><?php echo formatRupiah($total); ?></span>
                        </div>
                        
                        <a href="checkout.php" class="btn btn-primary w-100 mt-4">
                            <i class="bi bi-credit-card"></i> Checkout
                        </a>
                        
                        <p class="text-muted small text-center mt-3 mb-0">
                            <i class="bi bi-shield-check"></i> Pembayaran aman dan terpercaya
                        </p>
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?>
</div>

<!-- Toast Notification -->
<div class="toast-container position-fixed top-0 end-0 p-3" style="z-index: 9999;">
    <div id="cartToast" class="toast" role="alert" aria-live="assertive" aria-atomic="true">
        <div class="toast-header">
            <i class="bi bi-info-circle text-primary me-2"></i>
            <strong class="me-auto">Info</strong>
            <button type="button" class="btn-close" data-bs-dismiss="toast"></button>
        </div>
        <div class="toast-body" id="toastMessage"></div>
    </div>
</div>

<script>
// Ajax cart update functionality
document.addEventListener('DOMContentLoaded', function() {
    // Quantity input change
    document.querySelectorAll('.qty-input').forEach(input => {
        input.addEventListener('change', function() {
            updateCartItem(this.dataset.index, parseInt(this.value));
        });
    });
    
    // Decrease button
    document.querySelectorAll('.qty-decrease').forEach(btn => {
        btn.addEventListener('click', function() {
            const input = this.nextElementSibling;
            const currentQty = parseInt(input.value);
            if (currentQty > 1) {
                input.value = currentQty - 1;
                updateCartItem(input.dataset.index, currentQty - 1);
            }
        });
    });
    
    // Increase button
    document.querySelectorAll('.qty-increase').forEach(btn => {
        btn.addEventListener('click', function() {
            const input = this.previousElementSibling;
            const currentQty = parseInt(input.value);
            const maxQty = parseInt(input.max);
            if (currentQty < maxQty) {
                input.value = currentQty + 1;
                updateCartItem(input.dataset.index, currentQty + 1);
            } else {
                showToast('Stok tersedia hanya ' + maxQty + ' pcs');
            }
        });
    });
    
    // Remove button
    document.querySelectorAll('.remove-item').forEach(btn => {
        btn.addEventListener('click', function() {
            if (confirm('Hapus produk ini dari keranjang?')) {
                removeCartItem(this.dataset.index);
            }
        });
    });
});

function updateCartItem(index, qty) {
    const formData = new FormData();
    formData.append('action', 'update');
    formData.append('index', index);
    formData.append('qty', qty);
    
    fetch('cart.php', {
        method: 'POST',
        headers: {
            'X-Requested-With': 'XMLHttpRequest'
        },
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Update subtotal in table
            const row = document.querySelector(`tr[data-index="${index}"]`);
            const subtotalCell = row.querySelector('.subtotal-cell');
            const price = parseFloat(subtotalCell.dataset.price);
            subtotalCell.textContent = formatRupiah(price * qty);
            
            // Update totals
            document.getElementById('cartSubtotal').textContent = data.total;
            document.getElementById('cartTotal').textContent = data.total;
            
            // Update cart count in header if exists
            const cartBadge = document.querySelector('.badge.rounded-pill.bg-danger');
            if (cartBadge) {
                cartBadge.textContent = data.cart_count;
            }
        } else {
            showToast(data.message || 'Terjadi kesalahan');
            // Reload to sync
            location.reload();
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showToast('Terjadi kesalahan saat memperbarui keranjang');
    });
}

function removeCartItem(index) {
    const formData = new FormData();
    formData.append('action', 'remove');
    formData.append('index', index);
    
    fetch('cart.php', {
        method: 'POST',
        headers: {
            'X-Requested-With': 'XMLHttpRequest'
        },
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Remove row
            const row = document.querySelector(`tr[data-index="${index}"]`);
            row.style.transition = 'opacity 0.3s';
            row.style.opacity = '0';
            setTimeout(() => {
                row.remove();
                
                // Update totals
                document.getElementById('cartSubtotal').textContent = data.total;
                document.getElementById('cartTotal').textContent = data.total;
                
                // Update cart count
                const cartBadge = document.querySelector('.badge.rounded-pill.bg-danger');
                if (cartBadge) {
                    cartBadge.textContent = data.cart_count;
                }
                
                // Reload if cart is empty
                if (data.cart_count == 0) {
                    location.reload();
                }
            }, 300);
        } else {
            showToast(data.message || 'Terjadi kesalahan');
            location.reload();
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showToast('Terjadi kesalahan saat menghapus item');
    });
}

function formatRupiah(angka) {
    return 'Rp ' + angka.toString().replace(/\B(?=(\d{3})+(?!\d))/g, '.');
}

function showToast(message) {
    const toastEl = document.getElementById('cartToast');
    const toastMessage = document.getElementById('toastMessage');
    toastMessage.textContent = message;
    
    const toast = new bootstrap.Toast(toastEl);
    toast.show();
}
</script>

<?php include __DIR__ . '/includes/footer.php'; ?>
