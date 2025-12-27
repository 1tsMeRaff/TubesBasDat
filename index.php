<?php
/**
 * Homepage - Sakinah Style
 * Features: Hero Section, Hype Products, Clearance Products
 */

require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/functions.php';

$page_title = "Sakinah Style - Anggun & Syar'i";
$page_description = "Toko fashion muslimah terpercaya dengan koleksi terbaru dan berkualitas";

// Get featured products
$hype_products = getHypeProducts(8);
$clearance_products = getClearanceProducts(8);

include __DIR__ . '/includes/header.php';
?>

<!-- Hero Carousel -->
<section class="hero-carousel">
    <div id="heroCarousel" class="carousel slide" data-bs-ride="carousel" data-bs-interval="5000">
        <div class="carousel-indicators">
            <button type="button" data-bs-target="#heroCarousel" data-bs-slide-to="0" class="active"></button>
            <button type="button" data-bs-target="#heroCarousel" data-bs-slide-to="1"></button>
            <button type="button" data-bs-target="#heroCarousel" data-bs-slide-to="2"></button>
        </div>
        <div class="carousel-inner">
            <div class="carousel-item active" style="background-image: url('https://images.unsplash.com/photo-1594633312681-425c7b97ccd1?w=1920&q=80');">
                <div class="carousel-caption">
                    <h1 class="fade-in-up">Sakinah Style:<br>Anggun & Syar'i</h1>
                    <p class="fade-in-up">Temukan koleksi fashion muslimah terbaru dengan kualitas terbaik. Setiap produk dipilih dengan teliti untuk memberikan kenyamanan dan gaya yang elegan.</p>
                    <a href="shop.php" class="btn btn-primary btn-lg fade-in-up">
                        <i class="bi bi-bag"></i> Belanja Sekarang
                    </a>
                </div>
            </div>
            <div class="carousel-item" style="background-image: url('https://images.unsplash.com/photo-1583292650898-7d22cd27ca6f?w=1920&q=80');">
                <div class="carousel-caption">
                    <h1 class="fade-in-up">Koleksi Terbaru<br>Muslimah Modern</h1>
                    <p class="fade-in-up">Dari pashmina elegan hingga segi empat yang praktis, temukan gaya yang sesuai dengan kepribadian Anda.</p>
                    <a href="shop.php" class="btn btn-primary btn-lg fade-in-up">
                        <i class="bi bi-bag"></i> Belanja Sekarang
                    </a>
                </div>
            </div>
            <div class="carousel-item" style="background-image: url('https://images.unsplash.com/photo-1556306535-0f09a537f0a3?w=1920&q=80');">
                <div class="carousel-caption">
                    <h1 class="fade-in-up">Bahan Premium<br>Jahitan Berkualitas</h1>
                    <p class="fade-in-up">Setiap produk dibuat dengan bahan pilihan dan jahitan rapi untuk kenyamanan maksimal.</p>
                    <a href="shop.php" class="btn btn-primary btn-lg fade-in-up">
                        <i class="bi bi-bag"></i> Belanja Sekarang
                    </a>
                </div>
            </div>
        </div>
        <button class="carousel-control-prev" type="button" data-bs-target="#heroCarousel" data-bs-slide="prev">
            <span class="carousel-control-prev-icon" aria-hidden="true"></span>
            <span class="visually-hidden">Previous</span>
        </button>
        <button class="carousel-control-next" type="button" data-bs-target="#heroCarousel" data-bs-slide="next">
            <span class="carousel-control-next-icon" aria-hidden="true"></span>
            <span class="visually-hidden">Next</span>
        </button>
    </div>
</section>

<!-- Category Grid -->
<section class="section">
    <div class="container">
        <h2 class="section-title">Koleksi Kami</h2>
        <div class="category-grid">
            <div class="category-item hover-lift">
                <a href="<?php echo SITE_URL; ?>/shop.php?kategori=1">
                    <div class="category-image">
                        <img src="https://images.unsplash.com/photo-1594633312681-425c7b97ccd1?w=400&q=80" alt="Segi Empat">
                    </div>
                    <h5>Segi Empat</h5>
                </a>
            </div>
            <div class="category-item hover-lift">
                <a href="<?php echo SITE_URL; ?>/shop.php?kategori=2">
                    <div class="category-image">
                        <img src="https://images.unsplash.com/photo-1583292650898-7d22cd27ca6f?w=400&q=80" alt="Pashmina">
                    </div>
                    <h5>Pashmina</h5>
                </a>
            </div>
            <div class="category-item hover-lift">
                <a href="<?php echo SITE_URL; ?>/shop.php?kategori=3">
                    <div class="category-image">
                        <img src="https://images.unsplash.com/photo-1556306535-0f09a537f0a3?w=400&q=80" alt="Instan">
                    </div>
                    <h5>Instan</h5>
                </a>
            </div>
            <div class="category-item hover-lift">
                <a href="<?php echo SITE_URL; ?>/shop.php">
                    <div class="category-image">
                        <img src="https://images.unsplash.com/photo-1594633313593-bab3825d0caf?w=400&q=80" alt="Accessories">
                    </div>
                    <h5>Accessories</h5>
                </a>
            </div>
        </div>
    </div>
</section>

