<?php
session_start();
if(isset($_POST["attemptLogin"])){

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
        http_response_code(403);
        echo json_encode("Incorrect email/password");
        die();
    }

    require("../DataAccess/userFunctions.php");
    try{
        $loginAttempt = attemptLogin($email, md5($pass));
        if($loginAttempt){
            http_response_code(200);
            //Add session
            echo json_encode("index.html");
            die();
        }
        else{
            http_response_code(403);
            echo json_encode("Incorrect email/password");
            die();
        }
    }
    catch(PDOException $e){
        http_response_code(500);
    }
}
else{
    http_response_code(404);
    echo "Error 404: Page not found";
}
?>