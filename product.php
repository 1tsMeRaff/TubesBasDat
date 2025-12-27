<?php
/**
 * Product Detail Page
 * Sakinah Style - Enhanced with Dynamic Variant Selection
 */

require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/functions.php';

$id_induk = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if (!$id_induk) {
    header('Location: shop.php');
    exit;
}

$product = getProductDetail($id_induk);

if (!$product) {
    header('Location: shop.php');
    exit;
}

$page_title = htmlspecialchars($product['Nama_Produk']) . " - Sakinah Style";
$page_description = htmlspecialchars($product['Deskripsi_Lengkap']);

// Get related products
$related_products = getRelatedProducts($id_induk, 4);

// Prepare variants data for JavaScript
$variants_data = [];
foreach ($product['variants'] as $variant) {
    $variants_data[$variant['Kode_SKU']] = [
        'price' => (int)$variant['Harga_Jual'],
        'stock' => (int)$variant['Stok'],
        'image' => $variant['Foto_Produk'] ? SITE_URL . '/assets/images/products/' . $variant['Foto_Produk'] : '',
        'color_name' => $variant['Nama_Warna'],
        'color_hex' => $variant['Kode_Hex'] ?? ''
    ];
}

// Get default variant (first available or first in list)
$default_variant = null;
foreach ($product['variants'] as $variant) {
    if ($variant['Stok'] > 0) {
        $default_variant = $variant;
        break;
    }
}
if (!$default_variant && !empty($product['variants'])) {
    $default_variant = $product['variants'][0];
}

include __DIR__ . '/includes/header.php';
?>

