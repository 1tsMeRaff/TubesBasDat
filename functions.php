<?php
/**
 * Business Logic Functions
 * Sakinah Style E-Commerce
 */

require_once __DIR__ . '/config/database.php';

/**
 * Format number to Rupiah currency
 * @param int $angka
 * @return string
 */
function formatRupiah($angka) {
    return 'Rp ' . number_format($angka, 0, ',', '.');
}

/**
 * Check if hex color is dark
 * @param string $hex
 * @return bool
 */
function isDarkColor($hex) {
    if (!$hex) return false;
    $hex = str_replace('#', '', $hex);
    if (strlen($hex) != 6) return false;
    $r = hexdec(substr($hex, 0, 2));
    $g = hexdec(substr($hex, 2, 2));
    $b = hexdec(substr($hex, 4, 2));
    $brightness = (($r * 299) + ($g * 587) + ($b * 114)) / 1000;
    return $brightness < 128;
}

/**
 * Get Hype Products (Best Sellers in last 30 days)
 * @param int $limit
 * @return array
 */
function getHypeProducts($limit = 8) {
    $pdo = getDBConnection();
    
    try {
        $sql = "
            SELECT 
                pi.ID_Induk,
                pi.Kode_Model,
                pi.Nama_Produk,
                pi.Deskripsi_Lengkap,
                mk.Nama_Kategori,
                mb.Nama_Bahan,
                MIN(pv.Harga_Jual) as Harga_Min,
                MAX(pv.Harga_Jual) as Harga_Max,
                GROUP_CONCAT(DISTINCT pv.Foto_Produk ORDER BY pv.Foto_Produk LIMIT 1) as Foto_Produk,
                SUM(dt.Jumlah_Beli) as Total_Terjual
            FROM Produk_Induk pi
            INNER JOIN Produk_Varian pv ON pi.ID_Induk = pv.ID_Induk
            INNER JOIN Detail_Transaksi dt ON pv.Kode_SKU = dt.Kode_SKU
            INNER JOIN Transaksi t ON dt.No_Transaksi = t.No_Transaksi
            LEFT JOIN Master_Kategori mk ON pi.ID_Kategori = mk.ID_Kategori
            LEFT JOIN Master_Bahan mb ON pi.ID_Bahan = mb.ID_Bahan
            WHERE t.Tanggal_Transaksi >= DATE_SUB(NOW(), INTERVAL 30 DAY)
                AND pv.Is_Active = 1
            GROUP BY pi.ID_Induk, pi.Kode_Model, pi.Nama_Produk, pi.Deskripsi_Lengkap, mk.Nama_Kategori, mb.Nama_Bahan
            ORDER BY Total_Terjual DESC
            LIMIT :limit
        ";
        
        $stmt = $pdo->prepare($sql);
        $stmt->bindValue(':limit', (int)$limit, PDO::PARAM_INT);
        $stmt->execute();
        
        return $stmt->fetchAll();
    } catch (PDOException $e) {
        error_log("Error in getHypeProducts: " . $e->getMessage());
        return [];
    }
}

/**
 * Get Clearance Products (Low Stock <= 5)
 * @param int $limit
 * @return array
 */
function getClearanceProducts($limit = 8) {
    $pdo = getDBConnection();
    
    try {
        $sql = "
            SELECT 
                pv.Kode_SKU,
                pv.ID_Induk,
                pv.Harga_Jual,
                pv.Stok,
                pv.Foto_Produk,
                pi.Nama_Produk,
                pi.Kode_Model,
                mw.Nama_Warna,
                mk.Nama_Kategori,
                mb.Nama_Bahan
            FROM Produk_Varian pv
            INNER JOIN Produk_Induk pi ON pv.ID_Induk = pi.ID_Induk
            INNER JOIN Master_Warna mw ON pv.ID_Warna = mw.ID_Warna
            LEFT JOIN Master_Kategori mk ON pi.ID_Kategori = mk.ID_Kategori
            LEFT JOIN Master_Bahan mb ON pi.ID_Bahan = mb.ID_Bahan
            WHERE pv.Stok <= 5 
                AND pv.Stok > 0
                AND pv.Is_Active = 1
            ORDER BY pv.Stok ASC, pv.Harga_Jual ASC
            LIMIT :limit
        ";
        
        $stmt = $pdo->prepare($sql);
        $stmt->bindValue(':limit', (int)$limit, PDO::PARAM_INT);
        $stmt->execute();
        
        return $stmt->fetchAll();
    } catch (PDOException $e) {
        error_log("Error in getClearanceProducts: " . $e->getMessage());
        return [];
    }
}

