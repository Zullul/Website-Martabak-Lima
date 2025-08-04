<?php
require_once 'includes/functions.php';

// Get menu packages and toppings from database
$menuPackages = getMenuPackages();
$toppings = getToppings();

// Group toppings by category
$toppingsByCategory = [];
foreach ($toppings as $topping) {
    $toppingsByCategory[$topping['category']][] = $topping;
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Martabak Lima - All You Can Mix All You Can Eat</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
    <link href="css/style.css" rel="stylesheet">
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary fixed-top">
        <div class="container">
            <a class="navbar-brand fw-bold" href="#home">
                <i class="fas fa-utensils me-2"></i>Martabak Lima
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item"><a class="nav-link" href="#home">Home</a></li>
                    <li class="nav-item"><a class="nav-link" href="#menu">Menu</a></li>
                    <li class="nav-item"><a class="nav-link" href="#order">Pesan</a></li>
                    <li class="nav-item"><a class="nav-link" href="#about">Tentang</a></li>
                    <li class="nav-item"><a class="nav-link" href="#contact">Kontak</a></li>
                </ul>
                <button class="btn btn-warning ms-3" id="cartBtn">
                    <i class="fas fa-shopping-cart"></i> <span id="cartCount">0</span>
                </button>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section id="home" class="hero-section">
        <div class="container">
            <div class="row align-items-center min-vh-100">
                <div class="col-lg-6" data-aos="fade-right">
                    <h1 class="display-4 fw-bold text-white mb-4">
                        Martabak Lima
                    </h1>
                    <p class="lead text-warning fw-bold mb-4">
                        "All You Can Mix, All You Can Eat"
                    </p>
                    <p class="text-white-50 mb-4">
                        Nikmati sensasi martabak dengan 1001 kombinasi rasa unik. 
                        Dari Martabak Sapi Mozarella Telur Bebek hingga Ikan Tuna Asap, 
                        semuanya bisa dicampur sesuai selera Anda!
                    </p>
                    <div class="d-flex flex-wrap gap-3">
                        <a href="#menu" class="btn btn-warning btn-lg">
                            <i class="fas fa-eye me-2"></i>Lihat Menu
                        </a>
                        <a href="#order" class="btn btn-outline-light btn-lg">
                            <i class="fas fa-shopping-cart me-2"></i>Pesan Sekarang
                        </a>
                    </div>
                </div>
                <div class="col-lg-6" data-aos="fade-left">
                    <div class="hero-image">
                        <img src="https://via.placeholder.com/600x400/FF6B35/FFFFFF?text=Martabak+Lima" 
                             alt="Martabak Lima" class="img-fluid rounded shadow-lg">
                    </div>
                </div>
            </div>
        </div>
        <div class="hero-overlay"></div>
    </section>

    <!-- Quick Info -->
    <section class="quick-info bg-warning">
        <div class="container">
            <div class="row text-center py-4">
                <div class="col-md-3 mb-3" data-aos="fade-up" data-aos-delay="100">
                    <i class="fas fa-map-marker-alt fa-2x text-primary mb-2"></i>
                    <h6>Lokasi</h6>
                    <p class="mb-0 small">Jl. Perintis Kemerdekaan No.1, Banjar</p>
                </div>
                <div class="col-md-3 mb-3" data-aos="fade-up" data-aos-delay="200">
                    <i class="fas fa-clock fa-2x text-primary mb-2"></i>
                    <h6>Jam Buka</h6>
                    <p class="mb-0 small">15:00 - 21:15 (Sen-Jum)<br>15:00 - 21:30 (Sab-Min)</p>
                </div>
                <div class="col-md-3 mb-3" data-aos="fade-up" data-aos-delay="300">
                    <i class="fas fa-phone fa-2x text-primary mb-2"></i>
                    <h6>WhatsApp</h6>
                    <p class="mb-0 small">+62 812-3456-7890</p>
                </div>
                <div class="col-md-3 mb-3" data-aos="fade-up" data-aos-delay="400">
                    <i class="fas fa-motorcycle fa-2x text-primary mb-2"></i>
                    <h6>Delivery</h6>
                    <p class="mb-0 small">GoFood & GrabFood</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Menu Section -->
    <section id="menu" class="py-5">
        <div class="container">
            <div class="text-center mb-5" data-aos="fade-up">
                <h2 class="fw-bold">Menu Spesial Kami</h2>
                <p class="text-muted">Pilih favorit Anda atau buat kombinasi sendiri!</p>
            </div>

            <!-- Menu Filter -->
            <div class="text-center mb-4" data-aos="fade-up" data-aos-delay="100">
                <button class="btn btn-outline-primary me-2 filter-btn active" data-filter="all">Semua</button>
                <button class="btn btn-outline-primary me-2 filter-btn" data-filter="asin">Martabak Asin</button>
                <button class="btn btn-outline-primary filter-btn" data-filter="manis">Martabak Manis</button>
            </div>

            <div class="row" id="menuContainer">
                <?php foreach ($menuPackages as $item): ?>
                <div class="col-lg-4 col-md-6 mb-4" data-category="<?= $item['category'] ?>">
                    <div class="card menu-card h-100" data-aos="fade-up">
                        <?php if ($item['is_signature']): ?>
                        <span class="badge badge-signature">Signature</span>
                        <?php endif; ?>
                        <div class="position-relative overflow-hidden">
                            <img src="<?= $item['image'] ? $item['image'] : 'https://via.placeholder.com/300x200/F8981D/FFFFFF?text=' . urlencode($item['name']) ?>" 
                                 class="card-img-top" alt="<?= htmlspecialchars($item['name']) ?>">
                        </div>
                        <div class="card-body d-flex flex-column">
                            <h5 class="card-title"><?= htmlspecialchars($item['name']) ?></h5>
                            <p class="card-text text-muted flex-grow-1"><?= htmlspecialchars($item['description']) ?></p>
                            <div class="mb-2">
                                <small class="text-muted">Berisi: <?= htmlspecialchars($item['ingredients']) ?></small>
                            </div>
                            <div class="d-flex justify-content-between align-items-center">
                                <span class="h5 text-primary mb-0"><?= formatCurrency($item['price']) ?></span>
                                <button class="btn btn-outline-primary quick-order-btn" 
                                        data-item-id="<?= $item['id'] ?>"
                                        data-item-name="<?= htmlspecialchars($item['name']) ?>"
                                        data-item-price="<?= $item['price'] ?>"
                                        data-item-category="<?= $item['category'] ?>">
                                    <i class="fas fa-plus"></i> Pesan
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <!-- Mix & Match Section -->
    <section id="order" class="py-5 bg-light">
        <div class="container">
            <div class="text-center mb-5" data-aos="fade-up">
                <h2 class="fw-bold">Buat Martabak Impian Anda</h2>
                <p class="text-muted">Mix & Match sesuai selera dengan harga yang transparan</p>
            </div>

            <div class="row">
                <div class="col-lg-8">
                    <div class="card shadow" data-aos="fade-right">
                        <div class="card-header bg-primary text-white">
                            <h5 class="mb-0"><i class="fas fa-magic me-2"></i>Martabak Builder</h5>
                        </div>
                        <div class="card-body">
                            <form id="martabakForm">
                                <!-- Base Selection -->
                                <div class="mb-4">
                                    <h6 class="fw-bold">1. Pilih Base Martabak</h6>
                                    <div class="row">
                                        <div class="col-md-6 mb-2">
                                            <div class="form-check">
                                                <input class="form-check-input" type="radio" name="base" id="baseAsin" value="asin" data-price="15000">
                                                <label class="form-check-label d-flex justify-content-between" for="baseAsin">
                                                    <span>Martabak Asin</span>
                                                    <span class="text-primary fw-bold">Rp 15.000</span>
                                                </label>
                                            </div>
                                        </div>
                                        <div class="col-md-6 mb-2">
                                            <div class="form-check">
                                                <input class="form-check-input" type="radio" name="base" id="baseManis" value="manis" data-price="12000">
                                                <label class="form-check-label d-flex justify-content-between" for="baseManis">
                                                    <span>Martabak Manis</span>
                                                    <span class="text-primary fw-bold">Rp 12.000</span>
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Protein Selection -->
                                <div class="mb-4" id="proteinSection" style="display: none;">
                                    <h6 class="fw-bold">2. Pilih Protein (Optional)</h6>
                                    <div class="row">
                                        <?php 
                                        $proteinToppings = isset($toppingsByCategory['protein']) ? $toppingsByCategory['protein'] : [];
                                        foreach ($proteinToppings as $topping): 
                                        ?>
                                        <div class="col-md-6 mb-2">
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" 
                                                       id="topping_<?= $topping['id'] ?>" 
                                                       data-price="<?= $topping['price'] ?>"
                                                       data-name="<?= htmlspecialchars($topping['name']) ?>">
                                                <label class="form-check-label d-flex justify-content-between" for="topping_<?= $topping['id'] ?>">
                                                    <span><?= htmlspecialchars($topping['name']) ?></span>
                                                    <span class="text-primary">+<?= formatCurrency($topping['price']) ?></span>
                                                </label>
                                            </div>
                                        </div>
                                        <?php endforeach; ?>
                                    </div>
                                </div>

                                <!-- Cheese & Extras -->
                                <div class="mb-4" id="extrasSection" style="display: none;">
                                    <h6 class="fw-bold">3. Tambahan Lainnya</h6>
                                    <div class="row">
                                        <?php 
                                        $extraToppings = array_merge(
                                            isset($toppingsByCategory['cheese']) ? $toppingsByCategory['cheese'] : [],
                                            isset($toppingsByCategory['extras']) ? $toppingsByCategory['extras'] : []
                                        );
                                        foreach ($extraToppings as $topping): 
                                        ?>
                                        <div class="col-md-6 mb-2">
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" 
                                                       id="topping_<?= $topping['id'] ?>" 
                                                       data-price="<?= $topping['price'] ?>"
                                                       data-name="<?= htmlspecialchars($topping['name']) ?>">
                                                <label class="form-check-label d-flex justify-content-between" for="topping_<?= $topping['id'] ?>">
                                                    <span><?= htmlspecialchars($topping['name']) ?></span>
                                                    <span class="text-primary">+<?= formatCurrency($topping['price']) ?></span>
                                                </label>
                                            </div>
                                        </div>
                                        <?php endforeach; ?>
                                    </div>
                                </div>

                                <!-- Sweet Toppings for Martabak Manis -->
                                <div class="mb-4" id="sweetSection" style="display: none;">
                                    <h6 class="fw-bold">3. Pilih Topping Manis</h6>
                                    <div class="row">
                                        <?php 
                                        $sweetToppings = isset($toppingsByCategory['sweet']) ? $toppingsByCategory['sweet'] : [];
                                        foreach ($sweetToppings as $topping): 
                                        ?>
                                        <div class="col-md-6 mb-2">
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" 
                                                       id="topping_<?= $topping['id'] ?>" 
                                                       data-price="<?= $topping['price'] ?>"
                                                       data-name="<?= htmlspecialchars($topping['name']) ?>">
                                                <label class="form-check-label d-flex justify-content-between" for="topping_<?= $topping['id'] ?>">
                                                    <span><?= htmlspecialchars($topping['name']) ?></span>
                                                    <span class="text-primary">+<?= formatCurrency($topping['price']) ?></span>
                                                </label>
                                            </div>
                                        </div>
                                        <?php endforeach; ?>
                                    </div>
                                </div>

                                <div class="text-center">
                                    <button type="button" class="btn btn-primary btn-lg" id="addToCartBtn" disabled>
                                        <i class="fas fa-plus me-2"></i>Tambah ke Keranjang
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <div class="col-lg-4 mt-4 mt-lg-0">
                    <!-- Order Summary -->
                    <div class="card shadow sticky-top" data-aos="fade-left">
                        <div class="card-header bg-warning">
                            <h5 class="mb-0"><i class="fas fa-receipt me-2"></i>Ringkasan Pesanan</h5>
                        </div>
                        <div class="card-body">
                            <div id="orderSummary">
                                <p class="text-muted text-center">Silakan pilih base martabak terlebih dahulu</p>
                            </div>
                            <hr>
                            <div class="d-flex justify-content-between">
                                <strong>Total:</strong>
                                <strong class="text-primary" id="totalPrice">Rp 0</strong>
                            </div>
                        </div>
                    </div>

                    <!-- Cart -->
                    <div class="card shadow mt-4" data-aos="fade-left" data-aos-delay="100">
                        <div class="card-header bg-success text-white">
                            <h5 class="mb-0"><i class="fas fa-shopping-cart me-2"></i>Keranjang</h5>
                        </div>
                        <div class="card-body">
                            <div id="cartItems">
                                <p class="text-muted text-center">Keranjang kosong</p>
                            </div>
                            <hr>
                            <div class="d-flex justify-content-between mb-3">
                                <strong>Total Keranjang:</strong>
                                <strong class="text-success" id="cartTotal">Rp 0</strong>
                            </div>
                            <button class="btn btn-success w-100" id="checkoutBtn" disabled>
                                <i class="fas fa-whatsapp me-2"></i>Pesan via WhatsApp
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- About Section -->
    <section id="about" class="py-5">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-6" data-aos="fade-right">
                    <img src="https://via.placeholder.com/600x400/F8981D/FFFFFF?text=Tentang+Martabak+Lima" 
                         alt="Tentang Kami" class="img-fluid rounded shadow">
                </div>
                <div class="col-lg-6 mt-4 mt-lg-0" data-aos="fade-left">
                    <h2 class="fw-bold mb-4">Tentang Martabak Lima</h2>
                    <p class="text-muted mb-4">
                        Martabak Lima adalah UMKM yang berlokasi di Jl. Perintis Kemerdekaan No.1, 
                        Kota Banjar, yang menghadirkan konsep unik <strong>"All You Can Mix All You Can Eat"</strong>.
                    </p>
                    <p class="text-muted mb-4">
                        Kami mengkhususkan diri dalam pembuatan martabak dengan berbagai kombinasi topping 
                        yang dapat disesuaikan dengan selera pelanggan. Menu unggulan kami termasuk 
                        Martabak Sapi Mozarella Telur Bebek dan Martabak Ikan Tuna Asap yang kaya gizi.
                    </p>
                    <div class="row">
                        <div class="col-6">
                            <div class="text-center">
                                <h4 class="text-primary fw-bold">100+</h4>
                                <p class="small text-muted">Kombinasi Rasa</p>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="text-center">
                                <h4 class="text-primary fw-bold">500+</h4>
                                <p class="small text-muted">Pelanggan Puas</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Contact Section -->
    <section id="contact" class="py-5 bg-primary text-white">
        <div class="container">
            <div class="text-center mb-5" data-aos="fade-up">
                <h2 class="fw-bold">Hubungi Kami</h2>
                <p>Siap melayani pesanan Anda setiap hari!</p>
            </div>
            <div class="row">
                <div class="col-lg-8 mx-auto">
                    <div class="row">
                        <div class="col-md-6 mb-4" data-aos="fade-up" data-aos-delay="100">
                            <div class="text-center">
                                <i class="fas fa-map-marker-alt fa-3x mb-3"></i>
                                <h5>Alamat</h5>
                                <p>Jl. Perintis Kemerdekaan No.1<br>Kota Banjar, Jawa Barat<br>Depan Toserba Pajajaran</p>
                            </div>
                        </div>
                        <div class="col-md-6 mb-4" data-aos="fade-up" data-aos-delay="200">
                            <div class="text-center">
                                <i class="fas fa-phone fa-3x mb-3"></i>
                                <h5>WhatsApp</h5>
                                <p>+62 812-3456-7890</p>
                                <a href="https://wa.me/6281234567890" class="btn btn-warning">
                                    <i class="fab fa-whatsapp me-2"></i>Chat Sekarang
                                </a>
                            </div>
                        </div>
                    </div>
                    <div class="text-center mt-4" data-aos="fade-up" data-aos-delay="300">
                        <h5>Jam Operasional</h5>
                        <div class="row">
                            <div class="col-md-6">
                                <p><strong>Senin - Jumat:</strong><br>15:00 - 21:15</p>
                            </div>
                            <div class="col-md-6">
                                <p><strong>Sabtu - Minggu:</strong><br>15:00 - 21:30</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-dark text-white py-4">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-md-6">
                    <p class="mb-0">&copy; 2025 Martabak Lima. All rights reserved.</p>
                </div>
                <div class="col-md-6 text-md-end">
                    <p class="mb-0">Website Demo untuk Project Sekolah</p>
                </div>
            </div>
        </div>
    </footer>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <!-- AOS JS -->
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <!-- Custom JS -->
    <script src="js/script.js"></script>
</body>
</html>