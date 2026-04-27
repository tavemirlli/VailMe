<?php
session_start();
require_once 'config/db.php';
require_once 'classes/User.php';
require_once 'classes/Promocode.php';

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
    $privacy_consent = isset($_POST['privacy_consent']) ? 1 : 0;
    $data_consent = isset($_POST['data_consent']) ? 1 : 0;
    
    if (empty($first_name)) {
        $error = 'Введите имя';
    } elseif (empty($email)) {
        $error = 'Введите email';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Введите корректный email';
    } elseif (empty($phone)) {
        $error = 'Введите номер телефона';
    } elseif (strlen($password) < 6) {
        $error = 'Пароль должен быть не менее 6 символов';
    } elseif ($password !== $confirm_password) {
        $error = 'Пароли не совпадают';
    } elseif (!$privacy_consent) {
        $error = 'Необходимо принять политику конфиденциальности';
    } elseif (!$data_consent) {
        $error = 'Необходимо дать согласие на обработку персональных данных';
    } else {
        $user = new User();
        $registerResult = $user->register($email, $password, $first_name, $last_name, $phone);
        
        if ($registerResult['success']) {
            $userId = $user->getId();
        
            $user->privacy_consent = $privacy_consent;
            $user->data_consent = $data_consent;
            $user->consent_date = date('Y-m-d H:i:s');
            $user->save();

            $promocodeResult = Promocode::createForUser($userId);
            
            if ($promocodeResult) {
                $success = $registerResult['message'] . ' Вам начислен приветственный промокод!';
            } else {
                $success = $registerResult['message'];
            }
        } else {
            $error = $registerResult['message'];
        }
    }
}

include 'templates/header.php';
?>
<link rel="stylesheet" href="assets/css/auth.css">
<script src="assets/js/register.js"></script>


    <div class="auth-form">
        <h1>Регистрация</h1>
        
        <?php if ($error): ?>
            <div class="error-message"><?php echo $error; ?></div>
        <?php endif; ?>
        
        <?php if ($success): ?>
            <div class="success-message"><?php echo $success; ?></div>
        <?php endif; ?>
        
        <form method="POST" id="register-form">
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
                <label>Телефон *</label>
                <input type="tel" name="phone" required value="<?php echo htmlspecialchars($_POST['phone'] ?? ''); ?>">
            </div>
            
            <div class="form-row">
                <div class="form-group">
                    <label>Пароль * (мин. 6 символов)</label>
                    <input type="password" name="password" id="password" required>
                </div>
                <div class="form-group">
                    <label>Подтвердите пароль *</label>
                    <input type="password" name="confirm_password" id="confirm_password" required>
                </div>
            </div>
            
            <div class="checkbox-group">
                <label class="checkbox-label">
                    <input type="checkbox" name="privacy_consent" id="privacy_consent" required>
                    <span>Я принимаю <a href="privacy-policy.php" target="_blank">политику конфиденциальности</a></span>
                </label>
            </div>
            
            <div class="checkbox-group">
                <label class="checkbox-label">
                    <input type="checkbox" name="data_consent" id="data_consent" required>
                    <span>Я даю <a href="data-processing.php" target="_blank">согласие на обработку персональных данных</a></span>
                </label>
            </div>
            
            <button type="submit" id="register-btn">Зарегистрироваться</button>
        </form>
        
        <p class="auth-link">Уже есть аккаунт? <a href="login.php">Войти</a></p>
    </div>
</div>

<?php include 'templates/footer.php'; ?>