<?php
require_once '../config/db.php';
require_once '../classes/Product.php';
require_once '../classes/Category.php';
require_once '../classes/Subcategory.php';
require_once '../classes/ProductImage.php';

$pageTitle = 'Редактировать товар';

if (!isset($_GET['id'])) {
    header('Location: index.php');
    exit;
}

$product = Product::findById($_GET['id']);
if (!$product) {
    die('Товар не найден');
}

$categoriesWithSub = Category::getAllWithSubcategories();
$allCategories = Category::findAll('name');

// Обработка сохранения товара и загрузки новых изображений
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $product->subcategory_id = (int)$_POST['subcategory_id'];
    $product->name = $_POST['name'];
    $product->description = $_POST['description'] ?? '';
    $product->price = (float)$_POST['price'];
    $product->old_price = !empty($_POST['old_price']) ? (float)$_POST['old_price'] : null;
    $product->is_new = isset($_POST['is_new']) ? 1 : 0;
    
    if ($product->save()) {
        // Обработка загрузки новых изображений
        if (!empty($_FILES['new_images']['name'][0])) {
            $uploadDir = '../../uploads/products/' . $product->id . '/';
            
            // Создаем папку, если её нет
            if (!file_exists($uploadDir)) {
                mkdir($uploadDir, 0777, true);
            }
            
            $uploadCount = 0;
            foreach ($_FILES['new_images']['tmp_name'] as $key => $tmpName) {
                if ($_FILES['new_images']['error'][$key] === UPLOAD_ERR_OK) {
                    // Проверяем тип файла
                    $allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
                    $fileType = $_FILES['new_images']['type'][$key];
                    
                    if (!in_array($fileType, $allowedTypes)) {
                        continue;
                    }
                    
                    $extension = pathinfo($_FILES['new_images']['name'][$key], PATHINFO_EXTENSION);
                    $filename = uniqid() . '.' . $extension;
                    $filepath = $uploadDir . $filename;
                    
                    if (move_uploaded_file($tmpName, $filepath)) {
                        // Проверяем, есть ли уже изображения у товара
                        $existingImages = $product->getImages();
                        $isFirst = empty($existingImages) && $uploadCount === 0;
                        
                        $image = new ProductImage();
                        $image->product_id = $product->id;
                        $image->image_url = '/uploads/products/' . $product->id . '/' . $filename;
                        $image->is_main = $isFirst ? 1 : 0;
                        $image->save();
                        
                        $uploadCount++;
                    }
                }
            }
        }
        
        header('Location: update.php?id=' . $product->id . '&success=1');
        exit;
    } else {
        $error = "Ошибка при сохранении товара";
    }
}

// Получаем изображения товара для отображения
$images = $product->getImages();

include '../templates/admin-header.php';
?>

<h1>Редактировать товар "<?php echo htmlspecialchars($product->name); ?>"</h1>

<?php if (isset($_GET['success'])): ?>
    <div class="success">Товар успешно обновлен!</div>
<?php endif; ?>

<?php if (isset($error)): ?>
    <div class="error"><?php echo $error; ?></div>
<?php endif; ?>

