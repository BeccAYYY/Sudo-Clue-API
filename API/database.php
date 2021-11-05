<?php 

class database {

    protected $pdo;
    private $table;
    private $values;
    private $where;
    private $columns;

    function __construct($pdo, $statement_type, $table, $data_array)
    {
        $this->pdo = $pdo;
        $this->table = $table;
        if (isset($data_array["values"])) {
            $this->values = $data_array["values"];
            $this->columns = array_keys($data_array["values"]);
        }
        if (isset($data_array["where"])) {
            $this->where = $data_array["where"];
        }
        if (isset($data_array["select"])) {
            $this->select = $data_array["select"];
        }
        if ($statement_type == "insert") {
            $this->insert();
        } elseif ($statement_type == "update") {
            $this->update();
        }
    }

    function login_check() {
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

    public function select() {
        $query="SELECT `" . $this->table . "` (" . $this->columns() . ") VALUES (" . $this->params() . ")";
        echo $query;
        $stmt = $this->pdo->prepare($query);
        foreach($this->values as $k => &$v) {
            echo $k . ", " . $v . "<br>";
            $stmt->bindParam(":$k", $v);
        }
        $stmt->execute() or die(print_r($stmt->errorInfo(),true));
    }

    public function insert() {
        $query="INSERT INTO `" . $this->table . "` (" . $this->columns() . ") VALUES (" . $this->params() . ")";
        echo $query;
        $stmt = $this->pdo->prepare($query);
        foreach($this->values as $k => &$v) {
            echo $k . ", " . $v . "<br>";
            $stmt->bindParam(":$k", $v);
        }
        $stmt->execute() or die(print_r($stmt->errorInfo(),true));
    }

    public function update() {

    }

    function columns() {
        $array_keys = array_keys($this->values);
        $last_value = end($array_keys);
        $string = "";
        foreach($array_keys as $k) {
            if ($k !== $last_value) {
                $string .= "`$k`, ";
            } else {
                $string .= "`$k`";
            }
        }
        return $string;
    }

    function params() {
        $array_keys = array_keys($this->values);
        $last_value = end($array_keys);
        $string = "";
        foreach($array_keys as $k) {
            if ($k !== $last_value) {
                $string .= ":$k, ";
            } else {
                $string .= ":$k";
            }
        }
        return $string;
    }


}