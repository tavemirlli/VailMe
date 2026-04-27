

document.addEventListener('DOMContentLoaded', function() {
    const tabBtns = document.querySelectorAll('.tab-btn');
    const tabContents = document.querySelectorAll('.tab-content');
    
    tabBtns.forEach(btn => {
        btn.addEventListener('click', function() {
            const tabId = this.dataset.tab;

            tabBtns.forEach(b => b.classList.remove('active'));
            tabContents.forEach(t => t.classList.remove('active'));
   
            this.classList.add('active');
            const activeTab = document.getElementById('tab-' + tabId);
            if (activeTab) {
                activeTab.classList.add('active');
            }
        });
    });
});

function copyPromocode(code) {
    navigator.clipboard.writeText(code).then(() => {
        alert('Промокод скопирован: ' + code);
    }).catch(() => {
        alert('Не удалось скопировать промокод');
    });
}