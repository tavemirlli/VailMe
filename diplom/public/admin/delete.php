<?php
require_once '../config/db.php';

if (isset($_GET['id'])) {
    $id = (int)$_GET['id']; 

    $sql = "DELETE FROM products WHERE id = $id";
    mysqli_query($connect, $sql);
}
header('Location: index.php');
exit;