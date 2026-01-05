<?php
/**
 * Header Template
 * Sakinah Style E-Commerce
 */

if (!isset($page_title)) {
    $page_title = 'Sakinah Style - Anggun & Syar\'i';
}

if (!isset($page_description)) {
    $page_description = 'Toko fashion muslimah terpercaya dengan koleksi terbaru dan berkualitas';
}

// Get cart count
$cart_count = isset($_SESSION['cart']) ? count($_SESSION['cart']) : 0;
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($page_title); ?></title>
    <meta name="description" content="<?php echo htmlspecialchars($page_description); ?>">
    
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    
    <!-- Custom CSS -->
    <link rel="stylesheet" href="<?php echo SITE_URL; ?>/assets/css/style.css">
</head>
<body>
    <!-- Top Bar -->
    <div class="top-bar">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-md-6">
                    <small>
                        <i class="bi bi-truck"></i> Gratis Ongkir min. pembelian 300rb
                    </small>
                </div>
                <div class="col-md-6 text-end">
                    <a href="#" class="me-3"><i class="bi bi-instagram"></i></a>
                    <a href="#" class="me-3"><i class="bi bi-facebook"></i></a>
                    <a href="#" class="me-3"><i class="bi bi-whatsapp"></i></a>
                    <a href="#"><i class="bi bi-youtube"></i></a>
                </div>
            </div>
        </div>
    </div>

    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-light sticky-top" id="mainNavbar">
        <div class="container">
            <a class="navbar-brand d-flex align-items-center fw-bold"
                href="<?php echo SITE_URL; ?>/index.php">

                <img src="<?php echo SITE_URL; ?>/assets/images/logo/sakinah-logo.png"
                    alt="Sakinah Style"
                    style="height:60px; width:auto; display:block;"
                    class="me-2">

                <span>Sakinah Style</span>
            </a>


            
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav mx-auto">
                    <li class="nav-item">
                        <a class="nav-link <?php echo (basename($_SERVER['PHP_SELF']) == 'index.php') ? 'active' : ''; ?>" 
                           href="<?php echo SITE_URL; ?>/index.php">
                            Beranda
                        </a>
                    </li>
                    <li class="nav-item dropdown position-static">
                        <a class="nav-link dropdown-toggle <?php echo (basename($_SERVER['PHP_SELF']) == 'shop.php') ? 'active' : ''; ?>" 
                           href="<?php echo SITE_URL; ?>/shop.php"
                           id="koleksiDropdown"
                           role="button"
                           data-bs-toggle="dropdown"
                           aria-expanded="false">
                            Koleksi
                        </a>
                        <div class="dropdown-menu mega-menu w-100" aria-labelledby="koleksiDropdown">
                            <div class="container">
                                <div class="mega-menu-content">
                                    <div class="mega-menu-section">
                                        <h6>Berdasarkan Bahan</h6>
                                        <a href="<?php echo SITE_URL; ?>/shop.php?bahan=1">Ceruty Babydoll</a>
                                        <a href="<?php echo SITE_URL; ?>/shop.php?bahan=2">Voal Premium</a>
                                        <a href="<?php echo SITE_URL; ?>/shop.php?bahan=3">Polycotton</a>
                                    </div>
                                    <div class="mega-menu-section">
                                        <h6>Berdasarkan Jenis</h6>
                                        <a href="<?php echo SITE_URL; ?>/shop.php?kategori=1">Segi Empat</a>
                                        <a href="<?php echo SITE_URL; ?>/shop.php?kategori=2">Pashmina</a>
                                        <a href="<?php echo SITE_URL; ?>/shop.php?kategori=3">Instan</a>
                                    </div>
                                    <div class="mega-menu-section">
                                        <h6>Koleksi Spesial</h6>
                                        <a href="<?php echo SITE_URL; ?>/index.php#hype-products">Sedang Hype</a>
                                        <a href="<?php echo SITE_URL; ?>/index.php#clearance-products">Cuci Gudang</a>
                                        <a href="<?php echo SITE_URL; ?>/shop.php">Semua Produk</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#about">
                            Tentang Kami
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#contact">
                            Kontak
                        </a>
                    </li>
                </ul>
                
                <div class="d-flex gap-2 align-items-center">
                    <a href="<?php echo SITE_URL; ?>/cart.php" class="btn btn-outline-primary position-relative">
                        <i class="bi bi-cart3"></i> 
                        <span class="d-none d-md-inline">Keranjang</span>
                        <?php if ($cart_count > 0): ?>
                            <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                                <?php echo $cart_count; ?>
                            </span>
                        <?php endif; ?>
                    </a>
                    <?php if (isset($_SESSION['user'])): ?>
                        <div class="dropdown">
                            <button class="btn btn-outline-primary dropdown-toggle" type="button" id="userMenu" data-bs-toggle="dropdown">
                                <i class="bi bi-person-circle"></i> <?php echo htmlspecialchars($_SESSION['user']['name']); ?>
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="userMenu">
                                <li><a class="dropdown-item" href="<?php echo SITE_URL; ?>/pages/profile.php">
                                    <i class="bi bi-person"></i> Profil Saya
                                </a></li>
                                <li><a class="dropdown-item" href="<?php echo SITE_URL; ?>/pages/my-orders.php">
                                    <i class="bi bi-bag-check"></i> Riwayat Pesanan
                                </a></li>
                                <li><hr class="dropdown-divider"></li>
                                <li><a class="dropdown-item text-danger" href="<?php echo SITE_URL; ?>/pages/logout.php">
                                    <i class="bi bi-box-arrow-right"></i> Keluar
                                </a></li>
                            </ul>
                        </div>
                    <?php else: ?>
                        <a href="<?php echo SITE_URL; ?>/pages/login.php" class="btn btn-outline-primary">
                            Masuk
                        </a>
                        <a href="<?php echo SITE_URL; ?>/pages/register.php" class="btn btn-primary">
                            Daftar
                        </a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </nav>

    <!-- Navbar Scroll Script -->
    <script>
        window.addEventListener('scroll', function() {
            const navbar = document.getElementById('mainNavbar');
            if (window.scrollY > 50) {
                navbar.classList.add('scrolled');
            } else {
                navbar.classList.remove('scrolled');
            }
        });
        
        // Keep mega menu open on hover
        document.addEventListener('DOMContentLoaded', function() {
            const dropdown = document.getElementById('koleksiDropdown');
            const menu = dropdown?.nextElementSibling;
            
            if (dropdown && menu) {
                dropdown.addEventListener('mouseenter', function() {
                    const bsDropdown = new bootstrap.Dropdown(dropdown);
                    bsDropdown.show();
                });
                
                menu.addEventListener('mouseleave', function() {
                    const bsDropdown = bootstrap.Dropdown.getInstance(dropdown);
                    if (bsDropdown) {
                        bsDropdown.hide();
                    }
                });
            }
        });
    </script>

