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
                $category_id = $category->id;

                if (!empty($_FILES['category_image']['name'])) {
                    $uploadDir = '../../uploads/categories/';
                    
                    if (!file_exists($uploadDir)) {
                        mkdir($uploadDir, 0777, true);
                    }
                    
                    $allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
                    $fileType = $_FILES['category_image']['type'];
                    
                    if (in_array($fileType, $allowedTypes) && $_FILES['category_image']['error'] === UPLOAD_ERR_OK) {
                        $extension = pathinfo($_FILES['category_image']['name'], PATHINFO_EXTENSION);
                        $filename = 'cat_' . $category_id . '_' . uniqid() . '.' . $extension;
                        $filepath = $uploadDir . $filename;
                        
                        if (move_uploaded_file($_FILES['category_image']['tmp_name'], $filepath)) {
                            $category->image_url = '/uploads/categories/' . $filename;
                            $category->save();
                        }
                    }
                }
                
                $message = 'Категория успешно добавлена';
                $messageType = 'success';
            }
        }
        
        if ($action === 'update_category_image' && !empty($_POST['category_id'])) {
            $category = Category::findById($_POST['category_id']);
            
            if ($category && !empty($_FILES['new_category_image']['name'])) {
                $uploadDir = '../../uploads/categories/';
                
                if (!file_exists($uploadDir)) {
                    mkdir($uploadDir, 0777, true);
                }
                
                if ($category->hasImage()) {
                    $oldFile = $_SERVER['DOCUMENT_ROOT'] . $category->image_url;
                    if (file_exists($oldFile)) {
                        unlink($oldFile);
                    }
                }
                
                $allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
                $fileType = $_FILES['new_category_image']['type'];
                
                if (in_array($fileType, $allowedTypes) && $_FILES['new_category_image']['error'] === UPLOAD_ERR_OK) {
                    $extension = pathinfo($_FILES['new_category_image']['name'], PATHINFO_EXTENSION);
                    $filename = 'cat_' . $category->id . '_' . uniqid() . '.' . $extension;
                    $filepath = $uploadDir . $filename;
                    
                    if (move_uploaded_file($_FILES['new_category_image']['tmp_name'], $filepath)) {
                        $category->image_url = '/uploads/categories/' . $filename;
                        $category->save();
                        $message = 'Изображение категории обновлено';
                        $messageType = 'success';
                    }
                }
            }
        }
        
        if ($action === 'delete_category_image' && !empty($_POST['category_id'])) {
            $category = Category::findById($_POST['category_id']);
            
            if ($category && $category->hasImage()) {
                $oldFile = $_SERVER['DOCUMENT_ROOT'] . $category->image_url;
                if (file_exists($oldFile)) {
                    unlink($oldFile);
                }
                
                $category->image_url = null;
                $category->save();
                
                $message = 'Изображение категории удалено';
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
                    if ($category->hasImage()) {
                        $oldFile = $_SERVER['DOCUMENT_ROOT'] . $category->image_url;
                        if (file_exists($oldFile)) {
                            unlink($oldFile);
                        }
                    }
                    
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
<script src="assets/js/categories.js"></script>
<div class="forms">
    <div class="form-box">
        <h3>Добавить категорию</h3>
        <form method="POST" action="" enctype="multipart/form-data">
            <input type="text" name="category_name" placeholder="Название категории" required>
            
            <label style="margin-top: 10px;">Изображение категории (необязательно):</label>
            <input type="file" name="category_image" accept="image/*">
            <small>Рекомендуемый размер: 300x300px</small>
            
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
            <div style="display: flex; align-items: center;">
                <?php if (!empty($category['image_url'])): ?>
                    <img src="<?php echo htmlspecialchars($category['image_url']); ?>" 
                         style="width: 50px; height: 50px; object-fit: cover; border-radius: 4px; margin-right: 10px;">
                <?php else: ?>
                    <div style="width: 50px; height: 50px; background: #f0f0f0; border-radius: 4px; margin-right: 10px; display: flex; align-items: center; justify-content: center; color: #999; font-size: 10px;">нет фото</div>
                <?php endif; ?>
                <strong><?php echo htmlspecialchars($category['name']); ?></strong>
            </div>
            <div>
                <?php if (empty($category['subcategories'])): ?>
                    <form method="POST" style="display: inline;" onsubmit="return confirm('Точно удалить категорию?')">
                        <input type="hidden" name="action" value="delete_category">
                        <input type="hidden" name="category_id" value="<?php echo $category['id']; ?>">
                        <button type="submit" class="btn btn-delete" style="padding: 5px 10px;">Удалить</button>
                    </form>
                <?php endif; ?>

                <button type="button" class="btn btn-edit" style="padding: 5px 10px; margin-left: 5px;" onclick="document.getElementById('image-form-<?php echo $category['id']; ?>').style.display = 'block';">✎ фото</button>

                <div id="image-form-<?php echo $category['id']; ?>" style="display: none; margin-top: 10px;">
                    <form method="POST" action="" enctype="multipart/form-data">
                        <input type="hidden" name="action" value="update_category_image">
                        <input type="hidden" name="category_id" value="<?php echo $category['id']; ?>">
                        <input type="file" name="new_category_image" accept="image/*" required>
                        <button type="submit" class="btn btn-edit" style="padding: 3px 8px;">Загрузить</button>
                        <button type="button" class="btn btn-delete" style="padding: 3px 8px;" onclick="this.parentElement.style.display='none'">Отмена</button>
                    </form>
                    
                    <?php if (!empty($category['image_url'])): ?>
                        <form method="POST" action="" style="margin-top: 5px;">
                            <input type="hidden" name="action" value="delete_category_image">
                            <input type="hidden" name="category_id" value="<?php echo $category['id']; ?>">
                            <button type="submit" class="btn btn-delete" style="padding: 3px 8px;" onclick="return confirm('Удалить изображение?')">Удалить фото</button>
                        </form>
                    <?php endif; ?>
                </div>
            </div>
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