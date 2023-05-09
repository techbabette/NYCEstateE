<?php
session_start();
$requiredLevel = 3;
require("../DataAccess/generalFunctions.php");
checkAccessLevel($requiredLevel);

$json_params = file_get_contents("php://input");

if (strlen($json_params) > 0 && isValidJSON($json_params)){
    $decoded_params = json_decode($json_params, true);
    $_POST = $decoded_params;
}

$result;

if
(
   (!isset($_POST["userId"]))
|| (!isset($_POST["email"])) 
|| (!isset($_POST["name"]))
|| (!isset($_POST["lastName"]))
|| (!isset($_POST["roleId"]))
|| (!isset($_POST["password"]))
)
{
    echoImproperRequest("All fields are required");
}

$userId = $_POST["userId"];
$email = $_POST["email"];
$name = $_POST["name"];
$lastName = $_POST["lastName"];
$roleId = $_POST["roleId"];
$password = $_POST["password"];

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
//If new password provided, check if valid
if(!empty($password)){
    if(!preg_match($rePass1, $password) || !preg_match($rePass2, $password)
    || !preg_match($rePass3, $password) || !preg_match($rePass4, $password)
    || !preg_match($rePass5, $password))
    {
        echoUnprocessableEntity("Password does not fit criteria");
    }
}

if(!preg_match($reEmail, $email)){
    echoUnprocessableEntity("Invalid email");
}

//Check if provided role exists
$roleExists = count(getEveryRowWhereParamFromTable("roles", "role_id", $roleId)) > 0;
if(!$roleExists){
    echoUnprocessableEntity("Invalid role provided");
}

//Check if provided user exists
$userExists = count(getEveryRowWhereParamFromTable("users", "user_id", $userId)) > 0;
if(!$userExists){
    echoUnprocessableEntity("Provided user does not exist");
}

//Success
require("../DataAccess/userFunctions.php");
try{
    editUser($userId, $email, $name, $lastName, $roleId);
    if(!empty($password)){
        editUserPassword($userId, $password);
    }
}
catch(PDOException $e){
    echoUnexpectedError();
}

$result["general"] = "Successfully edited user";
http_response_code(200);
echo json_encode($result);
?>