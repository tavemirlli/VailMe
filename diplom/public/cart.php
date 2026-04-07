<?php
session_start();
require_once 'config/db.php';

$pageTitle = 'Корзина - VailMe';

if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

// Добавление товара
if (isset($_GET['add'])) {
    $id = (int)$_GET['add'];
    $quantity = isset($_GET['quantity']) ? (int)$_GET['quantity'] : 1;
    $color = isset($_GET['color']) ? $_GET['color'] : '';
    $size = isset($_GET['size']) ? $_GET['size'] : '';
    
    // Получаем информацию о товаре и варианте
    $sql = "SELECT p.*, pv.id as variant_id, pv.quantity as variant_quantity, pv.price as variant_price
            FROM products p
            LEFT JOIN product_variants pv ON p.id = pv.product_id AND pv.color = '$color' AND pv.size = '$size'
            WHERE p.id = $id";
    $result = mysqli_query($connect, $sql);
    $product = mysqli_fetch_assoc($result);
    
    if ($product) {
        $finalPrice = $product['variant_price'] ?? $product['price'];
        $maxQty = $product['variant_quantity'] ?? 99;
        $variantId = $product['variant_id'] ?? 0;
        
        $key = $id . '_' . $color . '_' . $size;
        
        if (isset($_SESSION['cart'][$key])) {
            $newQty = $_SESSION['cart'][$key]['quantity'] + $quantity;
            $_SESSION['cart'][$key]['quantity'] = ($newQty <= $maxQty) ? $newQty : $maxQty;
        } else {
            $_SESSION['cart'][$key] = [
                'id' => $product['id'],
                'name' => $product['name'],
                'price' => $finalPrice,
                'quantity' => min($quantity, $maxQty),
                'color' => $color,
                'size' => $size,
                'variant_id' => $variantId,
                'max_quantity' => $maxQty
            ];
        }
    }
    header('Location: cart.php');
    exit;
}

// Удаление товара
if (isset($_GET['remove'])) {
    $key = $_GET['remove'];
    unset($_SESSION['cart'][$key]);
    header('Location: cart.php');
    exit;
}

// Увеличение количества
if (isset($_GET['increase'])) {
    $key = $_GET['increase'];
    if (isset($_SESSION['cart'][$key])) {
        $maxQty = $_SESSION['cart'][$key]['max_quantity'];
        if ($_SESSION['cart'][$key]['quantity'] < $maxQty) {
            $_SESSION['cart'][$key]['quantity']++;
        }
    }
    header('Location: cart.php');
    exit;
}

// Уменьшение количества
if (isset($_GET['decrease'])) {
    $key = $_GET['decrease'];
    if (isset($_SESSION['cart'][$key])) {
        if ($_SESSION['cart'][$key]['quantity'] > 1) {
            $_SESSION['cart'][$key]['quantity']--;
        } else {
            unset($_SESSION['cart'][$key]);
        }
    }
    header('Location: cart.php');
    exit;
}

$cartItems = [];
$total = 0;
foreach ($_SESSION['cart'] as $key => $item) {
    $sql = "SELECT * FROM products WHERE id = {$item['id']}";
    $result = mysqli_query($connect, $sql);
    $product = mysqli_fetch_assoc($result);
    
    if ($product) {
        // Получаем фото товара
        $imgSql = "SELECT image_url FROM product_images WHERE product_id = {$item['id']} AND is_main = 1 LIMIT 1";
        $imgResult = mysqli_query($connect, $imgSql);
        $img = mysqli_fetch_assoc($imgResult);
        
        $cartItems[] = [
            'key' => $key,
            'id' => $item['id'],
            'name' => $product['name'],
            'price' => $item['price'],
            'quantity' => $item['quantity'],
            'color' => $item['color'] ?? '',
            'size' => $item['size'] ?? '',
            'image' => $img ? $img['image_url'] : '',
            'total' => $item['price'] * $item['quantity'],
            'max_quantity' => $item['max_quantity']
        ];
        $total += $item['price'] * $item['quantity'];
    }
}

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
                        <?php if ($item['image']): ?>
                            <img src="<?php echo $item['image']; ?>" alt="<?php echo $item['name']; ?>">
                        <?php else: ?>
                            <div class="no-image">Нет фото</div>
                        <?php endif; ?>
                    </div>
                    <div class="cart-item-info">
                        <h3><?php echo htmlspecialchars($item['name']); ?></h3>
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
                            <a href="cart.php?decrease=<?php echo urlencode($item['key']); ?>" class="quantity-btn minus">-</a>
                            <span class="quantity-value"><?php echo $item['quantity']; ?></span>
                            <a href="cart.php?increase=<?php echo urlencode($item['key']); ?>" class="quantity-btn plus">+</a>
                        </div>
                    </div>
                    <div class="cart-item-total">
                        <?php echo number_format($item['total'], 0, '.', ' '); ?> ₽
                    </div>
                    <div class="cart-item-remove">
                        <a href="cart.php?remove=<?php echo urlencode($item['key']); ?>" class="remove-btn">✕</a>
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