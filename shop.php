<?php
/**
 * Shop Page - Product Listing with Advanced Filters
 * Sakinah Style
 */

require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/functions.php';

$page_title = "Belanja - Sakinah Style";
$page_description = "Jelajahi koleksi lengkap fashion muslimah Sakinah Style";

// Get filter parameters
$kategori = isset($_GET['kategori']) ? (array)$_GET['kategori'] : [];
$bahan = isset($_GET['bahan']) ? (array)$_GET['bahan'] : [];
$warna = isset($_GET['warna']) ? (array)$_GET['warna'] : [];
$harga_min = isset($_GET['harga_min']) ? (int)$_GET['harga_min'] : null;
$harga_max = isset($_GET['harga_max']) ? (int)$_GET['harga_max'] : null;
$search = isset($_GET['search']) ? htmlspecialchars($_GET['search']) : '';
$sort = isset($_GET['sort']) ? $_GET['sort'] : 'terbaru';
$page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
$per_page = isset($_GET['per_page']) ? (int)$_GET['per_page'] : 12;

// Validate sort
$valid_sorts = ['terbaru', 'termurah', 'termahal', 'terlaris'];
if (!in_array($sort, $valid_sorts)) {
    $sort = 'terbaru';
}

// Get filter options
$categories = getAllCategories();
$materials = getAllMaterials();
$colors = getAllColors();
$price_range = getPriceRange();

// Build filters array
$filters = [
    'kategori' => $kategori,
    'bahan' => $bahan,
    'warna' => $warna,
    'harga_min' => $harga_min,
    'harga_max' => $harga_max,
    'search' => $search,
    'sort' => $sort,
    'page' => $page,
    'per_page' => $per_page
];

// Get products with filters
$result = getAllProducts($filters);
$products = $result['products'];
$total = $result['total'];
$current_page = $result['current_page'];
$total_pages = $result['total_pages'];

// Calculate active filters count
$active_filters = 0;
$active_filters += count($kategori);
$active_filters += count($bahan);
$active_filters += count($warna);
if ($harga_min !== null) $active_filters++;
if ($harga_max !== null) $active_filters++;
if (!empty($search)) $active_filters++;

// Simple mobile detection
$is_mobile = false;
if (isset($_SERVER['HTTP_USER_AGENT'])) {
    $mobile_agents = ['Android', 'iPhone', 'iPad', 'iPod', 'BlackBerry', 'Windows Phone'];
    foreach ($mobile_agents as $agent) {
        if (stripos($_SERVER['HTTP_USER_AGENT'], $agent) !== false) {
            $is_mobile = true;
            break;
        }
    }
}

include __DIR__ . '/includes/header.php';
?>

