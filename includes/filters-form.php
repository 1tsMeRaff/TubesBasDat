<?php
$filterManager = isset($filterManager) ? $filterManager : null;
$filters = $filterManager ? $filterManager->getFilters() : [];
?>
<form method="GET" action="shop.php" id="filterForm">
    <!-- Search Input -->
    <div class="mb-4">
        <div class="input-group">
            <span class="input-group-text"><i class="bi bi-search"></i></span>
            <input type="text" 
                   class="form-control" 
                   name="search" 
                   placeholder="Cari produk..." 
                   value="<?php echo htmlspecialchars($filters['search'] ?? ''); ?>"
                   onkeyup="if(event.keyCode === 13) this.form.submit()">
            <?php if (!empty($filters['search'])): ?>
            <button class="btn btn-outline-secondary" type="button" onclick="clearSearch()">
                <i class="bi bi-x"></i>
            </button>
            <?php endif; ?>
        </div>
    </div>
    
    <!-- Category Filter -->
    <div class="filter-group mb-4">
        <h6 class="filter-title mb-3">
            <i class="bi bi-tags"></i> Kategori
            <?php if (!empty($filters['kategori'])): ?>
            <span class="badge bg-primary ms-1"><?php echo count($filters['kategori']); ?></span>
            <?php endif; ?>
        </h6>
        <div class="filter-checkboxes">
            <?php foreach ($categories as $cat): ?>
            <div class="form-check mb-2">
                <input class="form-check-input" 
                       type="checkbox" 
                       name="kategori[]" 
                       value="<?php echo $cat['ID_Kategori']; ?>" 
                       id="kategori_<?php echo $cat['ID_Kategori']; ?>"
                       <?php echo in_array($cat['ID_Kategori'], $filters['kategori']) ? 'checked' : ''; ?>
                       onchange="this.form.submit()">
                <label class="form-check-label" for="kategori_<?php echo $cat['ID_Kategori']; ?>">
                    <?php echo htmlspecialchars($cat['Nama_Kategori']); ?>
                    <span class="text-muted small">(<?php echo $cat['product_count'] ?? 0; ?>)</span>
                </label>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
    
    <!-- Material Filter -->
    <div class="filter-group mb-4">
        <h6 class="filter-title mb-3">
            <i class="bi bi-bricks"></i> Bahan
            <?php if (!empty($filters['bahan'])): ?>
            <span class="badge bg-primary ms-1"><?php echo count($filters['bahan']); ?></span>
            <?php endif; ?>
        </h6>
        <div class="filter-checkboxes">
            <?php foreach ($materials as $mat): ?>
            <div class="form-check mb-2">
                <input class="form-check-input" 
                       type="checkbox" 
                       name="bahan[]" 
                       value="<?php echo $mat['ID_Bahan']; ?>" 
                       id="bahan_<?php echo $mat['ID_Bahan']; ?>"
                       <?php echo in_array($mat['ID_Bahan'], $filters['bahan']) ? 'checked' : ''; ?>
                       onchange="this.form.submit()">
                <label class="form-check-label" for="bahan_<?php echo $mat['ID_Bahan']; ?>">
                    <?php echo htmlspecialchars($mat['Nama_Bahan']); ?>
                </label>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
    
    <!-- Color Filter -->
    <div class="filter-group mb-4">
        <h6 class="filter-title mb-3">
            <i class="bi bi-palette"></i> Warna
            <?php if (!empty($filters['warna'])): ?>
            <span class="badge bg-primary ms-1"><?php echo count($filters['warna']); ?></span>
            <?php endif; ?>
        </h6>
        <div class="filter-checkboxes">
            <?php foreach ($colors as $col): ?>
            <div class="form-check mb-2">
                <input class="form-check-input" 
                       type="checkbox" 
                       name="warna[]" 
                       value="<?php echo $col['ID_Warna']; ?>" 
                       id="warna_<?php echo $col['ID_Warna']; ?>"
                       <?php echo in_array($col['ID_Warna'], $filters['warna']) ? 'checked' : ''; ?>
                       onchange="this.form.submit()">
                <label class="form-check-label" for="warna_<?php echo $col['ID_Warna']; ?>">
                    <span class="color-dot me-2" style="background-color: <?php echo htmlspecialchars($col['Kode_Warna'] ?? '#ccc'); ?>"></span>
                    <?php echo htmlspecialchars($col['Nama_Warna']); ?>
                </label>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
    
    <!-- Price Range -->
    <div class="filter-group mb-4">
        <h6 class="filter-title mb-3"><i class="bi bi-currency-dollar"></i> Rentang Harga</h6>
        <div class="price-range">
            <div class="row g-2 mb-3">
                <div class="col-6">
                    <label class="form-label small">Min</label>
                    <div class="input-group input-group-sm">
                        <span class="input-group-text">Rp</span>
                        <input type="number" 
                               class="form-control" 
                               name="harga_min" 
                               id="harga_min"
                               placeholder="Min"
                               value="<?php echo $filters['harga_min'] ?: ''; ?>"
                               min="0"
                               onchange="this.form.submit()">
                    </div>
                </div>
                <div class="col-6">
                    <label class="form-label small">Max</label>
                    <div class="input-group input-group-sm">
                        <span class="input-group-text">Rp</span>
                        <input type="number" 
                               class="form-control" 
                               name="harga_max" 
                               id="harga_max"
                               placeholder="Max"
                               value="<?php echo $filters['harga_max'] ?: ''; ?>"
                               min="0"
                               onchange="this.form.submit()">
                    </div>
                </div>
            </div>
            <div class="d-flex justify-content-between small text-muted">
                <span>Rp <?php echo number_format($price_range['min'] ?? 0, 0, ',', '.'); ?></span>
                <span>Rp <?php echo number_format($price_range['max'] ?? 0, 0, ',', '.'); ?></span>
            </div>
        </div>
    </div>
    
    <!-- Hidden fields -->
    <input type="hidden" name="sort" value="<?php echo htmlspecialchars($filters['sort'] ?? 'terbaru'); ?>">
    <input type="hidden" name="per_page" value="<?php echo htmlspecialchars($filters['per_page'] ?? 12); ?>">
    <input type="hidden" name="page" value="1">
</form>

<style>
.color-dot {
    display: inline-block;
    width: 16px;
    height: 16px;
    border-radius: 50%;
    border: 1px solid #dee2e6;
    vertical-align: text-bottom;
}
.filter-chip {
    background: #e3f2fd;
    border: 1px solid #bbdefb;
    border-radius: 20px;
    padding: 5px 15px;
    font-size: 0.875rem;
    transition: all 0.2s;
}
.filter-chip:hover {
    background: #bbdefb;
}
.filter-chip .btn-close-sm {
    padding: 0.5rem;
    font-size: 0.7rem;
}
</style>

<script>
function clearSearch() {
    document.querySelector('input[name="search"]').value = '';
    document.getElementById('filterForm').submit();
}

function removePriceFilter() {
    document.getElementById('harga_min').value = '';
    document.getElementById('harga_max').value = '';
    document.getElementById('filterForm').submit();
}

function removeSearchFilter() {
    document.querySelector('input[name="search"]').value = '';
    document.getElementById('filterForm').submit();
}
</script>