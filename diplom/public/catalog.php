<?php
require_once 'config/session.php';
require_once 'config/db.php';
require_once 'classes/Product.php';
require_once 'classes/Category.php';
require_once 'classes/Subcategory.php';

$pageTitle = 'Каталог - VailMe';

$selectedCategoryId = isset($_GET['category_id']) ? (int)$_GET['category_id'] : 0;
$selectedSubcategoryId = isset($_GET['subcategory_id']) ? (int)$_GET['subcategory_id'] : 0;

$allCategories = Category::findAll('name');
if ($allCategories === null) {
    $allCategories = [];
}

$products = [];

if ($selectedSubcategoryId > 0) {
    $subcategory = Subcategory::findById($selectedSubcategoryId);
    if ($subcategory) {
        $products = $subcategory->getProducts();
    }
} elseif ($selectedCategoryId > 0) {
    $category = Category::findById($selectedCategoryId);
    if ($category) {
        $products = $category->getAllProducts();
    }
} else {
    if (!empty($allCategories)) {
        $firstCategory = $allCategories[0];
        $selectedCategoryId = $firstCategory->id;
        $products = $firstCategory->getAllProducts();
    } else {
        $products = Product::findAll('id DESC');
    }
}

if ($products === null) {
    $products = [];
}

$categoryName = '';
if ($selectedCategoryId > 0) {
    $category = Category::findById($selectedCategoryId);
    if ($category && isset($category->name)) {
        $categoryName = $category->name;
    }
}

$subcategoryName = '';
if ($selectedSubcategoryId > 0) {
    $subcategory = Subcategory::findById($selectedSubcategoryId);
    if ($subcategory && isset($subcategory->name)) {
        $subcategoryName = $subcategory->name;
    }
}

include 'templates/header.php';
?>
<link rel="stylesheet" href="assets/css/style.css">
<link rel="stylesheet" href="assets/css/categories.css">
<link rel="stylesheet" href="assets/css/catalog.css">

<div class="container">
    <div class="main-content">
        
        <h1>
            <?php 
            if ($subcategoryName) {
                echo htmlspecialchars($subcategoryName);
            } elseif ($categoryName) {
                echo htmlspecialchars($categoryName);
            } else {
                echo 'Все товары';
            }
            ?>
        </h1>

        <div class="catalog-layout">
            <aside class="catalog-sidebar">
                <div class="sidebar-section">
                    <h3 class="sidebar-title">Категории</h3>
                    <ul class="sidebar-categories">
                        <?php if (!empty($allCategories)): ?>
                            <?php foreach ($allCategories as $category): ?>
                                <li class="sidebar-category">
                                    <a href="catalog.php?category_id=<?php echo $category->id; ?>" 
                                       class="<?php echo ($selectedCategoryId == $category->id && !$selectedSubcategoryId) ? 'active' : ''; ?>">
                                        <?php echo htmlspecialchars($category->name); ?>
                                    </a>
                                    
                                    <?php if ($selectedCategoryId == $category->id): ?>
                                        <?php $subs = Subcategory::getByCategory($category->id); ?>
                                        <?php if (!empty($subs)): ?>
                                            <ul class="sidebar-subcategories">
                                                <?php foreach ($subs as $sub): ?>
                                                    <li>
                                                        <a href="catalog.php?category_id=<?php echo $category->id; ?>&subcategory_id=<?php echo $sub->id; ?>" 
                                                           class="<?php echo ($selectedSubcategoryId == $sub->id) ? 'active' : ''; ?>">
                                                            <?php echo htmlspecialchars($sub->name); ?>
                                                        </a>
                                                    </li>
                                                <?php endforeach; ?>
                                            </ul>
                                        <?php endif; ?>
                                    <?php endif; ?>
                                </li>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <li>Нет категорий</li>
                        <?php endif; ?>
                    </ul>
                </div>
            </aside>

            <div class="catalog-products">
                <?php if (empty($products)): ?>
                    <div class="empty-state">
                        <p>В этой категории пока нет товаров</p>
                    </div>
                <?php else: ?>
                    <div class="products-grid">
                        <?php foreach ($products as $product): 
                            $mainImage = $product->getMainImage();
                        ?>
                            <div class="product-card">
                                <div class="product-image">
                                    <?php if ($mainImage): ?>
                                        <img src="<?php echo htmlspecialchars($mainImage->image_url); ?>" 
                                             alt="<?php echo htmlspecialchars($product->getName()); ?>">
                                    <?php else: ?>
                                        <div class="no-image">Нет фото</div>
                                    <?php endif; ?>
                                    <?php if ($product->getIsNew()): ?>
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
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>

    </div>
</div>

<?php include 'templates/footer.php'; ?>