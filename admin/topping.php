<?php
require_once '../includes/functions.php';
requireAdmin();

$action = $_GET['action'] ?? 'list';
$id = $_GET['id'] ?? null;
$error = '';
$success = '';

// Handle form submissions
if ($_POST) {
    if (!verifyCSRFToken($_POST['csrf_token'] ?? '')) {
        $error = 'Token CSRF tidak valid';
    } else {
        switch ($action) {
            case 'add':
                $name = sanitize($_POST['name'] ?? '');
                $price = floatval($_POST['price'] ?? 0);
                $category = sanitize($_POST['category'] ?? '');
                $description = sanitize($_POST['description'] ?? '');
                
                if (empty($name) || empty($category) || $price < 0) {
                    $error = 'Nama, kategori, dan harga harus diisi dengan benar';
                } else {
                    try {
                        $stmt = $pdo->prepare("INSERT INTO toppings (name, price, category, description) VALUES (?, ?, ?, ?)");
                        $stmt->execute([$name, $price, $category, $description]);
                        
                        setFlashMessage('success', 'Topping berhasil ditambahkan');
                        header('Location: topping.php');
                        exit();
                    } catch (PDOException $e) {
                        $error = 'Terjadi kesalahan saat menyimpan data';
                    }
                }
                break;
                
            case 'edit':
                $name = sanitize($_POST['name'] ?? '');
                $price = floatval($_POST['price'] ?? 0);
                $category = sanitize($_POST['category'] ?? '');
                $description = sanitize($_POST['description'] ?? '');
                
                if (empty($name) || empty($category) || $price < 0 || !$id) {
                    $error = 'Nama, kategori, dan harga harus diisi dengan benar';
                } else {
                    try {
                        $stmt = $pdo->prepare("UPDATE toppings SET name = ?, price = ?, category = ?, description = ? WHERE id = ?");
                        $stmt->execute([$name, $price, $category, $description, $id]);
                        
                        setFlashMessage('success', 'Topping berhasil diperbarui');
                        header('Location: topping.php');
                        exit();
                    } catch (PDOException $e) {
                        $error = 'Terjadi kesalahan saat menyimpan data';
                    }
                }
                break;
        }
    }
}

// Handle delete action
if ($action == 'delete' && $id) {
    try {
        // Soft delete
        $stmt = $pdo->prepare("UPDATE toppings SET is_active = 0 WHERE id = ?");
        $stmt->execute([$id]);
        
        setFlashMessage('success', 'Topping berhasil dihapus');
    } catch (PDOException $e) {
        setFlashMessage('error', 'Terjadi kesalahan saat menghapus topping');
    }
    
    header('Location: topping.php');
    exit();
}

// Get topping data for edit
$toppingData = null;
if ($action == 'edit' && $id) {
    $toppingData = getToppingById($id);
    if (!$toppingData) {
        header('Location: topping.php');
        exit();
    }
}

// Get all toppings for list
$toppings = [];
if ($action == 'list') {
    try {
        $stmt = $pdo->query("SELECT * FROM toppings WHERE is_active = 1 ORDER BY category, name ASC");
        $toppings = $stmt->fetchAll();
    } catch (PDOException $e) {
        $error = 'Terjadi kesalahan saat mengambil data topping';
    }
}

// Group toppings by category for display
$toppingsByCategory = [];
foreach ($toppings as $topping) {
    $toppingsByCategory[$topping['category']][] = $topping;
}

