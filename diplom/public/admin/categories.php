<?php
require_once '../config/db.php';
require_once '../classes/Category.php';
require_once '../classes/Subcategory.php';

$pageTitle = 'Управление категориями';
$message = '';
$messageType = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $action = $_POST['action'] ?? '';
        
        if ($action === 'add_category' && !empty($_POST['category_name'])) {
            $category = new Category();
            $category->name = $_POST['category_name'];
            
            if ($category->save()) {
                $message = 'Категория успешно добавлена';
                $messageType = 'success';
            }
        }
        
        if ($action === 'add_subcategory' && !empty($_POST['subcategory_name']) && !empty($_POST['category_id'])) {
            $subcategory = new Subcategory();
            $subcategory->category_id = (int)$_POST['category_id'];
            $subcategory->name = $_POST['subcategory_name'];
            
            if ($subcategory->save()) {
                $message = 'Подкатегория успешно добавлена';
                $messageType = 'success';
            }
        }
        
        if ($action === 'delete_category' && !empty($_POST['category_id'])) {
            $category = Category::findById($_POST['category_id']);
            
            if ($category) {
                if (!$category->hasSubcategories()) {
                    if ($category->delete()) {
                        $message = 'Категория успешно удалена';
                        $messageType = 'success';
                    }
                } else {
                    $message = 'Нельзя удалить категорию, в которой есть подкатегории';
                    $messageType = 'error';
                }
            }
        }
        
        if ($action === 'delete_subcategory' && !empty($_POST['subcategory_id'])) {
            $subcategory = Subcategory::findById($_POST['subcategory_id']);
            
            if ($subcategory) {
                if (!$subcategory->hasProducts()) {
                    if ($subcategory->delete()) {
                        $message = 'Подкатегория успешно удалена';
                        $messageType = 'success';
                    }
                } else {
                    $message = 'Нельзя удалить подкатегорию, в которой есть товары';
                    $messageType = 'error';
                }
            }
        }
        
    } catch (Exception $e) {
        $message = 'Ошибка: ' . $e->getMessage();
        $messageType = 'error';
    }
}

$categoriesWithSub = Category::getAllWithSubcategories();

$allCategories = Category::findAll('name');

include '../templates/admin-header.php';
?>

<h1>Управление категориями</h1>

<?php if ($message): ?>
    <div class="<?php echo $messageType; ?>"><?php echo htmlspecialchars($message); ?></div>
<?php endif; ?>

<div class="forms">
    <div class="form-box">
        <h3>Добавить категорию</h3>
        <form method="POST" action="">
            <input type="text" name="category_name" placeholder="Название категории" required>
            <input type="hidden" name="action" value="add_category">
            <button type="submit">Добавить категорию</button>
        </form>
    </div>
    
    <div class="form-box">
        <h3>Добавить подкатегорию</h3>
        <form method="POST" action="">
            <select name="category_id" required>
                <option value="">-- Выберите категорию --</option>
                <?php foreach ($allCategories as $category): ?>
                    <option value="<?php echo $category->id; ?>">
                        <?php echo htmlspecialchars($category->name); ?>
                    </option>
                <?php endforeach; ?>
            </select>
            <input type="text" name="subcategory_name" placeholder="Название подкатегории" required>
            <input type="hidden" name="action" value="add_subcategory">
            <button type="submit">Добавить подкатегорию</button>
        </form>
    </div>
</div>

<h2>Существующие категории</h2>

<?php foreach ($categoriesWithSub as $category): ?>
    <div class="category">
        <div style="display: flex; justify-content: space-between; align-items: center;">
            <strong><?php echo htmlspecialchars($category['name']); ?></strong>
            <?php if (empty($category['subcategories'])): ?>
                <form method="POST" style="display: inline;" onsubmit="return confirm('Точно удалить категорию?')">
                    <input type="hidden" name="action" value="delete_category">
                    <input type="hidden" name="category_id" value="<?php echo $category['id']; ?>">
                    <button type="submit" class="btn btn-delete" style="padding: 5px 10px;">Удалить</button>
                </form>
            <?php endif; ?>
        </div>
        
        <?php if (!empty($category['subcategories'])): ?>
            <?php foreach ($category['subcategories'] as $sub): ?>
                <div class="subcategory">
                    <div style="display: flex; justify-content: space-between; align-items: center;">
                        <span><?php echo htmlspecialchars($sub['name']); ?></span>
                        <form method="POST" style="display: inline;" onsubmit="return confirm('Точно удалить подкатегорию?')">
                            <input type="hidden" name="action" value="delete_subcategory">
                            <input type="hidden" name="subcategory_id" value="<?php echo $sub['id']; ?>">
                            <button type="submit" class="btn btn-delete" style="padding: 3px 8px; font-size: 12px;">Удалить</button>
                        </form>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="subcategory" style="color: #999;">Нет подкатегорий</div>
        <?php endif; ?>
    </div>
<?php endforeach; ?>

<a href="index.php" class="back-link">← Вернуться к товарам</a>

<?php include '../templates/admin-footer.php'; ?>