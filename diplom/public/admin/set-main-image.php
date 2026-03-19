<?php
require_once '../config/db.php';
require_once '../classes/ProductImage.php';

if (!isset($_GET['id']) || !isset($_GET['product_id'])) {
    header('Location: index.php');
    exit;
}

$imageId = (int)$_GET['id'];
$productId = (int)$_GET['product_id'];

$image = ProductImage::findById($imageId);

if ($image) {
    $image->setAsMain();
}

header('Location: update.php?id=' . $productId . '&success=1');
exit;
?>