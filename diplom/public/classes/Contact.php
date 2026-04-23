<?php
require_once 'BaseModel.php';

class Contact extends BaseModel {
    protected $table = 'contacts';
    protected $primaryKey = 'id';
    protected $fillable = ['name', 'email', 'subject', 'message', 'is_read'];
    
    public static function saveMessage($name, $email, $subject, $message) {
        $contact = new self();
        $contact->name = $name;
        $contact->email = $email;
        $contact->subject = $subject;
        $contact->message = $message;
        $contact->is_read = 0;
        
        if ($contact->save()) {
            return ['success' => true, 'id' => $contact->id];
        }
        return ['success' => false, 'message' => 'Ошибка при сохранении сообщения'];
    }
    
    public static function getUnreadCount() {
        $db = Database::getInstance();
        $sql = "SELECT COUNT(*) as count FROM contacts WHERE is_read = 0";
        $result = $db->query($sql);
        $data = $db->fetchOne($result);
        return $data['count'] ?? 0;
    }
    
    public static function getAllMessages() {
        $db = Database::getInstance();
        $sql = "SELECT * FROM contacts ORDER BY is_read ASC, created_at DESC";
        $result = $db->query($sql);
        return $db->fetchAll($result);
    }
    
    public static function markAsRead($id) {
        $db = Database::getInstance();
        $id = (int)$id;
        $sql = "UPDATE contacts SET is_read = 1 WHERE id = $id";
        return $db->query($sql);
    }
    
    public static function deleteMessage($id) {
        $db = Database::getInstance();
        $id = (int)$id;
        $sql = "DELETE FROM contacts WHERE id = $id";
        return $db->query($sql);
    }
}
?>