/**
 * Get All Products with Advanced Filters and Sorting
 * @param array $filters [
 *     'kategori' => array|int, 
 *     'bahan' => array|int, 
 *     'warna' => array|int, 
 *     'harga_min' => int,
 *     'harga_max' => int,
 *     'sort' => string ('termurah'|'termahal'|'terbaru'|'terlaris'),
 *     'page' => int, 
 *     'per_page' => int
 * ]
 * @return array ['products' => array, 'total' => int, 'current_page' => int, 'total_pages' => int]
 */
function getAllProducts($filters = []) {
    $pdo = getDBConnection();
    
    // Handle single values or arrays
    $kategori = $filters['kategori'] ?? null;
    $bahan = $filters['bahan'] ?? null;
    $warna = $filters['warna'] ?? null;
    $harga_min = isset($filters['harga_min']) ? (int)$filters['harga_min'] : null;
    $harga_max = isset($filters['harga_max']) ? (int)$filters['harga_max'] : null;
    $sort = $filters['sort'] ?? 'terbaru';
    $page = max(1, $filters['page'] ?? 1);
    $per_page = max(1, $filters['per_page'] ?? 12);
    $offset = ($page - 1) * $per_page;
    
    // Normalize to arrays
    if ($kategori && !is_array($kategori)) {
        $kategori = [$kategori];
    }
    if ($bahan && !is_array($bahan)) {
        $bahan = [$bahan];
    }
    if ($warna && !is_array($warna)) {
        $warna = [$warna];
    }
    
    try {
        // Build WHERE clause
        $where = ["pv.Is_Active = 1"];
        $params = [];
        $paramCounter = 0;
        
        // Category filter (IN clause)
        if ($kategori && !empty($kategori)) {
            $placeholders = [];
            foreach ($kategori as $kat) {
                $key = ':kategori_' . $paramCounter++;
                $placeholders[] = $key;
                $params[$key] = (int)$kat;
            }
            $where[] = "pi.ID_Kategori IN (" . implode(', ', $placeholders) . ")";
        }
        
        // Material filter (IN clause)
        if ($bahan && !empty($bahan)) {
            $placeholders = [];
            foreach ($bahan as $bah) {
                $key = ':bahan_' . $paramCounter++;
                $placeholders[] = $key;
                $params[$key] = (int)$bah;
            }
            $where[] = "pi.ID_Bahan IN (" . implode(', ', $placeholders) . ")";
        }
        
        // Color filter - Only show products if at least one variant has the selected color
        if ($warna && !empty($warna)) {
            $placeholders = [];
            foreach ($warna as $war) {
                $key = ':warna_' . $paramCounter++;
                $placeholders[] = $key;
                $params[$key] = (int)$war;
            }
            $where[] = "pi.ID_Induk IN (
                SELECT DISTINCT pv2.ID_Induk 
                FROM Produk_Varian pv2 
                WHERE pv2.ID_Warna IN (" . implode(', ', $placeholders) . ")
                AND pv2.Is_Active = 1
            )";
        }
        
        // Price range filter
        if ($harga_min !== null && $harga_min > 0) {
            $where[] = "pv.Harga_Jual >= :harga_min";
            $params[':harga_min'] = $harga_min;
        }
        if ($harga_max !== null && $harga_max > 0) {
            $where[] = "pv.Harga_Jual <= :harga_max";
            $params[':harga_max'] = $harga_max;
        }
        
        $whereClause = implode(' AND ', $where);
        
        // Build ORDER BY clause based on sort option
        $orderBy = "pi.Created_At DESC"; // Default
        switch ($sort) {
            case 'termurah':
                $orderBy = "Harga_Min ASC";
                break;
            case 'termahal':
                $orderBy = "Harga_Max DESC";
                break;
            case 'terbaru':
                $orderBy = "pi.Created_At DESC";
                break;
            case 'terlaris':
                // Need to join with Detail_Transaksi for sales count
                $orderBy = "Total_Terjual DESC";
                break;
        }
        
        // Count total - Use subquery to handle color filter correctly
        $countSql = "
            SELECT COUNT(DISTINCT pi.ID_Induk) as total
            FROM Produk_Induk pi
            INNER JOIN Produk_Varian pv ON pi.ID_Induk = pv.ID_Induk
            WHERE {$whereClause}
        ";
        
        $countStmt = $pdo->prepare($countSql);
        foreach ($params as $key => $value) {
            $countStmt->bindValue($key, $value, is_int($value) ? PDO::PARAM_INT : PDO::PARAM_STR);
        }
        $countStmt->execute();
        $total = $countStmt->fetch()['total'];
        
        // Get products with sales count for sorting
        $salesJoin = "";
        $salesSelect = ", 0 as Total_Terjual";
        if ($sort == 'terlaris') {
            $salesJoin = "
                LEFT JOIN (
                    SELECT pv3.ID_Induk, SUM(dt.Jumlah_Beli) as Total_Terjual
                    FROM Produk_Varian pv3
                    INNER JOIN Detail_Transaksi dt ON pv3.Kode_SKU = dt.Kode_SKU
                    GROUP BY pv3.ID_Induk
                ) sales ON pi.ID_Induk = sales.ID_Induk
            ";
            $salesSelect = ", COALESCE(sales.Total_Terjual, 0) as Total_Terjual";
        }
        
        $sql = "
            SELECT 
                pi.ID_Induk,
                pi.Kode_Model,
                pi.Nama_Produk,
                pi.Deskripsi_Lengkap,
                pi.Created_At,
                mk.Nama_Kategori,
                mb.Nama_Bahan,
                MIN(pv.Harga_Jual) as Harga_Min,
                MAX(pv.Harga_Jual) as Harga_Max,
                GROUP_CONCAT(DISTINCT pv.Foto_Produk ORDER BY pv.Foto_Produk LIMIT 1) as Foto_Produk,
                SUM(CASE WHEN pv.Stok > 0 THEN 1 ELSE 0 END) as Variant_Tersedia
                {$salesSelect}
            FROM Produk_Induk pi
            INNER JOIN Produk_Varian pv ON pi.ID_Induk = pv.ID_Induk
            LEFT JOIN Master_Kategori mk ON pi.ID_Kategori = mk.ID_Kategori
            LEFT JOIN Master_Bahan mb ON pi.ID_Bahan = mb.ID_Bahan
            {$salesJoin}
            WHERE {$whereClause}
            GROUP BY pi.ID_Induk, pi.Kode_Model, pi.Nama_Produk, pi.Deskripsi_Lengkap, pi.Created_At, mk.Nama_Kategori, mb.Nama_Bahan
            ORDER BY {$orderBy}
            LIMIT :limit OFFSET :offset
        ";
        
        $stmt = $pdo->prepare($sql);
        foreach ($params as $key => $value) {
            $stmt->bindValue($key, $value, is_int($value) ? PDO::PARAM_INT : PDO::PARAM_STR);
        }
        $stmt->bindValue(':limit', (int)$per_page, PDO::PARAM_INT);
        $stmt->bindValue(':offset', (int)$offset, PDO::PARAM_INT);
        $stmt->execute();
        
        $products = $stmt->fetchAll();
        $total_pages = ceil($total / $per_page);
        
        return [
            'products' => $products,
            'total' => $total,
            'current_page' => $page,
            'total_pages' => $total_pages
        ];
    } catch (PDOException $e) {
        error_log("Error in getAllProducts: " . $e->getMessage());
        return [
            'products' => [],
            'total' => 0,
            'current_page' => 1,
            'total_pages' => 0
        ];
    }
}

