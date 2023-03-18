<?php
session_start();
$result;

$data = json_decode(file_get_contents('php://input'), true);
$_POST = $data;

if(isset($_SESSION["user"])){
    http_response_code(403);
    $result["error"] = "You are already logged in";
    echo $result;
}
if(isset($data["createNewUser"])){
    $errors = 0;
    $greska = "";
    if(
    (!isset($_POST["name"]) || empty($_POST["name"])) 
    || (!isset($_POST["lastName"]) || empty($_POST["lastName"]))
    || (!isset($_POST["pass"]) || empty($_POST["pass"]))
    || (!isset($_POST["email"]) || empty($_POST["email"]))){
        $errors++;
        $result["error"] = "All fields are required";
        $result["sentData"] = $_POST;
        http_response_code(422);
        echo json_encode($result);
        die();
    }
    $name = $_POST["name"];
    $lastName = $_POST["lastName"];
    $pass = $_POST["pass"];
    $email = $_POST["email"];

    $reName = '/^[A-Z][a-z]{1,14}(\s[A-Z][a-z]{1,14}){0,2}$/';

    $rePass1 = '/[A-Z]/'; 
    $rePass2 = '/[a-z]/'; 
    $rePass3 = '/[0-9]/'; 
    $rePass4 = '/[!\?\.]/'; 
    $rePass5 = '/^[A-Za-z0-9!\?\.]{7,30}$/';
    
    if(!preg_match($reName, $name) || !preg_match($reName, $lastName))
    {
        $errors++;
        $greska .= "Name/last name do not fit criteria";
    }
    if(!preg_match($rePass1, $pass) || !preg_match($rePass2, $pass)
    || !preg_match($rePass3, $pass) || !preg_match($rePass4, $pass)
    || !preg_match($rePass5, $pass))
    {
        $errors++;
        $greska .= "Password does not fit criteria";
    }
    if(!filter_var($email, FILTER_VALIDATE_EMAIL)){
        $errors++;
        $greska .= "Email";
    }
    if($errors != 0){
        http_response_code(422);
        $result["error"] = $greska;
        echo json_encode($result);
        die();
    }
    require("../DataAccess/userFunctions.php");
    try{
        $emailInUse = checkIfEmailInUse($email);
        if($emailInUse){
            http_response_code(422);
            $greska .= "Email already in use";
            $result["error"] = $greska;
            echo json_encode($result);
            die();
        }
        else{
            createNewUser($email, $pass, $name, $lastName);
            http_response_code(302);
            $result["general"] = "login.html";
            echo json_encode($result);
        }
    }
    catch(PDOException $e){
        http_response_code(500);
        echo json_encode($e);
    }
}
else{
    http_response_code(404);
    $result["error"] = var_dump($data);
    echo json_encode($result);
}

?>