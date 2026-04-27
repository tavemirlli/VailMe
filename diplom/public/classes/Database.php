<?php
class Database {
    private static $instance = null;
    private $connection;
    private $host = 'mysql';
    private $user = 'root';
    private $pass = 'root123';
    private $name = 'vailme_store';
    
    private function __construct() {
        $this->connection = mysqli_connect($this->host, $this->user, $this->pass, $this->name);
        
        if (!$this->connection) {
            throw new Exception('Ошибка подключения к БД: ' . mysqli_connect_error());
        }
        
        mysqli_set_charset($this->connection, "utf8mb4");
    }
    
    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    public function getConnection() {
        return $this->connection;
    }
    
    public function query($sql) {
        $result = mysqli_query($this->connection, $sql);
        if (!$result) {
            throw new Exception('Ошибка запроса: ' . mysqli_error($this->connection));
        }
        return $result;
    }
    
    public function fetchAll($result) {
        return mysqli_fetch_all($result, MYSQLI_ASSOC);
    }
    
    public function fetchOne($result) {
        return mysqli_fetch_assoc($result);
    }
    
    public function escape($string) {
        return mysqli_real_escape_string($this->connection, $string);
    }
    
    public function lastInsertId() {
        return mysqli_insert_id($this->connection);
    }
    
    public function affectedRows() {
        return mysqli_affected_rows($this->connection);
    }
}
?>