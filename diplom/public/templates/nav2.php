<?php
$currentPage = basename($_SERVER['PHP_SELF']);
$cartCount = 0;
if (isset($_SESSION['cart'])) {
    foreach ($_SESSION['cart'] as $item) {
        $cartCount += $item['quantity'];
    }
}
?>
<nav class="main-nav">
    <a href="../index.php" class="<?php echo $currentPage == 'index.php' ? 'active' : ''; ?>">Главная</a>
    <a href="../new-arrivals.php" class="<?php echo $currentPage == 'new-arrivals.php' ? 'active' : ''; ?>">Новинки</a>
    <a href="../catalog.php" class="<?php echo $currentPage == 'catalog.php' ? 'active' : ''; ?>">Каталог</a>
    <a href="../categories.php" class="<?php echo $currentPage == 'categories.php' ? 'active' : ''; ?>">Категории</a>
    <a href="../contacts.php" class="<?php echo $currentPage == 'contacts.php' ? 'active' : ''; ?>">Контакты</a>
    <div class="header-icons">
    <a href="../cart.php" class="cart-icon">
        🛒
        <?php if ($cartCount > 0): ?>
            <span class="cart-count"><?php echo $cartCount; ?></span>
        <?php endif; ?>
    </a>
    
    <?php if (isset($_SESSION['user_id'])): ?>
        <a href="../profile.php" class="profile-icon">👤</a>
    <?php else: ?>
        <a href="../login.php" class="login-icon">👤</a>
    <?php endif; ?>
</div>
</nav>
