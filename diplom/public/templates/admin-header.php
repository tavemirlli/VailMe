<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Определяем базовый путь
$scriptPath = $_SERVER['SCRIPT_NAME'];
$basePath = rtrim(dirname($scriptPath), '/\\');
if ($basePath == '.' || $basePath == '\\') $basePath = '';
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $pageTitle ?? 'Админ-панель'; ?></title>
    <link rel="stylesheet" href="assets/css/admin-style.css">
</head>
<body>
    <div class="container">
        <div class="admin-nav">
            <div class="stat-box">

            <a href="index.php">Главная</a>            
            <a href="messages.php">Сообщения</a>
            <a href="categories.php">Категории</a>
            <a href="create.php">Добавить товар</a>
        </div>