<?php
require_once '../config/db.php';
require_once '../classes/Product.php';
require_once '../classes/Category.php';
require_once '../classes/Subcategory.php';

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

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
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

include '../templates/admin-header.php';
?>

<h1>Редактировать товар "<?php echo htmlspecialchars($product->name); ?>"</h1>

<?php if (isset($error)): ?>
    <div class="error"><?php echo $error; ?></div>
<?php endif; ?>

<form method="POST">
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
        loadSubcategories}