/**
 * Get Price Range for Products (Min and Max)
 * @return array ['min' => int, 'max' => int]
 */
function getPriceRange() {
    $pdo = getDBConnection();
    
    try {
        $sql = "
            SELECT 
                MIN(Harga_Jual) as min_price,
                MAX(Harga_Jual) as max_price
            FROM Produk_Varian
            WHERE Is_Active = 1
        ";
        
        $stmt = $pdo->query($sql);
        $result = $stmt->fetch();
        
        return [
            'min' => (int)($result['min_price'] ?? 0),
            'max' => (int)($result['max_price'] ?? 0)
        ];
    } catch (PDOException $e) {
        error_log("Error in getPriceRange: " . $e->getMessage());
        return ['min' => 0, 'max' => 1000000];
    }
}

/**
 * Render Bootstrap Pagination
 * @param int $total_pages
 * @param int $current_page
 * @param array $query_params Additional query parameters to preserve
 * @return string HTML
 */
function renderPagination($total_pages, $current_page, $query_params = []) {
    if ($total_pages <= 1) {
        return '';
    }
    
    // Build base URL with current query params (excluding page)
    $base_url = '?' . http_build_query($query_params);
    if (!empty($query_params)) {
        $base_url .= '&';
    } else {
        $base_url = '?';
    }
    
    $html = '<nav aria-label="Product pagination" class="mt-5"><ul class="pagination justify-content-center">';
    
    // Previous button
    if ($current_page > 1) {
        $html .= '<li class="page-item">';
        $html .= '<a class="page-link" href="' . $base_url . 'page=' . ($current_page - 1) . '">';
        $html .= '<i class="bi bi-chevron-left"></i> Sebelumnya</a></li>';
    }
    
    // Page numbers
    $start = max(1, $current_page - 2);
    $end = min($total_pages, $current_page + 2);
    
    if ($start > 1) {
        $html .= '<li class="page-item"><a class="page-link" href="' . $base_url . 'page=1">1</a></li>';
        if ($start > 2) {
            $html .= '<li class="page-item disabled"><span class="page-link">...</span></li>';
        }
    }
    
    for ($i = $start; $i <= $end; $i++) {
        $active = ($i == $current_page) ? ' active' : '';
        $html .= '<li class="page-item' . $active . '">';
        $html .= '<a class="page-link" href="' . $base_url . 'page=' . $i . '">' . $i . '</a></li>';
    }
    
    if ($end < $total_pages) {
        if ($end < $total_pages - 1) {
            $html .= '<li class="page-item disabled"><span class="page-link">...</span></li>';
        }
        $html .= '<li class="page-item"><a class="page-link" href="' . $base_url . 'page=' . $total_pages . '">' . $total_pages . '</a></li>';
    }
    
    // Next button
    if ($current_page < $total_pages) {
        $html .= '<li class="page-item">';
        $html .= '<a class="page-link" href="' . $base_url . 'page=' . ($current_page + 1) . '">';
        $html .= 'Selanjutnya <i class="bi bi-chevron-right"></i></a></li>';
    }
    
    $html .= '</ul></nav>';
    
    return $html;
}

