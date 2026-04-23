<?php
session_start();
require_once 'config/db.php';
require_once 'classes/User.php';
require_once 'classes/Cart.php';

$pageTitle = 'Вход - VailMe';

if (isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    
    $user = new User();
    $result = $user->login($email, $password);
    
    if ($result['success']) {
        $_SESSION['user_id'] = $user->getId();
        $_SESSION['user_name'] = $user->getName();
        $_SESSION['user_email'] = $user->getEmail();
        $_SESSION['user_phone'] = $user->getPhone();
        $_SESSION['is_admin'] = $user->isAdmin();

        Cart::syncCartForUser($user->getId());
        
        header('Location: index.php');
        exit;
    } else {
        $error = $result['message'];
    }
}

include 'templates/header.php';
?>
<link rel="stylesheet" href="assets/css/auth.css">
<link rel="stylesheet" href="assets/css/style.css">

    <div class="auth-form">
        <h1>Вход</h1>
        
        <?php if ($error): ?>
            <div class="error-message"><?php echo $error; ?></div>
        <?php endif; ?>
        
        <form method="POST">
            <div class="form-group">
                <label>Email</label>
                <input type="email" name="email" required>
            </div>
            
            <div class="form-group">
                <label>Пароль</label>
                <input type="password" name="password" required>
            </div>
            
            <button type="submit">Войти</button>
        </form>
        
        <p class="auth-link">Нет аккаунта? <a href="register.php">Зарегистрироваться</a></p>
    </div>
</div>