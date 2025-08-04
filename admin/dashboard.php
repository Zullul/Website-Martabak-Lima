<?php
require_once '../includes/functions.php';
requireAdmin();

// Get statistics
try {
    $stmt = $pdo->query("SELECT COUNT(*) as total_menus FROM menu_packages WHERE is_active = 1");
    $totalMenus = $stmt->fetch()['total_menus'];
    
    $stmt = $pdo->query("SELECT COUNT(*) as total_toppings FROM toppings WHERE is_active = 1");
    $totalToppings = $stmt->fetch()['total_toppings'];
    
    $stmt = $pdo->query("SELECT COUNT(*) as total_orders FROM orders WHERE DATE(order_date) = CURDATE()");
    $todayOrders = $stmt->fetch()['total_orders'];
    
    $stmt = $pdo->query("SELECT COALESCE(SUM(total_amount), 0) as today_revenue FROM orders WHERE DATE(order_date) = CURDATE() AND status != 'cancelled'");
    $todayRevenue = $stmt->fetch()['today_revenue'];
    
    // Recent orders
    $stmt = $pdo->query("SELECT * FROM orders ORDER BY order_date DESC LIMIT 5");
    $recentOrders = $stmt->fetchAll();
    
} catch (PDOException $e) {
    $totalMenus = $totalToppings = $todayOrders = $todayRevenue = 0;
    $recentOrders = [];
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Admin - Martabak Lima</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="../css/style.css" rel="stylesheet">
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
        <div class="container-fluid">
            <a class="navbar-brand" href="dashboard.php">
                <i class="fas fa-utensils me-2"></i>Martabak Lima Admin
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link active" href="dashboard.php">Dashboard</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="menu.php">Menu</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="topping.php">Topping</a>
                    </li>
                </ul>
                <ul class="navbar-nav">
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown">
                            <i class="fas fa-user me-1"></i><?= htmlspecialchars($_SESSION['admin_name']) ?>
                        </a>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="../index.php" target="_blank">
                                <i class="fas fa-globe me-2"></i>Lihat Website
                            </a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item" href="logout.php">
                                <i class="fas fa-sign-out-alt me-2"></i>Logout
                            </a></li>
                        </ul>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container-fluid py-4">
        <div class="row">
            <div class="col-12">
                <h1 class="h3 mb-4">Dashboard</h1>
                
                <!-- Statistics Cards -->
                <div class="row mb-4">
                    <div class="col-xl-3 col-md-6 mb-4">
                        <div class="card border-start border-primary border-4 h-100">
                            <div class="card-body">
                                <div class="row align-items-center">
                                    <div class="col-8">
                                        <div class="text-xs fw-bold text-primary text-uppercase mb-1">
                                            Total Menu
                                        </div>
                                        <div class="h5 mb-0 fw-bold text-gray-800"><?= $totalMenus ?></div>
                                    </div>
                                    <div class="col-4">
                                        <i class="fas fa-list fa-2x text-gray-300"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-xl-3 col-md-6 mb-4">
                        <div class="card border-start border-success border-4 h-100">
                            <div class="card-body">
                                <div class="row align-items-center">
                                    <div class="col-8">
                                        <div class="text-xs fw-bold text-success text-uppercase mb-1">
                                            Total Topping
                                        </div>
                                        <div class="h5 mb-0 fw-bold text-gray-800"><?= $totalToppings ?></div>
                                    </div>
                                    <div class="col-4">
                                        <i class="fas fa-plus fa-2x text-gray-300"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-xl-3 col-md-6 mb-4">
                        <div class="card border-start border-info border-4 h-100">
                            <div class="card-body">
                                <div class="row align-items-center">
                                    <div class="col-8">
                                        <div class="text-xs fw-bold text-info text-uppercase mb-1">
                                            Pesanan Hari Ini
                                        </div>
                                        <div class="h5 mb-0 fw-bold text-gray-800"><?= $todayOrders ?></div>
                                    </div>
                                    <div class="col-4">
                                        <i class="fas fa-shopping-cart fa-2x text-gray-300"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-xl-3 col-md-6 mb-4">
                        <div class="card border-start border-warning border-4 h-100">
                            <div class="card-body">
                                <div class="row align-items-center">
                                    <div class="col-8">
                                        <div class="text-xs fw-bold text-warning text-uppercase mb-1">
                                            Pendapatan Hari Ini
                                        </div>
                                        <div class="h5 mb-0 fw-bold text-gray-800"><?= formatCurrency($todayRevenue) ?></div>
                                    </div>
                                    <div class="col-4">
                                        <i class="fas fa-rupiah-sign fa-2x text-gray-300"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Quick Actions -->
                <div class="row mb-4">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="mb-0">Aksi Cepat</h5>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-3 mb-3">
                                        <a href="menu.php?action=add" class="btn btn-primary w-100">
                                            <i class="fas fa-plus me-2"></i>Tambah Menu
                                        </a>
                                    </div>
                                    <div class="col-md-3 mb-3">
                                        <a href="topping.php?action=add" class="btn btn-success w-100">
                                            <i class="fas fa-plus me-2"></i>Tambah Topping
                                        </a>
                                    </div>
                                    <div class="col-md-3 mb-3">
                                        <a href="menu.php" class="btn btn-outline-primary w-100">
                                            <i class="fas fa-list me-2"></i>Kelola Menu
                                        </a>
                                    </div>
                                    <div class="col-md-3 mb-3">
                                        <a href="topping.php" class="btn btn-outline-success w-100">
                                            <i class="fas fa-cogs me-2"></i>Kelola Topping
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Recent Orders -->
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <h5 class="mb-0">Pesanan Terbaru</h5>
                                <a href="orders.php" class="btn btn-sm btn-outline-primary">Lihat Semua</a>
                            </div>
                            <div class="card-body">
                                <?php if (empty($recentOrders)): ?>
                                <div class="text-center text-muted py-4">
                                    <i class="fas fa-inbox fa-3x mb-3"></i>
                                    <p>Belum ada pesanan hari ini</p>
                                </div>
                                <?php else: ?>
                                <div class="table-responsive">
                                    <table class="table table-hover">
                                        <thead>
                                            <tr>
                                                <th>No. Pesanan</th>
                                                <th>Customer</th>
                                                <th>Total</th>
                                                <th>Status</th>
                                                <th>Waktu</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($recentOrders as $order): ?>
                                            <tr>
                                                <td><code><?= htmlspecialchars($order['order_number']) ?></code></td>
                                                <td>
                                                    <?= $order['customer_name'] ? htmlspecialchars($order['customer_name']) : '-' ?>
                                                    <?php if ($order['customer_phone']): ?>
                                                    <br><small class="text-muted"><?= htmlspecialchars($order['customer_phone']) ?></small>
                                                    <?php endif; ?>
                                                </td>
                                                <td><?= formatCurrency($order['total_amount']) ?></td>
                                                <td>
                                                    <?php
                                                    $statusClass = [
                                                        'pending' => 'warning',
                                                        'confirmed' => 'info',
                                                        'preparing' => 'primary',
                                                        'ready' => 'success',
                                                        'delivered' => 'success',
                                                        'cancelled' => 'danger'
                                                    ];
                                                    $statusText = [
                                                        'pending' => 'Menunggu',
                                                        'confirmed' => 'Dikonfirmasi',
                                                        'preparing' => 'Diproses',
                                                        'ready' => 'Siap',
                                                        'delivered' => 'Dikirim',
                                                        'cancelled' => 'Dibatalkan'
                                                    ];
                                                    ?>
                                                    <span class="badge bg-<?= $statusClass[$order['status']] ?? 'secondary' ?>">
                                                        <?= $statusText[$order['status']] ?? $order['status'] ?>
                                                    </span>
                                                </td>
                                                <td>
                                                    <small><?= date('d/m/Y H:i', strtotime($order['order_date'])) ?></small>
                                                </td>
                                            </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>