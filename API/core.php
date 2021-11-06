<?php
    //Start the sessions and specify that the HTTP response body will be in JSON format.
    require("SessionHandler.php");
    $session_handler = new dbSessionHandler();
    $pdo = $session_handler->pdo;
    require("database.php");
    require("validation.php");
    require("actions.php");
    header('Content-Type: application/json; charset=utf-8');
    //The default response if none of the cases are triggered to change it:
    $response = [400, ["Message" => "Invalid Request"]];

    //function that takes a request and generates an array with two items: response code and response body.
    if (isset($_GET["action"])) {
        switch ($_GET["action"]) {
            case "login_check": 
                $response = login_check($pdo);
                break;
            case "username_exists":
                $response = username_exists($pdo);
                break;
            case "get_user_details":
                //stuff
                break;
            case "register":
                //stuff
                break;
            case "login":
                //stuff
                break;
            case "update_user_details":
                //stuff
                break;
            case "new_puzzle":
                //stuff
                break;
            case "update_puzzle_progress":
                //stuff
                break;
            case "complete_puzzle":
                //stuff
                break;    
            case "get_leaderboard":
                //stuff
                break;
            case "delete_account":
                //stuff
                break;
        }
    }

    
    
    http_response_code($response[0]);
    echo json_encode($response[1]); 


    
    ?>