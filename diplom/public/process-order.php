<?php
session_start();
require_once 'config/db.php';
require_once 'classes/Cart.php';
require_once 'classes/Order.php';

// Отключаем вывод ошибок для предотвращения проблем с заголовками
error_reporting(0);
ini_set('display_errors', 0);

// Проверяем, есть ли товары в корзине
$cart = Cart::getCurrentCart();
$items = $cart->getItems();
$total = $cart->getTotal();

if (empty($items)) {
    header('Location: cart.php');
    exit;
}

// Получаем данные из формы
$name = trim($_POST['name'] ?? '');
$phone = trim($_POST['phone'] ?? '');
$email = trim($_POST['email'] ?? '');
$comment = trim($_POST['comment'] ?? '');

// Валидация
if (empty($name) || empty($phone) || empty($email)) {
    $_SESSION['checkout_error'] = 'Заполните все обязательные поля';
    header('Location: checkout.php');
    exit;
}

// Создаем заказ через класс Order
$userId = $_SESSION['user_id'] ?? null;
$order = Order::create($userId, $name, $phone, $email, $items, $total);

if ($order) {
    // Отправляем счет на email (временно отключено)
    // Order::sendInvoiceEmail($order);
    // Order::markInvoiceSent($order['id']);
    
    // Очищаем корзину
    $cart->clear();
    
    $_SESSION['order_success'] = $order['order_number'];
    header('Location: order-success.php');
    exit;
} else {
    $_SESSION['checkout_error'] = 'Ошибка при создании заказа';
    header('Location: checkout.php');
    exit;
}
?>