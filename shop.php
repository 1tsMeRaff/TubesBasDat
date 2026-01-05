<?php
/**
 * Shop Page - Product Listing with Advanced Filters
 * Sakinah Style
 */

require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/functions.php';

$page_title = "Belanja - Sakinah Style";
$page_description = "Jelajahi koleksi lengkap fashion muslimah Sakinah Style";

// Get filter parameters (support arrays for multiple selections)
$kategori = isset($_GET['kategori']) ? $_GET['kategori'] : [];
$bahan = isset($_GET['bahan']) ? $_GET['bahan'] : [];
$warna = isset($_GET['warna']) ? $_GET['warna'] : [];
$harga_min = isset($_GET['harga_min']) ? (int)$_GET['harga_min'] : null;
$harga_max = isset($_GET['harga_max']) ? (int)$_GET['harga_max'] : null;
$sort = isset($_GET['sort']) ? $_GET['sort'] : 'terbaru';
$page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;

// Normalize to arrays
if (!is_array($kategori)) {
    $kategori = $kategori ? [(int)$kategori] : [];
}
if (!is_array($bahan)) {
    $bahan = $bahan ? [(int)$bahan] : [];
}
if (!is_array($warna)) {
    $warna = $warna ? [(int)$warna] : [];
}

// Get filter options
$categories = getAllCategories();
$materials = getAllMaterials();
$colors = getAllColors();
$price_range = getPriceRange();

// Get products with filters
$filters = [
    'kategori' => !empty($kategori) ? $kategori : null,
    'bahan' => !empty($bahan) ? $bahan : null,
    'warna' => !empty($warna) ? $warna : null,
    'harga_min' => $harga_min,
    'harga_max' => $harga_max,
    'sort' => $sort,
    'page' => $page,
    'per_page' => 12
];

$result = getAllProducts($filters);
$products = $result['products'];
$total = $result['total'];
$current_page = $result['current_page'];
$total_pages = $result['total_pages'];

// Build query params for pagination
$query_params = [];
if (!empty($kategori)) {
    $query_params['kategori'] = $kategori;
}
if (!empty($bahan)) {
    $query_params['bahan'] = $bahan;
}
if (!empty($warna)) {
    $query_params['warna'] = $warna;
}
if ($harga_min) {
    $query_params['harga_min'] = $harga_min;
}
if ($harga_max) {
    $query_params['harga_max'] = $harga_max;
}
$query_params['sort'] = $sort;

include __DIR__ . '/includes/header.php';
?>

