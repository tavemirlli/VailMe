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
                <p>8 988 888-88-88</p>
                <p>8 999 999-99-99</p>
                <small>Ежедневно с 9:00 до 21:00</small>
            </div>

            <div class="contact-card">
                <div class="contact-icon">✉️</div>
                <h3>Email</h3>
                <p>info@vailme.ru</p>
                <p>support@vailme.ru</p>
                <small>Ответ в течение 24 часов</small>
            </div>

            <div class="contact-card">
                <div class="contact-icon">📍</div>
                <h3>Адрес</h3>
                <p>г. Москва, ул. Тверская, д. 15</p>
                <p>м. Маяковская</p>
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
                    <small>@vailme_shop</small>
                </a>
                <a href="#" class="social-card">
                    <div class="social-icon whatsapp">💬</div>
                    <span>WhatsApp</span>
                    <small>+7 988 888-88-88</small>
                </a>
                <a href="#" class="social-card">
                    <div class="social-icon instagram">📷</div>
                    <span>Instagram</span>
                    <small>@vailme_official</small>
                </a>
                <a href="#" class="social-card">
                    <div class="social-icon vk">🌐</div>
                    <span>VKontakte</span>
                    <small>vk.com/vailme</small>
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
            <div class="map-container">
                <iframe 
                    src="https://yandex.ru/map-widget/v1/?um=constructor%3A1a2b3c4d5e6f7g8h9i0j&source=constructor" 
                    width="100%" 
                    height="400" 
                    frameborder="0"
                    allowfullscreen>
                </iframe>
            </div>
        </div>
    </div>
</div>

<?php include 'templates/footer.php'; ?>