<div class="container my-5">
    <div class="row">
        <!-- Product Images (Left Column) -->
        <div class="col-lg-6 mb-4">
            <!-- Main Image -->
            <div class="product-main-image mb-3">
                <img id="mainProductImage" 
                     src="<?php echo $default_variant && $default_variant['Foto_Produk'] ? SITE_URL . '/assets/images/products/' . htmlspecialchars($default_variant['Foto_Produk']) : ''; ?>" 
                     alt="<?php echo htmlspecialchars($product['Nama_Produk']); ?>"
                     class="product-detail-img img-fluid"
                     style="width: 100%; height: 500px; object-fit: cover; border-radius: 12px;">
                <?php if (!$default_variant || !$default_variant['Foto_Produk']): ?>
                    <div class="product-detail-img bg-secondary d-flex align-items-center justify-content-center text-white" style="height: 500px;">
                        <i class="bi bi-image" style="font-size: 5rem;"></i>
                    </div>
                <?php endif; ?>
            </div>
            
            <!-- Thumbnail Carousel -->
            <?php if (count($product['variants']) > 1): ?>
                <div class="product-thumbnails">
                    <div class="d-flex gap-2 overflow-auto pb-2" style="max-width: 100%;">
                        <?php foreach ($product['variants'] as $index => $variant): ?>
                            <?php if ($variant['Foto_Produk']): ?>
                                <img src="<?php echo SITE_URL; ?>/assets/images/products/<?php echo htmlspecialchars($variant['Foto_Produk']); ?>" 
                                     alt="<?php echo htmlspecialchars($variant['Nama_Warna']); ?>"
                                     class="thumbnail-img <?php echo $index === 0 ? 'active' : ''; ?>"
                                     data-sku="<?php echo htmlspecialchars($variant['Kode_SKU']); ?>"
                                     style="width: 80px; height: 80px; object-fit: cover; border-radius: 8px; cursor: pointer; border: 2px solid transparent; transition: all 0.3s;">
                            <?php endif; ?>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endif; ?>
        </div>
        
        <!-- Product Info (Right Column) -->
        <div class="col-lg-6">
            <h1 class="mb-3"><?php echo htmlspecialchars($product['Nama_Produk']); ?></h1>
            
            <div class="mb-3">
                <span class="badge bg-secondary"><?php echo htmlspecialchars($product['Nama_Kategori']); ?></span>
                <span class="badge bg-secondary"><?php echo htmlspecialchars($product['Nama_Bahan']); ?></span>
            </div>
            
            <!-- Price Display (Dynamic) -->
            <div class="mb-4">
                <h3 class="text-accent mb-2" id="productPrice">
                    <?php 
                    if ($default_variant) {
                        echo formatRupiah($default_variant['Harga_Jual']);
                    } else {
                        $prices = array_column($product['variants'], 'Harga_Jual');
                        $min_price = min($prices);
                        $max_price = max($prices);
                        if ($min_price == $max_price) {
                            echo formatRupiah($min_price);
                        } else {
                            echo formatRupiah($min_price) . ' - ' . formatRupiah($max_price);
                        }
                    }
                    ?>
                </h3>
            </div>
            
            <!-- Stock Availability (Dynamic) -->
            <div class="mb-3" id="stockInfo">
                <?php if ($default_variant && $default_variant['Stok'] > 0): ?>
                    <p class="text-success mb-0">
                        <i class="bi bi-check-circle"></i> 
                        <strong id="stockText">Sisa <?php echo $default_variant['Stok']; ?> pcs!</strong>
                    </p>
                <?php elseif ($default_variant && $default_variant['Stok'] <= 0): ?>
                    <p class="text-danger mb-0">
                        <i class="bi bi-x-circle"></i> 
                        <strong>Stok Habis</strong>
                    </p>
                <?php else: ?>
                    <p class="text-warning mb-0">
                        <i class="bi bi-exclamation-triangle"></i> 
                        <strong>Pilih warna untuk melihat stok</strong>
                    </p>
                <?php endif; ?>
            </div>
            
            <div class="mb-4">
                <h5>Deskripsi Produk</h5>
                <p class="text-muted">
                    <?php echo nl2br(htmlspecialchars($product['Deskripsi_Lengkap'])); ?>
                </p>
                <?php if ($product['Deskripsi_Bahan']): ?>
                    <p class="text-muted">
                        <strong>Bahan:</strong> <?php echo htmlspecialchars($product['Deskripsi_Bahan']); ?>
                    </p>
                <?php endif; ?>
            </div>
            
            <!-- Color Swatch Selector -->
            <div class="mb-4">
                <h5>Pilih Warna <span class="text-danger">*</span></h5>
                <div class="color-swatches d-flex flex-wrap gap-2">
                    <?php foreach ($product['variants'] as $variant): ?>
                        <?php 
                        $is_out_of_stock = ($variant['Stok'] <= 0);
                        $is_default = ($default_variant && $default_variant['Kode_SKU'] == $variant['Kode_SKU']);
                        ?>
                        <div class="color-swatch-wrapper position-relative" 
                             data-sku="<?php echo htmlspecialchars($variant['Kode_SKU']); ?>"
                             data-bs-toggle="tooltip" 
                             data-bs-placement="top" 
                             title="<?php echo htmlspecialchars($variant['Nama_Warna']); ?>">
                            <button type="button" 
                                    class="color-swatch <?php echo $is_default ? 'active' : ''; ?> <?php echo $is_out_of_stock ? 'out-of-stock' : ''; ?>"
                                    data-sku="<?php echo htmlspecialchars($variant['Kode_SKU']); ?>"
                                    <?php echo $is_out_of_stock ? 'disabled' : ''; ?>
                                    style="width: 50px; height: 50px; border-radius: 50%; border: 3px solid <?php echo $is_default ? '#DCAE96' : '#ddd'; ?>; background-color: <?php echo $variant['Kode_Hex'] ?: '#ccc'; ?>; cursor: pointer; transition: all 0.3s; position: relative;">
                                <?php if ($is_out_of_stock): ?>
                                    <span style="position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%) rotate(-45deg); width: 60%; height: 2px; background: #999;"></span>
                                <?php endif; ?>
                            </button>
                            <small class="d-block text-center mt-1" style="font-size: 0.75rem;">
                                <?php echo htmlspecialchars($variant['Nama_Warna']); ?>
                            </small>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
            
            <!-- Add to Cart Form -->
            <form method="POST" action="cart.php" id="addToCartForm" class="mb-4">
                <input type="hidden" name="action" value="add">
                <input type="hidden" name="kode_sku" id="selectedSku" value="<?php echo $default_variant ? htmlspecialchars($default_variant['Kode_SKU']) : ''; ?>">
                
                <div class="row mb-3">
                    <div class="col-4">
                        <label for="qty" class="form-label">Jumlah</label>
                        <input type="number" 
                               class="form-control" 
                               id="qty" 
                               name="qty" 
                               value="1" 
                               min="1" 
                               max="<?php echo $default_variant ? $default_variant['Stok'] : 0; ?>"
                               <?php echo (!$default_variant || $default_variant['Stok'] <= 0) ? 'disabled' : ''; ?>>
                    </div>
                </div>
                
                <button type="submit" 
                        class="btn btn-primary btn-lg w-100"
                        id="addToCartBtn"
                        <?php echo (!$default_variant || $default_variant['Stok'] <= 0) ? 'disabled' : ''; ?>>
                    <i class="bi bi-cart-plus"></i> 
                    <span id="addToCartText">
                        <?php if (!$default_variant): ?>
                            Pilih Warna Terlebih Dahulu
                        <?php elseif ($default_variant['Stok'] <= 0): ?>
                            Stok Habis
                        <?php else: ?>
                            Tambah ke Keranjang
                        <?php endif; ?>
                    </span>
                </button>
            </form>
            
            <div class="mt-4">
                <a href="shop.php" class="btn btn-outline-primary">
                    <i class="bi bi-arrow-left"></i> Kembali ke Katalog
                </a>
            </div>
        </div>
    </div>
    
    <!-- Related Products Section -->
    <?php if (!empty($related_products)): ?>
        <div class="row mt-5">
            <div class="col-12">
                <h3 class="section-title mb-4">Mungkin Anda Suka</h3>
                <div class="row g-4">
                    <?php foreach ($related_products as $related): ?>
                        <div class="col-lg-3 col-md-4 col-sm-6">
                            <div class="product-card hover-lift">
                                <a href="product.php?id=<?php echo $related['ID_Induk']; ?>">
                                    <?php if ($related['Foto_Produk']): ?>
                                        <img src="<?php echo SITE_URL; ?>/assets/images/products/<?php echo htmlspecialchars($related['Foto_Produk']); ?>" 
                                             alt="<?php echo htmlspecialchars($related['Nama_Produk']); ?>"
                                             class="product-card-img">
                                    <?php else: ?>
                                        <div class="product-card-img">
                                            <i class="bi bi-image"></i>
                                        </div>
                                    <?php endif; ?>
                                    
                                    <div class="product-card-body">
                                        <h5 class="product-card-title">
                                            <?php echo htmlspecialchars($related['Nama_Produk']); ?>
                                        </h5>
                                        <p class="text-muted small mb-2">
                                            <?php echo htmlspecialchars($related['Nama_Kategori']); ?> • 
                                            <?php echo htmlspecialchars($related['Nama_Bahan']); ?>
                                        </p>
                                        <div class="product-card-price">
                                            <?php 
                                            if ($related['Harga_Min'] == $related['Harga_Max']) {
                                                echo formatRupiah($related['Harga_Min']);
                                            } else {
                                                echo formatRupiah($related['Harga_Min']) . ' - ' . formatRupiah($related['Harga_Max']);
                                            }
                                            ?>
                                        </div>
                                    </div>
                                </a>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    <?php endif; ?>
