<?php
require_once 'config/session.php';
require_once 'config/db.php';
require_once 'classes/Product.php';
require_once 'classes/Category.php';

$pageTitle = 'Новинки - VailMe';

// Получаем товары с пометкой "новинка" (is_new = 1)
$newProducts = Product::findAll('is_new DESC, id DESC LIMIT 6');

if (empty($newProducts)) {
    $newProducts = Product::findAll('id DESC LIMIT 6');
}

include 'templates/header.php';
?>
<link rel="stylesheet" href="assets/css/style.css">
<link rel="stylesheet" href="assets/css/categories.css">
<link rel="stylesheet" href="assets/css/new-arrivals.css">

<div class="container">
    <div class="main-content">
        
        <h1>Новинки</h1>
        
        <!-- Рекламный баннер -->
        <div class="promo-banner">
            <div class="promo-content">
                <h3>Скидка за вход в аккаунт</h3>
                <p>Зарегистрируйтесь и получите скидку 10% на первую покупку</p>
                <a href="register.php" class="btn-promo">Зарегистрироваться</a>
            </div>
        </div>
        
        <!-- ПЕРВАЯ СТРОКА ТОВАРОВ (первые 3 товара) -->
        <div class="products-row">
            <?php for ($i = 0; $i < 3 && $i < count($newProducts); $i++): 
                $product = $newProducts[$i];
                $mainImage = $product->getMainImage();
                $isNew = $product->getIsNew();
            ?>
                <div class="product-card">
                    <div class="product-image">
                        <?php if ($mainImage): ?>
                            <img src="<?php echo htmlspecialchars($mainImage->image_url); ?>" 
                                 alt="<?php echo htmlspecialchars($product->getName()); ?>">
                        <?php else: ?>
                            <div class="no-image">Нет фото</div>
                        <?php endif; ?>
                        <?php if ($isNew): ?>
                            <span class="badge-new">Новинка</span>
                        <?php endif; ?>
                        <?php if ($product->isOnSale()): ?>
                            <span class="badge-sale">-<?php echo $product->getDiscountPercent(); ?>%</span>
                        <?php endif; ?>
                    </div>
                    <div class="product-info">
                        <h3 class="product-title">
                            <!-- ИСПРАВЛЕНО: ссылка на папку products -->
                            <a href="products/index.php?id=<?php echo $product->getId(); ?>">
                                <?php echo htmlspecialchars($product->getName()); ?>
                            </a>
                        </h3>
                        <div class="product-price">
                            <?php if ($product->isOnSale()): ?>
                                <span class="old-price"><?php echo number_format($product->getOldPrice(), 0, '.', ' '); ?> ₽</span>
                                <span class="current-price"><?php echo number_format($product->getPrice(), 0, '.', ' '); ?> ₽</span>
                            <?php else: ?>
                                <span class="current-price"><?php echo number_format($product->getPrice(), 0, '.', ' '); ?> ₽</span>
                            <?php endif; ?>
                        </div>
                        
                    </div>
                </div>
            <?php endfor; ?>
        </div>
        
        <!-- РЕКЛАМНЫЙ БАННЕР МЕЖДУ СТРОКАМИ -->
        <div class="promo-banner-middle">
            <div class="promo-content-middle">
                <h3>🔥 Горячая распродажа 🔥</h3>
                <p>Скидки до 50% на всю коллекцию</p>
                <a href="catalog.php?discount=1" class="btn-promo-middle">Успей купить</a>
            </div>
        </div>
        
        <!-- ВТОРАЯ СТРОКА ТОВАРОВ (следующие 3 товара) -->
        <div class="products-row">
            <?php for ($i = 3; $i < 6 && $i < count($newProducts); $i++): 
                $product = $newProducts[$i];
                $mainImage = $product->getMainImage();
                $isNew = $product->getIsNew();
            ?>
                <div class="product-card">
                    <div class="product-image">
                        <?php if ($mainImage): ?>
                            <img src="<?php echo htmlspecialchars($mainImage->image_url); ?>" 
                                 alt="<?php echo htmlspecialchars($product->getName()); ?>">
                        <?php else: ?>
                            <div class="no-image">Нет фото</div>
                        <?php endif; ?>
                        <?php if ($isNew): ?>
                            <span class="badge-new">Новинка</span>
                        <?php endif; ?>
                        <?php if ($product->isOnSale()): ?>
                            <span class="badge-sale">-<?php echo $product->getDiscountPercent(); ?>%</span>
                        <?php endif; ?>
                    </div>
                    <div class="product-info">
                        <h3 class="product-title">
                            <!-- ИСПРАВЛЕНО: ссылка на папку products -->
                            <a href="products/index.php?id=<?php echo $product->getId(); ?>">
                                <?php echo htmlspecialchars($product->getName()); ?>
                            </a>
                        </h3>
                        <div class="product-price">
                            <?php if ($product->isOnSale()): ?>
                                <span class="old-price"><?php echo number_format($product->getOldPrice(), 0, '.', ' '); ?> ₽</span>
                                <span class="current-price"><?php echo number_format($product->getPrice(), 0, '.', ' '); ?> ₽</span>
                            <?php else: ?>
                                <span class="current-price"><?php echo number_format($product->getPrice(), 0, '.', ' '); ?> ₽</span>
                            <?php endif; ?>
                        </div>
                        <a href="cart/add.php?id=<?php echo $product->getId(); ?>" class="btn-add">В корзину</a>
                    </div>
                </div>
            <?php endfor; ?>
            
            <!-- Если товаров меньше 6, показываем заглушки -->
            <?php for ($i = count($newProducts); $i < 6; $i++): ?>
                <div class="product-card placeholder">
                    <div class="product-image">
                        <div class="no-image">Скоро появится</div>
                    </div>
                    <div class="product-info">
                        <h3 class="product-title">Новинка скоро</h3>
                        <div class="product-price">— ₽</div>
                    </div>
                </div>
            <?php endfor; ?>
        </div>
        
        <!-- Кнопка к каталогу -->
        <div class="catalog-button-section">
            <a href="catalog.php" class="btn-catalog-large">Перейти в каталог</a>
        </div>
        
    </div>
</div>

<?php include 'templates/footer.php'; ?>