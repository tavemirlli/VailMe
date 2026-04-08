<?php
session_start();
require_once 'config/db.php';
require_once 'classes/Cart.php';
require_once 'classes/User.php';

if (isset($_SESSION['user_id'])) {
    $cart = Cart::getCurrentCart();
    $cartItems = $cart->getItems();
    
    if (!empty($cartItems)) {
        $cartData = [];
        foreach ($cartItems as $item) {
            $cartData[] = [
                'id' => $item['product_id'],
                'quantity' => $item['quantity'],
                'price' => $item['price'],
                'variant_id' => $item['variant_id'] ?? 0,
                'color' => $item['color'],
                'size' => $item['size']
            ];
        }
        Cart::saveCartForUser($_SESSION['user_id'], $cartData);
    }
}

session_destroy();
header('Location: index.php');
exit;
?>