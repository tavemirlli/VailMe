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

$allCategories = Category::findAll('name');
if ($allCategories === null) {
    $allCategories = [];
}

$allSubcategories = Subcategory::findAll('name');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $product->category_id = (int)$_POST['category_id'];
    $product->subcategory_id = (int)$_POST['subcategory_id'];
    $product->name = $_POST['name'];
    $product->description = $_POST['description'] ?? '';
    $product->price = (float)$_POST['price'];
    $product->old_price = !empty($_POST['old_price']) ? (float)$_POST['old_price'] : null;
    $product->is_new = isset($_POST['is_new']) ? 1 : 0;
    
    if ($product->save()) {
        header('Location: index.php?success=1');
        exit;
    } else {
        $error = "Ошибка при сохранении товара";
    }
}

$images = $product->getImages();

include '../templates/admin-header.php';
?>

<h1>Редактировать товар "<?php echo htmlspecialchars($product->name); ?>"</h1>

<?php if (isset($error)): ?>
    <div class="error"><?php echo $error; ?></div>
<?php endif; ?>

<form method="POST" enctype="multipart/form-data">
    <label>Категория:</label>
    <input type="text" name="category_id" value="<?php echo htmlspecialchars($product->category_id); ?>" required>
    
    <label>Подкатегория:</label>
    <select name="subcategory_id" required>
        <option value="">-- Выберите подкатегорию --</option>
        <?php foreach ($allSubcategories as $sub): ?>
            <option value="<?php echo $sub->id; ?>" <?php echo ($product->subcategory_id == $sub->id) ? 'selected' : ''; ?>>
                <?php echo htmlspecialchars($sub->name); ?>
            </option>
        <?php endforeach; ?>
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
    
    <h3>Изображения товара</h3>
    
    <?php if (!empty($images)): ?>
        <div class="current-images">
            <h4>Текущие изображения:</h4>
            <?php foreach ($images as $image): ?>
                <div class="image-item">
                    <img src="<?php echo htmlspecialchars($image->image_url); ?>" style="width: 100px;">
                    <?php if ($image->is_main): ?>
                        <span class="main-badge">Главное</span>
                    <?php endif; ?>
                    <div class="image-actions">
                        <a href="delete-image.php?id=<?php echo $image->id; ?>&product_id=<?php echo $product->id; ?>" onclick="return confirm('Удалить?')">Удалить</a>
                        <?php if (!$image->is_main): ?>
                            | <a href="set-main-image.php?id=<?php echo $image->id; ?>&product_id=<?php echo $product->id; ?>">Сделать главным</a>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
    
    <div class="image-upload">
        <h4>Добавить новые изображения:</h4>
        <input type="file" name="new_images[]" accept="image/*" multiple>
    </div>
    
    <button type="submit">Сохранить изменения</button>
</form>

<a href="index.php" class="back-link">← Вернуться к списку</a>

<?php include '../templates/admin-footer.php'; ?>