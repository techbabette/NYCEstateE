<?php
session_start();
require("../DataAccess/generalFunctions.php");

$json_params = file_get_contents("php://input");

if (strlen($json_params) > 0 && isValidJSON($json_params)){
    $decoded_params = json_decode($json_params, true);
    $_POST = $decoded_params;
}

$result;

if(isset($_SESSION["user"])){
    echoNoPermission("You are already logged in");
}

if(isset($_POST["attemptLogin"])){

    if(!isset($_POST["pass"])
    || !isset($_POST["email"])
    )
    {
        echoUnprocessableEntity("All fields are required");
    }

    $pass = $_POST["pass"];
    $email = $_POST["email"];

    $rePass1 = '/[A-Z]/'; 
    $rePass2 = '/[a-z]/'; 
    $rePass3 = '/[0-9]/'; 
    $rePass4 = '/[!\?\.]/'; 
    $rePass5 = '/^[A-Za-z0-9!\?\.]{7,30}$/';

    $errors = 0;

    if(!preg_match($rePass1, $pass) || !preg_match($rePass2, $pass)
    || !preg_match($rePass3, $pass) || !preg_match($rePass4, $pass)
    || !preg_match($rePass5, $pass))
    {
        $errors++;
    }

    if(!filter_var($email, FILTER_VALIDATE_EMAIL)){
        $errors++;
    }

    if($errors != 0){
        http_response_code(401);
        $result["error"] = "Incorrect email/password";
        echo json_encode($result);
        die();
    }

    require("../DataAccess/userFunctions.php");
    try{
        $loginAttempt = attemptLogin($email, $pass);
        if($loginAttempt){
            $_SESSION["user"] = getUserInformation($loginAttempt);
            http_response_code(302);
            $result["general"] = "Successful login";
            echo json_encode($result);
        }
        else{
            http_response_code(401);
            $result["loginAttempt"] = $loginAttempt;
            $result["error"] = ("Incorrect email/password");
            echo json_encode($result);
            die();
        }
    }
    catch(PDOException $e){
        echoUnexpectedError();
    }
}
else{
    http_response_code(404);
    echo "Error 404: Page not found";
}
?>