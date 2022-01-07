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


    if (isset($_SESSION["LASTACCESS"]) && round(microtime(true) * 1000) - $_SESSION["LASTACCESS"] < 1000) {
        http_response_code(429);
        $_SESSION["LASTACCESS"] = round(microtime(true) * 1000);
        die();
    }

    $_SESSION["LASTACCESS"] = round(microtime(true) * 1000);


// Every request from the front end has a URL parameter with a key of "action"
    if (isset($_GET["action"])) {
        //The validation checks that the action is one from the set list
        $validation = new validation($pdo, "logs", "action", $_GET["action"]);
        if ($validation->result) {
            //Uses the value of "action" as a string in a callback function from actions.php (functions are all names the same as their "action" parameter)
            $response = $_GET["action"]($pdo);
        } else {
            $response = [400, ["Message" => $validation->error]];
        }

    } else {
        $response = [400, ["Message" => "No action set."]];
    }

    http_response_code($response[0]);
    echo json_encode($response[1]); 


    
?>