<?php

require_once 'config/db.php';

$sql = "SELECT * FROM products ORDER BY id DESC"; 
$result = mysqli_query($connect, $sql);

if (!$result) {
    die('Ошибка запроса: ' . mysqli_error($connect));
}
$products = mysqli_fetch_all($result, MYSQLI_ASSOC); 
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <title>Главная страница магазина</title>
</head>
<body>
    <h1>Vailme</h1>
    <a href="admin/">Перейти в админку</a>

    <div style="display: flex; flex-wrap: wrap;">
        <?php foreach ($products as $product): ?>
            <div style="border: 1px solid #ccc; margin: 10px; padding: 10px; width: 200px;">
                <h3><?php echo htmlspecialchars($product['name']); ?></h3>
                <p>Цена: <?php echo htmlspecialchars($product['price']); ?> руб.</p>
            </div>
        <?php endforeach; ?>
    </div>
</body>
</html>