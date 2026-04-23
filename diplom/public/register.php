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
            
            // Обновляем согласия через класс User
            $user->privacy_consent = $privacy_consent;
            $user->data_consent = $data_consent;
            $user->consent_date = date('Y-m-d H:i:s');
            $user->save();
            
            // Создаем промокод для нового пользователя
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


<style>
.auth-form {
    max-width: 550px;
    margin: 50px auto;
    padding: 30px;
    background: white;
    border-radius: 16px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.05);
}
.auth-form h1 {
    text-align: center;
    margin-bottom: 30px;
}
.form-row {
    display: flex;
    gap: 20px;
}
.form-group {
    flex: 1;
    margin-bottom: 15px;
}
.form-group label {
    display: block;
    margin-bottom: 5px;
    font-weight: 500;
    font-size: 14px;
}
.form-group input {
    width: 100%;
    padding: 10px;
    border: 1px solid #ddd;
    border-radius: 8px;
    box-sizing: border-box;
}
.form-group input:focus {
    outline: none;
    border-color: #F0B1D3;
}
.checkbox-group {
    margin: 15px 0;
}
.checkbox-label {
    display: flex;
    align-items: center;
    gap: 10px;
    cursor: pointer;
    font-size: 14px;
    color: #555;
}
.checkbox-label input {
    width: 18px;
    height: 18px;
    cursor: pointer;
}
.checkbox-label a {
    color: #F0B1D3;
    text-decoration: none;
}
.checkbox-label a:hover {
    text-decoration: underline;
}
button {
    width: 100%;
    padding: 12px;
    background: #F0B1D3;
    color: white;
    border: none;
    border-radius: 30px;
    font-size: 16px;
    font-weight: 500;
    cursor: pointer;
    margin-top: 10px;
    transition: all 0.3s;
}
button:hover {
    background: #e091b8;
    transform: translateY(-2px);
}
.error-message {
    background: #ffebee;
    color: #f44336;
    padding: 12px;
    border-radius: 8px;
    margin-bottom: 20px;
    text-align: center;
    font-size: 14px;
}
.success-message {
    background: #e8f5e9;
    color: #4caf50;
    padding: 12px;
    border-radius: 8px;
    margin-bottom: 20px;
    text-align: center;
    font-size: 14px;
}
.auth-link {
    text-align: center;
    margin-top: 20px;
    font-size: 14px;
}
.auth-link a {
    color: #F0B1D3;
    text-decoration: none;
}
@media (max-width: 550px) {
    .form-row {
        flex-direction: column;
        gap: 0;
    }
}
</style>

<script>
document.getElementById('register-btn').addEventListener('click', function(e) {
    const privacy = document.getElementById('privacy_consent');
    const dataConsent = document.getElementById('data_consent');
    
    if (!privacy.checked) {
        e.preventDefault();
        alert('Необходимо принять политику конфиденциальности');
        return false;
    }
    
    if (!dataConsent.checked) {
        e.preventDefault();
        alert('Необходимо дать согласие на обработку персональных данных');
        return false;
    }
});
</script>

<?php include 'templates/footer.php'; ?>