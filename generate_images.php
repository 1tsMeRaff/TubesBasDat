<?php
/**
 * Generate Images Page - Display All Product Images
 * Sakinah Style
 */

require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/functions.php';

$page_title = "Semua Gambar Produk - Sakinah Style";
$page_description = "Tampilkan semua gambar produk yang tersedia";

$image_dir = __DIR__ . '/assets/images/products/';
$image_url_base = SITE_URL . '/assets/images/products/';

$images = [];
if (is_dir($image_dir)) {
    $files = scandir($image_dir);
    foreach ($files as $file) {
        if ($file !== '.' && $file !== '..' && preg_match('/\.(jpg|jpeg|png|gif)$/i', $file)) {
            $images[] = $file;
        }
    }
    sort($images);
}

include __DIR__ . '/includes/header.php';
?>

<div class="container my-5">
    <div class="row">
        <div class="col-12">
            <h1 class="mb-4">Semua Gambar Produk</h1>
            <p class="text-muted mb-4">Menampilkan <?php echo count($images); ?> gambar produk yang tersedia.</p>

            <?php if (empty($images)): ?>
                <div class="alert alert-info">
                    <i class="bi bi-info-circle"></i> Tidak ada gambar produk yang ditemukan.
                </div>
            <?php else: ?>
                <div class="row g-4">
                    <?php foreach ($images as $image): ?>
                        <div class="col-lg-3 col-md-4 col-sm-6">
                            <div class="card h-100">
                                <div class="card-body p-3">
                                    <img src="<?php echo htmlspecialchars($image_url_base . $image); ?>"
                                         alt="<?php echo htmlspecialchars($image); ?>"
                                         class="img-fluid rounded mb-3"
                                         style="width: 100%; height: 200px; object-fit: cover;">
                                    <h6 class="card-title text-truncate" title="<?php echo htmlspecialchars($image); ?>">
                                        <?php echo htmlspecialchars($image); ?>
                                    </h6>
                                    <a href="<?php echo htmlspecialchars($image_url_base . $image); ?>"
                                       target="_blank"
                                       class="btn btn-sm btn-outline-primary">
                                        <i class="bi bi-eye"></i> Lihat Asli
                                    </a>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
