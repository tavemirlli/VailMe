<?php
require_once '../config/session.php';
require_once '../config/db.php';
require_once '../classes/Product.php';
require_once '../classes/Category.php';
require_once '../classes/Subcategory.php';
require_once '../classes/ProductImage.php';
require_once '../classes/ProductVariant.php';

$productId = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($productId <= 0) {
    header('Location: ../index.php');
    exit;
}

$product = Product::findById($productId);

if (!$product) {
    header('Location: ../index.php');
    exit;
}

$pageTitle = $product->getName() . ' - VailMe';

$images = $product->getImages();
if ($images === null || !is_array($images)) {
    $images = [];
}
$mainImage = $product->getMainImage();

$variants = $product->getVariants();
if ($variants === null || !is_array($variants)) {
    $variants = [];
}

$colors = [];
$sizes = [];
$variantMap = [];

foreach ($variants as $variant) {
    $color = $variant->color ?? '';
    $size = $variant->size ?? '';
    $price = $variant->price ?? 0;
    $quantity = $variant->quantity ?? 0;
    
    if (!empty($color) && !in_array($color, $colors)) {
        $colors[] = $color;
    }
    if (!empty($size) && !in_array($size, $sizes)) {
        $sizes[] = $size;
    }
    
    $key = $color . '|' . $size;
    $variantMap[$key] = [
        'price' => $price,
        'quantity' => $quantity,
        'id' => $variant->id ?? 0
    ];
}

if (!empty($variants) && empty($colors)) {
    $colors = ['Стандарт'];
}
if (!empty($variants) && empty($sizes)) {
    $sizes = ['Стандарт'];
}

$selectedColor = !empty($colors) ? $colors[0] : 'Стандарт';
$selectedSize = !empty($sizes) ? $sizes[0] : 'Стандарт';
$defaultKey = $selectedColor . '|' . $selectedSize;
$displayPrice = isset($variantMap[$defaultKey]) ? $variantMap[$defaultKey]['price'] : ($product->getPrice() ?? 0);
$stockQuantity = isset($variantMap[$defaultKey]) ? $variantMap[$defaultKey]['quantity'] : 0;

$hasStock = false;
foreach ($variants as $variant) {
    if ($variant->quantity > 0) {
        $hasStock = true;
        break;
    }
}
if (empty($variants) && isset($product->quantity) && $product->quantity > 0) {
    $hasStock = true;
}

$variantsArray = [];
foreach ($variants as $variant) {
    $variantsArray[] = [
        'color' => $variant->color ?? '',
        'size' => $variant->size ?? '',
        'price' => $variant->price ?? 0,
        'quantity' => $variant->quantity ?? 0,
        'id' => $variant->id ?? 0
    ];
}

include '../templates/header2.php';
?>
<link rel="stylesheet" href="../assets/css/product-page.css">
<link rel="stylesheet" href="../assets/css/style.css">

<script>
    window.variantsData = <?php echo json_encode($variantsArray); ?>;
    window.selectedColor = '<?php echo $selectedColor; ?>';
    window.selectedSize = '<?php echo $selectedSize; ?>';
    window.stockQuantity = <?php echo $stockQuantity > 0 ? $stockQuantity : 0; ?>;
