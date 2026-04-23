<?php
require_once 'BaseModel.php';

class User extends BaseModel {
    protected $table = 'users';
    protected $primaryKey = 'id';
    protected $fillable = ['email', 'password_hash', 'first_name', 'last_name', 'phone', 'is_admin'];
    
    public function register($email, $password, $firstName, $lastName = '', $phone = '') {
        $existing = $this->findByEmail($email);
        if ($existing) {
            return ['success' => false, 'message' => 'Пользователь с таким email уже зарегистрирован'];
        }
        
        if (empty($email) || empty($password) || empty($firstName)) {
            return ['success' => false, 'message' => 'Заполните все обязательные поля'];
        }
        
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return ['success' => false, 'message' => 'Введите корректный email'];
        }
        
        if (strlen($password) < 6) {
            return ['success' => false, 'message' => 'Пароль должен быть не менее 6 символов'];
        }
        
        $this->email = $email;
        $this->password_hash = password_hash($password, PASSWORD_DEFAULT);
        $this->first_name = $firstName;
        $this->last_name = $lastName;
        $this->phone = $phone;
        $this->is_admin = 0;
        
        if ($this->save()) {
            return ['success' => true, 'message' => 'Регистрация прошла успешно!'];
        }
        
        return ['success' => false, 'message' => 'Ошибка при регистрации'];
    }
    
    public function login($email, $password) {
        $user = $this->findByEmail($email);
        
        if ($user && password_verify($password, $user->password_hash)) {
            $this->data = $user->data;
            return ['success' => true, 'user' => $this];
        }
        
        return ['success' => false, 'message' => 'Неверный email или пароль'];
    }
    
    public function findByEmail($email) {
        $db = Database::getInstance();
        $sql = "SELECT * FROM users WHERE email = '" . $db->escape($email) . "'";
        $result = $db->query($sql);
        $data = $db->fetchOne($result);
        
        if ($data) {
            $user = new self();
            $user->data = $data;
            return $user;
        }
        return null;
    }
    
    public function updateProfile($firstName, $lastName, $phone) {
        $this->first_name = $firstName;
        $this->last_name = $lastName;
        $this->phone = $phone;
        return $this->save();
    }
    
    public function changePassword($currentPassword, $newPassword, $confirmPassword) {
        if (!password_verify($currentPassword, $this->password_hash)) {
            return ['success' => false, 'message' => 'Текущий пароль введен неверно'];
        }
        
        if (strlen($newPassword) < 6) {
            return ['success' => false, 'message' => 'Новый пароль должен быть не менее 6 символов'];
        }
        
        if ($newPassword !== $confirmPassword) {
            return ['success' => false, 'message' => 'Новый пароль и подтверждение не совпадают'];
        }
        
        $this->password_hash = password_hash($newPassword, PASSWORD_DEFAULT);
        
        if ($this->save()) {
            return ['success' => true, 'message' => 'Пароль успешно изменен'];
        }
        
        return ['success' => false, 'message' => 'Ошибка при изменении пароля'];
    }
    
    public function saveCart() {
        if (!isset($this->data['id'])) return false;
        
        $db = Database::getInstance();
        $cartJson = json_encode($_SESSION['cart'] ?? []);
        $userId = (int)$this->data['id'];
        
        $sql = "UPDATE users SET cart_data = '$cartJson' WHERE id = $userId";
        return $db->query($sql);
    }
    
    public function loadCart() {
        if (!isset($this->data['id']) || empty($this->data['cart_data'])) return [];
        
        $cartData = json_decode($this->data['cart_data'], true);
        if (is_array($cartData)) {
            return $cartData;
        }
        return [];
    }
    
    public function syncCart() {
        $savedCart = $this->loadCart();
        $currentCart = $_SESSION['cart'] ?? [];
        
        foreach ($savedCart as $key => $item) {
            if (isset($currentCart[$key])) {
                $currentCart[$key]['quantity'] += $item['quantity'];
            } else {
                $currentCart[$key] = $item;
            }
        }
        
        $_SESSION['cart'] = $currentCart;
        $this->saveCart();
    }
    
    public function getId() {
        return $this->data['id'] ?? 0;
    }
    
    public function getEmail() {
        return $this->data['email'] ?? '';
    }
    
    public function getFirstName() {
        return $this->data['first_name'] ?? '';
    }
    
    public function getLastName() {
        return $this->data['last_name'] ?? '';
    }
    
    public function getName() {
        $name = $this->getFirstName();
        $lastName = $this->getLastName();
        if ($lastName) {
            $name .= ' ' . $lastName;
        }
        return $name;
    }
    
    public function getPhone() {
        return $this->data['phone'] ?? '';
    }
    
    public function isAdmin() {
        return ($this->data['is_admin'] ?? 0) == 1;
    }
}
?>