<!-- Main Content -->
<div class="container-fluid my-4">
    <div class="row">
        <!-- Desktop Filter Sidebar -->
        <?php if (!$is_mobile): ?>
        <div class="col-lg-3 col-xl-2 d-none d-lg-block">
            <div class="card shadow-sm sticky-top" style="top: 20px;">
                <div class="card-header bg-white border-bottom">
                    <h5 class="mb-0">
                        <i class="bi bi-funnel"></i> Filter Produk
                        <?php if ($active_filters > 0): ?>
                        <span class="badge bg-primary ms-2"><?php echo $active_filters; ?> aktif</span>
                        <?php endif; ?>
                    </h5>
                </div>
                <div class="card-body p-3">
                    <form method="GET" action="shop.php" id="filterForm">
                        <!-- Search Input -->
                        <div class="mb-4">
                            <label class="form-label small">Cari Produk</label>
                            <div class="input-group input-group-sm">
                                <span class="input-group-text"><i class="bi bi-search"></i></span>
                                <input type="text" 
                                       class="form-control" 
                                       name="search" 
                                       placeholder="Nama produk..." 
                                       value="<?php echo htmlspecialchars($search); ?>">
                                <?php if (!empty($search)): ?>
                                <button class="btn btn-outline-secondary" type="button" onclick="clearSearch()">
                                    <i class="bi bi-x"></i>
                                </button>
                                <?php endif; ?>
                            </div>
                        </div>
                        
                        <!-- Category Filter -->
                        <div class="mb-4">
                            <label class="form-label small fw-bold">Kategori</label>
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
                        
                        <!-- Material Filter -->
                        <div class="mb-4">
                            <label class="form-label small fw-bold">Bahan</label>
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
                        
                        <!-- Color Filter -->
                        <div class="mb-4">
                            <label class="form-label small fw-bold">Warna</label>
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
                        
                        <!-- Price Range -->
                        <div class="mb-4">
                            <label class="form-label small fw-bold">Rentang Harga</label>
                            <div class="row g-2">
                                <div class="col-6">
                                    <input type="number" 
                                           class="form-control form-control-sm" 
                                           name="harga_min" 
                                           id="harga_min"
                                           placeholder="Min"
                                           value="<?php echo $harga_min ?: ''; ?>"
                                           min="0">
                                </div>
                                <div class="col-6">
                                    <input type="number" 
                                           class="form-control form-control-sm" 
                                           name="harga_max" 
                                           id="harga_max"
                                           placeholder="Max"
                                           value="<?php echo $harga_max ?: ''; ?>"
                                           min="0">
                                </div>
                            </div>
                            <div class="d-flex justify-content-between mt-1">
                                <small class="text-muted">Rp <?php echo number_format($price_range['min'] ?? 0, 0, ',', '.'); ?></small>
                                <small class="text-muted">Rp <?php echo number_format($price_range['max'] ?? 0, 0, ',', '.'); ?></small>
                            </div>
                        </div>
                        
                        <!-- Hidden fields -->
                        <input type="hidden" name="sort" value="<?php echo htmlspecialchars($sort); ?>">
                        <input type="hidden" name="per_page" value="<?php echo $per_page; ?>">
                        <input type="hidden" name="page" value="1">
                        
                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary btn-sm">
                                <i class="bi bi-check-lg"></i> Terapkan Filter
                            </button>
                            <a href="shop.php" class="btn btn-outline-danger btn-sm">
                                <i class="bi bi-x-circle"></i> Reset Filter
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <?php endif; ?>
        
        <!-- Products Area -->
        <div class="<?php echo $is_mobile ? 'col-12' : 'col-lg-9 col-xl-10'; ?>">
            <!-- Page Header -->
            <div class="mb-4">
                <h2 class="mb-2">
                    <?php if ($active_filters > 0): ?>
                        <i class="bi bi-search text-primary"></i> Hasil Pencarian
                    <?php else: ?>
                        <i class="bi bi-grid-3x3-gap text-primary"></i> Semua Produk
                    <?php endif; ?>
                </h2>
                <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
                    <p class="text-muted mb-0">
                        <span class="fw-semibold"><?php echo $total; ?></span> produk ditemukan
                        <?php if ($active_filters > 0): ?>
                            <a href="shop.php" class="text-danger ms-2 small">
                                <i class="bi bi-x-circle"></i> Hapus semua filter
                            </a>
                        <?php endif; ?>
                    </p>
                    
                    <!-- Sort and Items Per Page -->
                    <div class="d-flex align-items-center gap-3">
                        <!-- Sort Dropdown -->
                        <div class="d-flex align-items-center gap-2">
                            <span class="small text-muted">Urutkan:</span>
                            <select class="form-select form-select-sm" style="width: auto;" onchange="updateSort(this.value)">
                                <option value="terbaru" <?php echo ($sort == 'terbaru') ? 'selected' : ''; ?>>Terbaru</option>
                                <option value="termurah" <?php echo ($sort == 'termurah') ? 'selected' : ''; ?>>Termurah</option>
                                <option value="termahal" <?php echo ($sort == 'termahal') ? 'selected' : ''; ?>>Termahal</option>
                                <option value="terlaris" <?php echo ($sort == 'terlaris') ? 'selected' : ''; ?>>Terlaris</option>
                            </select>
                        </div>
                        
                        <!-- Items Per Page -->
                        <div class="d-flex align-items-center gap-2">
                            <span class="small text-muted">Tampil:</span>
                            <select class="form-select form-select-sm" style="width: auto;" onchange="updatePerPage(this.value)">
                                <option value="12" <?php echo ($per_page == 12) ? 'selected' : ''; ?>>12</option>
                                <option value="24" <?php echo ($per_page == 24) ? 'selected' : ''; ?>>24</option>
                                <option value="48" <?php echo ($per_page == 48) ? 'selected' : ''; ?>>48</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Mobile Filter Button -->
            <?php if ($is_mobile): ?>
            <div class="mb-3">
                <button class="btn btn-outline-primary w-100" type="button" data-bs-toggle="collapse" data-bs-target="#mobileFilters">
                    <i class="bi bi-funnel"></i> Filter Produk
                    <?php if ($active_filters > 0): ?>
                    <span class="badge bg-danger ms-1"><?php echo $active_filters; ?></span>
                    <?php endif; ?>
                </button>
                <div class="collapse mt-3" id="mobileFilters">
                    <div class="card card-body">
                        <!-- Same filter form as desktop but simplified -->
                        <form method="GET" action="shop.php">
                            <!-- Mobile filter form content -->
                            <!-- You can copy the filter form here or include a separate mobile filter file -->
                            <button type="submit" class="btn btn-primary w-100 mt-3">Terapkan Filter</button>
                        </form>
                    </div>
                </div>
            </div>
            <?php endif; ?>
            
            <!-- Active Filters -->
            <?php if ($active_filters > 0): ?>
            <div class="mb-4">
                <div class="d-flex flex-wrap gap-2">
                    <?php
                    // Render active filters
                    // Categories
                    foreach ($categories as $cat) {
                        if (in_array($cat['ID_Kategori'], $kategori)) {
                            echo '<span class="badge bg-primary">' . htmlspecialchars($cat['Nama_Kategori']) . '</span>';
                        }
                    }
                    
                    // Materials
                    foreach ($materials as $mat) {
                        if (in_array($mat['ID_Bahan'], $bahan)) {
                            echo '<span class="badge bg-success">' . htmlspecialchars($mat['Nama_Bahan']) . '</span>';
                        }
                    }
                    
                    // Colors
                    foreach ($colors as $col) {
                        if (in_array($col['ID_Warna'], $warna)) {
                            echo '<span class="badge bg-warning text-dark">' . htmlspecialchars($col['Nama_Warna']) . '</span>';
                        }
                    }
                    
                    // Price range
                    if ($harga_min || $harga_max) {
                        $min = $harga_min ? 'Rp ' . number_format($harga_min, 0, ',', '.') : '';
                        $max = $harga_max ? 'Rp ' . number_format($harga_max, 0, ',', '.') : '';
                        echo '<span class="badge bg-info">Harga: ' . ($min ?: '0') . ' - ' . ($max ?: '∞') . '</span>';
                    }
                    
                    // Search
                    if (!empty($search)) {
                        echo '<span class="badge bg-secondary">Cari: "' . htmlspecialchars($search) . '"</span>';
                    }
                    ?>
                </div>
            </div>
            <?php endif; ?>
            
            <!-- Products Grid -->
            <?php if (empty($products)): ?>
                <div class="text-center py-5">
                    <div class="mb-4">
                        <i class="bi bi-search display-1 text-muted"></i>
                    </div>
                    <h4 class="mb-3">Produk tidak ditemukan</h4>
                    <p class="text-muted mb-4">Coba ubah filter pencarian Anda</p>
                    <a href="shop.php" class="btn btn-primary">
                        <i class="bi bi-arrow-clockwise"></i> Reset Filter
                    </a>
                </div>
            <?php else: ?>
                <div class="row row-cols-2 row-cols-md-3 row-cols-lg-4 row-cols-xl-5 g-3">
                    <?php foreach ($products as $product): ?>
                    <div class="col">
                        <div class="product-card card h-100 border-0 shadow-sm hover-shadow">
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
                                
                                <!-- Sold Out Badge -->
                                <?php if ($product['Variant_Tersedia'] == 0): ?>
                                <div class="position-absolute top-0 start-0 w-100 h-100 bg-dark bg-opacity-50 d-flex align-items-center justify-content-center">
                                    <span class="badge bg-dark fs-6">HABIS</span>
                                </div>
                                <?php endif; ?>
                            </div>
                            
                            <!-- Product Info -->
                            <div class="card-body">
                                <h6 class="card-title mb-1 text-truncate">
                                    <a href="product.php?id=<?php echo $product['ID_Induk']; ?>" class="text-decoration-none text-dark">
                                        <?php echo htmlspecialchars($product['Nama_Produk']); ?>
                                    </a>
                                </h6>
                                
                                <p class="text-muted small mb-2">
                                    <?php echo htmlspecialchars($product['Nama_Kategori']); ?>
                                    <?php if (!empty($product['Nama_Bahan'])): ?>
                                    • <?php echo htmlspecialchars($product['Nama_Bahan']); ?>
                                    <?php endif; ?>
                                </p>
                                
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <div class="fw-bold text-primary">
                                            <?php if ($product['Harga_Min'] == $product['Harga_Max']): ?>
                                            Rp <?php echo number_format($product['Harga_Min'], 0, ',', '.'); ?>
                                            <?php else: ?>
                                            Rp <?php echo number_format($product['Harga_Min'], 0, ',', '.'); ?> - Rp <?php echo number_format($product['Harga_Max'], 0, ',', '.'); ?>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                    
                                    <?php if ($product['Variant_Tersedia'] > 0): ?>
                                    <button class="btn btn-sm btn-outline-primary" 
                                            onclick="event.preventDefault(); addToCart(<?php echo $product['ID_Induk']; ?>)"
                                            title="Tambah ke Keranjang">
                                        <i class="bi bi-cart-plus"></i>
                                    </button>
                                    <?php else: ?>
                                    <span class="text-danger small">
                                        <i class="bi bi-x-circle"></i> Habis
                                    </span>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
                
                <!-- Pagination -->
                <?php if ($total_pages > 1): ?>
                <div class="mt-5">
                    <?php 
                    // Build query params for pagination
                    $query_params = [];
                    foreach ($kategori as $k) {
                        $query_params['kategori[]'] = $k;
                    }
                    foreach ($bahan as $b) {
                        $query_params['bahan[]'] = $b;
                    }
                    foreach ($warna as $w) {
                        $query_params['warna[]'] = $w;
                    }
                    if ($harga_min) $query_params['harga_min'] = $harga_min;
                    if ($harga_max) $query_params['harga_max'] = $harga_max;
                    if (!empty($search)) $query_params['search'] = $search;
                    $query_params['sort'] = $sort;
                    $query_params['per_page'] = $per_page;
                    
                    echo renderPagination($total_pages, $current_page, $query_params);
                    ?>
                </div>
                <?php endif; ?>
            <?php endif; ?>
        </div>
    </div>
</div>

<script>
function updateSort(sortValue) {
    const url = new URL(window.location.href);
    url.searchParams.set('sort', sortValue);
    url.searchParams.set('page', '1');
    window.location.href = url.toString();
}

function updatePerPage(perPage) {
    const url = new URL(window.location.href);
    url.searchParams.set('per_page', perPage);
    url.searchParams.set('page', '1');
    window.location.href = url.toString();
}

function clearSearch() {
    document.querySelector('input[name="search"]').value = '';
    document.getElementById('filterForm').submit();
}

function addToCart(productId) {
    // Placeholder for add to cart functionality
    alert('Menambahkan produk ke keranjang: ' + productId);
    // You can implement AJAX call here
}
</script>

<?php include __DIR__ . '/includes/footer.php'; ?>