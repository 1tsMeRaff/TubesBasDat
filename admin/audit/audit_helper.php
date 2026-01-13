<?php
function auditLog($conn, $kodeSKU, $oldValue, $newValue, $jenis, $admin)
{
    if ($jenis === 'STOK') {
        mysqli_query($conn, "
            INSERT INTO audit_stock_changes
            (Kode_SKU, Old_Stock, New_Stock, Reason, Changed_By)
            VALUES
            ('$kodeSKU', '$oldValue', '$newValue', 'Update Manual', '$admin')
        ");
    }

    if ($jenis === 'HARGA') {
        $percent = 0;
        if ($oldValue > 0) {
            $percent = (($newValue - $oldValue) / $oldValue) * 100;
        }

        mysqli_query($conn, "
            INSERT INTO audit_price_changes
            (Kode_SKU, Old_Price, New_Price, Percentage_Change, Changed_By)
            VALUES
            ('$kodeSKU', '$oldValue', '$newValue', '$percent', '$admin')
        ");
    }
}
