<?php
session_start();
require_once 'config/db.php';
require_once 'classes/Cart.php';
require_once 'classes/Order.php';
require_once 'classes/Promocode.php';

$cart = Cart::getCurrentCart();
$items = $cart->getItems();
$total = $cart->getTotal();

if (empty($items)) {
    header('Location: cart.php');
    exit;
}

$name = trim($_POST['name'] ?? '');
$phone = trim($_POST['phone'] ?? '');
$email = trim($_POST['email'] ?? '');
$comment = trim($_POST['comment'] ?? '');

if (empty($name) || empty($phone) || empty($email)) {
    $_SESSION['checkout_error'] = 'Заполните все обязательные поля';
    header('Location: checkout.php');
    exit;
}

$userId = $_SESSION['user_id'] ?? null;
$order = Order::create($userId, $name, $phone, $email, $items, $total);

if ($order) {
    $orderId = $order['id'];
    
    if (isset($_SESSION['promocode_id'])) {
        $promocode = Promocode::findById($_SESSION['promocode_id']);
        if ($promocode) {
            $result = $promocode->applyToOrder($orderId, $total);
            if ($result['success']) {

                unset($_SESSION['promocode_id']);
                unset($_SESSION['applied_promocode']);
                unset($_SESSION['promocode_discount']);
            }
        }
    }
    

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