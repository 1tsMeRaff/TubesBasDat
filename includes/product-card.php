<?php
// Product data should be passed as $product variable
if (!isset($product)) return;

// Calculate discount
$discount = 0;
if (isset($product['Harga_Asli']) && $product['Harga_Asli'] > 0) {
    if ($product['Harga_Asli'] > $product['Harga_Min']) {
        $discount = round((($product['Harga_Asli'] - $product['Harga_Min']) / $product['Harga_Asli']) * 100);
    }
}
?>

<div class="col">
    <div class="product-card card h-100 border-0 shadow-sm">
        <!-- Product Image -->
        <div class="position-relative overflow-hidden" style="padding-top: 100%;">
            <a href="product.php?id=<?php echo $product['ID_Induk']; ?>" class="stretched-link">
                <?php if (!empty($product['Foto_Produk'])): ?>
                <img src="<?php echo SITE_URL; ?>/assets/images/products/<?php echo htmlspecialchars($product['Foto_Produk']); ?>" 
                     alt="<?php echo htmlspecialchars($product['Nama_Produk']); ?>"
                     class="card-img-top"
                     style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; object-fit: cover;">
                <?php else: ?>
                <div class="bg-light d-flex align-items-center justify-content-center" 
                     style="position: absolute; top: 0; left: 0; width: 100%; height: 100%;">
                    <i class="bi bi-image text-muted display-4"></i>
                </div>
                <?php endif; ?>
            </a>
            
            <!-- Badges -->
            <div class="position-absolute top-0 start-0 p-2">
                <?php if ($discount > 0): ?>
                <span class="badge bg-danger">-<?php echo $discount; ?>%</span>
                <?php endif; ?>
                <?php if ($product['Variant_Tersedia'] == 0): ?>
                <span class="badge bg-dark">Habis</span>
                <?php endif; ?>
            </div>
        </div>
        
        <!-- Product Info -->
        <div class="card-body">
            <h6 class="card-title mb-1">
                <a href="product.php?id=<?php echo $product['ID_Induk']; ?>" class="text-decoration-none text-dark">
                    <?php echo htmlspecialchars($product['Nama_Produk']); ?>
                </a>
            </h6>
            
            <p class="text-muted small mb-2">
                <?php echo htmlspecialchars($product['Nama_Kategori']); ?>
            </p>
            
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <?php if ($discount > 0): ?>
                    <div class="text-muted small text-decoration-line-through">
                        Rp <?php echo number_format($product['Harga_Asli'], 0, ',', '.'); ?>
                    </div>
                    <?php endif; ?>
                    
                    <div class="fw-bold text-primary">
                        <?php if ($product['Harga_Min'] == $product['Harga_Max']): ?>
                        Rp <?php echo number_format($product['Harga_Min'], 0, ',', '.'); ?>
                        <?php else: ?>
                        Rp <?php echo number_format($product['Harga_Min'], 0, ',', '.'); ?>+
                        <?php endif; ?>
                    </div>
                </div>
                
                <?php if ($product['Variant_Tersedia'] > 0): ?>
                <button class="btn btn-sm btn-outline-primary" 
                        onclick="event.preventDefault(); addToCart(<?php echo $product['ID_Induk']; ?>)">
                    <i class="bi bi-cart-plus"></i>
                </button>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>