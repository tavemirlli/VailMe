<?php
session_start();
require_once 'config/db.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: index.php');
    exit;
}

$name = mysqli_real_escape_string($connect, $_POST['name']);
$phone = mysqli_real_escape_string($connect, $_POST['phone']);
$email = mysqli_real_escape_string($connect, $_POST['email']);
$comment = mysqli_real_escape_string($connect, $_POST['comment'] ?? '');

if (empty($name) || empty($phone) || empty($email)) {
    $_SESSION['error'] = 'Заполните все обязательные поля';
    header('Location: cart.php');
    exit;
}

if (empty($_SESSION['cart'])) {
    header('Location: cart.php');
    exit;
}

// Считаем сумму
$total = 0;
foreach ($_SESSION['cart'] as $id => $item) {
    $sql = "SELECT price FROM products WHERE id = $id";
    $result = mysqli_query($connect, $sql);
    $product = mysqli_fetch_assoc($result);
    $total += $product['price'] * $item['quantity'];
}

// Создаем заказ
$orderNumber = 'ORD-' . date('Ymd') . '-' . rand(1000, 9999);
$sql = "INSERT INTO orders (order_number, customer_name, customer_phone, customer_email, total_amount, admin_comment) 
        VALUES ('$orderNumber', '$name', '$phone', '$email', $total, '$comment')";
mysqli_query($connect, $sql);
$orderId = mysqli_insert_id($connect);

// Добавляем товары в заказ
foreach ($_SESSION['cart'] as $id => $item) {
    $sql = "SELECT name, price FROM products WHERE id = $id";
    $result = mysqli_query($connect, $sql);
    $product = mysqli_fetch_assoc($result);
    
    $totalPrice = $product['price'] * $item['quantity'];
    $sql = "INSERT INTO order_items (order_id, product_id, product_name, product_price, quantity, total_price) 
            VALUES ($orderId, $id, '{$product['name']}', {$product['price']}, {$item['quantity']}, $totalPrice)";
    mysqli_query($connect, $sql);
}

// Очищаем корзину
unset($_SESSION['cart']);

// Отправляем письмо с счетом
$to = $email;
$subject = "Счет на оплату №$orderNumber";

// Получаем товары для письма
$itemsHtml = '';
$itemsSql = "SELECT * FROM order_items WHERE order_id = $orderId";
$itemsResult = mysqli_query($connect, $itemsSql);
while ($item = mysqli_fetch_assoc($itemsResult)) {
    $itemsHtml .= "<tr>";
    $itemsHtml .= "<td>" . htmlspecialchars($item['product_name']) . "</td>";
    $itemsHtml .= "<td>" . $item['quantity'] . "</td>";
    $itemsHtml .= "<td>" . number_format($item['product_price'], 0, '.', ' ') . " ₽</td>";
    $itemsHtml .= "<td>" . number_format($item['total_price'], 0, '.', ' ') . " ₽</td>";
    $itemsHtml .= "</tr>";
}

$message = "
<!DOCTYPE html>
<html>
<head>
    <meta charset='UTF-8'>
    <title>Счет на оплату №$orderNumber</title>
    <style>
        body { font-family: Arial, sans-serif; }
        .invoice { max-width: 600px; margin: 0 auto; padding: 20px; border: 1px solid #ddd; }
        .header { text-align: center; border-bottom: 2px solid #F0B1D3; padding-bottom: 10px; }
        .total { font-size: 20px; font-weight: bold; color: #F0B1D3; text-align: right; margin-top: 20px; }
        table { width: 100%; border-collapse: collapse; }
        th, td { padding: 10px; text-align: left; border-bottom: 1px solid #eee; }
        .bank-details { background: #f9f9f9; padding: 15px; margin-top: 20px; border-radius: 8px; }
    </style>
</head>
<body>
    <div class='invoice'>
        <div class='header'>
            <h2>Счет на оплату №$orderNumber</h2>
            <p>Дата: " . date('d.m.Y') . "</p>
        </div>
        
        <h3>Данные покупателя:</h3>
        <p><strong>$name</strong><br>
        Телефон: $phone<br>
        Email: $email</p>
        
        <h3>Товары:</h3>
        <table>
            <thead><tr><th>Товар</th><th>Кол-во</th><th>Цена</th><th>Сумма</th></tr></thead>
            <tbody>$itemsHtml</tbody>
        </table>
        
        <div class='total'>
            Итого к оплате: " . number_format($total, 0, '.', ' ') . " ₽
        </div>
        
        <div class='bank-details'>
            <h3>Реквизиты для оплаты:</h3>
            <p><strong>ИП Фамилия Имя Отчество</strong><br>
            ИНН: 1234567890<br>
            Расчетный счет: 40802810XXXXXXXXXX<br>
            Банк: Т-Банк<br>
            БИК: 044525974</p>
            <p>После оплаты, пожалуйста, сообщите нам об этом по телефону <strong>+7-988-888-88-88</strong></p>
        </div>
        
        <p style='text-align: center; margin-top: 30px; color: #999;'>
            Спасибо за заказ!<br>
            Администратор свяжется с вами для уточнения деталей доставки.
        </p>
    </div>
</body>
</html>";

$headers = "MIME-Version: 1.0\r\n";
$headers .= "Content-type:text/html;charset=UTF-8\r\n";
$headers .= "From: VailMe <noreply@vailme.ru>\r\n";

mail($to, $subject, $message, $headers);

// Отмечаем, что счет отправлен
mysqli_query($connect, "UPDATE orders SET invoice_sent = 1, invoice_sent_at = NOW() WHERE id = $orderId");

$_SESSION['order_success'] = $orderNumber;
header('Location: order-success.php');
exit;
?>