/**
 * Get Product Detail by ID_Induk
 * @param int $id_induk
 * @return array|false
 */
function getProductDetail($id_induk) {
    $pdo = getDBConnection();
    
    try {
        // Get parent product info
        $sql = "
            SELECT 
                pi.*,
                mk.Nama_Kategori,
                mb.Nama_Bahan,
                mb.Deskripsi_Bahan
            FROM Produk_Induk pi
            LEFT JOIN Master_Kategori mk ON pi.ID_Kategori = mk.ID_Kategori
            LEFT JOIN Master_Bahan mb ON pi.ID_Bahan = mb.ID_Bahan
            WHERE pi.ID_Induk = :id_induk
        ";
        
        $stmt = $pdo->prepare($sql);
        $stmt->bindValue(':id_induk', $id_induk, PDO::PARAM_INT);
        $stmt->execute();
        
        $product = $stmt->fetch();
        
        if (!$product) {
            return false;
        }
        
        // Get all variants
        $sqlVariants = "
            SELECT 
                pv.Kode_SKU,
                pv.Harga_Jual,
                pv.Stok,
                pv.Foto_Produk,
                mw.Nama_Warna,
                mw.Kode_Hex
            FROM Produk_Varian pv
            INNER JOIN Master_Warna mw ON pv.ID_Warna = mw.ID_Warna
            WHERE pv.ID_Induk = :id_induk
                AND pv.Is_Active = 1
            ORDER BY mw.Nama_Warna
        ";
        
        $stmtVariants = $pdo->prepare($sqlVariants);
        $stmtVariants->bindValue(':id_induk', $id_induk, PDO::PARAM_INT);
        $stmtVariants->execute();
        
        $product['variants'] = $stmtVariants->fetchAll();
        
        return $product;
    } catch (PDOException $e) {
        error_log("Error in getProductDetail: " . $e->getMessage());
        return false;
    }
}

