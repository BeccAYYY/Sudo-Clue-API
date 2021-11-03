<?php
    //Start the sessions and specify that the HTTP response body will be in JSON format.
    //ini_set("session.cookie_lifetime","3600");
    require("SessionHandler.php");
    $session_handler = new dbSessionHandler();
    require("database.php");
    $db = new database($session_handler->pdo);
    header('Content-Type: application/json; charset=utf-8');
    //The default response if none of the cases are triggered to change it:
    $response = [400, ["Message" => "Invalid Request"]];

    //function that takes a request and generates an array with two items: response code and response body.
    if (isset($_GET["action"])) {
        switch ($_GET["action"]) {
            case "login-check": 
                $response = $db->loginCheck();
        }
    }


    
    http_response_code($response[0]);
    echo json_encode($response[1]); 


    /* 
    Users
        Check if user exists
        Register new user
        Update user details
        Log in
        Check if user is logged in
        Get user details

    Solutions
        Get solution
        Insert new solution
        Delete solution

    Puzzles
        Get puzzle
        Add new puzzle
        Delete puzzle

    Logs
        Get all logs
        Get logs for user
        Get logs for difficulty
        Get user's best time
    */
    ?>