<form method="POST" enctype="multipart/form-data">
    <label>Категория:</label>
    <select id="category_select" required>
        <option value="">-- Выберите категорию --</option>
        <?php 
        $currentSubcategory = $product->getSubcategory();
        $currentCategoryId = $currentSubcategory ? $currentSubcategory->category_id : 0;
        ?>
        <?php foreach ($allCategories as $category): ?>
            <option value="<?php echo $category->id; ?>" <?php echo $category->id == $currentCategoryId ? 'selected' : ''; ?>>
                <?php echo htmlspecialchars($category->name); ?>
            </option>
        <?php endforeach; ?>
    </select>
    
    <label>Подкатегория:</label>
    <select name="subcategory_id" id="subcategory_select" required>
        <option value="">-- Выберите подкатегорию --</option>
        <?php if ($currentSubcategory): ?>
            <option value="<?php echo $currentSubcategory->id; ?>" selected>
                <?php echo htmlspecialchars($currentSubcategory->name); ?>
            </option>
        <?php endif; ?>
    </select>
    
    <label>Название товара:</label>
    <input type="text" name="name" value="<?php echo htmlspecialchars($product->name); ?>" required>
    
    <label>Описание:</label>
    <textarea name="description" rows="5"><?php echo htmlspecialchars($product->description ?? ''); ?></textarea>
    
    <label>Цена (руб):</label>
    <input type="number" step="0.01" name="price" value="<?php echo $product->price; ?>" required>
    
    <label>Старая цена (руб):</label>
    <input type="number" step="0.01" name="old_price" value="<?php echo $product->old_price ?? ''; ?>">
    
    <label>
        <input type="checkbox" name="is_new" <?php echo $product->is_new ? 'checked' : ''; ?>> Новинка
    </label>
    
    <!-- БЛОК ИЗОБРАЖЕНИЙ -->
    <h3>Изображения товара</h3>
    
    <?php if (!empty($images)): ?>
        <div class="current-images">
            <h4>Текущие изображения:</h4>
            <?php foreach ($images as $image): ?>
                <div class="image-item">
                    <img src="<?php echo htmlspecialchars($image->image_url); ?>" alt="Фото товара">
                    <?php if ($image->is_main): ?>
                        <span class="main-badge">Главное</span>
                    <?php endif; ?>
                    <div class="image-actions">
                        <a href="delete-image.php?id=<?php echo $image->id; ?>&product_id=<?php echo $product->id; ?>" 
                           class="delete-image" 
                           onclick="return confirm('Удалить это изображение?')">Удалить</a>
                        <?php if (!$image->is_main): ?>
                            | <a href="set-main-image.php?id=<?php echo $image->id; ?>&product_id=<?php echo $product->id; ?>" 
                               class="set-main">Сделать главным</a>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php else: ?>
        <p class="no-images">У этого товара пока нет изображений</p>
    <?php endif; ?>
    
    <div class="image-upload">
        <h4>Добавить новые изображения:</h4>
        <input type="file" name="new_images[]" accept="image/jpeg,image/png,image/gif,image/webp" multiple>
        <small>Можно выбрать несколько файлов. Поддерживаются JPG, PNG, GIF, WEBP. Первое загруженное изображение станет главным, если других нет.</small>
    </div>
    <!-- КОНЕЦ БЛОКА ИЗОБРАЖЕНИЙ -->
    
    <button type="submit">Сохранить изменения</button>
</form>

<a href="index.php" class="back-link">← Вернуться к списку</a>

<script>
    const categoriesWithSub = <?php echo json_encode($categoriesWithSub); ?>;
    const currentCategoryId = <?php echo $currentCategoryId; ?>;
    const currentSubcategoryId = <?php echo $product->subcategory_id; ?>;
    
    function loadSubcategories(categoryId) {
        const subcategorySelect = document.getElementById('subcategory_select');
        subcategorySelect.innerHTML = '<option value="">-- Выберите подкатегорию --</option>';
        
        if (categoryId && categoriesWithSub[categoryId]) {
            categoriesWithSub[categoryId].subcategories.forEach(function(sub) {
                const option = document.createElement('option');
                option.value = sub.id;
                option.textContent = sub.name;
                if (sub.id == currentSubcategoryId) {
                    option.selected = true;
                }
                subcategorySelect.appendChild(option);
            });
        }
    }
    
    document.getElementById('category_select').addEventListener('change', function() {
        loadSubcategories(this.value);
    });
    
    // Загружаем подкатегории при загрузке страницы
    if (currentCategoryId) {
        loadSubcategories(currentCategoryId);
    }
</script>

<?php include '../templates/admin-footer.php'; ?>