<?php

require("functions.php");
/* $values = [
        "username" => "WinnerGuy",
        "password" => "abc123",
        "role" => "user",
        "dateCreated" => date("Y-m-d H:i:s", 1636156378)
    ];
    $columns = "*";
    $where = [
        "clause" => "`id` = :wid", 
        "params" => [
            "wid" => 1
            ]
        ];
    $data_array = [
        "values" => $values
    ];
    $db = new database(
        $session_handler->pdo, 
        "insert", 
        "users", 
        $data_array
    );
    var_dump($db->row_count);*/

    /*$test = new validation($session_handler->pdo, "inprogress", "id", "2aaaaa");
    echo $test->error;*/

    function login_check($pdo) {
        $id = (string) session_id();
        echo "session id = " . (string) session_id();
        $data_array = [
            "columns" => ["loggedUser"],
            "where" => [
                "clause" => "`id` = :wid",
                "params" => [
                    ":wid" => $id
                ]
            ]
        ];
        $db = new database($pdo, "select", "sessions", $data_array);
        if (isset($db->row[0]) and is_null($db->row[0]["loggedUser"])) {
            create_guest($pdo);
            return [401, ["Message" => "You are not currently logged in."]];
        } elseif (isset($db->row[0]) and get_user_role($pdo, $db->row[0]["loggedUser"]) == "guest") {
            $_SESSION["userID"] = $db->row[0]["loggedUser"];
            return [401, ["Message" => "You are not currently logged in."]];
        } elseif (isset($db->row[0]) and get_user_role($pdo, $db->row[0]["loggedUser"]) == "user") {
            $_SESSION["userID"] = $db->row[0]["loggedUser"];
            return [200, ["Message" => "You are logged in."]];
        }
        return [500, ["Message" => "Server error."]];
    }

    function username_exists($pdo) {
        if (!isset($_GET["username"])) {
            return [400, ["Message" => "No username sent."]];
        }
        $username = $_GET["username"];
        $validation = new validation($pdo, "users", "username", $username);
        if (!$validation->result) {
            return [400, ["Message" => $validation->error]];
        }
        $data_array = [
            "where" => [
                "clause" => "username = :wusername",
                "params" => [
                    ":wusername" => $username
                ]
            ]
        ];
        $db = new database($pdo, "select", "users", $data_array);
        if (count($db->row)) {
            return [200, ["Message" => "Username already exists."]];
        } 
        return [404, ["Message" => "Username does not exist."]];
    }

    function get_user_details($pdo) {
        $data_array = [
            "columns" => [
                "username",
                "dateCreated",
                "methods",
                "minimumClues",
                "colour"
            ],
            "where" => [
                "clause" => "id = :wid",
                "params" => [
                    ":wid" => $_SESSION["userID"]
                ]
            ]
        ];
        $user = new database($pdo, "select", "users", $data_array);

        $data_array = [
            "columns" => [
                "COUNT(*) as totalGames"
            ],
            "where" => [
                "clause" => "userID = :wid",
                "params" => [
                    ":wid" => $_SESSION["userID"]
                ]
            ]
        ];
        $total_games = new database($pdo, "select", "puzzlehistory", $data_array);

        $data_array = [
            "columns" => [
                "COUNT(*) AS completedGames",
                "AVG(time) AS averageTime",
                "MIN(time) AS bestTime"
            ],
            "where" => [
                "clause" => "userID = :wid AND status = 'completed'",
                "params" => [
                    ":wid" => $_SESSION["userID"]
                ]
            ]
        ];
        $completed_games = new database($pdo, "select", "puzzlehistory", $data_array);

        $data = array_merge($user->row[0], $total_games->row[0], $completed_games->row[0]);

        return [200, $data];
    }

    function register($pdo) {
        if (!isset($_POST["username"]) or !isset($_POST["password"]) or !isset($_POST["password2"])) {
            return [400, ["Message" => "Please fill all fields."]];
        }
        $validation = new validation($pdo, "users", "username", $_POST["username"]);
        if (!$validation->result) {
            return [400, ["Message" => $validation->error]];
        }
        if (get_user_role($pdo, $_SESSION["userID"]) !== "guest") {
            return [403, ["Message" => "You are already logged in."]];
        }
        
        $username_exists = check_if_username_exists($pdo, $_POST["username"]);
        if ($username_exists === true) {
            return [403, ["Message" => "Username already exists."]];
        } elseif ($username_exists) {
            return [400, ["Message" => $username_exists]];
        }
        $pass_val = new validation($pdo, "users", "password", $_POST["password"]);
        if (!$pass_val->result) {
            return [400, ["Message" => $pass_val->error]];
        }
        if ($_POST["password"] !== $_POST["password2"]) {
            return [400, ["Message" => "Passwords do not match."]];
        }
        $data_array = [
            "values" => [
                "username" => $_POST["username"],
                "password" => password_hash($_POST["password"], PASSWORD_DEFAULT),
                "role" => "user"
            ],
            "where" => [
                "clause" => "id = :wid",
                "params" => [
                    ":wid" => $_SESSION["userID"]
                ]
            ]
        ];
        $update = new database($pdo, "update", "users", $data_array);
        if ($update->result) {
            return [200, ["Message" => "User created"]];
        }
        return [500, ["Message" => "Server issue. Please try again later."]];
    }

    function login($pdo) {
        if (!isset($_POST["username"]) or !isset($_POST["password"])) {
            return [400, ["Message" => "Please fill all fields."]];
        }
        $un_val = new validation($pdo, "users", "username", $_POST["username"]);
        if (!$un_val->result) {
            return [400, ["Message" => $un_val->error]];
        }
        $pw_val = new validation($pdo, "users", "password", $_POST["password"]);
        if (!$pw_val->result) {
            return [400, ["Message" => $pw_val->error]];
        }
        if (!check_if_username_exists($pdo, $_POST["username"])) {
            return [400, ["Message" => "Invalid details."]];
        }
        $db_password = get_user_password($pdo, $_POST["username"]);
        if (!$db_password) {
            return [500, ["Message" => "Server error."]];
        }
        if (!password_verify($_POST["password"], $db_password)) {
            return [400, ["Message" => "Invalid details."]];
        }
        $id = get_user_id($pdo, $_POST["username"]);
        if (!$id) {
            return [500, ["Message" => "Server error."]];
        }
        
        $update = new database($pdo, "update", "sessions", [
            "values" => [
                "loggedUser" => $id
            ],
            "where" => [
                "clause" => "id = :wid",
                "params" => [
                    ":wid" => (string) session_id()
                ]
            ]
        ]);
        if (!$update->result) {
            return [500, ["Message" => "Server error."]];
        }
        $_SESSION["userID"] = $id;
        return [200, ["Message" => "Successfully logged in."]];
    }

    function logout($pdo) {
        if (create_guest($pdo)) {
            return [200, ["Message" => "Successfully logged out."]];
        }
        return [500, ["Message" => "Server error."]];
    }

    function update_user_details() {
        
    }

    function new_puzzle() {

    }

    function update_puzzle_progress() {

    }

    function complete_puzzle() {

    }

    function get_leaderboard() {

    }

    function delete_account() {

    }

    


?>