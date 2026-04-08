<?php
session_start();
require_once 'config/db.php';

$pageTitle = 'Заказ оформлен - VailMe';

$orderNumber = $_SESSION['order_success'] ?? '';
unset($_SESSION['order_success']);

include 'templates/header.php';
?>
<link rel="stylesheet" href="assets/css/style.css">
<link rel="stylesheet" href="assets/css/auth.css">
<div class="container">
    <div class="success-container">
        <h1>Заказ успешно оформлен!</h1>
        <div class="success-message">
            <p>Номер вашего заказа: <strong><?php echo $orderNumber; ?></strong></p>
            <p>Счет на оплату отправлен на вашу электронную почту.</p>
            <p>После оплаты администратор свяжется с вами для уточнения деталей доставки.</p>
            <p>Если у вас есть вопросы, звоните: <strong>8 988 888-88-88</strong></p>
        </div>
        <a href="catalog.php" class="btn">Продолжить покупки</a>
    </div>
</div>
<?php include 'templates/footer.php'; ?>