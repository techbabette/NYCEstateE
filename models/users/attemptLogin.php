<?php
session_start();
require("../functions/generalFunctions.php");

$json_params = file_get_contents("php://input");

if (strlen($json_params) > 0 && isValidJSON($json_params)){
    $decoded_params = json_decode($json_params, true);
    $_POST = $decoded_params;
}

$result;

//This check would not exist in a stateless design 
//So it should not exist here (Groundwork for switching to JWT)
/* if(isset($_SESSION["user"])){
        echoNoPermission("You are already logged in");
    }
*/

if(!isset($_POST["pass"])
|| !isset($_POST["email"])
)
{
    echoImproperRequest("All fields are required");
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
    echoUnauthorized("Incorrect email/password");
}

require("../functions/userFunctions.php");
try{
    $loginAttempt = attemptLogin($email, $pass);
    if(!$loginAttempt){
        echoUnauthorized("Incorrect email/password");
    }
    if($loginAttempt < 1){
        $time = time();
        addLineToFile(abs($loginAttempt)."::".$time."\n", "failedLoginAttempts");
        $disableAccount = checkIfThreeFailedLoginAttempts(abs($loginAttempt), $time);
        if($disableAccount){
            disableAccount(abs($loginAttempt));
        }
        echoUnauthorized("Incorrect email/password");
    }
    $user = getUserInformation($loginAttempt);
    if(!$user["level"] > 0){
        echoNoPermission("User banned");
    }

    $time = time();

    addLineToFile($loginAttempt."::".$time."\n", "successfulLogins");

    http_response_code(200);
    $_SESSION["user"]["user_id"] = $loginAttempt;
    $result["general"] = "Successful login";
    echo json_encode($result);
}
catch(PDOException $e){
    echoUnexpectedError();
}
?>