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

include '../templates/header2.php';
?>
<link rel="stylesheet" href="../assets/css/product-page.css">
<link rel="stylesheet" href="../assets/css/style.css">

<div class="container">
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
                        <div class="thumbnail <?php echo ($image->is_main) ? 'active' : ''; ?>">
                            <img src="<?php echo htmlspecialchars($image->image_url); ?>" 
                                 alt="<?php echo htmlspecialchars($product->getName()); ?>"
                                 onclick="changeImage(this.src, this)">
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
                    <button class="btn-add-to-cart" onclick="addToCart(<?php echo $product->getId(); ?>)">В корзину</button>
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

<script>
    document.addEventListener('DOMContentLoaded', function() {

        const variantsMap = <?php 
            $map = [];
            foreach ($variants as $variant) {
                $color = $variant->color ?? 'Стандарт';
                $size = $variant->size ?? 'Стандарт';
                $key = $color . '|' . $size;
                $map[$key] = [
                    'color' => $color,
                    'size' => $size,
                    'price' => $variant->price ?? 0,
                    'quantity' => $variant->quantity ?? 0,
                    'id' => $variant->id ?? 0
                ];
            }
            echo json_encode($map);
        ?>;
        
        let selectedColor = '<?php echo $selectedColor; ?>';
        let selectedSize = '<?php echo $selectedSize; ?>';
        let currentQuantity = 1;
        let maxQuantity = <?php echo $stockQuantity > 0 ? $stockQuantity : 99; ?>;
        
        let mainImage = document.getElementById('main-product-image');
        if (!mainImage) {
            mainImage = document.querySelector('.main-image img');
        }
        
        window.changeImage = function(imageUrl, element) {
            if (mainImage) {
                mainImage.src = imageUrl;
            }
            
            const thumbnails = document.querySelectorAll('.thumbnail');
            for (let i = 0; i < thumbnails.length; i++) {
                thumbnails[i].classList.remove('active');
            }
            
            if (element) {
                element.classList.add('active');
            }
        };
        
        const thumbnails = document.querySelectorAll('.thumbnail');
        for (let i = 0; i < thumbnails.length; i++) {
            thumbnails[i].onclick = function(e) {
                e.preventDefault();
                const img = this.querySelector('img');
                if (img) {
                    window.changeImage(img.src, this);
                }
            };
        }
        
        function updateQuantity() {
            const quantityValue = document.getElementById('quantity-value');
            if (quantityValue) {
                quantityValue.textContent = currentQuantity;
            }
        }
        
        function updatePriceAndStock() {
            const key = selectedColor + '|' + selectedSize;
            const variant = variantsMap[key];
            
            if (variant) {
                const priceElement = document.getElementById('product-price');
                if (priceElement) {
                    priceElement.textContent = variant.price.toLocaleString() + ' ₽';
                }
                
                const stockInfo = document.getElementById('stock-info');
                if (stockInfo) {
                    if (variant.quantity > 0) {
                        stockInfo.textContent = 'В наличии: ' + variant.quantity + ' шт.';
                    } else {
                        stockInfo.textContent = 'Нет в наличии';
                    }
                }
                
                maxQuantity = variant.quantity > 0 ? variant.quantity : 99;
                if (currentQuantity > maxQuantity) {
                    currentQuantity = maxQuantity;
                    updateQuantity();
                }
            }
        }

        window.addToCart = function(productId) {
            const quantity = currentQuantity;
            const color = encodeURIComponent(selectedColor);
            const size = encodeURIComponent(selectedSize);
            window.location.href = '../cart.php?add=' + productId + '&quantity=' + quantity + '&color=' + color + '&size=' + size;
        };
        
        const colorBtns = document.querySelectorAll('.color-btn');
        for (let i = 0; i < colorBtns.length; i++) {
            colorBtns[i].onclick = function() {
                for (let j = 0; j < colorBtns.length; j++) {
                    colorBtns[j].classList.remove('active');
                }
                this.classList.add('active');
                selectedColor = this.getAttribute('data-color');
                updatePriceAndStock();
            };
        }
        
        const sizeBtns = document.querySelectorAll('.size-btn');
        for (let i = 0; i < sizeBtns.length; i++) {
            sizeBtns[i].onclick = function() {
                for (let j = 0; j < sizeBtns.length; j++) {
                    sizeBtns[j].classList.remove('active');
                }
                this.classList.add('active');
                selectedSize = this.getAttribute('data-size');
                updatePriceAndStock();
            };
        }
        
        const minusBtn = document.querySelector('.minus');
        const plusBtn = document.querySelector('.plus');
        
        if (minusBtn) {
            minusBtn.onclick = function() {
                if (currentQuantity > 1) {
                    currentQuantity--;
                    updateQuantity();
                }
            };
        }
        
        if (plusBtn) {
            plusBtn.onclick = function() {
                if (currentQuantity < maxQuantity) {
                    currentQuantity++;
                    updateQuantity();
                }
            };
        }
        
        updatePriceAndStock();
        updateQuantity();
    });
</script>

<?php include '../templates/footer.php'; ?>