<?php 

class dbSessionHandler {

public $pdo;
protected $dsn = "mysql:dbname=sudoclue;host=localhost";
protected $db_username = "root";
protected $db_password = "";

//Seconds for expiry
protected $expiry = 31536000;


public function __construct() {
    session_cache_expire(($this->expiry / 60));
    session_set_save_handler(
        array(&$this, "open"),
        array(&$this, "close"), 
        array(&$this, "read"), 
        array(&$this, "write"), 
        array(&$this, "destroy"), 
        array(&$this, "gc")
    );
    session_start();
    if (!isset($_SESSION['CREATED'])) {
        $_SESSION['CREATED'] = time();
    } else if (time() - $_SESSION['CREATED'] > $this->expiry) {
        session_regenerate_id(true);
        $_SESSION['CREATED'] = time();
    }
    setcookie("PHPSESSID", session_id(), time() + $this->expiry, "/");
}

public function open() {
    try {
        $this->pdo = new PDO(
                $this->dsn,
                $this->db_username,
                $this->db_password
        );
        return true;
    } catch (PDOException $e) {
        echo 'Connection to session database failed: ' . $e->getMessage();
    }
    return false;
}

public function read($id) {
    $query = "SELECT `data` FROM `sessions` WHERE `id` = :id";
    $stmt = $this->pdo->prepare($query);
    $stmt->bindParam(":id", $id);
    $stmt->execute();
    $row = $stmt->fetch();
    if ($row) {
        return $row['data'];
    }
    return '';
}

public function write($id, $data) {
    $lastUpdate = time();
    $query = "INSERT INTO `sessions` (`id`, `data`, `lastUpdate`) VALUES (:id, :data, :lastUpdate) ON DUPLICATE KEY UPDATE `id` = :id, `data` = :data, `lastUpdate` = :lastUpdate";
    $stmt = $this->pdo->prepare($query);
    $stmt->bindParam(":id", $id);
    $stmt->bindParam(":data", $data);
    $stmt->bindParam(":lastUpdate", $lastUpdate);
    $result = $stmt->execute();
    return $result;
}

public function destroy($id) {
    $query = "DELETE FROM `sessions` WHERE `id` = :id";
    $stmt = $this->pdo->prepare($query);
    $stmt->bindParam(":id", $id);
    $result = $stmt->execute();
    return $result;
}

public function gc($maxlifetime) {
    $expired = time() - $maxlifetime;
    $query = "DELETE FROM `sessions` WHERE `lastUpdate` < :expired";
    $stmt = $this->pdo->prepare($query);
    $stmt->bindParam(":expired", $expired);
    $result = $stmt->execute();
    return $result;
}

public function close() {
    $this->pdo = null;
    return true;
}

public function __destruct() {
    session_write_close();
}

public function getTimeout() {
    return (int) ini_get('session.gc_maxlifetime');
}

}



?>