<!-- Features Section -->
<section class="features-section">
    <div class="container">
        <div class="row g-4">
            <div class="col-lg-3 col-md-6">
                <div class="feature-item hover-lift">
                    <div class="feature-icon">
                        <i class="bi bi-star-fill"></i>
                    </div>
                    <h5>Bahan Premium</h5>
                    <p>Setiap produk menggunakan bahan berkualitas tinggi untuk kenyamanan maksimal</p>
                </div>
            </div>
            <div class="col-lg-3 col-md-6">
                <div class="feature-item hover-lift">
                    <div class="feature-icon">
                        <i class="bi bi-scissors"></i>
                    </div>
                    <h5>Jahitan Rapi</h5>
                    <p>Dijahit dengan teliti oleh pengrajin berpengalaman untuk hasil yang sempurna</p>
                </div>
            </div>
            <div class="col-lg-3 col-md-6">
                <div class="feature-item hover-lift">
                    <div class="feature-icon">
                        <i class="bi bi-truck"></i>
                    </div>
                    <h5>Pengiriman Cepat</h5>
                    <p>Pengiriman cepat dan aman ke seluruh Indonesia dengan packing yang rapi</p>
                </div>
            </div>
            <div class="col-lg-3 col-md-6">
                <div class="feature-item hover-lift">
                    <div class="feature-icon">
                        <i class="bi bi-shield-check"></i>
                    </div>
                    <h5>Garansi Retur</h5>
                    <p>Garansi retur 7 hari jika produk tidak sesuai atau ada kerusakan</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Sedang Hype Section -->
<section id="hype-products" class="section">
    <div class="container">
        <h2 class="section-title">Sedang Hype!</h2>
        
        <?php if (empty($hype_products)): ?>
            <div class="alert alert-info text-center">
                <i class="bi bi-info-circle"></i> Belum ada produk yang sedang hype. 
                <a href="shop.php" class="alert-link">Lihat semua produk</a>
            </div>
        <?php else: ?>
            <div class="row g-4">
                <?php foreach ($hype_products as $product): ?>
                    <div class="col-lg-3 col-md-4 col-sm-6">
                        <div class="product-card position-relative">
                            <span class="product-card-badge badge-best-seller">
                                <i class="bi bi-fire"></i> Best Seller
                            </span>
                            
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
                                
                                <div class="product-card-body">
                                    <h5 class="product-card-title">
                                        <?php echo htmlspecialchars($product['Nama_Produk']); ?>
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
                                    <small class="text-muted">
                                        <i class="bi bi-cart-check"></i> Terjual <?php echo $product['Total_Terjual']; ?> pcs
                                    </small>
                                </div>
                            </a>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
            
            <div class="text-center mt-4">
                <a href="shop.php" class="btn btn-outline-primary">
                    Lihat Semua Produk <i class="bi bi-arrow-right"></i>
                </a>
            </div>
        <?php endif; ?>
    </div>
</section>

<!-- Cuci Gudang / Last Piece Section -->
<section id="clearance-products" class="section section-alt">
    <div class="container">
        <h2 class="section-title">Cuci Gudang / Last Piece</h2>
        
        <?php if (empty($clearance_products)): ?>
            <div class="alert alert-info text-center">
                <i class="bi bi-info-circle"></i> Tidak ada produk dengan stok terbatas saat ini.
            </div>
        <?php else: ?>
            <div class="row g-4">
                <?php foreach ($clearance_products as $product): ?>
                    <div class="col-lg-3 col-md-4 col-sm-6">
                        <div class="product-card position-relative">
                            <?php if ($product['Stok'] <= 2): ?>
                                <span class="product-card-badge badge-low-stock">
                                    <i class="bi bi-exclamation-triangle"></i> Last Piece!
                                </span>
                            <?php else: ?>
                                <span class="product-card-badge badge-low-stock">
                                    <i class="bi bi-tag"></i> Stok Terbatas
                                </span>
                            <?php endif; ?>
                            
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
                                
                                <div class="product-card-body">
                                    <h5 class="product-card-title">
                                        <?php echo htmlspecialchars($product['Nama_Produk']); ?>
                                    </h5>
                                    <p class="text-muted small mb-2">
                                        Warna: <?php echo htmlspecialchars($product['Nama_Warna']); ?>
                                    </p>
                                    <div class="product-card-price">
                                        <?php echo formatRupiah($product['Harga_Jual']); ?>
                                    </div>
                                    <div class="product-card-stock">
                                        <i class="bi bi-box"></i> Stok: <?php echo $product['Stok']; ?> pcs
                                    </div>
                                </div>
                            </a>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
            
            <div class="text-center mt-4">
                <a href="shop.php" class="btn btn-primary">
                    Lihat Semua Produk <i class="bi bi-arrow-right"></i>
                </a>
            </div>
        <?php endif; ?>
    </div>
</section>

<!-- Newsletter Section -->
<section class="newsletter-section" style="background-image: url('https://images.unsplash.com/photo-1594633312681-425c7b97ccd1?w=1920&q=80');">
    <div class="container">
        <div class="newsletter-content">
            <h2>Dapatkan Update Koleksi Terbaru</h2>
            <p class="mb-4">Berlangganan newsletter kami untuk mendapatkan informasi produk baru dan penawaran spesial</p>
            <form class="newsletter-form" method="POST" action="#">
                <input type="email" class="form-control" placeholder="Masukkan email Anda" required>
                <button type="submit" class="btn-custom">
                    <i class="bi bi-envelope"></i> Berlangganan
                </button>
            </form>
        </div>
    </div>
</section>

<?php include __DIR__ . '/includes/footer.php'; ?>
