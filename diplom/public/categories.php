<?php
require_once 'config/db.php';
require_once 'classes/Category.php';

$pageTitle = 'Категории - VailMe';

$allCategories = Category::findAll('name');
if ($allCategories === null) {
    $allCategories = [];
}

include 'templates/header.php';
?>
<link rel="stylesheet" href="assets/css/style.css">
<link rel="stylesheet" href="assets/css/categories.css">

    <div class="main-content">
        
        <h1>Категории</h1>

        <div class="categories-section">
            <?php if (empty($allCategories)): ?>
                <p>Нет доступных категорий</p>
            <?php else: ?>
                <ul class="categories-list">
                    <?php foreach ($allCategories as $category): ?>
                        <a href="catalog.php?category_id=<?php echo $category->id; ?>" class="category-name">
                        <li class="category-item">
                            <div class="category-image">
                                <?php if ($category->hasImage()): ?>
                                    <img src="<?php echo htmlspecialchars($category->image_url); ?>" 
                                         alt="<?php echo htmlspecialchars($category->name); ?>">
                                <?php else: ?>
                                    <div class="no-image">Нет фото</div>
                                <?php endif; ?>
                            </div>
                                <?php echo htmlspecialchars($category->name); ?>
                            
                        </li></a>
                    <?php endforeach; ?>
                </ul>
            <?php endif; ?>
        </div>

        <div class="promo-banner-middle">
            <div class="promo-content-middle">
                <h3>Новинки</h3>
                <p>Скидки до 50% на всю коллекцию</p>
                <a href="new-arrivals.php" class="btn-promo-middle">Успей купить</a>
            </div>
        </div>

    </div>


<?php include 'templates/footer.php'; ?>