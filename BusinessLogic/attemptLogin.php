<?php
session_start();

$data = json_decode(file_get_contents('php://input'), true);
$_POST = $data;

$result;
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
            $_SESSION["user"] = getUserInformation($email);
            http_response_code(308);
            $result["general"] = "login.html";
            echo $result;
        }
        else{
            http_response_code(403);
            $result["error"] = ("Incorrect email/password");
            echo json_encode($result);
            die();
        }
    }
    catch(PDOException $e){
        http_response_code(500);
        $result["error"] = $e;
        echo json_encode($result);
    }
}
else{
    http_response_code(404);
    echo "Error 404: Page not found";
}
?>