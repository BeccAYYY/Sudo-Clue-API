<?php 

class validation {


    protected $pdo;
    private $value;
    private $error;
    private $tables = [
        "inprogress" => [
            "id" => "id",
            "historyID" => "test",
            "numberReplacement" => "test",
            "filledSquares" => "test",
            "filledCandidates" => "test"
        ],
        "logs" => [
            "id" => "id",
            "sessionID" => "test",
            "userID" => "test",
            "action" => "test",
            "timestamp" => "test",
            "ip" => "test",
            "responseCode" => "test"
        ],
        "puzzlehistory" => [
            "id" => "id",
            "userID" => "test",
            "puzzleID" => "test",
            "date" => "test",
            "status" => "test",
            "time" => "test",
            "hintsUsed" => "test"
        ],
        "puzzles" => [
            "id" => "id",
            "solutionID" => "test",
            "numbers" => "test",
            "createdDate" => "test",
            "methods" => "test",
            "clues" => "test"
        ],
        "settings" => [
            "id" => "id",
            "userID" => "test",
            "methods" => "test",
            "minimumClues" => "test",
            "colour" => "test"
        ],
        "solutions" => [
            "id" => "id",
            "numbers" => "test",
            "createdDate" => "test"
        ],
        "users" => [
            "id" => "id",
            "username" => "test",
            "password" => "test",
            "role" => "test",
            "dateCreated" => "test"
        ]
    ];

    function __construct($pdo, $table_name, $column_name, $value) {
        $this->pdo = $pdo;
        $this->value = $value;
        $this->test_input($this->value);
        $callback = $this->tables[$table_name][$column_name];
        $this->$callback();
    }

    function test_input($data) {
        $data = trim($data);
        $data = stripslashes($data);
        $data = htmlspecialchars($data);
        return $data;
    }
    
    function length($min_length, $max_length) {
        $length = strlen((string) $this->value);
        if ($length < $min_length) {
            $this->error = "Please enter at least " . $min_length . " characters";
            return false;
        } elseif ($length > $min_length) {
            $this->error = "The maximum length is " . $max_length . " characters";
            return false;
        }
        return true;
    }

    

    function id() {
        if (gettype($this->value) == "string") {
            if ($this->value != (string) (int) $this->value) {
                $this->error = "Invalid format.";
                return false;
            } else {
                $this->value = (int) $this->value;
            }
        }
        if (gettype($this->value) !== "integer") {
            $this->error = "Invalid format.";
            return false;
        }
        if (!$this->length(1, 10)) {
            return false;
        }
        return true;
    }

    function historyID() {

    }

    function numberReplacement() {
        
    }

    function filledSquares() {
        
    }

    function filledCandidates() {
        
    }

    function sessionID() {
        
    }

}



?>