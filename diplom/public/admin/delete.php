<?php
require_once '../classes/Product.php';

if (isset($_GET['id'])) {
    $id = (int)$_GET['id'];
    
    $product = Product::findById($id);
    
    if ($product) {
        $images = $product->getImages();
        foreach ($images as $image) {
            if (!empty($image->image_url)) {
                $filePath = $_SERVER['DOCUMENT_ROOT'] . $image->image_url;
                if (file_exists($filePath)) {
                    unlink($filePath);
                }
            }
        }
        
        $product->delete();
    }
}

header('Location: index.php');
exit;
?>