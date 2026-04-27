
document.addEventListener('DOMContentLoaded', function() {
    const registerBtn = document.getElementById('register-btn');
    const privacyCheckbox = document.getElementById('privacy_consent');
    const dataConsentCheckbox = document.getElementById('data_consent');
    
    if (registerBtn) {
        registerBtn.addEventListener('click', function(e) {
            if (!privacyCheckbox.checked) {
                e.preventDefault();
                alert('Необходимо принять политику конфиденциальности');
                return false;
            }
            
            if (!dataConsentCheckbox.checked) {
                e.preventDefault();
                alert('Необходимо дать согласие на обработку персональных данных');
                return false;
            }
        });
    }
});