/**
 * Get Variant by SKU
 * @param string $kode_sku
 * @return array|false
 */
function getVariantBySKU($kode_sku) {
    $pdo = getDBConnection();
    
    try {
        $sql = "
            SELECT 
                pv.*,
                pi.Nama_Produk,
                pi.Kode_Model,
                mw.Nama_Warna
            FROM Produk_Varian pv
            INNER JOIN Produk_Induk pi ON pv.ID_Induk = pi.ID_Induk
            INNER JOIN Master_Warna mw ON pv.ID_Warna = mw.ID_Warna
            WHERE pv.Kode_SKU = :kode_sku
                AND pv.Is_Active = 1
        ";
        
        $stmt = $pdo->prepare($sql);
        $stmt->bindValue(':kode_sku', $kode_sku, PDO::PARAM_STR);
        $stmt->execute();
        
        return $stmt->fetch();
    } catch (PDOException $e) {
        error_log("Error in getVariantBySKU: " . $e->getMessage());
        return false;
    }
}

/**
 * Get all categories
 * @return array
 */
function getAllCategories() {
    $pdo = getDBConnection();
    
    try {
        $sql = "SELECT * FROM Master_Kategori ORDER BY Nama_Kategori";
        $stmt = $pdo->query($sql);
        return $stmt->fetchAll();
    } catch (PDOException $e) {
        error_log("Error in getAllCategories: " . $e->getMessage());
        return [];
    }
}

/**
 * Get all materials (Bahan)
 * @return array
 */
function getAllMaterials() {
    $pdo = getDBConnection();
    
    try {
        $sql = "SELECT * FROM Master_Bahan ORDER BY Nama_Bahan";
        $stmt = $pdo->query($sql);
        return $stmt->fetchAll();
    } catch (PDOException $e) {
        error_log("Error in getAllMaterials: " . $e->getMessage());
        return [];
    }
}

/**
 * Get Related Products (same category or material)
 * @param int $id_induk Current product ID
 * @param int $limit Number of products to return
 * @return array
 */
function getRelatedProducts($id_induk, $limit = 4) {
    $pdo = getDBConnection();
    
    try {
        // Get current product's category and material
        $currentSql = "SELECT ID_Kategori, ID_Bahan FROM Produk_Induk WHERE ID_Induk = :id";
        $currentStmt = $pdo->prepare($currentSql);
        $currentStmt->execute([':id' => $id_induk]);
        $current = $currentStmt->fetch();
        
        if (!$current) {
            return [];
        }
        
        // Get related products (same category OR same material, excluding current)
        $sql = "
            SELECT 
                pi.ID_Induk,
                pi.Kode_Model,
                pi.Nama_Produk,
                mk.Nama_Kategori,
                mb.Nama_Bahan,
                MIN(pv.Harga_Jual) as Harga_Min,
                MAX(pv.Harga_Jual) as Harga_Max,
                GROUP_CONCAT(DISTINCT pv.Foto_Produk ORDER BY pv.Foto_Produk LIMIT 1) as Foto_Produk
            FROM Produk_Induk pi
            INNER JOIN Produk_Varian pv ON pi.ID_Induk = pv.ID_Induk
            LEFT JOIN Master_Kategori mk ON pi.ID_Kategori = mk.ID_Kategori
            LEFT JOIN Master_Bahan mb ON pi.ID_Bahan = mb.ID_Bahan
            WHERE pi.ID_Induk != :id_induk
                AND pv.Is_Active = 1
                AND (
                    pi.ID_Kategori = :kategori 
                    OR pi.ID_Bahan = :bahan
                )
            GROUP BY pi.ID_Induk, pi.Kode_Model, pi.Nama_Produk, mk.Nama_Kategori, mb.Nama_Bahan
            ORDER BY RAND()
            LIMIT :limit
        ";
        
        $stmt = $pdo->prepare($sql);
        $stmt->bindValue(':id_induk', $id_induk, PDO::PARAM_INT);
        $stmt->bindValue(':kategori', $current['ID_Kategori'], PDO::PARAM_INT);
        $stmt->bindValue(':bahan', $current['ID_Bahan'], PDO::PARAM_INT);
        $stmt->bindValue(':limit', (int)$limit, PDO::PARAM_INT);
        $stmt->execute();
        
        return $stmt->fetchAll();
    } catch (PDOException $e) {
        error_log("Error in getRelatedProducts: " . $e->getMessage());
        return [];
    }
}

