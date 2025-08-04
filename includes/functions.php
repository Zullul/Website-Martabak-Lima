<?php
/**
 * Common functions for Martabak Lima Website
 */

// Start session if not already started
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Include database configuration
require_once __DIR__ . '/../config/database.php';

/**
 * Check if user is logged in as admin
 */
function isAdminLoggedIn() {
    return isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true;
}

/**
 * Redirect if not admin
 */
function requireAdmin() {
    if (!isAdminLoggedIn()) {
        header('Location: login.php');
        exit();
    }
}

/**
 * Sanitize input data
 */
function sanitize($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

/**
 * Generate CSRF token
 */
function generateCSRFToken() {
    if (!isset($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

/**
 * Verify CSRF token
 */
function verifyCSRFToken($token) {
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}

/**
 * Format currency to Indonesian Rupiah
 */
function formatCurrency($amount) {
    return 'Rp ' . number_format($amount, 0, ',', '.');
}

/**
 * Get all active menu packages
 */
function getMenuPackages($category = null) {
    global $pdo;
    
    $sql = "SELECT * FROM menu_packages WHERE is_active = 1";
    $params = [];
    
    if ($category) {
        $sql .= " AND category = ?";
        $params[] = $category;
    }
    
    $sql .= " ORDER BY is_signature DESC, name ASC";
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    
    return $stmt->fetchAll();
}

/**
 * Get all active toppings
 */
function getToppings($category = null) {
    global $pdo;
    
    $sql = "SELECT * FROM toppings WHERE is_active = 1";
    $params = [];
    
    if ($category) {
        $sql .= " AND category = ?";
        $params[] = $category;
    }
    
    $sql .= " ORDER BY name ASC";
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    
    return $stmt->fetchAll();
}

/**
 * Get topping by ID
 */
function getToppingById($id) {
    global $pdo;
    
    $stmt = $pdo->prepare("SELECT * FROM toppings WHERE id = ?");
    $stmt->execute([$id]);
    
    return $stmt->fetch();
}

/**
 * Get menu package by ID
 */
function getMenuPackageById($id) {
    global $pdo;
    
    $stmt = $pdo->prepare("SELECT * FROM menu_packages WHERE id = ?");
    $stmt->execute([$id]);
    
    return $stmt->fetch();
}

/**
 * Generate unique order number
 */
function generateOrderNumber() {
    return 'ML-' . date('Ymd') . '-' . str_pad(rand(1, 9999), 4, '0', STR_PAD_LEFT);
}

/**
 * Upload image file
 */
function uploadImage($file, $uploadDir = 'uploads/') {
    // Create upload directory if it doesn't exist
    if (!file_exists($uploadDir)) {
        mkdir($uploadDir, 0755, true);
    }
    
    $allowedTypes = ['jpg', 'jpeg', 'png', 'gif'];
    $fileInfo = pathinfo($file['name']);
    $extension = strtolower($fileInfo['extension']);
    
    if (!in_array($extension, $allowedTypes)) {
        return ['success' => false, 'message' => 'File type not allowed'];
    }
    
    $fileName = uniqid() . '.' . $extension;
    $targetPath = $uploadDir . $fileName;
    
    if (move_uploaded_file($file['tmp_name'], $targetPath)) {
        return ['success' => true, 'filename' => $fileName];
    } else {
        return ['success' => false, 'message' => 'Failed to upload file'];
    }
}

/**
 * Delete image file
 */
function deleteImage($filename, $uploadDir = 'uploads/') {
    $filePath = $uploadDir . $filename;
    if (file_exists($filePath)) {
        unlink($filePath);
        return true;
    }
    return false;
}

/**
 * Flash message functions
 */
function setFlashMessage($type, $message) {
    $_SESSION['flash'][$type] = $message;
}

function getFlashMessage($type) {
    if (isset($_SESSION['flash'][$type])) {
        $message = $_SESSION['flash'][$type];
        unset($_SESSION['flash'][$type]);
        return $message;
    }
    return null;
}

function hasFlashMessage($type) {
    return isset($_SESSION['flash'][$type]);
}

/**
 * Pagination helper
 */
function pagination($currentPage, $totalPages, $url) {
    $html = '<nav aria-label="Page navigation">';
    $html .= '<ul class="pagination justify-content-center">';
    
    // Previous button
    if ($currentPage > 1) {
        $html .= '<li class="page-item"><a class="page-link" href="' . $url . '?page=' . ($currentPage - 1) . '">Previous</a></li>';
    }
    
    // Page numbers
    for ($i = 1; $i <= $totalPages; $i++) {
        $active = ($i == $currentPage) ? 'active' : '';
        $html .= '<li class="page-item ' . $active . '"><a class="page-link" href="' . $url . '?page=' . $i . '">' . $i . '</a></li>';
    }
    
    // Next button
    if ($currentPage < $totalPages) {
        $html .= '<li class="page-item"><a class="page-link" href="' . $url . '?page=' . ($currentPage + 1) . '">Next</a></li>';
    }
    
    $html .= '</ul>';
    $html .= '</nav>';
    
    return $html;
}
?>