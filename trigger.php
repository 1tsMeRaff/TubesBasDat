<?php
// business-logic-trigger.php

/**
 * PROSES CHECKOUT dengan TRIGGER SUPPORT
 * Sekarang lebih sederhana karena trigger handle banyak validasi
 */
function processCheckoutWithTriggers($data) {
    $pdo = getDBConnection();
    
    try {
        $pdo->beginTransaction();
        
        // Generate transaction number
        $no_transaksi = 'TRX-' . date('Ymd') . '-' . str_pad(rand(1, 9999), 4, '0', STR_PAD_LEFT);
        
        // Step 1: Insert ke Transaksi
        $sqlTransaksi = "
            INSERT INTO Transaksi 
            (No_Transaksi, ID_Pelanggan, Nama_Penerima, Alamat_Pengiriman, Total_Bayar)
            VALUES (?, ?, ?, ?, 0)  -- Total dihitung trigger!
        ";
        
        $stmtTransaksi = $pdo->prepare($sqlTransaksi);
        $stmtTransaksi->execute([
            $no_transaksi,
            $data['user_id'] ?? null,
            $data['nama'],
            $data['alamat']
        ]);
        
        // Step 2: Insert ke Detail_Transaksi (TRIGGER akan jalan otomatis!)
        foreach ($data['cart_items'] as $item) {
            $sqlDetail = "
                INSERT INTO Detail_Transaksi 
                (No_Transaksi, Kode_SKU, Jumlah_Beli, Harga_Satuan_Snapshot, Subtotal)
                VALUES (?, ?, ?, 
                    (SELECT Harga_Jual FROM Produk_Varian WHERE Kode_SKU = ?), 
                    ? * (SELECT Harga_Jual FROM Produk_Varian WHERE Kode_SKU = ?)
                )
            ";
            
            $stmtDetail = $pdo->prepare($sqlDetail);
            $stmtDetail->execute([
                $no_transaksi,
                $item['kode_sku'],
                $item['qty'],
                $item['kode_sku'],  // Untuk harga snapshot
                $item['qty'],       // Untuk subtotal
                $item['kode_sku']   // Untuk harga dalam subtotal
            ]);
            
            // PERHATIAN: Tidak perlu update stok manual!
            // Trigger trg_update_stok_setelah_detail_transaksi akan handle!
        }
        
        $pdo->commit();
        
        return [
            'success' => true,
            'no_transaksi' => $no_transaksi,
            'message' => 'Pesanan berhasil! Stok otomatis terupdate oleh sistem.'
        ];
        
    } catch (PDOException $e) {
        $pdo->rollBack();
        
        // Tangkap error dari trigger!
        $error_message = $e->getMessage();
        
        // Format pesan error dari trigger untuk user-friendly
        if (strpos($error_message, 'Stok tidak mencukupi') !== false) {
            $message = "Maaf, stok produk tidak mencukupi. Silakan kurangi jumlah atau pilih produk lain.";
        } elseif (strpos($error_message, 'Produk tidak aktif') !== false) {
            $message = "Produk tidak tersedia untuk saat ini.";
        } else {
            $message = "Terjadi kesalahan sistem: " . $error_message;
        }
        
        return [
            'success' => false,
            'no_transaksi' => null,
            'message' => $message,
            'debug_error' => $error_message  // Hanya untuk development
        ];
    }
}

/**
 * UPDATE HARGA dengan TRIGGER SUPPORT
 */
function updateProductPriceWithTrigger($kode_sku, $new_price) {
    $pdo = getDBConnection();
    
    try {
        $sql = "UPDATE Produk_Varian SET Harga_Jual = ? WHERE Kode_SKU = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$new_price, $kode_sku]);
        
        // Cek apakah trigger mengkoreksi harga
        $sqlCheck = "SELECT Harga_Jual, Harga_Modal FROM Produk_Varian WHERE Kode_SKU = ?";
        $stmtCheck = $pdo->prepare($sqlCheck);
        $stmtCheck->execute([$kode_sku]);
        $result = $stmtCheck->fetch();
        
        if ($result['Harga_Jual'] != $new_price) {
            return [
                'success' => true,
                'message' => 'Harga berhasil diupdate (dikoreksi oleh sistem)',
                'final_price' => $result['Harga_Jual'],
                'was_corrected' => true
            ];
        }
        
        return [
            'success' => true,
            'message' => 'Harga berhasil diupdate',
            'final_price' => $result['Harga_Jual'],
            'was_corrected' => false
        ];
        
    } catch (PDOException $e) {
        // Tangkap error dari trigger trg_validasi_harga_jual
        if (strpos($e->getMessage(), 'Penurunan harga maksimal') !== false) {
            return [
                'success' => false,
                'message' => 'Penurunan harga tidak boleh lebih dari 50%'
            ];
        }
        
        return [
            'success' => false,
            'message' => 'Error: ' . $e->getMessage()
        ];
    }
}

/**
 * GET STOK dengan LOG dari Trigger
 */
