<?php
session_start();
require_once '../config/db.php';
require_once '../classes/Contact.php';

if (!isset($_SESSION['is_admin']) || $_SESSION['is_admin'] !== true) {
    header('Location: ../index.php');
    exit;
}
if (isset($_GET['read'])) {
    $id = (int)$_GET['read'];
    Contact::markAsRead($id);
    header('Location: messages.php');
    exit;
}
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    Contact::deleteMessage($id);
    header('Location: messages.php');
    exit;
}

$messages = Contact::getAllMessages();
$unreadCount = Contact::getUnreadCount();

$pageTitle = 'Сообщения - Админка';
include '../templates/admin-header.php';
?>
<link rel="stylesheet" href="assets/css/message.css">
<h1>Сообщения из формы обратной связи</h1>

<div class="messages-stats">
    <div class="stat">
        Всего: <?php echo count($messages); ?>
    </div>
    <div class="stat unread">
        Непрочитанных: <?php echo $unreadCount; ?>
    </div>
</div>

<div class="messages-list">
    <?php if (empty($messages)): ?>
        <div class="empty-messages">Сообщений пока нет</div>
    <?php else: ?>
        <?php foreach ($messages as $msg): ?>
        <div class="message-card <?php echo $msg['is_read'] ? 'read' : 'unread'; ?>">
            <div class="message-header">
                <div class="message-info">
                    <strong><?php echo htmlspecialchars($msg['name']); ?></strong>
                    <span class="message-email"><?php echo htmlspecialchars($msg['email']); ?></span>
                    <span class="message-date"><?php echo date('d.m.Y H:i', strtotime($msg['created_at'])); ?></span>
                </div>
                <div class="message-actions">
                    <?php if (!$msg['is_read']): ?>
                        <a href="messages.php?read=<?php echo $msg['id']; ?>" class="btn-read">Отметить прочитанным</a>
                    <?php endif; ?>
                    <a href="messages.php?delete=<?php echo $msg['id']; ?>" class="btn-delete" onclick="return confirm('Удалить сообщение?')">Удалить</a>
                </div>
            </div>
            <div class="message-subject">
                Тема: <?php echo htmlspecialchars($msg['subject']); ?>
            </div>
            <div class="message-body">
                <?php echo nl2br(htmlspecialchars($msg['message'])); ?>
            </div>
        </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>

<?php include '../templates/admin-footer.php'; ?>