</script>
<script src="../assets/js/product.js"></script>

    <div class="main-content">
        
        <div class="breadcrumbs">
            <a href="../index.php">Главная</a> / 
            <a href="../catalog.php">Каталог</a> / 
            <span><?php echo htmlspecialchars($product->getName()); ?></span>
        </div>
        
        <div class="product-page">
            <div class="product-gallery">
                <div class="main-image">
                    <?php if ($mainImage): ?>
                        <img src="<?php echo htmlspecialchars($mainImage->image_url); ?>" 
                             alt="<?php echo htmlspecialchars($product->getName()); ?>"
                             id="main-product-image">
                    <?php else: ?>
                        <div class="no-image">Нет фото</div>
                    <?php endif; ?>
                </div>
                <?php if (count($images) > 1): ?>
                <div class="thumbnail-list">
                    <?php foreach ($images as $image): ?>
                        <div class="thumbnail <?php echo ($image->is_main) ? 'active' : ''; ?>"
                             onclick="changeImage('<?php echo htmlspecialchars($image->image_url); ?>', this)">
                            <img src="<?php echo htmlspecialchars($image->image_url); ?>" 
                                 alt="<?php echo htmlspecialchars($product->getName()); ?>">
                        </div>
                    <?php endforeach; ?>
                </div>
                <?php endif; ?>
            </div>
            
            <div class="product-info-col">
                <h1 class="product-title"><?php echo htmlspecialchars($product->getName()); ?></h1>
                
                <div class="product-price-block">
                    <span class="product-price" id="product-price">
                        <?php echo number_format($displayPrice, 0, '.', ' '); ?> ₽
                    </span>
                    <?php if ($product->isOnSale()): ?>
                        <span class="old-price"><?php echo number_format($product->getOldPrice(), 0, '.', ' '); ?> ₽</span>
                        <span class="discount-badge">-<?php echo $product->getDiscountPercent(); ?>%</span>
                    <?php endif; ?>
                </div>
                
                <?php if (!empty($colors)): ?>
                <div class="option-group">
                    <label>Цвет:</label>
                    <div class="color-options" id="color-options">
                        <?php foreach ($colors as $color): ?>
                            <button class="color-btn <?php echo ($color === $selectedColor) ? 'active' : ''; ?>" 
                                    data-color="<?php echo htmlspecialchars($color); ?>">
                                <?php echo htmlspecialchars($color); ?>
                            </button>
                        <?php endforeach; ?>
                    </div>
                </div>
                <?php endif; ?>
                
                <?php if (!empty($sizes)): ?>
                <div class="option-group">
                    <label>Размер:</label>
                    <div class="size-options" id="size-options">
                        <?php foreach ($sizes as $size): ?>
                            <button class="size-btn <?php echo ($size === $selectedSize) ? 'active' : ''; ?>" 
                                    data-size="<?php echo htmlspecialchars($size); ?>">
                                <?php echo htmlspecialchars($size); ?>
                            </button>
                        <?php endforeach; ?>
                    </div>
                </div>
                <?php endif; ?>
                
                <div class="quantity-selector">
                    <label>Количество:</label>
                    <div class="quantity-wrapper">
                        <div class="quantity-control">
                            <button class="quantity-btn minus" type="button">-</button>
                            <span class="quantity-value" id="quantity-value">1</span>
                            <button class="quantity-btn plus" type="button">+</button>
                        </div>
                        <span class="stock-info" id="stock-info">
                            <?php if ($stockQuantity > 0): ?>
                                В наличии: <?php echo $stockQuantity; ?> шт.
                            <?php else: ?>
                                Нет в наличии
                            <?php endif; ?>
                        </span>
                    </div>
                </div>
                
                <div class="product-actions">
                    <?php if ($hasStock && $stockQuantity > 0): ?>
                        <button class="btn-add-to-cart" onclick="addToCart(<?php echo $product->getId(); ?>)">В корзину</button>
                    <?php else: ?>
                        <button class="btn-add-to-cart disabled" disabled style="background: #ccc; cursor: not-allowed;">Нет в наличии</button>
                    <?php endif; ?>
                </div>
                
                <div class="product-meta">
                    <p>Артикул: <?php echo $product->getId(); ?></p>
                    <?php 
                    $subcategory = $product->getSubcategory();
                    if ($subcategory) {
                        $category = $subcategory->getCategory();
                        if ($category) {
                            echo '<p>Категория: ' . htmlspecialchars($category->name) . '</p>';
                        }
                    }
                    ?>
                </div>
            </div>
        </div>
        
        <?php if (!empty($product->getDescription())): ?>
        <div class="product-description-section">
            <h2>Характеристики</h2>
            <div class="description-content">
                <?php echo nl2br(htmlspecialchars($product->getDescription())); ?>
            </div>
        </div>
        <?php endif; ?>
        
    </div>
</div>

<?php include '../templates/footer2.php'; ?>