/**
 * Get all colors (Warna)
 * @return array
 */
function getAllColors() {
    $pdo = getDBConnection();
    
    try {
        $sql = "SELECT * FROM Master_Warna ORDER BY Nama_Warna";
        $stmt = $pdo->query($sql);
        return $stmt->fetchAll();
    } catch (PDOException $e) {
        error_log("Error in getAllColors: " . $e->getMessage());
        return [];
    }
}

/**
 * Generate Transaction Number
 * @return string
 */
function generateTransactionNumber() {
    return 'TRX-' . date('Ymd') . '-' . str_pad(rand(1, 9999), 4, '0', STR_PAD_LEFT);
}

/**
 * Process Checkout with Database Transaction
 * @param array $data ['nama', 'alamat', 'whatsapp', 'email', 'user_id', 'cart_items', 'shipping_cost']
 * @return array ['success' => bool, 'no_transaksi' => string, 'message' => string, 'nama' => string]
 */
function processCheckout($data) {
    $pdo = getDBConnection();
    
    try {
        $pdo->beginTransaction();
        
        $no_transaksi = generateTransactionNumber();
        $nama = $data['nama'];
        $alamat = $data['alamat'];
        $whatsapp = $data['whatsapp'] ?? null;
        $email = $data['email'] ?? null;
        $user_id = $data['user_id'] ?? null; // Can be null for guest checkout
        $cart_items = $data['cart_items'];
        $shipping_cost = $data['shipping_cost'] ?? 10000; // Default Rp 10.000
        
        // Step 1: Create Guest Customer if needed
        if (!$user_id && ($email || $whatsapp)) {
            // Check if customer already exists by email or phone
            $checkSql = "SELECT ID_Pelanggan FROM Pelanggan WHERE ";
            $checkParams = [];
            
            if ($email) {
                $checkSql .= "Email = :email";
                $checkParams[':email'] = $email;
            }
            if ($whatsapp) {
                if ($email) {
                    $checkSql .= " OR No_HP = :phone";
                } else {
                    $checkSql .= "No_HP = :phone";
                }
                $checkParams[':phone'] = $whatsapp;
            }
            
            $checkStmt = $pdo->prepare($checkSql);
            $checkStmt->execute($checkParams);
            $existing = $checkStmt->fetch();
            
            if ($existing) {
                $user_id = $existing['ID_Pelanggan'];
            } else {
                // Create new guest customer
                $year = date('Y');
                $checkLast = $pdo->query("SELECT ID_Pelanggan FROM Pelanggan WHERE ID_Pelanggan LIKE 'PLG-{$year}-%' ORDER BY ID_Pelanggan DESC LIMIT 1");
                $lastId = $checkLast->fetch();
                
                if ($lastId) {
                    $lastNum = (int)substr($lastId['ID_Pelanggan'], -3);
                    $newNum = str_pad($lastNum + 1, 3, '0', STR_PAD_LEFT);
                } else {
                    $newNum = '001';
                }
                
                $user_id = "PLG-{$year}-{$newNum}";
                
                $insertCustomer = "
                    INSERT INTO Pelanggan (ID_Pelanggan, Nama_Pelanggan, Email, No_HP, Alamat_Utama, Role)
                    VALUES (:id, :nama, :email, :phone, :alamat, 'customer')
                ";
                
                $stmtCustomer = $pdo->prepare($insertCustomer);
                $stmtCustomer->execute([
                    ':id' => $user_id,
                    ':nama' => $nama,
                    ':email' => $email,
                    ':phone' => $whatsapp,
                    ':alamat' => $alamat
                ]);
            }
        }
        
        // Step 2: Validate stock and calculate subtotal
        $subtotal = 0;
        $stock_checks = [];
        
        foreach ($cart_items as $item) {
            $variant = getVariantBySKU($item['kode_sku']);
            if (!$variant) {
                throw new Exception("Produk tidak ditemukan: " . $item['kode_sku']);
            }
            
            // Check current stock
            if ($variant['Stok'] < $item['qty']) {
                throw new Exception("Stok tidak mencukupi untuk produk: " . $variant['Nama_Produk'] . " (Tersedia: " . $variant['Stok'] . " pcs)");
            }
            
            // Calculate what stock will be after purchase
            $stock_after = $variant['Stok'] - $item['qty'];
            if ($stock_after < 0) {
                throw new Exception("Stok habis untuk produk: " . $variant['Nama_Produk']);
            }
            
            $stock_checks[$item['kode_sku']] = [
                'current_stock' => $variant['Stok'],
                'qty' => $item['qty'],
                'stock_after' => $stock_after
            ];
            
            $subtotal += $variant['Harga_Jual'] * $item['qty'];
        }
        
        $total_bayar = $subtotal + $shipping_cost;
        
        // Step 3: Insert Transaction
        $sqlTransaksi = "
            INSERT INTO Transaksi (No_Transaksi, ID_Pelanggan, Nama_Penerima, Alamat_Pengiriman, Total_Bayar, Status_Transaksi)
            VALUES (:no_transaksi, :user_id, :nama, :alamat, :total_bayar, 'Pending')
        ";
        
        $stmtTransaksi = $pdo->prepare($sqlTransaksi);
        $stmtTransaksi->execute([
            ':no_transaksi' => $no_transaksi,
            ':user_id' => $user_id,
            ':nama' => $nama,
            ':alamat' => $alamat,
            ':total_bayar' => $total_bayar
        ]);
        
        // Step 4: Insert Detail Transaksi and Update Stock
        foreach ($cart_items as $item) {
            $variant = getVariantBySKU($item['kode_sku']);
            
            // Use Harga_Jual from database as snapshot price
            $harga_satuan = $variant['Harga_Jual'];
            $subtotal_item = $harga_satuan * $item['qty'];
            
            // Insert Detail Transaksi
            $sqlDetail = "
                INSERT INTO Detail_Transaksi (No_Transaksi, Kode_SKU, Jumlah_Beli, Harga_Satuan_Snapshot, Subtotal)
                VALUES (:no_transaksi, :kode_sku, :jumlah, :harga, :subtotal)
            ";
            
            $stmtDetail = $pdo->prepare($sqlDetail);
            $stmtDetail->execute([
                ':no_transaksi' => $no_transaksi,
                ':kode_sku' => $item['kode_sku'],
                ':jumlah' => $item['qty'],
                ':harga' => $harga_satuan,
                ':subtotal' => $subtotal_item
            ]);
            
            // Step 5: Decrease Stock with validation
            $sqlUpdateStock = "
                UPDATE Produk_Varian 
                SET Stok = Stok - :qty 
                WHERE Kode_SKU = :kode_sku
                AND Stok >= :qty
            ";
            
            $stmtUpdate = $pdo->prepare($sqlUpdateStock);
            $stmtUpdate->execute([
                ':qty' => $item['qty'],
                ':kode_sku' => $item['kode_sku']
            ]);
            
            // Verify stock was actually updated
            if ($stmtUpdate->rowCount() == 0) {
                throw new Exception("Stok habis untuk produk: " . $variant['Nama_Produk']);
            }
            
            // Double-check: Get updated stock to ensure it's not negative
            $checkStock = $pdo->prepare("SELECT Stok FROM Produk_Varian WHERE Kode_SKU = :kode_sku");
            $checkStock->execute([':kode_sku' => $item['kode_sku']]);
            $updatedStock = $checkStock->fetch()['Stok'];
            
            if ($updatedStock < 0) {
                throw new Exception("Stok tidak mencukupi untuk produk: " . $variant['Nama_Produk']);
            }
        }
        
        $pdo->commit();
        
        return [
            'success' => true,
            'no_transaksi' => $no_transaksi,
            'message' => 'Pesanan berhasil dibuat!',
            'nama' => $nama,
            'whatsapp' => $whatsapp
        ];
    } catch (Exception $e) {
        $pdo->rollBack();
        error_log("Error in processCheckout: " . $e->getMessage());
        return [
            'success' => false,
            'no_transaksi' => null,
            'message' => $e->getMessage(),
            'nama' => null,
            'whatsapp' => null
        ];
    }
}
?>

