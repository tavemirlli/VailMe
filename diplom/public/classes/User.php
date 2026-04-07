<?php
require_once 'BaseModel.php';

class User extends BaseModel {
    protected $table = 'users';
    protected $primaryKey = 'id';
    protected $fillable = ['email', 'password_hash', 'first_name', 'last_name', 'phone', 'is_admin'];
    
    public function register($name, $email, $phone, $password) {
        // Разделяем имя на first_name и last_name если нужно
        $nameParts = explode(' ', $name, 2);
        $firstName = $nameParts[0];
        $lastName = $nameParts[1] ?? '';
        
        $this->first_name = $firstName;
        $this->last_name = $lastName;
        $this->email = $email;
        $this->phone = $phone;
        $this->password_hash = password_hash($password, PASSWORD_DEFAULT);
        $this->is_admin = 0;
        return $this->save();
    }
    
    public function login($email, $password) {
        $user = User::findByEmail($email);
        if ($user && password_verify($password, $user->password_hash)) {
            $this->data = $user->data;
            return true;
        }
        return false;
    }
    
    public static function findByEmail($email) {
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
    
    // ДОБАВЛЕНЫ МЕТОДЫ GETTERS
    public function getId() {
        return $this->data['id'] ?? 0;
    }
    
    public function getName() {
        $firstName = $this->data['first_name'] ?? '';
        $lastName = $this->data['last_name'] ?? '';
        if ($lastName) {
            return $firstName . ' ' . $lastName;
        }
        return $firstName;
    }
    
    public function getFirstName() {
        return $this->data['first_name'] ?? '';
    }
    
    public function getLastName() {
        return $this->data['last_name'] ?? '';
    }
    
    public function getEmail() {
        return $this->data['email'] ?? '';
    }
    
    public function getPhone() {
        return $this->data['phone'] ?? '';
    }
    
    public function isAdmin() {
        return ($this->data['is_admin'] ?? 0) == 1;
    }
    
    public function loadById($id) {
        return $this->load($id);
    }
}
?>