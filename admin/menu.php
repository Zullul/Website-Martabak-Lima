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
                $category = sanitize($_POST['category'] ?? '');
                $price = floatval($_POST['price'] ?? 0);
                $description = sanitize($_POST['description'] ?? '');
                $ingredients = sanitize($_POST['ingredients'] ?? '');
                $is_signature = isset($_POST['is_signature']) ? 1 : 0;
                
                if (empty($name) || empty($category) || $price <= 0) {
                    $error = 'Nama, kategori, dan harga harus diisi dengan benar';
                } else {
                    try {
                        $image = '';
                        if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
                            $upload = uploadImage($_FILES['image']);
                            if ($upload['success']) {
                                $image = $upload['filename'];
                            } else {
                                $error = $upload['message'];
                            }
                        }
                        
                        if (!$error) {
                            $stmt = $pdo->prepare("INSERT INTO menu_packages (name, category, price, description, image, ingredients, is_signature) VALUES (?, ?, ?, ?, ?, ?, ?)");
                            $stmt->execute([$name, $category, $price, $description, $image, $ingredients, $is_signature]);
                            
                            setFlashMessage('success', 'Menu berhasil ditambahkan');
                            header('Location: menu.php');
                            exit();
                        }
                    } catch (PDOException $e) {
                        $error = 'Terjadi kesalahan saat menyimpan data';
                    }
                }
                break;
                
            case 'edit':
                $name = sanitize($_POST['name'] ?? '');
                $category = sanitize($_POST['category'] ?? '');
                $price = floatval($_POST['price'] ?? 0);
                $description = sanitize($_POST['description'] ?? '');
                $ingredients = sanitize($_POST['ingredients'] ?? '');
                $is_signature = isset($_POST['is_signature']) ? 1 : 0;
                
                if (empty($name) || empty($category) || $price <= 0 || !$id) {
                    $error = 'Nama, kategori, dan harga harus diisi dengan benar';
                } else {
                    try {
                        // Get current menu data
                        $stmt = $pdo->prepare("SELECT image FROM menu_packages WHERE id = ?");
                        $stmt->execute([$id]);
                        $currentMenu = $stmt->fetch();
                        
                        if (!$currentMenu) {
                            $error = 'Menu tidak ditemukan';
                        } else {
                            $image = $currentMenu['image'];
                            
                            // Handle image upload
                            if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
                                $upload = uploadImage($_FILES['image']);
                                if ($upload['success']) {
                                    // Delete old image
                                    if ($image) {
                                        deleteImage($image);
                                    }
                                    $image = $upload['filename'];
                                } else {
                                    $error = $upload['message'];
                                }
                            }
                            
                            if (!$error) {
                                $stmt = $pdo->prepare("UPDATE menu_packages SET name = ?, category = ?, price = ?, description = ?, image = ?, ingredients = ?, is_signature = ? WHERE id = ?");
                                $stmt->execute([$name, $category, $price, $description, $image, $ingredients, $is_signature, $id]);
                                
                                setFlashMessage('success', 'Menu berhasil diperbarui');
                                header('Location: menu.php');
                                exit();
                            }
                        }
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
        // Get menu data for image deletion
        $stmt = $pdo->prepare("SELECT image FROM menu_packages WHERE id = ?");
        $stmt->execute([$id]);
        $menu = $stmt->fetch();
        
        if ($menu) {
            // Soft delete
            $stmt = $pdo->prepare("UPDATE menu_packages SET is_active = 0 WHERE id = ?");
            $stmt->execute([$id]);
            
            setFlashMessage('success', 'Menu berhasil dihapus');
        } else {
            setFlashMessage('error', 'Menu tidak ditemukan');
        }
    } catch (PDOException $e) {
        setFlashMessage('error', 'Terjadi kesalahan saat menghapus menu');
    }
    
    header('Location: menu.php');
    exit();
}

// Get menu data for edit
$menuData = null;
if ($action == 'edit' && $id) {
    $menuData = getMenuPackageById($id);
    if (!$menuData) {
        header('Location: menu.php');
        exit();
    }
}

