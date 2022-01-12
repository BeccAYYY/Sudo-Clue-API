<?php
    ini_set("allow_url_fopen", true);
    require("SessionHandler.php");
    $session_handler = new dbSessionHandler();
    $pdo = $session_handler->pdo;
    require("database.php");
    require("validation.php");
    require("actions.php");
    header('Access-Control-Allow-Origin: http://127.0.0.1:5500');
    header('Access-Control-Allow-Credentials: true');
    header("Content-Type: application/json");    
    header("Access-Control-Allow-Methods: GET, POST, DELETE, OPTIONS");    
    header("Access-Control-Max-Age: 3600");    
    header("Access-Control-Allow-Headers: Content-Type, origin, Accept");


    if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {   
        http_response_code(200);
        die();
    }
    
    $_POST = json_decode(file_get_contents('php://input'), true);

//Checks if the difference between the last access and current time is less than 1000 milliseconds, then sends back a 429 and stops executing if it is.
    /*if (isset($_SESSION["LASTACCESS"]) && round(microtime(true) * 1000) - $_SESSION["LASTACCESS"] < 1000) {
        http_response_code(429);
        $_SESSION["LASTACCESS"] = round(microtime(true) * 1000);
        die();
    }*/

    $_SESSION["LASTACCESS"] = round(microtime(true) * 1000);

    $access_count = new database($pdo, "select", "logs", [
        "columns" => ["COUNT(*)"],
        "where" => [
            "clause" => "`sessionID` = :wsid AND `timestamp` > :wt",
            "params" => [
                ":wsid" => (string) session_id(),
                ":wt" => date('Y-m-d H:i:s',time() - 86400)
            ]
        ]
    ]);
    if ($access_count->row[0]['COUNT(*)'] > 1000) {
        http_response_code(429);
        die();
    }

// Every request from the front end has a URL parameter with a key of "action"
    if (isset($_GET["action"])) {
        if ($_GET["action"] != "login_check" and !isset($_SESSION["userID"])) {
            login_check($pdo);
        }
        //The validation checks that the action is one from the set list
        $validation = new validation($pdo, "logs", "action", $_GET["action"]);
        if ($validation->result) {
            //Uses the value of "action" as a string in a callback function from actions.php (functions are all names the same as their "action" parameter)
            $response = $_GET["action"]($pdo);
            $log = new database($pdo, "insert", "logs", ["values" => [
                "sessionID" => (string) session_id(),
                "userID" => $_SESSION["userID"],
                "action" => $_GET["action"],
                "timestamp" => date('Y-m-d H:i:s',time()),
                "ip" => $_SERVER['REMOTE_ADDR'],
                "responseCode" => $response[0]
            ]]);
        } else {
            $response = [400, ["Message" => $validation->error]];
            $log = new database($pdo, "insert", "logs", ["values" => [
                "sessionID" => (string) session_id(),
                "userID" => $_SESSION["userID"],
                "action" => "Invalid",
                "timestamp" => date('Y-m-d H:i:s',time()),
                "ip" => $_SERVER['REMOTE_ADDR'],
                "responseCode" => $response[0]
            ]]);
        }
    } else {
        if (!isset($_SESSION["userID"])) {
            login_check($pdo);
        }
        $response = [400, ["Message" => "No action set."]];
        $log = new database($pdo, "insert", "logs", ["values" => [
            "sessionID" => (string) session_id(),
            "userID" => $_SESSION["userID"],
            "action" => "No action set",
            "timestamp" => date('Y-m-d H:i:s',time()),
            "ip" => $_SERVER['REMOTE_ADDR'],
            "responseCode" => $response[0]
        ]]);
    }

    http_response_code($response[0]);
    echo json_encode($response[1]); 


    
?>