</div>

<!-- Toast Notification Container -->
<div class="toast-container position-fixed top-0 end-0 p-3" style="z-index: 9999;">
    <div id="toastNotification" class="toast" role="alert" aria-live="assertive" aria-atomic="true">
        <div class="toast-header">
            <i class="bi bi-exclamation-triangle text-warning me-2"></i>
            <strong class="me-auto">Peringatan</strong>
            <button type="button" class="btn-close" data-bs-dismiss="toast"></button>
        </div>
        <div class="toast-body" id="toastMessage">
            Harap pilih warna terlebih dahulu.
        </div>
    </div>
</div>

<script>
// Variants data from PHP
const variants = <?php echo json_encode($variants_data); ?>;

// Initialize tooltips
const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
tooltipTriggerList.map(function (tooltipTriggerEl) {
    return new bootstrap.Tooltip(tooltipTriggerEl);
});

// Color swatch click handler
document.querySelectorAll('.color-swatch').forEach(swatch => {
    swatch.addEventListener('click', function() {
        if (this.disabled) return;
        
        const sku = this.getAttribute('data-sku');
        selectVariant(sku);
    });
});

// Thumbnail click handler
document.querySelectorAll('.thumbnail-img').forEach(thumb => {
    thumb.addEventListener('click', function() {
        const sku = this.getAttribute('data-sku');
        selectVariant(sku);
        
        // Update active thumbnail
        document.querySelectorAll('.thumbnail-img').forEach(t => t.classList.remove('active'));
        this.classList.add('active');
        this.style.borderColor = '#DCAE96';
    });
});