// Get all menus for list
$menus = [];
if ($action == 'list') {
    try {
        $stmt = $pdo->query("SELECT * FROM menu_packages WHERE is_active = 1 ORDER BY is_signature DESC, name ASC");
        $menus = $stmt->fetchAll();
    } catch (PDOException $e) {
        $error = 'Terjadi kesalahan saat mengambil data menu';
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Menu - Martabak Lima Admin</title>
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
                        <a class="nav-link active" href="menu.php">Menu</a>
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
                <!-- Page Header -->
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h1 class="h3">
                        <?php if ($action == 'add'): ?>
                        Tambah Menu Baru
                        <?php elseif ($action == 'edit'): ?>
                        Edit Menu
                        <?php else: ?>
                        Kelola Menu
                        <?php endif; ?>
                    </h1>
                    
                    <?php if ($action == 'list'): ?>
                    <a href="menu.php?action=add" class="btn btn-primary">
                        <i class="fas fa-plus me-2"></i>Tambah Menu
                    </a>
                    <?php else: ?>
                    <a href="menu.php" class="btn btn-secondary">
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
                        <form method="POST" enctype="multipart/form-data">
                            <input type="hidden" name="csrf_token" value="<?= generateCSRFToken() ?>">
                            
                            <div class="row">
                                <div class="col-md-8">
                                    <div class="mb-3">
                                        <label for="name" class="form-label">Nama Menu *</label>
                                        <input type="text" class="form-control" id="name" name="name" 
                                               value="<?= htmlspecialchars($menuData['name'] ?? '') ?>" required>
                                    </div>
                                    
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label for="category" class="form-label">Kategori *</label>
                                                <select class="form-control" id="category" name="category" required>
                                                    <option value="">Pilih Kategori</option>
                                                    <option value="asin" <?= ($menuData['category'] ?? '') == 'asin' ? 'selected' : '' ?>>Martabak Asin</option>
                                                    <option value="manis" <?= ($menuData['category'] ?? '') == 'manis' ? 'selected' : '' ?>>Martabak Manis</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label for="price" class="form-label">Harga *</label>
                                                <div class="input-group">
                                                    <span class="input-group-text">Rp</span>
                                                    <input type="number" class="form-control" id="price" name="price" 
                                                           value="<?= $menuData['price'] ?? '' ?>" step="0.01" min="0" required>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label for="description" class="form-label">Deskripsi</label>
                                        <textarea class="form-control" id="description" name="description" rows="3"><?= htmlspecialchars($menuData['description'] ?? '') ?></textarea>
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label for="ingredients" class="form-label">Bahan/Komposisi</label>
                                        <input type="text" class="form-control" id="ingredients" name="ingredients" 
                                               value="<?= htmlspecialchars($menuData['ingredients'] ?? '') ?>"
                                               placeholder="Contoh: Daging Sapi, Keju Mozarella, Telur Bebek">
                                    </div>
                                    
                                    <div class="mb-3">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" id="is_signature" name="is_signature" 
                                                   <?= ($menuData['is_signature'] ?? 0) ? 'checked' : '' ?>>
                                            <label class="form-check-label" for="is_signature">
                                                Menu Signature
                                            </label>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <label for="image" class="form-label">Gambar Menu</label>
                                        
                                        <?php if ($action == 'edit' && $menuData['image']): ?>
                                        <div class="mb-2">
                                            <img src="../<?= htmlspecialchars($menuData['image']) ?>" 
                                                 alt="Current Image" class="img-fluid rounded" style="max-width: 200px;">
                                        </div>
                                        <?php endif; ?>
                                        
                                        <input type="file" class="form-control" id="image" name="image" accept="image/*">
                                        <div class="form-text">Format: JPG, PNG, GIF. Maksimal 2MB.</div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="text-end">
                                <a href="menu.php" class="btn btn-secondary me-2">Batal</a>
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save me-2"></i>Simpan
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
                
                <?php else: ?>
                <!-- Menu List -->
                <div class="card">
                    <div class="card-body">
                        <?php if (empty($menus)): ?>
                        <div class="text-center py-5">
                            <i class="fas fa-list fa-3x text-muted mb-3"></i>
                            <h5 class="text-muted">Belum ada menu</h5>
                            <p class="text-muted">Mulai dengan menambahkan menu pertama Anda</p>
                            <a href="menu.php?action=add" class="btn btn-primary">
                                <i class="fas fa-plus me-2"></i>Tambah Menu
                            </a>
                        </div>
                        <?php else: ?>
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Gambar</th>
                                        <th>Nama</th>
                                        <th>Kategori</th>
                                        <th>Harga</th>
                                        <th>Status</th>
                                        <th width="150">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($menus as $menu): ?>
                                    <tr>
                                        <td>
                                            <?php if ($menu['image']): ?>
                                            <img src="../<?= htmlspecialchars($menu['image']) ?>" 
                                                 alt="<?= htmlspecialchars($menu['name']) ?>" 
                                                 class="rounded" style="width: 60px; height: 40px; object-fit: cover;">
                                            <?php else: ?>
                                            <div class="bg-light rounded d-flex align-items-center justify-content-center" 
                                                 style="width: 60px; height: 40px;">
                                                <i class="fas fa-image text-muted"></i>
                                            </div>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <strong><?= htmlspecialchars($menu['name']) ?></strong>
                                            <?php if ($menu['is_signature']): ?>
                                            <span class="badge bg-warning text-dark ms-2">Signature</span>
                                            <?php endif; ?>
                                            <?php if ($menu['description']): ?>
                                            <br><small class="text-muted"><?= htmlspecialchars(substr($menu['description'], 0, 50)) ?><?= strlen($menu['description']) > 50 ? '...' : '' ?></small>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <span class="badge bg-<?= $menu['category'] == 'asin' ? 'primary' : 'success' ?>">
                                                <?= $menu['category'] == 'asin' ? 'Asin' : 'Manis' ?>
                                            </span>
                                        </td>
                                        <td><?= formatCurrency($menu['price']) ?></td>
                                        <td>
                                            <span class="badge bg-success">Aktif</span>
                                        </td>
                                        <td>
                                            <div class="btn-group btn-group-sm">
                                                <a href="menu.php?action=edit&id=<?= $menu['id'] ?>" 
                                                   class="btn btn-outline-primary" title="Edit">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <a href="menu.php?action=delete&id=<?= $menu['id'] ?>" 
                                                   class="btn btn-outline-danger" title="Hapus"
                                                   onclick="return confirm('Yakin ingin menghapus menu ini?')">
                                                    <i class="fas fa-trash"></i>
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>