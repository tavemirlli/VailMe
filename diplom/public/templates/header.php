<?php
$currentPage = basename($_SERVER['PHP_SELF']);
$base_url = '';
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
</head>
<body>
    <div class="container">
        <div class="logo">
            <a href="index.php">VailMe</a>
        </div>
        <?php include 'nav.php'; ?>
