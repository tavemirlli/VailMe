<?php

?>
<!DOCTYPE html>
<html lang="ru">
    
<link rel="stylesheet" href="assets/css/admin-style.css">
<div class="forms">
    <?php if ($formType === 'category' || $formType === 'both'): ?>
    <div class="form-box">
        <h3>Добавить категорию</h3>
        <form method="POST" action="">
            <input type="text" name="category_name" placeholder="Название категории" required>
            <input type="hidden" name="action" value="add_category">
            <button type="submit">Добавить категорию</button>
        </form>
    </div>
    <?php endif; ?>
    
    <?php if ($formType === 'subcategory' || $formType === 'both'): ?>
    <div class="form-box">
        <h3>Добавить подкатегорию</h3>
        <form method="POST" action="">
            <select name="category_id" required>
                <option value="">Выберите категорию</option>
                <?php foreach ($categories as $category): ?>
                    <option value="<?php echo $category['id']; ?>">
                        <?php echo htmlspecialchars($category['name']); ?>
                    </option>
                <?php endforeach; ?>
            </select>
            <input type="text" name="subcategory_name" placeholder="Название подкатегории" required>
            <input type="hidden" name="action" value="add_subcategory">
            <button type="submit">Добавить подкатегорию</button>
        </form>
    </div>
    <?php endif; ?>
</div>