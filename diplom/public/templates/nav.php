<?php
// Получаем имя текущего файла без пути
$currentPage = basename($_SERVER['PHP_SELF']);
?>
<nav class="main-nav">
    <a href="index.php" class="<?php echo $currentPage == 'index.php' ? 'active' : ''; ?>">Главная</a>
    <a href="new-arrivals.php" class="<?php echo $currentPage == 'new-arrivals.php' ? 'active' : ''; ?>">Новинки</a>
    <a href="catalog.php" class="<?php echo $currentPage == 'catalog.php' ? 'active' : ''; ?>">Каталог</a>
    <a href="categories.php" class="<?php echo $currentPage == 'categories.php' ? 'active' : ''; ?>">Категории</a>
    <a href="contacts.php" class="<?php echo $currentPage == 'contacts.php' ? 'active' : ''; ?>">Контакты</a>
</nav>