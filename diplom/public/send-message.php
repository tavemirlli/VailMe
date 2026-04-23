<?php
session_start();
require_once 'config/db.php';
require_once 'classes/Contact.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: contacts.php');
    exit;
}

$name = trim($_POST['name'] ?? '');
$email = trim($_POST['email'] ?? '');
$subject = trim($_POST['subject'] ?? '');
$message = trim($_POST['message'] ?? '');

$errors = [];

if (empty($name)) {
    $errors[] = 'Введите ваше имя';
}
if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $errors[] = 'Введите корректный email';
}
if (empty($subject)) {
    $errors[] = 'Введите тему сообщения';
}
if (empty($message)) {
    $errors[] = 'Введите текст сообщения';
}

if (!empty($errors)) {
    $_SESSION['contact_errors'] = $errors;
    header('Location: contacts.php');
    exit;
}

$result = Contact::saveMessage($name, $email, $subject, $message);

if ($result['success']) {
    $_SESSION['contact_success'] = 'Ваше сообщение отправлено! Мы свяжемся с вами в ближайшее время.';
} else {
    $_SESSION['contact_errors'] = [$result['message']];
}

header('Location: contacts.php');
exit;
?>