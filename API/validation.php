<?php 

class validation {

    //Class for validating data that is received from client-side.
    //Some functions are currently incomplete and commented out, as these will not be needed until admin panel is created.


    //$pdo passed in during __construct()
    protected $pdo;

    //$value is the data that is being validated
    private $value;

    //functions within this class with assign a string to $error if validation is unsuccessful
    public $error;

    //$result will be true is $value is validated and false if it is not.
    public $result;

    /*
    The tables array provides the callback function for the validation of $value. 
    The table name and column name are provided on instantiation of the class.
    $tables array = [
        "table name" => [
            "column name" => "callback function for validation"
        ]
    ]*/

    private $tables = [
        "inprogress" => [
            "id" => "id",
            "historyID" => "historyID",
            "numberReplacement" => "numberReplacement",
            "filledSquares" => "filledSquares",
            "filledCandidates" => "filledCandidates"
        ],
        "logs" => [
            "id" => "id",
            "sessionID" => "sessionID",
            "userID" => "userID",
            "action" => "action",
            "timestamp" => "timestamp",
            "ip" => "ip",
            "responseCode" => "responseCode"
        ],
        "puzzlehistory" => [
            "id" => "id",
            "userID" => "userID",
            "puzzleID" => "puzzleID",
            "date" => "timestamp",
            "status" => "status",
            "time" => "time",
            "hintsUsed" => "hintsUsed"
        ],
        "puzzles" => [
            "id" => "id",
            "solutionID" => "solutionID",
            "numbers" => "puzzleNumbers",
            "createdDate" => "timestamp",
            "methods" => "methods",
            "clues" => "clues"
        ],
        "solutions" => [
            "id" => "id",
            "numbers" => "solutionNumbers",
            "createdDate" => "timestamp"
        ],
        "users" => [
            "id" => "id",
            "username" => "username",
            "password" => "password",
            "role" => "role",
            "dateCreated" => "timestamp",
            "methods" => "methods",
            "minimumClues" => "clues",
            "colour" => "colour"
        ]
    ];

    protected $actions = [
        "login_check",
        "username_exists",
        "get_user_details",
        "register",
        "login",
        "logout",
        "update_user_details",
        "new_puzzle",
        "update_puzzle_progress",
        "complete_puzzle",
        "get_leaderboard",
        "delete_account"
    ];

    //Function that runs upon instantiation of the class.
    //$pdo is the database connect, $table_name and $column_name are the table and column for which $value is being valdated.
    function __construct($pdo, $table_name, $column_name, $value) {
        $this->pdo = $pdo;
        $this->value = $value;
        //Gets the callback function from the tables array using the table and column names provided.
        $callback = $this->tables[$table_name][$column_name];
        $this->result = $this->$callback();
    }

    function sanitize($data) {
        $data = trim($data);
        $data = stripslashes($data);
        $data = htmlspecialchars($data);
        return $data;
    }
    
    function length($min_length, $max_length) {
        $length = strlen((string) $this->value);
        if ($length < $min_length) {
            $this->error = "Cannot be less than " . $min_length . " characters";
            return false;
        } elseif ($length > $min_length) {
            $this->error = "Cannot be more than " . $max_length . " characters";
            return false;
        }
        return true;
    }

    function integer() {
        if (gettype($this->value) == "string") {
            if ($this->value != (string) (int) $this->value) {
                $this->error = "Invalid format (string contains characters aside from numbers).";
                return false;
            } else {
                $this->value = (int) $this->value;
            }
        }
        if (gettype($this->value) !== "integer") {
            $this->error = "Invalid format (data type not string or integer).";
            return false;
        }
        return true;
    }

    function id() {
        if ($this->integer()) {
            if ($this->length(1, 10)) {
                return true;
            }
        }
        return false;
    }

    /*function historyID() {
        if ($this->id()) {
            //see if id exists in history table
        }
        return false;
    }

    function numberReplacement() {
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
        if (!$this->length(9, 9)) {
            return false;
        }
        if (Doesn't contain 1-9) {
            $this->error = "Invalid Format.";
            return false;
        }
    }*/

    function filledSquares() {
        if (preg_match("/^[0-9]{81}$/", $this->value)) {
            return true;
        }
        $this->error = "Invalid format for filled squares.";
        return false;
    }

    function filledCandidates() {
        $json = json_decode($this->value);
        if (gettype($this->value) == "string" and json_last_error() === JSON_ERROR_NONE and count($json) == 9) {
            foreach($json as $row) {
                if (count($row) !== 9) {
                    $this->error = "Invalid format for filled candidates.";
                    return false;
                }
                foreach ($row as $cell) {
                    if (gettype($cell) === "array" and $cell) {
                        $last_candidate = 0;
                        foreach ($cell as $candidate) {
                            if ($candidate > 9 or $candidate < 1 or $candidate <= $last_candidate) {
                                $this->error = "Invalid format.";
                                return false;
                            } 
                            $last_candidate = $candidate;
                        }
                    }
                }
            }
            return true;
        }
        $this->error = "Invalid format for filled candidates.";
        return false;
    }

    /*function sessionID() {

    }*/

    function action() {
        if (in_array($this->value, $this->actions, true)) {
            return true;
        }
        $this->error = "Invalid action.";
        return false;
    }

    /*function timestamp() {
        
    }

    function ip() {
        
    }

    function responseCode() {
        
    }

    function puzzleID() {
        
    }

    function date() {
        
    }*/

    function time() {
        if ($this->integer()) {
            if ($this->length(1, 6)) {
                return true;
            }
        }
        $this->error = "Inavlid time format.";
        return false;
    }

    function hintsUsed() {
        if ($this->integer()) {
            if ($this->length(1, 4)) {
                return true;
            }
        }
        $this->error = "Inavlid hint format.";
        return false;
    }

    /*function solutionID() {
        
    }

    function puzzleNumbers() {
        
    }*/

    function methods() {
        
    }

    function clues() {
        if ($this->integer()) {
            if ($this->value < 81 and $this->value > 16) {
                return true;
            }
        }
        $this->error = "Inavlid clue format.";
        return false;
    }

    /*function colour() {
        
    }

    function solutionNumbers() {
        
    }*/

    function username() {
        if (preg_match("/^([0-9]|[a-z]|[A-Z]|[ \-_]){1,40}$/", $this->value)) {
            return true;
        }
        $this->error = "Please enter a username containing only letters, numbers and special characters (- or _)";
        return false;
    }

    function password() {
        if (preg_match("/^(?=.*?[A-Z])(?=.*?[a-z])(?=.*?[0-9])(?=.*?[#?!@$ %^&*-]).{8,}$/", $this->value)) {
            //Hash password
            return true;
        }
        $this->error = "Password must contain one number, once special character, one capital and one lowercase letter";
        return false;
    }

}



?>