<?php 

class database {

    protected $pdo;
    function __construct($pdo)
    {
        $this->pdo = $pdo;
    }

    function loginCheck() {
        $id = session_id();
        $query = "SELECT `loggedUser` FROM `sessions` WHERE `id` = :id";
        $stmt = $this->pdo->prepare($query);
        $stmt->bindParam(":id", $id);
        $stmt->execute();
        $row = $stmt->fetch();
        if (is_null($row["loggedUser"])) {
            return [401, ["Message" => "You are not currently logged in."]];
        } else {
            return [200, ["Message" => "You are logged in."]];
        }
    }

    function createLog() {
        $query = "INSERT INTO `logs` (`sessionID`, `userID`, `action`, `ip`, `responseCode`) VALUES (:sessionID, :userID, :action, :ip, :responseCode)";
    }

    public function insert() {
        $query="INSERT INTO " . $this->tableName . " (" . $this->columns . ") VALUES (" . $this->parameters . ")";
        $stmt = $this->pdo->prepare($query);
        foreach($this->columns as $v) {
            $stmt->bindParam(":$v", $this->$v);
        }
        $stmt->execute() or die(print_r($stmt->errorInfo(),true));
    }

}