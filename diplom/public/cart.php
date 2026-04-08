<?php
session_start();
require_once 'config/db.php';
require_once 'classes/Cart.php';

$pageTitle = 'Корзина - VailMe';

// Получаем текущую корзину
$cart = Cart::getCurrentCart();

// Добавление товара
if (isset($_GET['add'])) {
    $id = (int)$_GET['add'];
    $quantity = isset($_GET['quantity']) ? (int)$_GET['quantity'] : 1;
    $color = isset($_GET['color']) ? $_GET['color'] : '';
    $size = isset($_GET['size']) ? $_GET['size'] : '';
    
    $cart->addItem($id, $quantity, $color, $size);
    header('Location: cart.php');
    exit;
}

// Удаление товара
if (isset($_GET['remove'])) {
    $itemId = (int)$_GET['remove'];
    $cart->removeItem($itemId);
    header('Location: cart.php');
    exit;
}

// Увеличение количества
if (isset($_GET['increase'])) {
    $itemId = (int)$_GET['increase'];
    $cart->increaseQuantity($itemId);
    header('Location: cart.php');
    exit;
}

// Уменьшение количества
if (isset($_GET['decrease'])) {
    $itemId = (int)$_GET['decrease'];
    $cart->decreaseQuantity($itemId);
    header('Location: cart.php');
    exit;
}

$cartItems = $cart->getItems();
$total = $cart->getTotal();

include 'templates/header.php';
?>
<link rel="stylesheet" href="assets/css/style.css">
<link rel="stylesheet" href="assets/css/cart.css">

<div class="container">
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
                        // Получаем фото товара
                        $imgSql = "SELECT image_url FROM product_images WHERE product_id = {$item['product_id']} AND is_main = 1 LIMIT 1";
                        $imgResult = mysqli_query($connect, $imgSql);
                        $img = mysqli_fetch_assoc($imgResult);
                        ?>
                        <?php if ($img): ?>
                            <img src="<?php echo $img['image_url']; ?>" alt="<?php echo htmlspecialchars($item['product_name']); ?>">
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
            
            <div class="cart-summary">
                <div class="cart-total">
                    <span>Итого:</span>
                    <span class="total-amount"><?php echo number_format($total, 0, '.', ' '); ?> ₽</span>
                </div>
                <div class="cart-buttons">
                    <button type="button" class="btn-checkout" onclick="openCheckoutModal()">Оформить заказ</button>
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>

<!-- Модальное окно оформления заказа -->
<div id="checkoutModal" class="modal">
    <div class="modal-content">
        <span class="close" onclick="closeCheckoutModal()">&times;</span>
        <h2>Оформление заказа</h2>
        <form id="checkoutForm" method="POST" action="process-order.php">
            <input type="text" name="name" placeholder="Ваше имя" required>
            <input type="tel" name="phone" placeholder="Телефон" required>
            <input type="email" name="email" placeholder="Email" required>
            <textarea name="comment" placeholder="Комментарий к заказу (необязательно)" rows="3"></textarea>
            <button type="submit">Отправить заказ</button>
            <p class="info-text">После оформления заказа на ваш email придет счет на оплату. Администратор свяжется с вами для уточнения деталей доставки.</p>
        </form>
    </div>
</div>

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