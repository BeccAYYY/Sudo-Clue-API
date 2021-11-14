<?php 

class database {

    protected $pdo;
    private $table_name;
    private $columns;
    private $values;
    private $where;
    public $result;
    public $row_count;
    public $row;

    function __construct($pdo, $statement_type, $table_name, $data_array) {
        $this->pdo = $pdo;
        $this->table_name = $table_name;
        if (isset($data_array["values"])) {
            $this->values = $data_array["values"];
            $this->columns = array_keys($data_array["values"]);
        }
        if (isset($data_array["columns"])) {
            $this->columns = $data_array["columns"];
        }
        if (isset($data_array["where"])) {
            $this->where = $data_array["where"];
        }
        if (isset($data_array["select"])) {
            $this->select = $data_array["select"];
        }
        $this->$statement_type();
    }

    function insert() {
        $query="INSERT INTO `" . $this->table_name . "` (" . $this->columnsString() . ") VALUES (" . $this->paramsString() . ")";
        $stmt = $this->pdo->prepare($query);
        foreach($this->values as $k => &$v) {
            $stmt->bindParam(":$k", $v);
        }
        $this->result = $stmt->execute() or die(print_r($stmt->errorInfo(),true));
        $this->row_count = $stmt->rowCount();

    }

    function select() {
        $query="SELECT " . $this->columnsString() . " FROM `" . $this->table_name . "`";
        if (isset($this->where)) {
            $query .= " WHERE " . $this->where["clause"];
        }
        $stmt = $this->pdo->prepare($query);
        if (isset($this->where)) {
            foreach($this->where["params"] as $k => &$v) {
                $stmt->bindParam($k, $v);
            }
        }
        $this->result = $stmt->execute() or die(print_r($stmt->errorInfo(),true));
        $this->row = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $this->row_count = $stmt->rowCount();
    }

    function update() {
        $query="UPDATE `" . $this->table_name . "` SET " . $this->setString() . " WHERE ". $this->where["clause"];
        $stmt = $this->pdo->prepare($query);
        foreach($this->where["params"] as $k => &$v) {
            $stmt->bindParam($k, $v);
        }
        foreach($this->values as $k => &$v) {
            $stmt->bindParam(":$k", $v);
        }
        $this->result = $stmt->execute() or die(print_r($stmt->errorInfo(),true));
        $this->row_count = $stmt->rowCount();
    }

    function delete() {
        $query="DELETE FROM `" . $this->table_name . "` WHERE ". $this->where["clause"];
        $stmt = $this->pdo->prepare($query);
        foreach($this->where["params"] as $k => &$v) {
            $stmt->bindParam(":$k", $v);
        }
        $this->result = $stmt->execute() or die(print_r($stmt->errorInfo(),true));
        $this->row_count = $stmt->rowCount();
    }



    function columnsString() {
        if ($this->columns == "*" || !isset($this->columns)) {
            return "*";
        }
        $last_value = end($this->columns);
        $string = "";
        foreach($this->columns as $c) {
            if ($c !== $last_value) {
                $string .= "$c, ";
            } else {
                $string .= "$c";
            }
        }
        return $string;
    }

    function paramsString() {
        $last_value = end($this->columns);
        $string = "";
        foreach($this->columns as $p) {
            if ($p !== $last_value) {
                $string .= ":$p, ";
            } else {
                $string .= ":$p";
            }
        }
        return $string;
    }

    function setString() {
        $last_value = end($this->columns);
        $string = "";
        foreach($this->columns as $v) {
            if ($v !== $last_value) {
                $string .= "`" . $v . "` = :" . $v . ", ";
            } else {
                $string .= "`" . $v . "` = :" . $v;
            }
        }
        return $string;
    }




    function createLog() {
        $query = "INSERT INTO `logs` (`sessionID`, `userID`, `action`, `ip`, `responseCode`) VALUES (:sessionID, :userID, :action, :ip, :responseCode)";
    }

}