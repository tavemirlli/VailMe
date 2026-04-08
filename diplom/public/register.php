<?php
session_start();
require_once 'config/db.php';
require_once 'classes/User.php';

$pageTitle = 'Регистрация - VailMe';

if (isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit;
}

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';
    $first_name = trim($_POST['first_name'] ?? '');
    $last_name = trim($_POST['last_name'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    
    if ($password !== $confirm_password) {
        $error = 'Пароли не совпадают';
    } else {
        $user = new User();
        $result = $user->register($email, $password, $first_name, $last_name, $phone);
        
        if ($result['success']) {
            $success = $result['message'];
        } else {
            $error = $result['message'];
        }
    }
}

include 'templates/header.php';
?>
<link rel="stylesheet" href="assets/css/auth.css">
<link rel="stylesheet" href="assets/css/style.css">
<div class="container">
    <div class="auth-form">
        <h1>Регистрация</h1>
        
        <?php if ($error): ?>
            <div class="error-message"><?php echo $error; ?></div>
        <?php endif; ?>
        
        <?php if ($success): ?>
            <div class="success-message"><?php echo $success; ?></div>
        <?php endif; ?>
        
        <form method="POST">
            <div class="form-row">
                <div class="form-group">
                    <label>Имя *</label>
                    <input type="text" name="first_name" required value="<?php echo htmlspecialchars($_POST['first_name'] ?? ''); ?>">
                </div>
                <div class="form-group">
                    <label>Фамилия</label>
                    <input type="text" name="last_name" value="<?php echo htmlspecialchars($_POST['last_name'] ?? ''); ?>">
                </div>
            </div>
            
            <div class="form-group">
                <label>Email *</label>
                <input type="email" name="email" required value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>">
            </div>
            
            <div class="form-group">
                <label>Телефон</label>
                <input type="tel" name="phone" value="<?php echo htmlspecialchars($_POST['phone'] ?? ''); ?>">
            </div>
            
            <div class="form-row">
                <div class="form-group">
                    <label>Пароль * (мин. 6 символов)</label>
                    <input type="password" name="password" required>
                </div>
                <div class="form-group">
                    <label>Подтвердите пароль *</label>
                    <input type="password" name="confirm_password" required>
                </div>
            </div>
            
            <button type="submit">Зарегистрироваться</button>
        </form>
        
        <p class="auth-link">Уже есть аккаунт? <a href="login.php">Войти</a></p>
    </div>
</div>
<?php include 'templates/footer.php'; ?>