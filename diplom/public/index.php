<?php
require_once 'config/session.php';
require_once 'config/db.php';
require_once 'classes/Product.php';
require_once 'classes/HomePage.php';
require_once 'classes/Category.php';

$pageTitle = 'Главная - VailMe';

$homePage = new HomePage();
$heroData = $homePage->getHeroData();

// Получаем все товары
$allProducts = Product::findAll('id DESC');

// Товары недели - первые 4 товара
$weeklyProducts = array_slice($allProducts, 0, 4);

// Женские товары - category_id = 1
$womenProducts = array_filter($allProducts, function($product) {
    return $product->category_id == 1;
});
$womenProducts = array_slice($womenProducts, 0, 4);

// Мужские товары - category_id = 2
$menProducts = array_filter($allProducts, function($product) {
    return $product->category_id == 2;
});
$menProducts = array_slice($menProducts, 0, 4);

$orderSteps = $homePage->getOrderSteps();

include 'templates/header.php';
?>
<link rel="stylesheet" href="assets/css/style.css">
<link rel="stylesheet" href="assets/css/categories.css">

<div class="main-content">
    <a href="admin/index.php">Перейти в админку</a>
    
    <div class="hero-section">
        <div class="hero-text">
            <h2><?php echo htmlspecialchars($heroData['title']); ?></h2>
            <p><?php echo htmlspecialchars($heroData['subtitle']); ?></p>
            <a href="<?php echo $heroData['button_link']; ?>" class="btn-login">
                <?php echo $heroData['button_text']; ?>
            </a>
        </div>
        <div class="hero-image">
            <?php echo $heroData['image_size']; ?>
        </div>
    </div>
    
    <!-- Категории в круглешках -->
    <div class="categories-circle-section">
        <div class="section-header">
            <h3>Категории</h3>
            <a href="categories.php" class="section-link">Все категории →</a>
        </div>
        <div class="categories-circle-list">
            <?php 
            $allCategories = Category::findAll('name');
            $displayCategories = array_slice($allCategories, 0, 5);
            foreach ($displayCategories as $category):
                $categoryImage = $category->getImageUrl();
            ?>
            <div class="category-circle-item">
                <div class="category-circle">
                    <?php if ($categoryImage): ?>
                        <img src="<?php echo htmlspecialchars($categoryImage); ?>" 
                             alt="<?php echo htmlspecialchars($category->name); ?>">
                    <?php else: ?>
                        <div class="no-image-circle">Нет фото</div>
                    <?php endif; ?>
                </div>
                <span class="category-circle-name"><?php echo htmlspecialchars($category->name); ?></span>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
    
    <!-- Товары недели -->
    <div class="weekly-products">
        <div class="section-header">
            <h3>Товары недели →</h3>
            <a href="catalog.php" class="section-link">Все товары</a>
        </div>
        <div class="products-list">
            <?php if (empty($weeklyProducts)): ?>
                <div class="empty-state">Товары скоро появятся</div>
            <?php else: ?>
                <?php foreach ($weeklyProducts as $product): 
                    $mainImage = $product->getMainImage();
                ?>
                <div class="product-item">
                    <div class="product-image">
                        <?php if ($mainImage): ?>
                            <img src="<?php echo htmlspecialchars($mainImage->image_url); ?>" 
                                 alt="<?php echo htmlspecialchars($product->getName()); ?>">
                        <?php else: ?>
                            <div class="no-image">Нет фото</div>
                        <?php endif; ?>
                    </div>
                    <div class="product-name">
                        <!-- ИСПРАВЛЕНО: ссылка на папку products -->
                        <a href="products/index.php?id=<?php echo $product->getId(); ?>">
                            <?php echo htmlspecialchars($product->getName()); ?>
                        </a>
                    </div>
                    <div class="product-price"><?php echo number_format($product->getPrice(), 0, '.', ' '); ?> ₽</div>
                </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>

    <div class="catalog-banner">
        <a href="catalog.php" class="btn-catalog">К каталогу</a>
    </div>

    <!-- Секция для женщин -->
    <div class="women-section">
        <div class="section-header">
            <h3>Для женщин →</h3>
            <a href="catalog.php?category_id=1" class="section-link">Смотреть все</a>
        </div>
        <div class="products-list">
            <?php if (empty($womenProducts)): ?>
                <div class="empty-state">Товары в этой категории скоро появятся</div>
            <?php else: ?>
                <?php foreach ($womenProducts as $product): 
                    $mainImage = $product->getMainImage();
                ?>
                <div class="product-item">
                    <div class="product-image">
                        <?php if ($mainImage): ?>
                            <img src="<?php echo htmlspecialchars($mainImage->image_url); ?>" 
                                 alt="<?php echo htmlspecialchars($product->getName()); ?>">
                        <?php else: ?>
                            <div class="no-image">Нет фото</div>
                        <?php endif; ?>
                    </div>
                    <div class="product-name">
                        <!-- ИСПРАВЛЕНО: ссылка на папку products -->
                        <a href="products/index.php?id=<?php echo $product->getId(); ?>">
                            <?php echo htmlspecialchars($product->getName()); ?>
                        </a>
                    </div>
                    <div class="product-price"><?php echo number_format($product->getPrice(), 0, '.', ' '); ?> ₽</div>
                </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>

    <!-- Секция для мужчин -->
    <div class="men-section">
        <div class="section-header">
            <h3>Для мужчин →</h3>
            <a href="catalog.php?category_id=2" class="section-link">Смотреть все</a>
        </div>
        <div class="products-list">
            <?php if (empty($menProducts)): ?>
                <div class="empty-state">Товары в этой категории скоро появятся</div>
            <?php else: ?>
                <?php foreach ($menProducts as $product): 
                    $mainImage = $product->getMainImage();
                ?>
                <div class="product-item">
                    <div class="product-image">
                        <?php if ($mainImage): ?>
                            <img src="<?php echo htmlspecialchars($mainImage->image_url); ?>" 
                                 alt="<?php echo htmlspecialchars($product->getName()); ?>">
                        <?php else: ?>
                            <div class="no-image">Нет фото</div>
                        <?php endif; ?>
                    </div>
                    <div class="product-name">
                        <!-- ИСПРАВЛЕНО: ссылка на папку products -->
                        <a href="products/index.php?id=<?php echo $product->getId(); ?>">
                            <?php echo htmlspecialchars($product->getName()); ?>
                        </a>
                    </div>
                    <div class="product-price"><?php echo number_format($product->getPrice(), 0, '.', ' '); ?> ₽</div>
                </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>

    <div class="how-to-order">
        <div class="section-header">
            <h3>Как оформить заказ?</h3>
        </div>
        <div class="steps-list">
            <?php foreach ($orderSteps as $step): ?>
            <div class="step-item">
                <div class="step-number"><?php echo $step['number']; ?>.</div>
                <div class="step-text"><?php echo htmlspecialchars($step['text']); ?></div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>

</div>

<?php include 'templates/footer.php'; ?>