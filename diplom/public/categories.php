<?php
require_once 'config/session.php';
require_once 'config/db.php';
require_once 'classes/Category.php';

$pageTitle = 'Категории - VailMe';

$allCategories = Category::findAll('name');

$mainCategories = [];
$highlightCategories = [];

foreach ($allCategories as $category) {
    $name = $category->name;
    

    if (in_array($name, ['Верхняя одежда', 'Женская одежда', 'Мужская одежда', 'Аксессуары','Обувь', 'Спортивный стиль'])) {
        $mainCategories[] = $category;
    }
}

include 'templates/header.php';
?>
<link rel="stylesheet" href="assets/css/style.css">
<link rel="stylesheet" href="assets/css/categories.css">
<div class="main-content">
    <h1>Категории</h1>
    <div class="categories-section">
        <?php if (empty($mainCategories)): ?>
            <p>Нет доступных категорий</p>
        <?php else: ?>
            <ul class="categories-list">
                <?php foreach ($mainCategories as $category): ?>
                    <li class="category-item">
                        <div class="category-image">
                            <?php if ($category->hasImage()): ?>
                                <img src="<?php echo htmlspecialchars($category->image_url); ?>" 
                                     alt="<?php echo htmlspecialchars($category->name); ?>">
                            <?php else: ?>
                                <div class="no-image">Нет фото</div>
                            <?php endif; ?>
                        </div>
                        <a href="catalog.php?category_id=<?php echo $category->id; ?>" class="category-name">
                            <?php echo htmlspecialchars($category->name); ?>
                        </a>
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php endif; ?>
    </div>


    <!-- Frame 104 -->
    <div class="frame-104">
        <div class="frame-label">Frame 104</div>
        <div class="frame-size">1384 × 1081</div>
    </div>

<?php include 'templates/footer.php'; ?>
