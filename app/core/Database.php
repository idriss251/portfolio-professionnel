<?php
/**
 * Database - Singleton PDO pour connexion à la base de données
 */

class Database
{
    private static $instance = null;
    private $pdo = null;

    private function __construct()
    {
        try {
            $host = Config::get('DB_HOST', 'localhost');
            $port = Config::get('DB_PORT', '3306');
            $database = Config::get('DB_DATABASE');
            $username = Config::get('DB_USERNAME', 'root');
            $password = Config::get('DB_PASSWORD', '');

            $dsn = "mysql:host=$host;port=$port;dbname=$database;charset=utf8mb4";
            
            $this->pdo = new PDO($dsn, $username, $password, [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4",
            ]);
        } catch (PDOException $e) {
            die('Erreur connexion BD: ' . $e->getMessage());
        }
    }

    public static function getInstance()
    {
        if (self::$instance === null) {
            self::$instance = new Database();
        }
        return self::$instance;
    }

    public function query($sql, $params = [])
    {
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt;
    }

    public function fetch($sql, $params = [])
    {
        return $this->query($sql, $params)->fetch();
    }

    public function fetchAll($sql, $params = [])
    {
        return $this->query($sql, $params)->fetchAll();
    }

    public function insert($table, $data)
    {
        $keys = array_keys($data);
        $values = array_values($data);
        $placeholders = str_repeat('?,', count($keys) - 1) . '?';
        
        $sql = "INSERT INTO $table (" . implode(',', $keys) . ") VALUES ($placeholders)";
        $stmt = $this->query($sql, $values);
        
        return $this->pdo->lastInsertId();
    }

    public function update($table, $data, $where, $whereParams = [])
    {
        $set = implode(',', array_map(fn($k) => "$k=?", array_keys($data)));
        $sql = "UPDATE $table SET $set WHERE $where";
        
        $params = array_merge(array_values($data), $whereParams);
        return $this->query($sql, $params)->rowCount();
    }

    public function delete($table, $where, $params = [])
    {
        $sql = "DELETE FROM $table WHERE $where";
        return $this->query($sql, $params)->rowCount();
    }

    public function tableExists($table)
    {
        $result = $this->fetch(
            "SELECT 1 FROM information_schema.tables WHERE table_schema = ? AND table_name = ?",
            [Config::get('DB_DATABASE'), $table]
        );
        return $result !== false;
    }

    public function beginTransaction()
    {
        return $this->pdo->beginTransaction();
    }

    public function commit()
    {
        return $this->pdo->commit();
    }

    public function rollback()
    {
        return $this->pdo->rollBack();
    }
}
