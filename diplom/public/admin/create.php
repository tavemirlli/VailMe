<?php
require_once '../config/db.php';
require_once '../classes/Product.php';
require_once '../classes/Category.php';
require_once '../classes/Subcategory.php';
require_once '../classes/ProductImage.php';

$pageTitle = 'Добавить товар';

$categoriesWithSub = Category::getAllWithSubcategories();
$allCategories = Category::findAll('name');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $product = new Product();
    $product->subcategory_id = (int)$_POST['subcategory_id'];
    $product->name = $_POST['name'];
    $product->description = $_POST['description'] ?? '';
    $product->price = (float)$_POST['price'];
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
        
        // Обработка загрузки изображений
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

<h1>Добавить новый товар</h1>

<?php if (isset($error)): ?>
    <div class="error"><?php echo $error; ?></div>
<?php endif; ?>

<form method="POST" enctype="multipart/form-data">
    <label>Категория:</label>
    <select id="category_select" required>
        <option value="">-- Выберите категорию --</option>
        <?php foreach ($allCategories as $category): ?>
            <option value="<?php echo $category->id; ?>">
                <?php echo htmlspecialchars($category->name); ?>
            </option>
        <?php endforeach; ?>
    </select>
    
    <label>Подкатегория:</label>
    <select name="subcategory_id" id="subcategory_select" required>
        <option value="">-- Сначала выберите категорию --</option>
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
            <input type="text" name="variants[0][color]" placeholder="Цвет (например, Синий)">
            <input type="text" name="variants[0][size]" placeholder="Размер (например, M)">
            <input type="number" name="variants[0][price]" step="0.01" placeholder="Цена (если отличается)">
            <input type="number" name="variants[0][quantity]" placeholder="Количество на складе">
            <button type="button" class="remove-variant" onclick="this.parentElement.remove()">Удалить</button>
        </div>
    </div>
    <button type="button" onclick="addVariant()">+ Добавить вариант</button>
    
    <h3>Изображения товара</h3>
    <div class="image-upload">
        <input type="file" name="images[]" accept="image/*" multiple>
        <small>Можно выбрать несколько файлов. Поддерживаются JPG, PNG, GIF, WEBP. Первое изображение станет главным.</small>
    </div>
    
    <button type="submit">Сохранить товар</button>
</form>

<a href="index.php" class="back-link">← Вернуться к списку товаров</a>

<script>
    const categoriesWithSub = <?php echo json_encode($categoriesWithSub); ?>;
    
    document.getElementById('category_select').addEventListener('change', function() {
        const categoryId = this.value;
        const subcategorySelect = document.getElementById('subcategory_select');
        
        subcategorySelect.innerHTML = '<option value="">-- Выберите подкатегорию --</option>';
        
        if (categoryId && categoriesWithSub[categoryId]) {
            categoriesWithSub[categoryId].subcategories.forEach(function(sub) {
                const option = document.createElement('option');
                option.value = sub.id;
                option.textContent = sub.name;
                subcategorySelect.appendChild(option);
            });
        }
    });
    
    let variantCount = 1;
    function addVariant() {
        const variantsDiv = document.getElementById('variants');
        const newVariant = document.createElement('div');
        newVariant.className = 'variant-row';
        newVariant.innerHTML = `
            <input type="text" name="variants[${variantCount}][color]" placeholder="Цвет (например, Синий)">
            <input type="text" name="variants[${variantCount}][size]" placeholder="Размер (например, M)">
            <input type="number" name="variants[${variantCount}][price]" step="0.01" placeholder="Цена (если отличается)">
            <input type="number" name="variants[${variantCount}][quantity]" placeholder="Количество на складе" value="0">
            <button type="button" class="remove-variant" onclick="this.parentElement.remove()">Удалить</button>
        `;
        variantsDiv.appendChild(newVariant);
        variantCount++;
    }
</script>

<?php include '../templates/admin-footer.php'; ?>