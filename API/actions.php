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
        $id = session_id();
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
        } elseif (get_user_role($pdo, $db->row[0]["loggedUser"]) == "guest") {
            $_SESSION["userID"] = $db->row[0]["loggedUser"];
            return [401, ["Message" => "You are not currently logged in."]];
        } elseif (get_user_role($pdo, $db->row[0]["loggedUser"]) == "user") {
            $_SESSION["userID"] = $db->row[0]["loggedUser"];
            return [200, ["Message" => "You are logged in."]];
        }
        return [500, ["Message" => "Server Error"]];
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

        //games completed
        //average time
        return [200, ["Message" => "Test", "User" => $user->row[0]]];
    }

    function register() {

    }

    function login() {

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