-- ==========================================
-- BAGIAN 1: PERSIAPAN DATABASE
-- ==========================================
-- Hapus tabel lama jika ada (Urutan penghapusan harus dari Child ke Parent)
DROP TABLE IF EXISTS Detail_Transaksi;
DROP TABLE IF EXISTS Transaksi;
DROP TABLE IF EXISTS Produk_Varian;
DROP TABLE IF EXISTS Produk_Induk;
DROP TABLE IF EXISTS Pelanggan;
DROP TABLE IF EXISTS Master_Warna;
DROP TABLE IF EXISTS Master_Bahan;
DROP TABLE IF EXISTS Master_Kategori;

-- ==========================================
-- BAGIAN 2: TABEL MASTER (LOOKUP TABLES)
-- ==========================================

-- 1. Master Kategori
CREATE TABLE Master_Kategori (
    ID_Kategori INT AUTO_INCREMENT PRIMARY KEY,
    Nama_Kategori VARCHAR(50) NOT NULL UNIQUE -- Contoh: Segi Empat, Pashmina
);

-- 2. Master Bahan (Solusi Robust untuk konsistensi material)
CREATE TABLE Master_Bahan (
    ID_Bahan INT AUTO_INCREMENT PRIMARY KEY,
    Nama_Bahan VARCHAR(50) NOT NULL UNIQUE, -- Contoh: Ceruty, Voal, Polycotton
    Deskripsi_Bahan TEXT -- Opsional: Penjelasan tekstur bahan untuk edukasi pelanggan
);

