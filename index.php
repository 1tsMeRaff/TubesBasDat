<?php
// index.php - LANDING PAGE UTAMA
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);

session_start();

// Include configuration
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/functions.php';

// Set page metadata
$page_title = "HijabStore - Marketplace Kerudung Terlengkap";
$page_description = "Temukan koleksi kerudung terbaru dari berbagai penjual terpercaya";
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($page_title); ?></title>
    <meta name="description" content="<?php echo htmlspecialchars($page_description); ?>">
    
    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    
    <!-- Custom CSS -->
    <link rel="stylesheet" href="assets/css/custom.css">
    
    <style>
        /* Inline CSS sebagai fallback */
        :root {
            --primary-color: #8B5FBF;
            --primary-light: #f8e9e1;
            --secondary-color: #2C3E50;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        .navbar-brand {
            font-weight: bold;
            color: var(--primary-color) !important;
        }
        
        .btn-primary {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
        }
        
        .hero-section {
            background: linear-gradient(135deg, var(--primary-light) 0%, #f0f4ff 100%);
            padding: 80px 0;
        }
        
        .product-card {
            transition: transform 0.3s;
            border: 1px solid #eee;
        }
        
        .product-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(0,0,0,0.1);
        }
    </style>
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm sticky-top">
        <div class="container">
            <a class="navbar-brand" href="#">
                <i class="bi bi-shop"></i> HijabStore
            </a>
            
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav mx-auto">
                    <li class="nav-item"><a class="nav-link active" href="#">Beranda</a></li>
                    <li class="nav-item"><a class="nav-link" href="#categories">Kategori</a></li>
                    <li class="nav-item"><a class="nav-link" href="#products">Produk</a></li>
                    <li class="nav-item"><a class="nav-link" href="#tutorial">Tutorial</a></li>
                    <li class="nav-item"><a class="nav-link" href="#contact">Kontak</a></li>
                </ul>
                
                <div class="d-flex gap-2">
                    <a href="pages/login.php" class="btn btn-outline-primary">Masuk</a>
                    <a href="pages/register.php" class="btn btn-primary">Daftar</a>
                    <a href="#" class="btn btn-light"><i class="bi bi-cart3"></i></a>
                </div>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="hero-section">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-6">
                    <h1 class="display-4 fw-bold mb-3">
                        Temukan <span class="text-primary">Kerudung Terbaik</span> untuk Gaya Muslimah Modern
                    </h1>
                    <p class="lead mb-4">
                        Marketplace pertama yang menghubungkan penjual kerudung berkualitas dengan pembeli cerdas.
                    </p>
                    
                    <div class="input-group mb-3 shadow" style="max-width: 500px;">
                        <input type="text" class="form-control" placeholder="Cari kerudung...">
                        <button class="btn btn-primary" type="button">Cari</button>
                    </div>
                    
                    <div class="d-flex gap-2">
                        <a href="#products" class="btn btn-primary btn-lg">Belanja Sekarang</a>
                        <a href="#tutorial" class="btn btn-outline-primary btn-lg">Lihat Tutorial</a>
                    </div>
                </div>
                
                <div class="col-lg-6">
                    <img src="https://images.unsplash.com/photo-1556306535-0f09a537f0a3?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80" 
                         class="img-fluid rounded shadow-lg" 
                         alt="Hijab Fashion">
                </div>
            </div>
        </div>
    </section>

    <!-- Categories -->
    <section id="categories" class="py-5 bg-light">
        <div class="container">
            <h2 class="text-center fw-bold mb-5">Kategori Populer</h2>
            
            <div class="row g-4">
                <?php
                $categories = [
                    ['Segi Empat', 'bi-square', 'primary', '1.200+'],
                    ['Pashmina', 'bi-scarf', 'success', '850+'],
                    ['Instan', 'bi-lightning', 'warning', '650+'],
                    ['Bergo', 'bi-capsule', 'info', '320+'],
                ];
                
                foreach ($categories as $cat) {
                    echo '
                    <div class="col-md-3 col-6">
                        <div class="card text-center border-0 shadow-sm h-100">
                            <div class="card-body p-4">
                                <i class="bi ' . $cat[1] . ' fs-1 text-' . $cat[2] . ' mb-3"></i>
                                <h5 class="fw-bold">' . $cat[0] . '</h5>
                                <p class="text-muted">' . $cat[3] . ' Produk</p>
                            </div>
                        </div>
                    </div>';
                }
                ?>
            </div>
        </div>
    </section>

    <!-- Products -->
    <section id="products" class="py-5">
        <div class="container">
            <h2 class="fw-bold mb-5">Produk Terbaru</h2>
            
            <div class="row g-4">
                <?php
                $products = [
                    ['Pashmina Silk Premium', 'Rp 89.000', 'Rp 129.000', 'BESTSELLER'],
                    ['Kerudung Segi Empat Katun', 'Rp 45.000', 'Rp 65.000', 'DISKON 30%'],
                    ['Hijab Instan Paris', 'Rp 75.000', '', 'NEW'],
                    ['Bergo Jersey Motif', 'Rp 55.000', 'Rp 75.000', 'LIMITED'],
                ];
                
                foreach ($products as $product) {
                    echo '
                    <div class="col-md-3 col-6">
                        <div class="card product-card h-100">
                            <div class="card-img-top bg-secondary" style="height: 200px;"></div>
                            <div class="card-body">
                                <span class="badge bg-danger">' . $product[3] . '</span>
                                <h6 class="card-title mt-2">' . $product[0] . '</h6>
                                <div class="text-warning mb-2">
                                    <i class="bi bi-star-fill"></i>
                                    <i class="bi bi-star-fill"></i>
                                    <i class="bi bi-star-fill"></i>
                                    <i class="bi bi-star-fill"></i>
                                    <i class="bi bi-star-half"></i>
                                    <span class="text-muted">(124)</span>
                                </div>
                                <div>
                                    <span class="fw-bold text-primary">' . $product[1] . '</span>';
                    if ($product[2]) {
                        echo ' <del class="text-muted small">' . $product[2] . '</del>';
                    }
                    echo '
                                </div>
                                <button class="btn btn-sm btn-outline-primary w-100 mt-2">
                                    <i class="bi bi-cart-plus"></i> Tambah
                                </button>
                            </div>
                        </div>
                    </div>';
                }
                ?>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-dark text-white py-5">
        <div class="container">
            <div class="row">
                <div class="col-lg-4 mb-4">
                    <h5 class="fw-bold">HijabStore</h5>
                    <p>Marketplace kerudung terpercaya untuk muslimah modern.</p>
                </div>
                <div class="col-lg-8">
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <h6>Belanja</h6>
                            <ul class="list-unstyled">
                                <li><a href="#" class="text-white-50 text-decoration-none">Semua Produk</a></li>
                                <li><a href="#" class="text-white-50 text-decoration-none">Kategori</a></li>
                                <li><a href="#" class="text-white-50 text-decoration-none">Flash Sale</a></li>
                            </ul>
                        </div>
                        <div class="col-md-4 mb-3">
                            <h6>Bantuan</h6>
                            <ul class="list-unstyled">
                                <li><a href="#" class="text-white-50 text-decoration-none">Cara Belanja</a></li>
                                <li><a href="#" class="text-white-50 text-decoration-none">Pengiriman</a></li>
                                <li><a href="#" class="text-white-50 text-decoration-none">Retur Produk</a></li>
                            </ul>
                        </div>
                        <div class="col-md-4 mb-3">
                            <h6>Kontak</h6>
                            <ul class="list-unstyled">
                                <li><i class="bi bi-envelope"></i> hello@hijabstore.com</li>
                                <li><i class="bi bi-whatsapp"></i> +62 812-3456-7890</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
            
            <hr class="bg-white-50">
            
            <div class="text-center text-white-50">
                <p>&copy; <?php echo date('Y'); ?> HijabStore. All rights reserved.</p>
            </div>
        </div>
    </footer>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
    // Smooth scroll
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function (e) {
            e.preventDefault();
            const target = document.querySelector(this.getAttribute('href'));
            if (target) {
                target.scrollIntoView({ behavior: 'smooth' });
            }
        });
    });
    </script>
</body>
</html>