function getStockWithAudit($kode_sku) {
    $pdo = getDBConnection();
    
    $sql = "
        SELECT 
            pv.Kode_SKU,
            pv.Stok,
            pi.Nama_Produk,
            -- Audit trail
            (SELECT COUNT(*) FROM Detail_Transaksi WHERE Kode_SKU = pv.Kode_SKU) as total_terjual,
            (SELECT Changed_At FROM Audit_Stock_Changes 
             WHERE Kode_SKU = pv.Kode_SKU 
             ORDER BY Changed_At DESC LIMIT 1) as last_stock_change,
            -- Log dari trigger
            (SELECT GROUP_CONCAT(CONCAT(Perubahan, ': ', Jumlah) SEPARATOR ', ') 
             FROM Log_Stok_Changes 
             WHERE Kode_SKU = pv.Kode_SKU 
             AND Created_At >= DATE_SUB(NOW(), INTERVAL 7 DAY)
            ) as recent_stock_movements
        FROM Produk_Varian pv
        JOIN Produk_Induk pi ON pv.ID_Induk = pi.ID_Induk
        WHERE pv.Kode_SKU = ?
    ";
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$kode_sku]);
    
    return $stmt->fetch();
}

/**
 * GET AUDIT REPORT (untuk monitoring trigger)
 */
function getTriggerAuditReport($start_date, $end_date) {
    $pdo = getDBConnection();
    
    $report = [];
    
    // 1. Audit Price Changes (dari trigger trg_audit_produk_changes)
    $sqlPrice = "
        SELECT * FROM Audit_Price_Changes 
        WHERE Changed_At BETWEEN ? AND ?
        ORDER BY Changed_At DESC
    ";
    $stmtPrice = $pdo->prepare($sqlPrice);
    $stmtPrice->execute([$start_date, $end_date]);
    $report['price_changes'] = $stmtPrice->fetchAll();
    
    // 2. Stock Changes (dari berbagai trigger)
    $sqlStock = "
        SELECT 
            ls.Kode_SKU,
            pi.Nama_Produk,
            ls.Perubahan,
            ls.Jumlah,
            ls.No_Transaksi,
            ls.Created_At,
            CASE 
                WHEN ls.No_Transaksi IS NOT NULL THEN 'TRANSAKSI'
                ELSE 'MANUAL'
            END as Change_Type
        FROM Log_Stok_Changes ls
        JOIN Produk_Varian pv ON ls.Kode_SKU = pv.Kode_SKU
        JOIN Produk_Induk pi ON pv.ID_Induk = pi.ID_Induk
        WHERE ls.Created_At BETWEEN ? AND ?
        ORDER BY ls.Created_At DESC
    ";
    $stmtStock = $pdo->prepare($sqlStock);
    $stmtStock->execute([$start_date, $end_date]);
    $report['stock_changes'] = $stmtStock->fetchAll();
    
    // 3. System Logs (dari trigger warning/error)
    $sqlSystem = "
        SELECT * FROM System_Logs 
        WHERE Created_At BETWEEN ? AND ?
        AND Level IN ('WARNING', 'ERROR')
        ORDER BY Created_At DESC
    ";
    $stmtSystem = $pdo->prepare($sqlSystem);
    $stmtSystem->execute([$start_date, $end_date]);
    $report['system_logs'] = $stmtSystem->fetchAll();
    
    return $report;
}

/**
 * SIMULASI ERROR HANDLING dari Trigger
 * Untuk demo di UAS
 */
function demonstrateTriggerErrorHandling() {
    $pdo = getDBConnection();
    
    $test_cases = [];
    
    // Test Case 1: Stok tidak cukup
    try {
        $sql = "INSERT INTO Detail_Transaksi 
                (No_Transaksi, Kode_SKU, Jumlah_Beli, Harga_Satuan_Snapshot, Subtotal)
                VALUES ('TEST-001', 'SAK-DRS-0001-BL', 1000, 150000, 150000000)";
        $pdo->exec($sql);
        $test_cases[] = ['test' => 'Stok Overflow', 'result' => 'UNEXPECTED SUCCESS'];
    } catch (PDOException $e) {
        $test_cases[] = ['test' => 'Stok Overflow', 'result' => 'TRIGGER BLOCKED: ' . $e->getMessage()];
    }
    
    // Test Case 2: Harga minus
    try {
        $sql = "UPDATE Produk_Varian SET Harga_Jual = -5000 WHERE Kode_SKU = 'SAK-DRS-0001-BL'";
        $pdo->exec($sql);
        $test_cases[] = ['test' => 'Harga Negatif', 'result' => 'UNEXPECTED SUCCESS'];
    } catch (PDOException $e) {
        $test_cases[] = ['test' => 'Harga Negatif', 'result' => 'CONSTRAINT BLOCKED'];
    }
    
    // Test Case 3: Delete produk yang sudah terjual
    try {
        // First, create a transaction
        $pdo->exec("INSERT INTO Transaksi VALUES ('TEST-DEL-001', NULL, 'Test', 'Test', 1000, 'Pending', NOW())");
        $pdo->exec("INSERT INTO Detail_Transaksi VALUES (NULL, 'TEST-DEL-001', 'SAK-DRS-0001-BL', 1, 150000, 150000)");
        
        // Try to delete
        $pdo->exec("DELETE FROM Produk_Varian WHERE Kode_SKU = 'SAK-DRS-0001-BL'");
        $test_cases[] = ['test' => 'Delete Produk Terjual', 'result' => 'UNEXPECTED SUCCESS'];
    } catch (PDOException $e) {
        $test_cases[] = ['test' => 'Delete Produk Terjual', 'result' => 'TRIGGER BLOCKED: ' . $e->getMessage()];
    }
    
    // Cleanup
    $pdo->exec("DELETE FROM Detail_Transaksi WHERE No_Transaksi = 'TEST-DEL-001'");
    $pdo->exec("DELETE FROM Transaksi WHERE No_Transaksi = 'TEST-DEL-001'");
    
    return $test_cases;
}