<div class="container my-5">
    <div class="row">
        <!-- Filter Sidebar -->
        <div class="col-lg-3 mb-4">
            <div class="filter-sidebar">
                <h3 class="filter-title">
                    <i class="bi bi-funnel"></i> Filter Produk
                </h3>
                
                <form method="GET" action="shop.php" id="filterForm">
                    <!-- Category Filter (Checkboxes) -->
                    <div class="filter-group">
                        <label class="fw-bold mb-2">Kategori</label>
                        <div class="filter-checkboxes">
                            <?php foreach ($categories as $cat): ?>
                                <div class="form-check">
                                    <input class="form-check-input" 
                                           type="checkbox" 
                                           name="kategori[]" 
                                           value="<?php echo $cat['ID_Kategori']; ?>" 
                                           id="kategori_<?php echo $cat['ID_Kategori']; ?>"
                                           <?php echo in_array($cat['ID_Kategori'], $kategori) ? 'checked' : ''; ?>>
                                    <label class="form-check-label" for="kategori_<?php echo $cat['ID_Kategori']; ?>">
                                        <?php echo htmlspecialchars($cat['Nama_Kategori']); ?>
                                    </label>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                    
                    <!-- Material Filter (Checkboxes) -->
                    <div class="filter-group">
                        <label class="fw-bold mb-2">Bahan</label>
                        <div class="filter-checkboxes">
                            <?php foreach ($materials as $mat): ?>
                                <div class="form-check">
                                    <input class="form-check-input" 
                                           type="checkbox" 
                                           name="bahan[]" 
                                           value="<?php echo $mat['ID_Bahan']; ?>" 
                                           id="bahan_<?php echo $mat['ID_Bahan']; ?>"
                                           <?php echo in_array($mat['ID_Bahan'], $bahan) ? 'checked' : ''; ?>>
                                    <label class="form-check-label" for="bahan_<?php echo $mat['ID_Bahan']; ?>">
                                        <?php echo htmlspecialchars($mat['Nama_Bahan']); ?>
                                    </label>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                    
                    <!-- Color Filter (Checkboxes) -->
                    <div class="filter-group">
                        <label class="fw-bold mb-2">Warna</label>
                        <div class="filter-checkboxes">
                            <?php foreach ($colors as $col): ?>
                                <div class="form-check">
                                    <input class="form-check-input" 
                                           type="checkbox" 
                                           name="warna[]" 
                                           value="<?php echo $col['ID_Warna']; ?>" 
                                           id="warna_<?php echo $col['ID_Warna']; ?>"
                                           <?php echo in_array($col['ID_Warna'], $warna) ? 'checked' : ''; ?>>
                                    <label class="form-check-label" for="warna_<?php echo $col['ID_Warna']; ?>">
                                        <?php echo htmlspecialchars($col['Nama_Warna']); ?>
                                    </label>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                    
                    <!-- Price Range Filter -->
                    <div class="filter-group">
                        <label class="fw-bold mb-2">Rentang Harga</label>
                        <div class="price-range">
                            <div class="d-flex justify-content-between mb-2">
                                <span class="small">Rp <?php echo number_format($price_range['min'], 0, ',', '.'); ?></span>
                                <span class="small">Rp <?php echo number_format($price_range['max'], 0, ',', '.'); ?></span>
                            </div>
                            <div class="row g-2">
                                <div class="col-6">
                                    <input type="number" 
                                           class="form-control form-control-sm" 
                                           name="harga_min" 
                                           id="harga_min"
                                           placeholder="Min"
                                           value="<?php echo $harga_min ?: ''; ?>"
                                           min="<?php echo $price_range['min']; ?>"
                                           max="<?php echo $price_range['max']; ?>">
                                </div>
                                <div class="col-6">
                                    <input type="number" 
                                           class="form-control form-control-sm" 
                                           name="harga_max" 
                                           id="harga_max"
                                           placeholder="Max"
                                           value="<?php echo $harga_max ?: ''; ?>"
                                           min="<?php echo $price_range['min']; ?>"
                                           max="<?php echo $price_range['max']; ?>">
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Preserve sort when submitting -->
                    <input type="hidden" name="sort" value="<?php echo htmlspecialchars($sort); ?>">
                    
                    <button type="submit" class="btn btn-primary w-100 mb-2">
                        <i class="bi bi-search"></i> Terapkan Filter
                    </button>
                    <a href="shop.php" class="btn btn-outline-primary w-100">
                        <i class="bi bi-x-circle"></i> Reset Filter
                    </a>
                </form>
            </div>
        </div>
        
        <!-- Product Grid -->
        <div class="col-lg-9">
            <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap">
                <div>
                    <h2 class="mb-0">
                        <?php if (!empty($kategori) || !empty($bahan) || !empty($warna) || $harga_min || $harga_max): ?>
                            Produk Filter
                        <?php else: ?>
                            Semua Produk
                        <?php endif; ?>
                    </h2>
                    <p class="text-muted small mb-0">
                        Menampilkan <?php echo count($products); ?> dari <?php echo $total; ?> produk
                    </p>
                </div>
                
                <!-- Sort Dropdown -->
                <div class="d-flex align-items-center gap-2">
                    <label for="sortSelect" class="small mb-0">Urutkan:</label>
                    <select class="form-select form-select-sm" id="sortSelect" style="width: auto;" onchange="updateSort(this.value)">
                        <option value="terbaru" <?php echo ($sort == 'terbaru') ? 'selected' : ''; ?>>Terbaru</option>
                        <option value="termurah" <?php echo ($sort == 'termurah') ? 'selected' : ''; ?>>Termurah</option>
                        <option value="termahal" <?php echo ($sort == 'termahal') ? 'selected' : ''; ?>>Termahal</option>
                        <option value="terlaris" <?php echo ($sort == 'terlaris') ? 'selected' : ''; ?>>Terlaris</option>
                    </select>
                </div>
            </div>
            
            <?php if (empty($products)): ?>
                <div class="alert alert-info text-center">
                    <i class="bi bi-info-circle"></i> Tidak ada produk yang ditemukan dengan filter yang dipilih.
                    <a href="shop.php" class="alert-link">Lihat semua produk</a>
                </div>
            <?php else: ?>
                <div class="row g-4">
                    <?php foreach ($products as $product): 
                        $is_sold_out = ($product['Variant_Tersedia'] == 0);
                    ?>
                        <div class="col-lg-4 col-md-6">
                            <div class="product-card position-relative">
                                <?php if ($is_sold_out): ?>
                                    <div class="sold-out-overlay">
                                        <span class="sold-out-badge">Habis / Sold Out</span>
                                    </div>
                                <?php endif; ?>
                                
                                <div class="product-card-image-wrapper">
                                    <a href="product.php?id=<?php echo $product['ID_Induk']; ?>">
                                        <?php if ($product['Foto_Produk']): ?>
                                            <img src="<?php echo SITE_URL; ?>/assets/images/products/<?php echo htmlspecialchars($product['Foto_Produk']); ?>" 
                                                 alt="<?php echo htmlspecialchars($product['Nama_Produk']); ?>"
                                                 class="product-card-img">
                                        <?php else: ?>
                                            <div class="product-card-img">
                                                <i class="bi bi-image"></i>
                                            </div>
                                        <?php endif; ?>
                                    </a>
                                    
                                    <!-- Quick View Button -->
                                    <button class="btn btn-sm btn-primary quick-view-btn" 
                                            data-product-id="<?php echo $product['ID_Induk']; ?>"
                                            onclick="openQuickView(<?php echo $product['ID_Induk']; ?>)">
                                        <i class="bi bi-eye"></i> Quick View
                                    </button>
                                </div>
                                
                                <div class="product-card-body">
                                    <h5 class="product-card-title">
                                        <a href="product.php?id=<?php echo $product['ID_Induk']; ?>" class="text-decoration-none">
                                            <?php echo htmlspecialchars($product['Nama_Produk']); ?>
                                        </a>
                                    </h5>
                                    <p class="text-muted small mb-2">
                                        <?php echo htmlspecialchars($product['Nama_Kategori']); ?> • 
                                        <?php echo htmlspecialchars($product['Nama_Bahan']); ?>
                                    </p>
                                    <div class="product-card-price">
                                        <?php 
                                        if ($product['Harga_Min'] == $product['Harga_Max']) {
                                            echo formatRupiah($product['Harga_Min']);
                                        } else {
                                            echo formatRupiah($product['Harga_Min']) . ' - ' . formatRupiah($product['Harga_Max']);
                                        }
                                        ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
                
                <!-- Pagination -->
                <?php echo renderPagination($total_pages, $current_page, $query_params); ?>
            <?php endif; ?>
        </div>
    </div>
</div>

<script>
function updateSort(sortValue) {
    const url = new URL(window.location.href);
    url.searchParams.set('sort', sortValue);
    url.searchParams.set('page', '1'); // Reset to first page
    window.location.href = url.toString();
}

function openQuickView(productId) {
    // Placeholder for Quick View Modal (to be implemented in next module)
    console.log('Quick View for product:', productId);
    // TODO: Implement modal with product details
}
</script>

<?php include __DIR__ . '/includes/footer.php'; ?>

