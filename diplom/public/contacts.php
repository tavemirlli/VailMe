<?php
$pageTitle = 'Контакты - VailMe';
include 'templates/header.php';

$success = $_SESSION['contact_success'] ?? '';
$errors = $_SESSION['contact_errors'] ?? [];
unset($_SESSION['contact_success'], $_SESSION['contact_errors']);
?>
<link rel="stylesheet" href="assets/css/style.css">
<link rel="stylesheet" href="assets/css/contacts.css">
<div class="contacts-page">
    <div class="contacts-hero">
        <h1>Связь с нами</h1>
        <p>Мы всегда на связи и готовы ответить на ваши вопросы</p>
    </div>

    <div class="contacts-container">
        <div class="contacts-grid">
            <div class="contact-card">
                <div class="contact-icon">📞</div>
                <h3>Телефон</h3>
                <p>8 988 517-84-20</p>
                <p>8 928 184-41-17</p>
                <small>Ежедневно с 9:00 до 21:00</small>
            </div>

            <div class="contact-card">
                <div class="contact-icon">✉️</div>
                <h3>Email</h3>
                <p>vailme@mail.ru</p>
                <small>Ответ в течение 24 часов</small>
            </div>

            <div class="contact-card">
                <div class="contact-icon">📍</div>
                <h3>Адрес</h3>
                <p>г. Каменск-Шахтинский проспект Карла Маркса, д15</p>
                <small>Пн-Пт: 10:00 - 19:00</small>
            </div>
        </div>

        <div class="social-section">
            <h2>Мы в социальных сетях</h2>
            <p>Подписывайтесь, чтобы быть в курсе новинок и акций</p>
            <div class="social-grid">
                <a href="#" class="social-card">
                    <div class="social-icon telegram">📱</div>
                    <span>Telegram</span>
                    <small>@VaILme1</small>
                </a>
                <a href="https://wa.me/79885178420" class="social-card">
                    <div class="social-icon whatsapp">💬</div>
                    <span>WhatsApp</span>
                    <small>+7 988 517-84-20</small>
                </a>
                <a href="https://t.me/mgvailme" class="social-card">
                    <div class="social-icon instagram">📷</div>
                    <span>Instagram</span>
                    <small>va1lme</small>
                </a>
                <a href="https://vk.com/vailme" class="social-card">
                    <div class="social-icon vk">🌐</div>
                    <span>VKontakte</span>
                    <small>VailMe</small>
                </a>
            </div>
        </div>

        <div class="feedback-section">
            <h2>Напишите нам</h2>
            <p>Оставьте сообщение, и мы свяжемся с вами в ближайшее время</p>
            
            <?php if ($success): ?>
                <div class="success-message"><?php echo $success; ?></div>
            <?php endif; ?>
            
            <?php if (!empty($errors)): ?>
                <div class="error-messages">
                    <?php foreach ($errors as $error): ?>
                        <div class="error-message"><?php echo $error; ?></div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
            
            <form class="feedback-form" method="POST" action="send-message.php">
                <div class="form-row">
                    <input type="text" name="name" placeholder="Ваше имя" required>
                    <input type="email" name="email" placeholder="Ваш Email" required>
                </div>
                <input type="text" name="subject" placeholder="Тема сообщения" required>
                <textarea name="message" rows="5" placeholder="Ваше сообщение..." required></textarea>
                <button type="submit" class="submit-btn">Отправить сообщение</button>
            </form>
        </div>

        <div class="map-section">
            <h2>Как нас найти</h2>
            <div style="position:relative;overflow:hidden;"><a href="https://yandex.ru/maps/11043/kamensk-shakhtinsky/?utm_medium=mapframe&utm_source=maps" style="color:#eee;font-size:12px;position:absolute;top:0px;">Каменск‑Шахтинский</a><a href="https://yandex.ru/maps/11043/kamensk-shakhtinsky/house/prospekt_karla_marksa_15/YEkYdABjTUEGQFpifX9ycX5gbA==/?ll=40.239707%2C48.333077&utm_medium=mapframe&utm_source=maps&z=12.03" style="color:#eee;font-size:12px;position:absolute;top:14px;">Проспект Карла Маркса, 15 — Яндекс Карты</a><iframe src="https://yandex.ru/map-widget/v1/?ll=40.239707%2C48.333077&mode=search&ol=geo&ouri=ymapsbm1%3A%2F%2Fgeo%3Fdata%3DCgoxNTM0Mjc0NTExEoQB0KDQvtGB0YHQuNGPLCDQoNC-0YHRgtC-0LLRgdC60LDRjyDQvtCx0LvQsNGB0YLRjCwg0JrQsNC80LXQvdGB0Lot0KjQsNGF0YLQuNC90YHQutC40LksINC_0YDQvtGB0L_QtdC60YIg0JrQsNGA0LvQsCDQnNCw0YDQutGB0LAsIDE1IgoNARghQhUqUkFC&z=12.03" width="1260" height="400" frameborder="1" allowfullscreen="true" style="position:relative;"></iframe></div>
        </div>
    </div>
</div>

<?php include 'templates/footer.php'; ?>