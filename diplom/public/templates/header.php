<?php
session_start();
$currentPage = basename($_SERVER['PHP_SELF']);
$base_url = '';

// Подсчет товаров в корзине
$cartCount = 0;
if (isset($_SESSION['cart'])) {
    foreach ($_SESSION['cart'] as $item) {
        $cartCount += $item['quantity'];
    }
}
?>
<!DOCTYPE html>
<html lang="ru">
<link rel="stylesheet" href="assets/css/style.css">
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
        
        <?php include 'nav.php'; ?>
    