$categoryNames = [
    'protein' => 'Protein',
    'cheese' => 'Keju',
    'extras' => 'Tambahan',
    'sweet' => 'Topping Manis'
];
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Topping - Martabak Lima Admin</title>
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
                        <a class="nav-link" href="dashboard.php">Dashboard</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="menu.php">Menu</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" href="topping.php">Topping</a>
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
                <!-- Page Header -->
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h1 class="h3">
                        <?php if ($action == 'add'): ?>
                        Tambah Topping Baru
                        <?php elseif ($action == 'edit'): ?>
                        Edit Topping
                        <?php else: ?>
                        Kelola Topping
                        <?php endif; ?>
                    </h1>
                    
                    <?php if ($action == 'list'): ?>
                    <a href="topping.php?action=add" class="btn btn-primary">
                        <i class="fas fa-plus me-2"></i>Tambah Topping
                    </a>
                    <?php else: ?>
                    <a href="topping.php" class="btn btn-secondary">
                        <i class="fas fa-arrow-left me-2"></i>Kembali
                    </a>
                    <?php endif; ?>
                </div>

                <!-- Flash Messages -->
                <?php if (hasFlashMessage('success')): ?>
                <div class="alert alert-success alert-dismissible fade show">
                    <i class="fas fa-check-circle me-2"></i><?= getFlashMessage('success') ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
                <?php endif; ?>
                
                <?php if (hasFlashMessage('error')): ?>
                <div class="alert alert-danger alert-dismissible fade show">
                    <i class="fas fa-exclamation-triangle me-2"></i><?= getFlashMessage('error') ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
                <?php endif; ?>
                
                <?php if ($error): ?>
                <div class="alert alert-danger">
                    <i class="fas fa-exclamation-triangle me-2"></i><?= htmlspecialchars($error) ?>
                </div>
                <?php endif; ?>

                <!-- Content -->
                <?php if ($action == 'add' || $action == 'edit'): ?>
                <!-- Add/Edit Form -->
                <div class="card">
                    <div class="card-body">
                        <form method="POST">
                            <input type="hidden" name="csrf_token" value="<?= generateCSRFToken() ?>">
                            
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="name" class="form-label">Nama Topping *</label>
                                        <input type="text" class="form-control" id="name" name="name" 
                                               value="<?= htmlspecialchars($toppingData['name'] ?? '') ?>" required>
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label for="category" class="form-label">Kategori *</label>
                                        <select class="form-control" id="category" name="category" required>
                                            <option value="">Pilih Kategori</option>
                                            <option value="protein" <?= ($toppingData['category'] ?? '') == 'protein' ? 'selected' : '' ?>>Protein</option>
                                            <option value="cheese" <?= ($toppingData['category'] ?? '') == 'cheese' ? 'selected' : '' ?>>Keju</option>
                                            <option value="extras" <?= ($toppingData['category'] ?? '') == 'extras' ? 'selected' : '' ?>>Tambahan</option>
                                            <option value="sweet" <?= ($toppingData['category'] ?? '') == 'sweet' ? 'selected' : '' ?>>Topping Manis</option>
                                        </select>
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label for="price" class="form-label">Harga *</label>
                                        <div class="input-group">
                                            <span class="input-group-text">Rp</span>
                                            <input type="number" class="form-control" id="price" name="price" 
                                                   value="<?= $toppingData['price'] ?? '' ?>" step="0.01" min="0" required>
                                        </div>
                                        <div class="form-text">Masukkan 0 jika topping gratis</div>
                                    </div>
                                </div>
                                
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="description" class="form-label">Deskripsi</label>
                                        <textarea class="form-control" id="description" name="description" rows="5" 
                                                  placeholder="Deskripsi singkat tentang topping ini"><?= htmlspecialchars($toppingData['description'] ?? '') ?></textarea>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="text-end">
                                <a href="topping.php" class="btn btn-secondary me-2">Batal</a>
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save me-2"></i>Simpan
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
                
                <?php else: ?>
                <!-- Topping List -->
                <?php if (empty($toppings)): ?>
                <div class="card">
                    <div class="card-body">
                        <div class="text-center py-5">
                            <i class="fas fa-plus fa-3x text-muted mb-3"></i>
                            <h5 class="text-muted">Belum ada topping</h5>
                            <p class="text-muted">Mulai dengan menambahkan topping pertama Anda</p>
                            <a href="topping.php?action=add" class="btn btn-primary">
                                <i class="fas fa-plus me-2"></i>Tambah Topping
                            </a>
                        </div>
                    </div>
                </div>
                <?php else: ?>
                
                <!-- Display toppings by category -->
                <?php foreach ($categoryNames as $categoryKey => $categoryName): ?>
                <?php if (isset($toppingsByCategory[$categoryKey])): ?>
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0">
                            <i class="fas fa-<?= $categoryKey == 'protein' ? 'drumstick-bite' : ($categoryKey == 'cheese' ? 'cheese' : ($categoryKey == 'sweet' ? 'candy-cane' : 'plus')) ?> me-2"></i>
                            <?= $categoryName ?>
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Nama</th>
                                        <th>Harga</th>
                                        <th>Deskripsi</th>
                                        <th width="120">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($toppingsByCategory[$categoryKey] as $topping): ?>
                                    <tr>
                                        <td>
                                            <strong><?= htmlspecialchars($topping['name']) ?></strong>
                                        </td>
                                        <td>
                                            <?php if ($topping['price'] > 0): ?>
                                            <?= formatCurrency($topping['price']) ?>
                                            <?php else: ?>
                                            <span class="badge bg-success">Gratis</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <?php if ($topping['description']): ?>
                                            <?= htmlspecialchars(substr($topping['description'], 0, 100)) ?><?= strlen($topping['description']) > 100 ? '...' : '' ?>
                                            <?php else: ?>
                                            <span class="text-muted">-</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <div class="btn-group btn-group-sm">
                                                <a href="topping.php?action=edit&id=<?= $topping['id'] ?>" 
                                                   class="btn btn-outline-primary" title="Edit">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <a href="topping.php?action=delete&id=<?= $topping['id'] ?>" 
                                                   class="btn btn-outline-danger" title="Hapus"
                                                   onclick="return confirm('Yakin ingin menghapus topping ini?')">
                                                    <i class="fas fa-trash"></i>
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <?php endif; ?>
                <?php endforeach; ?>
                
                <?php endif; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>