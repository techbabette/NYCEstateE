<?php
require("../DataAccess/generalFunctions.php");

session_start();
$result;

$json_params = file_get_contents("php://input");

if (strlen($json_params) > 0 && isValidJSON($json_params)){
    $decoded_params = json_decode($json_params, true);
    $_POST = $decoded_params;
}

if(isset($_SESSION["user"])){
    http_response_code(403);
    $result["error"] = "You are already logged in";
    echo $result;
}
if(isset($_POST["createNewUser"])){
    $errors = 0;
    $greska = "";
    if(
    (!isset($_POST["name"]) || empty($_POST["name"])) 
    || (!isset($_POST["lastName"]) || empty($_POST["lastName"]))
    || (!isset($_POST["password"]) || empty($_POST["password"]))
    || (!isset($_POST["email"]) || empty($_POST["email"]))){
        $errors++;
        $result["error"] = "All fields are required";
        http_response_code(422);
        echo json_encode($result);
        die();
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
        $errors++;
        $greska .= "Name/last name does not fit criteria";
    }
    if(!preg_match($rePass1, $pass) || !preg_match($rePass2, $pass)
    || !preg_match($rePass3, $pass) || !preg_match($rePass4, $pass)
    || !preg_match($rePass5, $pass))
    {
        $errors++;
        $greska .= "Password does not fit criteria";
    }
    if(!preg_match($reEmail, $email)){
        $errors++;
        $greska .= "Email";
    }
    if($errors != 0){
        echoUnprocessableEntity($greska);
    }
    require("../DataAccess/userFunctions.php");
    try{
        $emailInUse = checkIfEmailInUse($email);
        if($emailInUse){
            echoUnprocessableEntity("Email already in use");
        }
        else{
            createNewUser($email, $pass, $name, $lastName);
            http_response_code(302);
            $result["general"] = "Successful registration";
            echo json_encode($result);
        }
    }
    catch(PDOException $e){
        echoUnexpectedError();
    }
}
else{
    http_response_code(404);
    echo json_encode($result);
}

?>