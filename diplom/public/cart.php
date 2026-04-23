<?php
session_start();
require_once 'config/db.php';
require_once 'classes/Cart.php';
require_once 'classes/Product.php';
require_once 'classes/Promocode.php';

$pageTitle = 'Корзина - VailMe';

$cart = Cart::getCurrentCart();

// Обработка действий
if (isset($_GET['add'])) {
    $id = (int)$_GET['add'];
    $quantity = isset($_GET['quantity']) ? (int)$_GET['quantity'] : 1;
    $color = isset($_GET['color']) ? $_GET['color'] : '';
    $size = isset($_GET['size']) ? $_GET['size'] : '';
    
    $cart->addItem($id, $quantity, $color, $size);
    header('Location: cart.php');
    exit;
}

if (isset($_GET['remove'])) {
    $itemId = (int)$_GET['remove'];
    $cart->removeItem($itemId);
    header('Location: cart.php');
    exit;
}

if (isset($_GET['increase'])) {
    $itemId = (int)$_GET['increase'];
    $cart->increaseQuantity($itemId);
    header('Location: cart.php');
    exit;
}

if (isset($_GET['decrease'])) {
    $itemId = (int)$_GET['decrease'];
    $cart->decreaseQuantity($itemId);
    header('Location: cart.php');
    exit;
}

// Применение промокода
$discount = 0;
$promocodeError = '';
$promocodeSuccess = '';
$appliedPromocode = null;

if (isset($_POST['apply_promocode'])) {
    $code = trim($_POST['promocode'] ?? '');
    $total = $cart->getTotal();
    
    $promocode = Promocode::findByCode($code);
    
    if ($promocode) {
        $validation = $promocode->isValid($total);
        if ($validation['valid']) {
            $discount = $promocode->calculateDiscount($total);
            $_SESSION['applied_promocode'] = $code;
            $_SESSION['promocode_discount'] = $discount;
            $_SESSION['promocode_id'] = $promocode->id;
            $promocodeSuccess = 'Промокод применен! Скидка: ' . number_format($discount, 0, '.', ' ') . ' ₽';
            $appliedPromocode = $promocode;
        } else {
            $promocodeError = $validation['message'];
        }
    } else {
        $promocodeError = 'Промокод не найден';
    }
}

if (isset($_POST['remove_promocode'])) {
    unset($_SESSION['applied_promocode']);
    unset($_SESSION['promocode_discount']);
    unset($_SESSION['promocode_id']);
    $promocodeSuccess = 'Промокод удален';
}

// Получаем актуальную скидку
if (isset($_SESSION['applied_promocode'])) {
    $promocode = Promocode::findByCode($_SESSION['applied_promocode']);
    if ($promocode) {
        $total = $cart->getTotal();
        $discount = $promocode->calculateDiscount($total);
        $_SESSION['promocode_discount'] = $discount;
        $appliedPromocode = $promocode;
    } else {
        unset($_SESSION['applied_promocode']);
        unset($_SESSION['promocode_discount']);
        unset($_SESSION['promocode_id']);
    }
}

$cartItems = $cart->getItems();
$originalTotal = $cart->getTotal();
$finalTotal = $originalTotal - ($_SESSION['promocode_discount'] ?? 0);
if ($finalTotal < 0) $finalTotal = 0;

include 'templates/header.php';
?>
<link rel="stylesheet" href="assets/css/style.css">
<link rel="stylesheet" href="assets/css/cart.css">

