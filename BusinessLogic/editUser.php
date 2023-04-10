<?php
session_start();
$requiredLevel = 3;
require("../DataAccess/generalFunctions.php");
// If user not logged in, die
// if(!isset($_SESSION["user"])){
//     echoNoPermission();
// }
//If user's access level is too low, die
// if($_SESSION["user"]["level"] < $requiredLevel){
//     echoNoPermission();
// }

$json_params = file_get_contents("php://input");

if (strlen($json_params) > 0 && isValidJSON($json_params)){
    $decoded_params = json_decode($json_params, true);
    $_POST = $decoded_params;
}

$result;

if
(
   (!isset($_POST["userId"]))
|| (!isset($_POST["email"]) || empty($_POST["email"])) 
|| (!isset($_POST["name"]) || empty($_POST["name"]))
|| (!isset($_POST["lastName"]) || empty($_POST["lastName"]))
|| (!isset($_POST["roleId"]))
|| (!isset($_POST["password"]))
)
{
    $result["error"] = "All fields are required";
    $result["data"] = var_dump($_POST);
    http_response_code(422);
    echo json_encode($result);
    die();
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
    http_response_code(422);
    $result["error"] = "Name/last name does not fit criteria";
    echo json_encode($result);
    die();
}
//If new password provided, check if valid
if(!empty($password)){
    if(!preg_match($rePass1, $password) || !preg_match($rePass2, $password)
    || !preg_match($rePass3, $password) || !preg_match($rePass4, $password)
    || !preg_match($rePass5, $password))
    {
        http_response_code(422);
        $result["error"] = "Password does not fit criteria";
        echo json_encode($result);
        die();
    }
}

if(!preg_match($reEmail, $email)){
    http_response_code(422);
    $result["error"] = "Invalid email";
    echo json_encode($result);
    die();
}

//Check if provided role exists
$roleExists = count(getEveryRowWhereParamFromTable("roles", "role_id", $roleId)) > 0;
if(!$roleExists){
    http_response_code(422);
    $result["error"] = "Provided role id does not exist";
    echo json_encode($result);
    die();
}

//Check if provided user exists
$userExists = count(getEveryRowWhereParamFromTable("users", "user_id", $userId)) > 0;
if(!$userExists){
    http_response_code(422);
    $result["error"] = "Provided user id does not exist";
    echo json_encode($result);
    die();
}

//Success
require("../DataAccess/userFunctions.php");
try{
    editUser($userId, $email, $name, $lastName, $roleId);
    if(!empty($password)){
        editUserPassword($userId, $password);
    }
    $result["general"] = "Success";
    http_response_code(201);
    echo json_encode($result);
}
catch(PDOException $e){
    http_response_code(500);
    // $result["error"] = "Unexpected error occured";
    $result["error"] = $e;
    echo json_encode($result);
}
?>