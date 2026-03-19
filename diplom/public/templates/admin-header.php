<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $pageTitle ?? 'Админ-панель'; ?></title>
    <link rel="stylesheet" href="assets/css/admin-style.css">
    <link href='https://fonts.googleapis.com/css?family=Montserrat' rel='stylesheet'>
</head>
<body>
    <div class="container">
        <div class="admin-nav">
            <a href="index.php">Товары</a>
            <a href="categories.php">Категории</a>
            <a href="create.php">Добавить товар</a>
        </div>