function selectVariant(sku) {
    if (!variants[sku]) return;
    
    const variant = variants[sku];
    
    // Update active swatch
    document.querySelectorAll('.color-swatch').forEach(s => {
        s.classList.remove('active');
        s.style.borderColor = '#ddd';
    });
    const activeSwatch = document.querySelector(`.color-swatch[data-sku="${sku}"]`);
    if (activeSwatch) {
        activeSwatch.classList.add('active');
        activeSwatch.style.borderColor = '#DCAE96';
    }
    
    // Update main image
    if (variant.image) {
        const mainImg = document.getElementById('mainProductImage');
        mainImg.style.opacity = '0';
        setTimeout(() => {
            mainImg.src = variant.image;
            mainImg.style.opacity = '1';
        }, 150);
    }
    
    // Update price with animation
    const priceEl = document.getElementById('productPrice');
    priceEl.style.transform = 'scale(1.1)';
    priceEl.style.transition = 'transform 0.3s';
    setTimeout(() => {
        const formattedPrice = new Intl.NumberFormat('id-ID', {
            style: 'currency',
            currency: 'IDR',
            minimumFractionDigits: 0
        }).format(variant.price).replace('IDR', 'Rp');
        priceEl.textContent = formattedPrice;
        priceEl.style.transform = 'scale(1)';
    }, 150);
    
    // Update stock info
    const stockInfo = document.getElementById('stockInfo');
    const stockText = document.getElementById('stockText');
    if (variant.stock > 0) {
        stockInfo.innerHTML = `
            <p class="text-success mb-0">
                <i class="bi bi-check-circle"></i> 
                <strong id="stockText">Sisa ${variant.stock} pcs!</strong>
            </p>
        `;
    } else {
        stockInfo.innerHTML = `
            <p class="text-danger mb-0">
                <i class="bi bi-x-circle"></i> 
                <strong>Stok Habis</strong>
            </p>
        `;
    }
    
    // Update hidden input
    document.getElementById('selectedSku').value = sku;
    
    // Update quantity input
    const qtyInput = document.getElementById('qty');
    qtyInput.max = variant.stock;
    qtyInput.disabled = variant.stock <= 0;
    if (variant.stock <= 0 || parseInt(qtyInput.value) > variant.stock) {
        qtyInput.value = variant.stock > 0 ? 1 : 0;
    }
    
    // Update add to cart button
    const addBtn = document.getElementById('addToCartBtn');
    const addText = document.getElementById('addToCartText');
    if (variant.stock <= 0) {
        addBtn.disabled = true;
        addText.textContent = 'Stok Habis';
    } else {
        addBtn.disabled = false;
        addText.textContent = 'Tambah ke Keranjang';
    }
}

// Form validation
document.getElementById('addToCartForm').addEventListener('submit', function(e) {
    const selectedSku = document.getElementById('selectedSku').value;
    
    if (!selectedSku) {
        e.preventDefault();
        showToast('Harap pilih warna terlebih dahulu.');
        return false;
    }
    
    const variant = variants[selectedSku];
    if (!variant || variant.stock <= 0) {
        e.preventDefault();
        showToast('Varian yang dipilih tidak tersedia.');
        return false;
    }
    
    const qty = parseInt(document.getElementById('qty').value);
    if (qty > variant.stock) {
        e.preventDefault();
        showToast(`Stok tersedia hanya ${variant.stock} pcs.`);
        return false;
    }
    
    return true;
});

// Quantity validation
document.getElementById('qty').addEventListener('change', function() {
    const selectedSku = document.getElementById('selectedSku').value;
    if (!selectedSku) return;
    
    const variant = variants[selectedSku];
    if (!variant) return;
    
    const qty = parseInt(this.value);
    if (qty > variant.stock) {
        this.value = variant.stock;
        showToast(`Stok tersedia hanya ${variant.stock} pcs.`);
    }
    if (qty < 1) {
        this.value = 1;
    }
});

// Toast notification function
function showToast(message) {
    const toastEl = document.getElementById('toastNotification');
    const toastMessage = document.getElementById('toastMessage');
    toastMessage.textContent = message;
    
    const toast = new bootstrap.Toast(toastEl);
    toast.show();
}

// Initialize default variant selection
<?php if ($default_variant): ?>
selectVariant('<?php echo htmlspecialchars($default_variant['Kode_SKU']); ?>');
<?php endif; ?>
</script>

<style>
.color-swatch {
    transition: all 0.3s ease;
}

.color-swatch:hover:not(:disabled) {
    transform: scale(1.1);
    box-shadow: 0 4px 8px rgba(0,0,0,0.2);
}

.color-swatch.active {
    transform: scale(1.1);
    box-shadow: 0 4px 12px rgba(220, 174, 150, 0.5);
}

.color-swatch.out-of-stock {
    opacity: 0.5;
    cursor: not-allowed;
}

.thumbnail-img {
    transition: all 0.3s ease;
}

.thumbnail-img:hover {
    transform: scale(1.1);
    border-color: #DCAE96 !important;
}

.thumbnail-img.active {
    border-color: #DCAE96 !important;
    box-shadow: 0 2px 8px rgba(220, 174, 150, 0.3);
}

#productPrice {
    transition: transform 0.3s ease;
}

.product-main-image img {
    transition: opacity 0.3s ease;
}
</style>

<?php include __DIR__ . '/includes/footer.php'; ?>