-- 3. Master Warna (Solusi Robust untuk konsistensi warna)
CREATE TABLE Master_Warna (
    ID_Warna INT AUTO_INCREMENT PRIMARY KEY,
    Nama_Warna VARCHAR(50) NOT NULL UNIQUE, -- Contoh: Navy, Maroon, Dusty Pink
    Kode_Hex VARCHAR(7) -- Opsional: Kode warna visual (misal #000080) untuk tampilan web
);

-- ==========================================
-- BAGIAN 3: TABEL PRODUK (PARENT-CHILD)
-- ==========================================

-- 4. Produk Induk (Parent)
-- Menyimpan informasi umum yang tidak berubah per warna
CREATE TABLE Produk_Induk (
    ID_Induk INT AUTO_INCREMENT PRIMARY KEY,
    Kode_Model VARCHAR(20) NOT NULL UNIQUE, -- Cth: MDL-001
    Nama_Produk VARCHAR(100) NOT NULL,      -- Cth: Pashmina Ceruty Premium
    ID_Kategori INT,
    ID_Bahan INT,
    Deskripsi_Lengkap TEXT,
    Created_At TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (ID_Kategori) REFERENCES Master_Kategori(ID_Kategori),
    FOREIGN KEY (ID_Bahan) REFERENCES Master_Bahan(ID_Bahan)
);

-- 5. Produk Varian (Child/SKU)
-- Menyimpan data spesifik seperti Stok dan Warna. Inilah yang dijual.
CREATE TABLE Produk_Varian (
    Kode_SKU VARCHAR(30) PRIMARY KEY,       -- Cth: MDL-001-BLK (Kombinasi Model + Warna)
    ID_Induk INT NOT NULL,
    ID_Warna INT NOT NULL,
    Harga_Jual INT NOT NULL,                -- Harga bisa beda tiap warna (opsional, tapi robust)
    Stok INT NOT NULL DEFAULT 0,
    Foto_Produk VARCHAR(255),               -- Foto spesifik warna tersebut
    Is_Active BOOLEAN DEFAULT TRUE,         -- Soft Delete: Jika barang discontinue, jangan dihapus datanya
    FOREIGN KEY (ID_Induk) REFERENCES Produk_Induk(ID_Induk) ON DELETE CASCADE,
    FOREIGN KEY (ID_Warna) REFERENCES Master_Warna(ID_Warna)
);

-- ==========================================
-- BAGIAN 4: TABEL TRANSAKSI
-- ==========================================

-- 6. Pelanggan
CREATE TABLE Pelanggan (
    ID_Pelanggan VARCHAR(15) PRIMARY KEY,   -- Cth: PLG-2023-001
    Nama_Pelanggan VARCHAR(100) NOT NULL,
    No_HP VARCHAR(15),
    Email VARCHAR(100),
    Alamat_Utama TEXT,
    Tanggal_Gabung DATE DEFAULT (CURRENT_DATE)
);

-- 7. Transaksi
CREATE TABLE Transaksi (
    No_Transaksi VARCHAR(20) PRIMARY KEY,   -- Cth: TRX-20231025-001
    ID_Pelanggan VARCHAR(15) NULL,          -- Nullable untuk Guest Checkout
    Tanggal_Transaksi DATETIME DEFAULT CURRENT_TIMESTAMP,
    
    -- Snapshot Data (Robustness: Simpan info saat transaksi terjadi)
    Nama_Penerima VARCHAR(100),
    Alamat_Pengiriman TEXT,                 -- Alamat saat beli (bisa beda dgn profil skrg)
    
    Status_Transaksi ENUM('Pending', 'Paid', 'Sent', 'Cancelled') DEFAULT 'Pending',
    Total_Bayar INT NOT NULL,
    FOREIGN KEY (ID_Pelanggan) REFERENCES Pelanggan(ID_Pelanggan) ON DELETE SET NULL
);

-- 8. Detail Transaksi
CREATE TABLE Detail_Transaksi (
    ID_Detail INT AUTO_INCREMENT PRIMARY KEY,
    No_Transaksi VARCHAR(20) NOT NULL,
    Kode_SKU VARCHAR(30) NOT NULL,
    
    Jumlah_Beli INT NOT NULL,
    Harga_Satuan_Snapshot INT NOT NULL,     -- Penting: Harga saat deal (mengatasi perubahan harga masa depan)
    Subtotal INT NOT NULL,                  -- Jumlah * Harga Snapshot
    
    FOREIGN KEY (No_Transaksi) REFERENCES Transaksi(No_Transaksi) ON DELETE RESTRICT,
    FOREIGN KEY (Kode_SKU) REFERENCES Produk_Varian(Kode_SKU)
);

-- ==========================================
-- BAGIAN 5: CONTOH DATA (DUMMY DATA)
-- ==========================================

-- 1. Isi Master Data
INSERT INTO Master_Kategori (Nama_Kategori) VALUES ('Segi Empat'), ('Pashmina'), ('Instan');
INSERT INTO Master_Bahan (Nama_Bahan) VALUES ('Ceruty Babydoll'), ('Voal Premium'), ('Polycotton');
INSERT INTO Master_Warna (Nama_Warna, Kode_Hex) VALUES ('Hitam', '#000000'), ('Broken White', '#F4F6F0'), ('Maroon', '#800000'), ('Navy', '#000080');

-- 2. Isi Produk Induk (Modelnya)
INSERT INTO Produk_Induk (Kode_Model, Nama_Produk, ID_Kategori, ID_Bahan, Deskripsi_Lengkap) VALUES 
('MDL-01', 'Pashmina Ceruty Basic', 2, 1, 'Pashmina jatuh dan mudah dibentuk.'),
('MDL-02', 'Segi Empat Voal Paris', 1, 2, 'Tegak di dahi dan adem.');

-- 3. Isi Produk Varian (Barang Fisiknya)
-- Perhatikan Kode SKU: Model + Kode Warna
INSERT INTO Produk_Varian (Kode_SKU, ID_Induk, ID_Warna, Harga_Jual, Stok, Foto_Produk) VALUES 
('MDL-01-BLK', 1, 1, 45000, 50, 'pashmina_ceruty_hitam.jpg'), -- Stok Banyak (Hype Candidate)
('MDL-01-MRN', 1, 3, 45000, 4,  'pashmina_ceruty_maroon.jpg'),-- Stok Sedikit (Clearance Candidate)
('MDL-02-NVY', 2, 4, 30000, 100, 'segiempat_voal_navy.jpg');

-- 4. Isi Pelanggan
INSERT INTO Pelanggan (ID_Pelanggan, Nama_Pelanggan, No_HP, Alamat_Utama) VALUES 
('PLG-001', 'Teh Anisa', '08123456789', 'Jl. Sakinah No. 1, Bandung');

-- 5. Simulasi Transaksi (Status Paid)
INSERT INTO Transaksi (No_Transaksi, ID_Pelanggan, Total_Bayar, Status_Transaksi, Nama_Penerima, Alamat_Pengiriman) VALUES 
('TRX-001', 'PLG-001', 90000, 'Paid', 'Anisa', 'Jl. Sakinah No. 1, Bandung');

-- 6. Simulasi Detail (Beli 2 pcs Pashmina Hitam)
-- Ini akan membuat Pashmina Hitam jadi produk Hype
INSERT INTO Detail_Transaksi (No_Transaksi, Kode_SKU, Jumlah_Beli, Harga_Satuan_Snapshot, Subtotal) VALUES 
('TRX-001', 'MDL-01-BLK', 2, 45000, 90000);