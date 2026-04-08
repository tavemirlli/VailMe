<?php
session_start();

// Проверка прав администратора
if (!isset($_SESSION['is_admin']) || $_SESSION['is_admin'] !== true) {
    header('Location: ../index.php');
    exit;
}

require_once '../config/db.php';
require_once '../classes/Product.php';
require_once '../classes/Category.php';
require_once '../classes/Subcategory.php';
require_once '../classes/ProductImage.php';
require_once '../classes/Order.php';

$pageTitle = 'Управление товарами - Админка';
$products = Product::findAll('id DESC');

// Получаем статистику
$statsSql = "SELECT 
                COUNT(*) as total_products,
                SUM(CASE WHEN is_new = 1 THEN 1 ELSE 0 END) as new_products
             FROM products";
$statsResult = mysqli_query($connect, $statsSql);
$stats = mysqli_fetch_assoc($statsResult);

include '../templates/admin-header.php';
?>

<h1>Управление товарами</h1>

<a href="../index.php">Выйти</a>

<?php if (isset($_GET['success'])): ?>
    <div class="success">Операция выполнена успешно!</div>
<?php endif; ?>

<div class="stats">
    <div class="stat-box">
        <strong>Всего товаров:</strong> <?php echo $stats['total_products']; ?>
    </div>
    <div class="stat-box">
        <strong>Новинок:</strong> <?php echo $stats['new_products']; ?>
    </div>
    <div class="stat-box">
        <a href="create.php" class="btn" style="background: #4CAF50;">Добавить новый товар</a>
    </div>
    <div class="stat-box">
        <a href="orders.php" class="btn" style="background: #F0B1D3;">Заказы</a>
    </div>
</div>

<table class="products-table">
    <thead>
        <tr>
            <th>ID</th>
            <th>Фото</th>
            <th>Категория</th>
            <th>Подкатегория</th>
            <th>Название</th>
            <th>Цена</th>
            <th>Старая цена</th>
            <th>Новинка</th>
            <th>Действия</th>
        </tr>
    </thead>
    <tbody>
        <?php if (empty($products)): ?>
            <tr>
                <td colspan="9">
                    Товаров пока нет. <a href="create.php">Добавить первый товар</a>
                </td>
            </tr>
        <?php else: ?>
            <?php foreach ($products as $product): 
                $subcategory = $product->getSubcategory();
                $category = $subcategory ? $subcategory->getCategory() : null;
                $mainImage = $product->getMainImage();
            ?>
            <tr>
                <td><?php echo $product->id; ?></td>
                <td>
                    <?php if ($mainImage): ?>
                        <img src="<?php echo htmlspecialchars($mainImage->image_url); ?>" 
                             style="width: 50px; height: 50px; object-fit: cover; border-radius: 4px;">
                    <?php else: ?>
                        <span>нет фото</span>
                    <?php endif; ?>
                </td>
                <td><?php echo $category ? htmlspecialchars($category->name) : '-'; ?></td>
                <td><?php echo $subcategory ? htmlspecialchars($subcategory->name) : '-'; ?></td>
                <td><?php echo htmlspecialchars($product->name); ?></td>
                <td><?php echo number_format($product->price, 0, '.', ' '); ?> ₽</td>
                <td>
                    <?php if ($product->old_price && $product->old_price > $product->price): ?>
                        <span>
                            <?php echo number_format($product->old_price, 0, '.', ' '); ?> ₽
                        </span>
                    <?php else: ?>
                        -
                    <?php endif; ?>
                </td>
                <td><?php echo $product->is_new ? 'Да' : 'Нет'; ?></td>
                <td>
                    <a href="update.php?id=<?php echo $product->id; ?>" class="btn btn-edit">Изменить</a>
                    <a href="delete.php?id=<?php echo $product->id; ?>" class="btn btn-delete" 
                       onclick="return confirm('Точно удалить товар?')">Удалить</a>
                </td>
            </tr>
            <?php endforeach; ?>
        <?php endif; ?>
    </tbody>
</table>

<?php include '../templates/admin-footer.php'; ?>