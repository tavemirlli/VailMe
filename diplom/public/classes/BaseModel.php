<?php
abstract class BaseModel {
    protected $db;
    protected $table;
    protected $primaryKey = 'id';
    protected $fillable = [];
    protected $data = [];
    
    public function __construct() {
        $this->db = Database::getInstance();
    }
    
    public function __get($name) {
        return $this->data[$name] ?? null;
    }
    
    public function __set($name, $value) {
        if (in_array($name, $this->fillable) || $name === $this->primaryKey) {
            $this->data[$name] = $value;
        }
    }
    
    public function load($id) {
        $id = (int)$id;
        $sql = "SELECT * FROM {$this->table} WHERE {$this->primaryKey} = $id";
        $result = $this->db->query($sql);
        $data = $this->db->fetchOne($result);
        
        if ($data) {
            $this->data = $data;
            return true;
        }
        return false;
    }
    
    public function save() {
        if (isset($this->data[$this->primaryKey]) && $this->data[$this->primaryKey]) {
            return $this->update();
        } else {
            return $this->insert();
        }
    }
    
    protected function insert() {
        $fields = [];
        $values = [];
        
        foreach ($this->fillable as $field) {
            if (isset($this->data[$field])) {
                $fields[] = "`$field`";
                $values[] = "'" . $this->db->escape($this->data[$field]) . "'";
            }
        }
        
        if (empty($fields)) {
            return false;
        }
        
        $sql = "INSERT INTO {$this->table} (" . implode(', ', $fields) . ") 
                VALUES (" . implode(', ', $values) . ")";
        
        $result = $this->db->query($sql);
        if ($result) {
            $this->data[$this->primaryKey] = $this->db->lastInsertId();
        }
        return $result;
    }
    
    protected function update() {
        $sets = [];
        
        foreach ($this->fillable as $field) {
            if (isset($this->data[$field])) {
                $sets[] = "`$field` = '" . $this->db->escape($this->data[$field]) . "'";
            }
        }
        
        if (empty($sets)) {
            return false;
        }
        
        $sql = "UPDATE {$this->table} SET " . implode(', ', $sets) . " 
                WHERE {$this->primaryKey} = {$this->data[$this->primaryKey]}";
        
        return $this->db->query($sql);
    }
    
    public function delete() {
        if (!isset($this->data[$this->primaryKey])) {
            return false;
        }
        
        $sql = "DELETE FROM {$this->table} WHERE {$this->primaryKey} = {$this->data[$this->primaryKey]}";
        return $this->db->query($sql);
    }
    
    public static function findAll($orderBy = null) {
        $db = Database::getInstance();
        $calledClass = get_called_class();
        $model = new $calledClass();
        
        $sql = "SELECT * FROM {$model->table}";
        if ($orderBy) {
            $sql .= " ORDER BY $orderBy";
        }
        
        $result = $db->query($sql);
        $rows = $db->fetchAll($result);
        
        $items = [];
        foreach ($rows as $row) {
            $item = new $calledClass();
            $item->data = $row;
            $items[] = $item;
        }
        
        return $items;
    }
    
    public static function findById($id) {
        $calledClass = get_called_class();
        $item = new $calledClass();
        return $item->load($id) ? $item : null;
    }
    
    public function toArray() {
        return $this->data;
    }
    
    public function getData() {
        return $this->data;
    }
}
?>