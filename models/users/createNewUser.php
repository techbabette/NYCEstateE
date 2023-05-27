<?php
require("../functions/generalFunctions.php");

session_start();
$result;

$json_params = file_get_contents("php://input");

if (strlen($json_params) > 0 && isValidJSON($json_params)){
    $decoded_params = json_decode($json_params, true);
    $_POST = $decoded_params;
}

if(isset($_SESSION["user"])){
    echoNoPermission("You are already logged in");
}

if(
   !isset($_POST["name"])
|| !isset($_POST["lastName"])
|| !isset($_POST["password"])
|| !isset($_POST["email"])
)
{
    echoImproperRequest("All fields are required");
}
$name = $_POST["name"];
$lastName = $_POST["lastName"];
$pass = $_POST["password"];
$email = $_POST["email"];

$reName = '/^[A-Z][a-z]{1,14}(\s[A-Z][a-z]{1,14}){0,2}$/';

$rePass1 = '/[A-Z]/'; 
$rePass2 = '/[a-z]/'; 
$rePass3 = '/[0-9]/'; 
$rePass4 = '/[!\?\.]/'; 
$rePass5 = '/^[A-Za-z0-9!\?\.]{7,30}$/';

$reEmail = '/^\w+([\.-]?\w+)*@\w+([\.-]?\w+)*(\.\w{2,3})+$/';

if(!preg_match($reName, $name) || !preg_match($reName, $lastName))
{
    echoUnprocessableEntity("Name/last name does not fit criteria");
}

if(!preg_match($rePass1, $pass) || !preg_match($rePass2, $pass)
|| !preg_match($rePass3, $pass) || !preg_match($rePass4, $pass)
|| !preg_match($rePass5, $pass))
{
    echoUnprocessableEntity("Password does not match format");
}
if(!preg_match($reEmail, $email)){
    echoUnprocessableEntity("Invalid email");
}

require("../functions/userFunctions.php");
require("../functions/emailFunctions.php");
try{
    $emailInUse = checkIfEmailInUse($email);
    if($emailInUse){
        http_response_code(409);
        $result["error"] = "Email already in use";
        echo json_encode($result);
        die();
    }
    else{
        $newUser = createNewUser($email, $pass, $name, $lastName);
        $newLink = createActivationLink($newUser);
        sendActivationLink($email, $newLink);
        http_response_code(201);
        $result["general"] = "We sent you an activation email to $email";
        echo json_encode($result);
        die();
    }
}
catch(PDOException $e){
    echoUnexpectedError($e);
}
?>