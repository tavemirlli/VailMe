
    let variantCount = 1;
    function addVariant() {
        const variantsDiv = document.getElementById('variants');
        const newVariant = document.createElement('div');
        newVariant.className = 'variant-row';
        newVariant.innerHTML = `
            <input type="text" name="variants[${variantCount}][color]" placeholder="Цвет">
            <input type="text" name="variants[${variantCount}][size]" placeholder="Размер">
            <input type="number" name="variants[${variantCount}][price]" step="0.01" placeholder="Цена (если отличается)">
            <input type="number" name="variants[${variantCount}][quantity]" placeholder="Количество" value="0">
            <button type="button" class="remove-variant" onclick="this.parentElement.remove()">Удалить</button>
        `;
        variantsDiv.appendChild(newVariant);
        variantCount++;
    }