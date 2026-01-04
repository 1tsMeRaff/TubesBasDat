<?php
class FilterManager {
    private $filters = [];
    private $per_page = 12;
    private $sort_options = ['terbaru', 'termurah', 'termahal', 'terlaris'];
    
    public function __construct() {
        $this->initializeFilters();
    }
    
    private function initializeFilters() {
        $this->filters = [
            'kategori' => [],
            'bahan' => [],
            'warna' => [],
            'harga_min' => null,
            'harga_max' => null,
            'search' => '',
            'sort' => 'terbaru',
            'page' => 1,
            'per_page' => $this->per_page
        ];
    }
    
    public function processFilters($input) {
        // Sanitize array inputs
        $this->filters['kategori'] = $this->sanitizeArrayInput($input['kategori'] ?? []);
        $this->filters['bahan'] = $this->sanitizeArrayInput($input['bahan'] ?? []);
        $this->filters['warna'] = $this->sanitizeArrayInput($input['warna'] ?? []);
        
        // Sanitize other inputs
        $this->filters['harga_min'] = $this->sanitizePriceInput($input['harga_min'] ?? null);
        $this->filters['harga_max'] = $this->sanitizePriceInput($input['harga_max'] ?? null);
        $this->filters['search'] = $this->sanitizeSearchInput($input['search'] ?? '');
        $this->filters['sort'] = $this->validateSortInput($input['sort'] ?? 'terbaru');
        $this->filters['page'] = max(1, (int)($input['page'] ?? 1));
        $this->filters['per_page'] = $this->sanitizePerPage($input['per_page'] ?? $this->per_page);
    }
    
    private function sanitizeArrayInput($input) {
        if (is_array($input)) {
            return array_map('intval', array_filter($input, 'is_numeric'));
        }
        if (is_numeric($input) && $input > 0) {
            return [(int)$input];
        }
        return [];
    }
    
    private function sanitizePriceInput($input) {
        if (is_numeric($input) && $input > 0) {
            return (int)$input;
        }
        return null;
    }
    
    private function sanitizeSearchInput($input) {
        return htmlspecialchars(trim($input), ENT_QUOTES, 'UTF-8');
    }
    
    private function validateSortInput($input) {
        return in_array($input, $this->sort_options) ? $input : 'terbaru';
    }
    
    private function sanitizePerPage($input) {
        $per_page_options = [12, 24, 48];
        $per_page = (int)$input;
        return in_array($per_page, $per_page_options) ? $per_page : $this->per_page;
    }
    
    public function getFilters() {
        return $this->filters;
    }
    
    public function getFilteredProducts() {
        return getAllProducts($this->filters);
    }
    
    public function getActiveFiltersCount() {
        $count = 0;
        $count += count($this->filters['kategori']);
        $count += count($this->filters['bahan']);
        $count += count($this->filters['warna']);
        if ($this->filters['harga_min'] !== null) $count++;
        if ($this->filters['harga_max'] !== null) $count++;
        if (!empty($this->filters['search'])) $count++;
        return $count;
    }
    
    public function getSort() {
        return $this->filters['sort'];
    }
    
    public function getPerPage() {
        return $this->filters['per_page'];
    }
    
    public function getQueryParams() {
        $params = [];
        
        // Add array filters
        if (!empty($this->filters['kategori'])) {
            foreach ($this->filters['kategori'] as $kategori) {
                $params['kategori[]'] = $kategori;
            }
        }
        
        if (!empty($this->filters['bahan'])) {
            foreach ($this->filters['bahan'] as $bahan) {
                $params['bahan[]'] = $bahan;
            }
        }
        
        if (!empty($this->filters['warna'])) {
            foreach ($this->filters['warna'] as $warna) {
                $params['warna[]'] = $warna;
            }
        }
        
        // Add other filters
        if ($this->filters['harga_min'] !== null) {
            $params['harga_min'] = $this->filters['harga_min'];
        }
        
        if ($this->filters['harga_max'] !== null) {
            $params['harga_max'] = $this->filters['harga_max'];
        }
        
        if (!empty($this->filters['search'])) {
            $params['search'] = $this->filters['search'];
        }
        
        $params['sort'] = $this->filters['sort'];
        $params['per_page'] = $this->filters['per_page'];
        
        return $params;
    }
    
    public function renderActiveFilters() {
        $html = '';
        
        // Categories
        if (!empty($this->filters['kategori'])) {
            $categories = getAllCategories();
            foreach ($categories as $cat) {
                if (in_array($cat['ID_Kategori'], $this->filters['kategori'])) {
                    $html .= $this->renderFilterChip('kategori', $cat['ID_Kategori'], $cat['Nama_Kategori']);
                }
            }
        }
        
        // Materials
        if (!empty($this->filters['bahan'])) {
            $materials = getAllMaterials();
            foreach ($materials as $mat) {
                if (in_array($mat['ID_Bahan'], $this->filters['bahan'])) {
                    $html .= $this->renderFilterChip('bahan', $mat['ID_Bahan'], $mat['Nama_Bahan']);
                }
            }
        }
        
        // Colors
        if (!empty($this->filters['warna'])) {
            $colors = getAllColors();
            foreach ($colors as $col) {
                if (in_array($col['ID_Warna'], $this->filters['warna'])) {
                    $html .= $this->renderFilterChip('warna', $col['ID_Warna'], $col['Nama_Warna']);
                }
            }
        }
        
        // Price range
        if ($this->filters['harga_min'] !== null || $this->filters['harga_max'] !== null) {
            $min = $this->filters['harga_min'] ? formatRupiah($this->filters['harga_min']) : '';
            $max = $this->filters['harga_max'] ? formatRupiah($this->filters['harga_max']) : '';
            $label = "Harga: " . ($min ? "{$min} - " : "0 - ") . ($max ?: "∞");
            $html .= '<span class="filter-chip d-inline-flex align-items-center me-2 mb-2">' . $label . 
                     '<button type="button" class="btn-close btn-close-sm ms-2" onclick="removePriceFilter()"></button></span>';
        }
        
        // Search
        if (!empty($this->filters['search'])) {
            $html .= '<span class="filter-chip d-inline-flex align-items-center me-2 mb-2">' .
                     'Pencarian: "' . htmlspecialchars($this->filters['search']) . '"' .
                     '<button type="button" class="btn-close btn-close-sm ms-2" onclick="removeSearchFilter()"></button></span>';
        }
        
        return $html;
    }
    
    private function renderFilterChip($type, $id, $label) {
        $url = $this->buildRemoveFilterUrl($type, $id);
        return '<span class="filter-chip d-inline-flex align-items-center me-2 mb-2">' .
               htmlspecialchars($label) .
               '<a href="' . $url . '" class="btn-close btn-close-sm ms-2"></a>' .
               '</span>';
    }
    
    private function buildRemoveFilterUrl($type, $id) {
        $params = $this->getQueryParams();
        
        // Remove the specific filter
        if ($type === 'kategori') {
            $params['kategori[]'] = array_diff($this->filters['kategori'], [$id]);
            if (empty($params['kategori[]'])) {
                unset($params['kategori[]']);
            }
        } elseif ($type === 'bahan') {
            $params['bahan[]'] = array_diff($this->filters['bahan'], [$id]);
            if (empty($params['bahan[]'])) {
                unset($params['bahan[]']);
            }
        } elseif ($type === 'warna') {
            $params['warna[]'] = array_diff($this->filters['warna'], [$id]);
            if (empty($params['warna[]'])) {
                unset($params['warna[]']);
            }
        }
        
        return 'shop.php?' . http_build_query($params);
    }
}