<div class="cart-page">
    <h1>Корзина</h1>
    
    <?php if (empty($cartItems)): ?>
        <div class="empty-cart">
            <p>Ваша корзина пуста</p>
            <a href="catalog.php" class="btn-continue">Продолжить покупки</a>
        </div>
    <?php else: ?>
        <div class="cart-items">
            <?php foreach ($cartItems as $item): ?>
            <div class="cart-item">
                <div class="cart-item-image">
                    <?php 
                    $product = Product::findById($item['product_id']);
                    $mainImage = $product ? $product->getMainImage() : null;
                    ?>
                    <?php if ($mainImage): ?>
                        <img src="<?php echo $mainImage->image_url; ?>" alt="<?php echo htmlspecialchars($item['product_name']); ?>">
                    <?php else: ?>
                        <div class="no-image">Нет фото</div>
                    <?php endif; ?>
                </div>
                <div class="cart-item-info">
                    <h3><?php echo htmlspecialchars($item['product_name']); ?></h3>
                    <div class="cart-item-price"><?php echo number_format($item['price'], 0, '.', ' '); ?> ₽</div>
                    <div class="cart-item-variant">
                        <?php if ($item['color']): ?>
                            <span>Цвет: <?php echo htmlspecialchars($item['color']); ?></span>
                        <?php endif; ?>
                        <?php if ($item['size']): ?>
                            <span>Размер: <?php echo htmlspecialchars($item['size']); ?></span>
                        <?php endif; ?>
                    </div>
                    <div class="cart-item-stock">
                        Доступно: <?php echo $item['max_quantity']; ?> шт.
                    </div>
                </div>
                <div class="cart-item-quantity">
                    <div class="quantity-control">
                        <a href="cart.php?decrease=<?php echo $item['id']; ?>" class="quantity-btn minus">-</a>
                        <span class="quantity-value"><?php echo $item['quantity']; ?></span>
                        <a href="cart.php?increase=<?php echo $item['id']; ?>" class="quantity-btn plus">+</a>
                    </div>
                </div>
                <div class="cart-item-total">
                    <?php echo number_format($item['total'], 0, '.', ' '); ?> ₽
                </div>
                <div class="cart-item-remove">
                    <a href="cart.php?remove=<?php echo $item['id']; ?>" class="remove-btn">✕</a>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        
        <!-- Блок промокода -->
        <div class="promocode-section">
            <h3>Промокод</h3>
            <?php if ($promocodeError): ?>
                <div class="promocode-error"><?php echo $promocodeError; ?></div>
            <?php endif; ?>
            <?php if ($promocodeSuccess): ?>
                <div class="promocode-success"><?php echo $promocodeSuccess; ?></div>
            <?php endif; ?>
            
            <?php if (isset($_SESSION['applied_promocode'])): ?>
                <div class="applied-promocode">
                    <span>Применен промокод: <strong><?php echo $_SESSION['applied_promocode']; ?></strong></span>
                    <form method="POST" style="display: inline;">
                        <button type="submit" name="remove_promocode" class="remove-promocode-btn">✕</button>
                    </form>
                </div>
            <?php else: ?>
                <form method="POST" class="promocode-form">
                    <input type="text" name="promocode" placeholder="Введите промокод">
                    <button type="submit" name="apply_promocode">Применить</button>
                </form>
            <?php endif; ?>
        </div>
        
        <div class="cart-summary">
            <div class="cart-total">
                <span>Товары на сумму:</span>
                <span><?php echo number_format($originalTotal, 0, '.', ' '); ?> ₽</span>
            </div>
            <?php if ($discount > 0): ?>
            <div class="cart-discount">
                <span>Скидка:</span>
                <span class="discount-amount">- <?php echo number_format($discount, 0, '.', ' '); ?> ₽</span>
            </div>
            <?php endif; ?>
            <div class="cart-final-total">
                <span>Итого к оплате:</span>
                <span class="total-amount"><?php echo number_format($finalTotal, 0, '.', ' '); ?> ₽</span>
            </div>
            <div class="cart-buttons">
                <button type="button" class="btn-checkout" onclick="openCheckoutModal()">Оформить заказ</button>
            </div>
        </div>
    <?php endif; ?>
</div>

<!-- Модальное окно оформления заказа -->
<div id="checkoutModal" class="modal">
    <div class="modal-content">
        <span class="close" onclick="closeCheckoutModal()">&times;</span>
        <h2>Оформление заказа</h2>
        <form id="checkoutForm" method="POST" action="process-order.php">
            <?php if (isset($_SESSION['user_id'])): ?>
                <input type="hidden" name="name" value="<?php echo $_SESSION['user_name'] ?? ''; ?>">
                <input type="hidden" name="phone" value="<?php echo $_SESSION['user_phone'] ?? ''; ?>">
                <input type="hidden" name="email" value="<?php echo $_SESSION['user_email'] ?? ''; ?>">
            <?php else: ?>
                <input type="text" name="name" placeholder="Ваше имя" required>
                <input type="tel" name="phone" placeholder="Телефон" required>
                <input type="email" name="email" placeholder="Email" required>
            <?php endif; ?>
            <textarea name="comment" placeholder="Комментарий к заказу (необязательно)" rows="3"></textarea>
            <button type="submit">Оформить заказ</button>
            <p class="info-text">После оформления заказа на ваш email придет счет на оплату. Администратор свяжется с вами для уточнения деталей доставки.</p>
        </form>
    </div>
</div>

<style>
.promocode-section {
    background: #f9f9f9;
    padding: 20px;
    border-radius: 12px;
    margin: 20px 0;
}
.promocode-form {
    display: flex;
    gap: 10px;
}
.promocode-form input {
    flex: 1;
    padding: 12px;
    border: 1px solid #ddd;
    border-radius: 8px;
}
.promocode-form button {
    padding: 12px 20px;
    background: #F0B1D3;
    color: white;
    border: none;
    border-radius: 8px;
    cursor: pointer;
}
.applied-promocode {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 12px;
    background: #e8f5e9;
    border-radius: 8px;
    color: #2e7d32;
}
.remove-promocode-btn {
    background: none;
    border: none;
    cursor: pointer;
    font-size: 18px;
    color: #999;
}
.promocode-error {
    color: #f44336;
    margin-bottom: 10px;
}
.promocode-success {
    color: #4caf50;
    margin-bottom: 10px;
}
.cart-discount {
    display: flex;
    justify-content: space-between;
    margin: 10px 0;
    color: #4caf50;
}
.discount-amount {
    font-weight: bold;
}
.cart-final-total {
    display: flex;
    justify-content: space-between;
    font-size: 24px;
    margin-top: 15px;
    padding-top: 15px;
    border-top: 1px solid #ddd;
}
</style>

<script>
function openCheckoutModal() {
    document.getElementById('checkoutModal').style.display = 'block';
}
function closeCheckoutModal() {
    document.getElementById('checkoutModal').style.display = 'none';
}
window.onclick = function(event) {
    if (event.target == document.getElementById('checkoutModal')) {
        closeCheckoutModal();
    }
}
</script>

<?php include 'templates/footer.php'; ?>