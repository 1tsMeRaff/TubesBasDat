# Sakinah Style - E-Commerce Setup Guide

## Overview
Sakinah Style adalah website e-commerce untuk toko fashion muslimah dengan sistem Parent-Child untuk produk (Induk vs Varian) dan Look-up tables untuk atribut.

## File Structure
```
TubesBasDat/
├── config/
│   └── database.php          # PDO Database Connection
├── includes/
│   ├── header.php            # Navigation Header
│   └── footer.php            # Footer Template
├── assets/
│   ├── css/
│   │   └── style.css         # Custom CSS (Sakinah Style Design)
│   └── images/
│       └── products/         # Product Images Folder
├── functions.php              # Business Logic Functions
├── index.php                  # Homepage
├── shop.php                   # Product Catalog with Filters
├── product.php                # Product Detail Page
├── cart.php                   # Shopping Cart
├── checkout.php               # Checkout & Transaction Processing
└── database.sql              # Database Schema
```

## Installation Steps

### 1. Database Setup
1. Open phpMyAdmin (http://localhost/phpmyadmin)
2. Create a new database named `hijabstore_db`
3. Import the `database.sql` file to create all tables and sample data

### 2. Configuration
The database configuration is in `config/database.php`. Default settings:
- **Host:** localhost
- **Database:** hijabstore_db
- **Username:** root
- **Password:** (empty for XAMPP)

If your database name is different, update it in `config/database.php`:
```php
define('DB_NAME', 'your_database_name');
```

### 3. File Permissions
Ensure the following directories are writable (for future image uploads):
- `assets/images/products/`

### 4. Access the Website
Open your browser and navigate to:
```
http://localhost/TubesBasDat/index.php
```

## Features

### 1. Homepage (index.php)
- **Hero Section:** "Sakinah Style: Anggun & Syar'i"
- **Sedang Hype Section:** Displays best-selling products (last 30 days)
- **Cuci Gudang Section:** Shows products with low stock (≤5 items)

### 2. Shop Page (shop.php)
- **Filter Sidebar:** Filter by Kategori, Bahan, Warna
- **Product Grid:** Displays products with pagination
- **Dynamic Filters:** All filters fetched from Master tables

### 3. Product Detail (product.php)
- **Variant Selection:** User must select a color/variant
- **Dynamic Price:** Price updates based on selected variant
- **Stock Display:** Shows available stock for each variant
- **Add to Cart:** Disabled if no variant selected or stock = 0

### 4. Shopping Cart (cart.php)
- **Session-based:** Uses PHP `$_SESSION['cart']`
- **Cart Structure:** `['kode_sku' => 'MDL-01-BLK', 'qty' => 2]`
- **Update/Remove:** Users can update quantities or remove items
- **Stock Validation:** Checks stock availability

### 5. Checkout (checkout.php)
- **Guest Checkout:** No login required
- **Form Fields:** Name, Address, WhatsApp
- **Transaction Processing:**
  1. Inserts into `Transaksi` table
  2. Inserts into `Detail_Transaksi` (with price snapshot)
  3. Updates/decreases `Stok` in `Produk_Varian`
  4. Clears cart session
  5. Shows success message with Order ID

## Database Schema Overview

### Master Tables (Lookup)
- `Master_Kategori`: Product categories
- `Master_Bahan`: Material types
- `Master_Warna`: Color options

### Product Tables
- `Produk_Induk`: Parent products (general info)
- `Produk_Varian`: Product variants/SKUs (specific colors, stock, price)

### Transaction Tables
- `Pelanggan`: Customer information
- `Transaksi`: Transaction headers
- `Detail_Transaksi`: Transaction details (with price snapshots)

## Key Functions (functions.php)

1. **getHypeProducts($limit)**
   - Joins Detail_Transaksi → Produk_Varian → Produk_Induk
   - Calculates total sold per Parent Product (last 30 days)
   - Returns top products

2. **getClearanceProducts($limit)**
   - Queries Produk_Varian where Stok <= 5 AND Stok > 0
   - Returns specific variants for "Cuci Gudang" section

3. **getAllProducts($filters)**
   - Filters by Category, Material, Color
   - Handles pagination
   - Returns products with price ranges

4. **getProductDetail($id_induk)**
   - Fetches Parent info + all available Variants
   - Returns colors, stock, prices for each variant

5. **formatRupiah($angka)**
   - Formats number to Indonesian Rupiah currency

6. **processCheckout($data)**
   - Processes complete checkout transaction
   - Uses database transactions for data integrity
   - Updates stock and creates transaction records

## Design System

### Colors
- **Primary:** Dusty Rose (#DCAE96)
- **Secondary:** Soft Beige (#F5E6E0)
- **Text:** Charcoal (#333333)
- **Accent:** Maroon (#800000) - for buttons

### Framework
- Bootstrap 5 (via CDN)
- Bootstrap Icons

## Testing Checklist

1. ✅ Homepage displays Hype and Clearance products
2. ✅ Shop page filters work correctly
3. ✅ Product detail requires variant selection
4. ✅ Cart adds/updates/removes items correctly
5. ✅ Checkout processes transactions
6. ✅ Stock decreases after checkout
7. ✅ Transaction numbers are generated correctly

## Troubleshooting

### Database Connection Error
- Check if MySQL is running in XAMPP
- Verify database name in `config/database.php`
- Ensure database exists and tables are created

### Products Not Showing
- Check if `database.sql` was imported correctly
- Verify sample data exists in tables
- Check browser console for errors

### Cart Not Working
- Ensure `session_start()` is called (handled in `config/database.php`)
- Check PHP session configuration
- Clear browser cookies if needed

### Images Not Displaying
- Ensure product images exist in `assets/images/products/`
- Check file permissions
- Verify image filenames match database records

## Next Steps (Optional Enhancements)

1. User Authentication System
2. Admin Panel for Product Management
3. Image Upload Functionality
4. Payment Gateway Integration
5. Order Tracking System
6. Email Notifications
7. Product Reviews & Ratings

## Support

For issues or questions, please check:
- Database schema in `database.sql`
- Function documentation in `functions.php`
- PHP error logs in XAMPP

---

**Sakinah Style** - Anggun & Syar'i ✨

