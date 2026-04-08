<?php
session_start();
require_once 'config/db.php';
require_once 'classes/User.php';
require_once 'classes/Order.php';

$pageTitle = 'Профиль - VailMe';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$user = new User();
$user->load($_SESSION['user_id']);

$orders = Order::getUserOrders($_SESSION['user_id']);

$success = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['update_profile'])) {
        $firstName = trim($_POST['first_name'] ?? '');
        $lastName = trim($_POST['last_name'] ?? '');
        $phone = trim($_POST['phone'] ?? '');
        
        if ($user->updateProfile($firstName, $lastName, $phone)) {
            $_SESSION['user_name'] = $user->getName();
            $success = 'Профиль успешно обновлен';
        } else {
            $error = 'Ошибка при обновлении профиля';
        }
    }
    
    if (isset($_POST['change_password'])) {
        $currentPassword = $_POST['current_password'] ?? '';
        $newPassword = $_POST['new_password'] ?? '';
        $confirmPassword = $_POST['confirm_password'] ?? '';
        
        $result = $user->changePassword($currentPassword, $newPassword, $confirmPassword);
        
        if ($result['success']) {
            $success = $result['message'];
        } else {
            $error = $result['message'];
        }
    }
}

include 'templates/header.php';
?>
<link rel="stylesheet" href="assets/css/profile.css">
<link rel="stylesheet" href="assets/css/style.css">
<div class="container">
    <div class="profile-container">
        <div class="profile-sidebar">
            <div class="profile-avatar">
                <div class="avatar-placeholder">
                    <?php echo strtoupper(substr($user->getFirstName(), 0, 1)); ?>
                </div>
            </div>
            <h3><?php echo htmlspecialchars($user->getName()); ?></h3>
            <p><?php echo htmlspecialchars($user->getEmail()); ?></p>
            <p><?php echo $user->getPhone() ? htmlspecialchars($user->getPhone()) : 'Телефон не указан'; ?></p>
            <a href="logout.php" class="logout-btn">Выйти</a>
        </div>
        
        <div class="profile-content">
            <div class="profile-tabs">
                <button class="tab-btn active" data-tab="orders">Мои заказы</button>
                <button class="tab-btn" data-tab="settings">Настройки профиля</button>
                <button class="tab-btn" data-tab="security">Безопасность</button>
            </div>
            
            <div class="tab-content active" id="tab-orders">
                <h2>Мои заказы</h2>
                <?php if (empty($orders)): ?>
                    <div class="empty-orders">
                        <p>У вас пока нет заказов</p>
                        <a href="catalog.php" class="btn-shop">Перейти к покупкам</a>
                    </div>
                <?php else: ?>
                    <div class="orders-list">
                        <?php foreach ($orders as $order): ?>
                        <div class="order-card">
                            <div class="order-header">
                                <div class="order-number">Заказ №<?php echo $order['order_number']; ?></div>
                                <div class="order-date"><?php echo date('d.m.Y', strtotime($order['created_at'])); ?></div>
                            </div>
                            <div class="order-status">
                                Статус: 
                                <span class="status status-<?php echo $order['order_status']; ?>">
                                    <?php echo Order::getStatusText($order['order_status']); ?>
                                </span>
                            </div>
                            <div class="order-total">Сумма: <?php echo number_format($order['total_amount'], 0, '.', ' '); ?> ₽</div>
                            <a href="order-view.php?id=<?php echo $order['id']; ?>" class="btn-order-details">Детали заказа</a>
                        </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
            
            <div class="tab-content" id="tab-settings">
                <h2>Настройки профиля</h2>
                
                <?php if ($success): ?>
                    <div class="success-message"><?php echo $success; ?></div>
                <?php endif; ?>
                
                <?php if ($error): ?>
                    <div class="error-message"><?php echo $error; ?></div>
                <?php endif; ?>
                
                <form method="POST" class="profile-form">
                    <div class="form-group">
                        <label>Имя</label>
                        <input type="text" name="first_name" value="<?php echo htmlspecialchars($user->getFirstName()); ?>" required>
                    </div>
                    <div class="form-group">
                        <label>Фамилия</label>
                        <input type="text" name="last_name" value="<?php echo htmlspecialchars($user->getLastName()); ?>">
                    </div>
                    <div class="form-group">
                        <label>Email</label>
                        <input type="email" value="<?php echo htmlspecialchars($user->getEmail()); ?>" disabled>
                        <small>Email нельзя изменить</small>
                    </div>
                    <div class="form-group">
                        <label>Телефон</label>
                        <input type="tel" name="phone" value="<?php echo htmlspecialchars($user->getPhone()); ?>">
                    </div>
                    <button type="submit" name="update_profile" class="btn-save">Сохранить изменения</button>
                </form>
            </div>
            
            <div class="tab-content" id="tab-security">
                <h2>Смена пароля</h2>
                <form method="POST" class="password-form">
                    <div class="form-group">
                        <label>Текущий пароль</label>
                        <input type="password" name="current_password" required>
                    </div>
                    <div class="form-group">
                        <label>Новый пароль (мин. 6 символов)</label>
                        <input type="password" name="new_password" required>
                    </div>
                    <div class="form-group">
                        <label>Подтвердите новый пароль</label>
                        <input type="password" name="confirm_password" required>
                    </div>
                    <button type="submit" name="change_password" class="btn-save">Изменить пароль</button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
document.querySelectorAll('.tab-btn').forEach(btn => {
    btn.addEventListener('click', function() {
        const tabId = this.dataset.tab;
        document.querySelectorAll('.tab-btn').forEach(b => b.classList.remove('active'));
        document.querySelectorAll('.tab-content').forEach(t => t.classList.remove('active'));
        this.classList.add('active');
        document.getElementById('tab-' + tabId).classList.add('active');
    });
});
</script>

<?php include 'templates/footer.php'; ?>