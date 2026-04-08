<?php
session_start();
$currentPage = basename($_SERVER['PHP_SELF']);
$base_url = '';

$cartCount = 0;
if (isset($_SESSION['cart'])) {
    foreach ($_SESSION['cart'] as $item) {
        $cartCount += $item['quantity'];
    }
}

$isAdmin = isset($_SESSION['is_admin']) && $_SESSION['is_admin'] === true;
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $pageTitle ?? 'VailMe - интернет-магазин'; ?></title>
    <link rel="stylesheet" href="/assets/css/style.css">
    <link rel="stylesheet" href="/assets/css/categories.css">
    <link rel="stylesheet" href="assets/css/home.css">
    <link rel="stylesheet" href="assets/css/catalog.css">
    <link rel="stylesheet" href="assets/css/header-icons.css">
</head>
<body>
    <div class="container">
        <div class="logo">
            <a href="index.php">VailMe</a>
        </div>
        
          <?php if ($isAdmin): ?>
    <div class="admin-bar">
        <a href="admin/index.php" class="admin-link">Админ-панель</a>
    </div>
    <?php endif; ?>
        
        <?php include 'nav.php'; ?>