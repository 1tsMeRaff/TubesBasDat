    <!-- Footer -->
    <footer class="bg-dark text-white py-5 mt-5">
        <div class="container">
            <div class="row">
                <!-- Column 1: Brand Info -->
                <div class="col-lg-3 col-md-6 mb-4">
                    <h5 class="fw-bold mb-3">
                        <i class="bi bi-scarf"></i> Sakinah Style
                    </h5>
                    <p class="text-white-50 mb-3">
                        Toko fashion muslimah terpercaya dengan koleksi terbaru dan berkualitas. 
                        Anggun & Syar'i untuk muslimah modern.
                    </p>
                    <div class="d-flex gap-3">
                        <a href="#" class="text-white-50"><i class="bi bi-instagram fs-5"></i></a>
                        <a href="#" class="text-white-50"><i class="bi bi-facebook fs-5"></i></a>
                        <a href="#" class="text-white-50"><i class="bi bi-whatsapp fs-5"></i></a>
                        <a href="#" class="text-white-50"><i class="bi bi-youtube fs-5"></i></a>
                    </div>
                </div>
                
                <!-- Column 2: Quick Links -->
                <div class="col-lg-3 col-md-6 mb-4">
                    <h6 class="fw-bold mb-3">Tautan Cepat</h6>
                    <ul class="list-unstyled">
                        <li class="mb-2"><a href="<?php echo SITE_URL; ?>/index.php" class="text-white-50 text-decoration-none">Beranda</a></li>
                        <li class="mb-2"><a href="<?php echo SITE_URL; ?>/shop.php" class="text-white-50 text-decoration-none">Semua Produk</a></li>
                        <li class="mb-2"><a href="#about" class="text-white-50 text-decoration-none">Tentang Kami</a></li>
                        <li class="mb-2"><a href="#contact" class="text-white-50 text-decoration-none">Kontak</a></li>
                        <li class="mb-2"><a href="#" class="text-white-50 text-decoration-none">FAQ</a></li>
                    </ul>
                </div>
                
                <!-- Column 3: Customer Service -->
                <div class="col-lg-3 col-md-6 mb-4">
                    <h6 class="fw-bold mb-3">Layanan Pelanggan</h6>
                    <ul class="list-unstyled">
                        <li class="mb-2">
                            <a href="https://wa.me/6281234567890" target="_blank" class="text-white-50 text-decoration-none">
                                <i class="bi bi-whatsapp text-success"></i> Chat WhatsApp
                            </a>
                        </li>
                        <li class="mb-2">
                            <a href="#" class="text-white-50 text-decoration-none">
                                <i class="bi bi-question-circle"></i> Cara Belanja
                            </a>
                        </li>
                        <li class="mb-2">
                            <a href="#" class="text-white-50 text-decoration-none">
                                <i class="bi bi-truck"></i> Info Pengiriman
                            </a>
                        </li>
                        <li class="mb-2">
                            <a href="#" class="text-white-50 text-decoration-none">
                                <i class="bi bi-arrow-left-right"></i> Kebijakan Retur
                            </a>
                        </li>
                    </ul>
                </div>
                
                <!-- Column 4: Payment Methods -->
                <div class="col-lg-3 col-md-6 mb-4">
                    <h6 class="fw-bold mb-3">Metode Pembayaran</h6>
                    <p class="text-white-50 small mb-3">Kami menerima pembayaran melalui:</p>
                    <div class="payment-methods">
                        <img src="https://via.placeholder.com/80x40/0066CC/FFFFFF?text=BCA" alt="BCA" class="payment-logo">
                        <img src="https://via.placeholder.com/80x40/00A859/FFFFFF?text=Mandiri" alt="Mandiri" class="payment-logo">
                        <img src="https://via.placeholder.com/80x40/FF6B00/FFFFFF?text=QRIS" alt="QRIS" class="payment-logo">
                    </div>
                    <p class="text-white-50 small mt-3 mb-0">
                        <i class="bi bi-shield-check text-success"></i> Pembayaran Aman & Terpercaya
                    </p>
                </div>
            </div>
            
            <hr class="bg-white-50 my-4">
            
            <div class="row align-items-center">
                <div class="col-md-6">
                    <p class="text-white-50 mb-0">
                        &copy; <?php echo date('Y'); ?> Sakinah Style. All rights reserved.
                    </p>
                </div>
                <div class="col-md-6 text-end">
                    <p class="text-white-50 mb-0">
                        Made with <i class="bi bi-heart-fill text-danger"></i> for Muslimah
                    </p>
                </div>
            </div>
        </div>
    </footer>

    <!-- Bootstrap 5 JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- Custom JS -->
    <script>
        // Smooth scroll
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                e.preventDefault();
                const target = document.querySelector(this.getAttribute('href'));
                if (target) {
                    target.scrollIntoView({ behavior: 'smooth', block: 'start' });
                }
            });
        });
    </script>
</body>
</html>

