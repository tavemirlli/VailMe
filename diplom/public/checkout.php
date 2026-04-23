<?php
session_start();
require_once 'config/db.php';
require_once 'classes/Cart.php';
require_once 'classes/Order.php';
require_once 'classes/User.php';

$pageTitle = 'Оформление заказа - VailMe';

$cart = Cart::getCurrentCart();
$items = $cart->getItems();
$total = $cart->getTotal();

if (empty($items)) {
    header('Location: cart.php');
    exit;
}

$isLoggedIn = isset($_SESSION['user_id']);
$userData = [];

if ($isLoggedIn) {
    $user = new User();
    $user->load($_SESSION['user_id']);
    $userData = [
        'name' => $user->getName(),
        'phone' => $user->getPhone(),
        'email' => $user->getEmail()
    ];
}

$error = $_SESSION['checkout_error'] ?? '';
unset($_SESSION['checkout_error']);

include 'templates/header.php';
?>
<link rel="stylesheet" href="assets/css/checkout.css">
<link rel="stylesheet" href="assets/css/style.css">
<div class="container">
    <div class="checkout-container">
        <h1>Оформление заказа</h1>
        
        <?php if ($isLoggedIn): ?>
            <div class="user-info-card">
                <h3>Ваши данные</h3>
                <div class="user-info-row">
                    <span class="info-label">Имя:</span>
                    <span class="info-value"><?php echo htmlspecialchars($userData['name']); ?></span>
                </div>
                <div class="user-info-row">
                    <span class="info-label">Телефон:</span>
                    <span class="info-value"><?php echo htmlspecialchars($userData['phone'] ?: 'Не указан'); ?></span>
                </div>
                <div class="user-info-row">
                    <span class="info-label">Email:</span>
                    <span class="info-value"><?php echo htmlspecialchars($userData['email']); ?></span>
                </div>
                <div class="user-info-row">
                    <span class="info-label">Адрес доставки:</span>
                    <span class="info-value">Уточнит администратор при звонке</span>
                </div>
                <p class="info-note">
                    Если данные неверны, <a href="profile.php">измените их в профиле</a>
                </p>
            </div>
        <?php endif; ?>
        
        <div class="checkout-layout">
            <div class="checkout-form">
                <?php if ($error): ?>
                    <div class="error-message"><?php echo $error; ?></div>
                <?php endif; ?>
                
                <?php if (!$isLoggedIn): ?>
                    <form method="POST" action="process-order.php">
                        <div class="form-group">
                            <label>Ваше имя *</label>
                            <input type="text" name="name" required>
                        </div>
                        <div class="form-group">
                            <label>Телефон *</label>
                            <input type="tel" name="phone" required>
                        </div>
                        <div class="form-group">
                            <label>Email *</label>
                            <input type="email" name="email" required>
                        </div>
                        <div class="form-group">
                            <label>Комментарий к заказу</label>
                            <textarea name="comment" rows="3" placeholder="Дополнительная информация"></textarea>
                        </div>
                        
                        <button type="submit" class="submit-btn">Оформить заказ</button>
                        
                        <p class="info-text">
                            После оформления заказа на ваш email придет счет на оплату.<br>
                            Администратор свяжется с вами для уточнения деталей доставки.
                        </p>
                    </form>
                <?php else: ?>
                    <form method="POST" action="process-order.php">
                        <input type="hidden" name="name" value="<?php echo htmlspecialchars($userData['name']); ?>">
                        <input type="hidden" name="phone" value="<?php echo htmlspecialchars($userData['phone']); ?>">
                        <input type="hidden" name="email" value="<?php echo htmlspecialchars($userData['email']); ?>">
                        
                        <div class="form-group">
                            <label>Комментарий к заказу</label>
                            <textarea name="comment" rows="3" placeholder="Дополнительная информация (необязательно)"></textarea>
                        </div>
                        
                        <button type="submit" class="submit-btn">Оформить заказ</button>
                        
                        <p class="info-text">
                            После оформления заказа на ваш email придет счет на оплату.<br>
                            Администратор свяжется с вами для уточнения деталей доставки.
                        </p>
                    </form>
                <?php endif; ?>
            </div>
            
            <div class="checkout-summary">
                <h3>Ваш заказ</h3>
                <div class="checkout-items">
                    <?php foreach ($items as $item): ?>
                    <div class="checkout-item">
                        <div class="checkout-item-info">
                            <span class="checkout-item-name"><?php echo htmlspecialchars($item['product_name']); ?></span>
                            <span class="checkout-item-quantity">x <?php echo $item['quantity']; ?></span>
                        </div>
                        <div class="checkout-item-price">
                            <?php echo number_format($item['total'], 0, '.', ' '); ?> ₽
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
                <div class="checkout-total">
                    <span>Итого:</span>
                    <span class="total-amount"><?php echo number_format($total, 0, '.', ' '); ?> ₽</span>
                </div>
            </div>
        </div>
    </div>
</div>
<?php include 'templates/footer.php'; ?>