<?php
require_once __DIR__ . '/../classes/Database.php';

try {
    $db = Database::getInstance();
    $connect = $db->getConnection();
} catch (Exception $e) {
    die('Ошибка подключения к БД: ' . $e->getMessage());
}

session_start();
?>