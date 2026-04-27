/**
 * Страница товара - все JavaScript функции
 */

// Делаем функцию глобальной для доступа из onclick в HTML
window.changeImage = function(imageUrl, element) {
    console.log('changeImage вызвана, URL:', imageUrl);
    
    const mainImage = document.getElementById('main-product-image');
    if (mainImage) {
        mainImage.src = imageUrl;
    }
    
    const thumbnails = document.querySelectorAll('.thumbnail');
    for (let i = 0; i < thumbnails.length; i++) {
        thumbnails[i].classList.remove('active');
    }
    
    if (element) {
        element.classList.add('active');
    }
};

const variantsData = window.variantsData || [];

let selectedColor = window.selectedColor || 'Стандарт';
let selectedSize = window.selectedSize || 'Стандарт';
let currentQuantity = 1;
let maxQuantity = window.stockQuantity || 0;

function updateQuantity() {
    const quantityValue = document.getElementById('quantity-value');
    if (quantityValue) {
        quantityValue.textContent = currentQuantity;
    }
}

function updatePriceAndStock() {
    const variant = variantsData.find(v => (v.color === selectedColor || (selectedColor === 'Стандарт' && !v.color)) && 
                                          (v.size === selectedSize || (selectedSize === 'Стандарт' && !v.size)));
    
    const priceElement = document.getElementById('product-price');
    const stockInfo = document.getElementById('stock-info');
    const addToCartBtn = document.querySelector('.btn-add-to-cart');
    
    if (variant) {
        if (priceElement) {
            priceElement.textContent = variant.price.toLocaleString() + ' ₽';
        }
        if (stockInfo) {
            if (variant.quantity > 0) {
                stockInfo.textContent = 'В наличии: ' + variant.quantity + ' шт.';
                if (addToCartBtn) {
                    addToCartBtn.disabled = false;
                    addToCartBtn.style.background = '#F0B1D3';
                    addToCartBtn.style.cursor = 'pointer';
                    addToCartBtn.textContent = 'В корзину';
                }
            } else {
                stockInfo.textContent = 'Нет в наличии';
                if (addToCartBtn) {
                    addToCartBtn.disabled = true;
                    addToCartBtn.style.background = '#ccc';
                    addToCartBtn.style.cursor = 'not-allowed';
                    addToCartBtn.textContent = 'Нет в наличии';
                }
            }
        }
        maxQuantity = variant.quantity > 0 ? variant.quantity : 0;
    } else {
        if (stockInfo) {
            stockInfo.textContent = 'Нет в наличии';
        }
        if (addToCartBtn) {
            addToCartBtn.disabled = true;
            addToCartBtn.style.background = '#ccc';
            addToCartBtn.style.cursor = 'not-allowed';
            addToCartBtn.textContent = 'Нет в наличии';
        }
        maxQuantity = 0;
    }
    
    if (currentQuantity > maxQuantity && maxQuantity > 0) {
        currentQuantity = maxQuantity;
        updateQuantity();
    } else if (maxQuantity === 0) {
        currentQuantity = 0;
        updateQuantity();
    }
}

function addToCart(productId) {
    const addToCartBtn = document.querySelector('.btn-add-to-cart');
    if (addToCartBtn && addToCartBtn.disabled) {
        alert('Товар временно недоступен для заказа');
        return;
    }
    
    if (maxQuantity <= 0) {
        alert('Товара нет в наличии');
        return;
    }
    
    const quantity = currentQuantity;
    if (quantity <= 0) {
        alert('Выберите количество');
        return;
    }
    
    const color = encodeURIComponent(selectedColor);
    const size = encodeURIComponent(selectedSize);
    window.location.href = '../cart.php?add=' + productId + '&quantity=' + quantity + '&color=' + color + '&size=' + size;
}

// Инициализация при загрузке страницы
document.addEventListener('DOMContentLoaded', function() {
    // Выбор цвета
    const colorBtns = document.querySelectorAll('.color-btn');
    for (let i = 0; i < colorBtns.length; i++) {
        colorBtns[i].onclick = function() {
            for (let j = 0; j < colorBtns.length; j++) {
                colorBtns[j].classList.remove('active');
            }
            this.classList.add('active');
            selectedColor = this.getAttribute('data-color');
            updatePriceAndStock();
        };
    }
    
    // Выбор размера
    const sizeBtns = document.querySelectorAll('.size-btn');
    for (let i = 0; i < sizeBtns.length; i++) {
        sizeBtns[i].onclick = function() {
            for (let j = 0; j < sizeBtns.length; j++) {
                sizeBtns[j].classList.remove('active');
            }
            this.classList.add('active');
            selectedSize = this.getAttribute('data-size');
            updatePriceAndStock();
        };
    }
    
    // Кнопки количества
    const minusBtn = document.querySelector('.minus');
    const plusBtn = document.querySelector('.plus');
    
    if (minusBtn) {
        minusBtn.onclick = function() {
            if (currentQuantity > 1 && maxQuantity > 0) {
                currentQuantity--;
                updateQuantity();
            }
        };
    }
    
    if (plusBtn) {
        plusBtn.onclick = function() {
            if (currentQuantity < maxQuantity && maxQuantity > 0) {
                currentQuantity++;
                updateQuantity();
            }
        };
    }
    
    // Активация первого варианта по умолчанию
    if (colorBtns.length > 0 && colorBtns[0]) {
        colorBtns[0].classList.add('active');
    }
    if (sizeBtns.length > 0 && sizeBtns[0]) {
        sizeBtns[0].classList.add('active');
    }
    
    // Обновление цены и наличия
    updatePriceAndStock();
    updateQuantity();
});