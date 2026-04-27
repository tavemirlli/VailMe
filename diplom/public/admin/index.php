<?php
session_start();

if (!isset($_SESSION['is_admin']) || $_SESSION['is_admin'] !== true) {
    header('Location: ../index.php');
    exit;
}

require_once '../classes/AdminProductService.php';

$pageTitle = 'Управление товарами - Админка';

// Получаем данные через класс
$products = AdminProductService::getAllProducts();
$stats = AdminProductService::getProductsStats();

include '../templates/admin-header.php';
?>
<link rel="stylesheet" href="assets/css/admin-style.css">

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
        <a href="create.php" class="btn">Добавить новый товар</a>
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
                $category = AdminProductService::getProductCategory($product);
                $mainImage = AdminProductService::getProductMainImage($product);
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
                <td><?php echo $product->getSubcategory() ? htmlspecialchars($product->getSubcategory()->name) : '-'; ?></td>
                <td><?php echo htmlspecialchars($product->name); ?></td>
                <td><?php echo number_format($product->price, 0, '.', ' '); ?> ₽</td>
                <td>
                    <?php if ($product->old_price && $product->old_price > $product->price): ?>
                        <span><?php echo number_format($product->old_price, 0, '.', ' '); ?> ₽</span>
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