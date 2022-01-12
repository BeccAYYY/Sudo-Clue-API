<?php

function create_guest($pdo) {
        do {
            $guest_name = "Guest" . rand(10000, 999999);
            $taken_name = check_if_username_exists($pdo, $guest_name);
        } while ($taken_name);
        create_user($pdo, $guest_name, "n/a", "guest", "r", 40, "default");
        $db = new database($pdo, "select", "users", [
            "columns" => ["id"],
            "where" => [
                "clause" => "username = :wusername",
                "params" => [
                    ":wusername" => $guest_name
                ]
            ]
        ]);
        $id = $db->row[0]["id"];
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
        $_SESSION["userID"] = $id;
        return $update->result;
    }

    function check_if_username_exists($pdo, $username) {
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
            return true;
        } 
        return false;
    }

    function create_user($pdo, $username, $password, $role, $methods, $minimumClues, $colour) {
        $values = [
            "username" => $username,
            "password" => $password,
            "role" => $role,
            "methods" => $methods,
            "minimumClues" => $minimumClues,
            "colour" => $colour
        ];
        $data_array = [
            "values" => $values
        ];
        $db = new database($pdo, "insert", "users", $data_array);
    }


    function get_user_role($pdo, $id) {
        $columns = [
            "role"
        ];
        $where = [
            "clause" => "`id` = :wid",
            "params" => [
                ":wid" => $id
            ]
        ];
        $data_array = [
            "where" => $where,
            "columns" => $columns
        ];
        $db = new database($pdo, "select", "users", $data_array);
        return $db->row[0]["role"];
    }

    function get_user_password($pdo, $username) {
        $columns = [
            "password"
        ];
        $where = [
            "clause" => "`username` = :wusername",
            "params" => [
                ":wusername" => $username
            ]
        ];
        $data_array = [
            "where" => $where,
            "columns" => $columns
        ];
        $db = new database($pdo, "select", "users", $data_array);
        if (count($db->row) == 1) {
            return $db->row[0]["password"];
        }
        return false;
    }

    function get_user_id($pdo, $username) {
        $columns = [
            "id"
        ];
        $where = [
            "clause" => "`username` = :wusername",
            "params" => [
                ":wusername" => $username
            ]
        ];
        $data_array = [
            "where" => $where,
            "columns" => $columns
        ];
        $db = new database($pdo, "select", "users", $data_array);
        if (count($db->row) == 1) {
            return $db->row[0]["id"];
        }
        return false;
    }

    ?>