<?php
require_once '../config/db.php';
require_once '../classes/Product.php';
require_once '../classes/Category.php';
require_once '../classes/Subcategory.php';
require_once '../classes/ProductImage.php';
require_once '../classes/ProductVariant.php';

$pageTitle = 'Добавить товар';

$allCategories = Category::findAll('name');
if ($allCategories === null) {
    $allCategories = [];
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $product = new Product();
    
    // ОБЯЗАТЕЛЬНО заполняем category_id
    $product->category_id = $_POST['category_id'] ?? '';
    $product->subcategory_id = !empty($_POST['subcategory_id']) ? (int)$_POST['subcategory_id'] : 0;
    $product->name = $_POST['name'] ?? '';
    $product->description = $_POST['description'] ?? '';
    $product->price = !empty($_POST['price']) ? (float)$_POST['price'] : 0;
    $product->old_price = !empty($_POST['old_price']) ? (float)$_POST['old_price'] : null;
    $product->is_new = isset($_POST['is_new']) ? 1 : 0;
    
    $variants = [];
    if (!empty($_POST['variants'])) {
        foreach ($_POST['variants'] as $variant) {
            if (!empty($variant['color']) || !empty($variant['size'])) {
                $variants[] = $variant;
            }
        }
    }
    
    if ($product->saveWithVariants($variants)) {
        $product_id = $product->id;
        
        if (!empty($_FILES['images']['name'][0])) {
            $uploadDir = '../../uploads/products/' . $product_id . '/';
            if (!file_exists($uploadDir)) {
                mkdir($uploadDir, 0777, true);
            }
            
            $uploadCount = 0;
            foreach ($_FILES['images']['tmp_name'] as $key => $tmpName) {
                if ($_FILES['images']['error'][$key] === UPLOAD_ERR_OK) {
                    $allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
                    $fileType = $_FILES['images']['type'][$key];
                    
                    if (!in_array($fileType, $allowedTypes)) {
                        continue;
                    }
                    
                    $extension = pathinfo($_FILES['images']['name'][$key], PATHINFO_EXTENSION);
                    $filename = uniqid() . '.' . $extension;
                    $filepath = $uploadDir . $filename;
                    
                    if (move_uploaded_file($tmpName, $filepath)) {
                        $image = new ProductImage();
                        $image->product_id = $product_id;
                        $image->image_url = '/uploads/products/' . $product_id . '/' . $filename;
                        $image->is_main = ($uploadCount === 0) ? 1 : 0;
                        $image->save();
                        $uploadCount++;
                    }
                }
            }
        }
        
        header('Location: index.php?success=1');
        exit;
    } else {
        $error = "Ошибка при сохранении товара";
    }
}

include '../templates/admin-header.php';
?>

<h1>Добавить товар</h1>

<?php if (isset($error)): ?>
    <div class="error"><?php echo $error; ?></div>
<?php endif; ?>

<form method="POST" enctype="multipart/form-data">
    <label>Категория:</label>
    <input type="text" name="category_id" required>
    
    <label>Подкатегория:</label>
    <select name="subcategory_id" required>
        <option value="">-- Выберите подкатегорию --</option>
        <?php 
        $allSubcategories = Subcategory::findAll('name');
        if (!empty($allSubcategories)):
            foreach ($allSubcategories as $sub): 
        ?>
            <option value="<?php echo $sub->id; ?>">
                <?php echo htmlspecialchars($sub->name); ?>
            </option>
        <?php 
            endforeach;
        endif;
        ?>
    </select>
    
    <label>Название товара:</label>
    <input type="text" name="name" required>
    
    <label>Описание:</label>
    <textarea name="description" rows="5"></textarea>
    
    <label>Цена (руб):</label>
    <input type="number" step="0.01" name="price" required>
    
    <label>Старая цена (руб):</label>
    <input type="number" step="0.01" name="old_price">
    
    <label>
        <input type="checkbox" name="is_new" checked> Новинка
    </label>
    
    <h3>Варианты товара (цвет/размер)</h3>
    <div id="variants">
        <div class="variant-row">
            <input type="text" name="variants[0][color]" placeholder="Цвет">
            <input type="text" name="variants[0][size]" placeholder="Размер">
            <input type="number" name="variants[0][price]" step="0.01" placeholder="Цена (если отличается)">
            <input type="number" name="variants[0][quantity]" placeholder="Количество" value="0">
            <button type="button" class="remove-variant" onclick="this.parentElement.remove()">Удалить</button>
        </div>
    </div>
    <button type="button" onclick="addVariant()">+ Добавить вариант</button>
    
    <h3>Изображения товара</h3>
    <div class="image-upload">
        <input type="file" name="images[]" accept="image/*" multiple>
        <small>Можно выбрать несколько файлов. Первое изображение станет главным.</small>
    </div>
    
    <button type="submit">Сохранить товар</button>
</form>

<a href="index.php" class="back-link">← Вернуться к списку</a>

<script>
    let variantCount = 1;
    function addVariant() {
        const variantsDiv = document.getElementById('variants');
        const newVariant = document.createElement('div');
        newVariant.className = 'variant-row';
        newVariant.innerHTML = `
            <input type="text" name="variants[${variantCount}][color]" placeholder="Цвет">
            <input type="text" name="variants[${variantCount}][size]" placeholder="Размер">
            <input type="number" name="variants[${variantCount}][price]" step="0.01" placeholder="Цена (если отличается)">
            <input type="number" name="variants[${variantCount}][quantity]" placeholder="Количество" value="0">
            <button type="button" class="remove-variant" onclick="this.parentElement.remove()">Удалить</button>
        `;
        variantsDiv.appendChild(newVariant);
        variantCount++;
    }
</script>

<?php include '../templates/admin-footer.php'; ?>