<?php
    require("SessionHandler.php");
    $session_handler = new dbSessionHandler();
    $pdo = $session_handler->pdo;
    require("database.php");
    require("validation.php");
    require("actions.php");
    header('Content-Type: application/json');
    header('Access-Control-Allow-Origin: http://127.0.0.1:5500');
    header('Access-Control-Allow-Credentials: true');



    if (isset($_GET["action"])) {
        $validation = new validation($pdo, "logs", "action", $_GET["action"]);
        if ($validation->result) {
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