<?php
session_start();
require("../functions/generalFunctions.php");
require("../functions/userFunctions.php");
$activationLink = "";

$json_params = file_get_contents("php://input");

if (strlen($json_params) > 0 && isValidJSON($json_params)){
    $decoded_params = json_decode($json_params, true);
    $_POST = $decoded_params;
}

if(!isset($_POST["activationLink"])){
    echoImproperRequest();
}

$activationLink = $_POST["activationLink"];

try{
    $user = getUserFromActivationLink($activationLink);

    $email = $user["email"];
    $user_id = $user["user_id"];

    $success = activateUser($user_id);

    if($success){
        deleteActivationLink($activationLink);
    }

    $time = time();

    addLineToFile($user_id."::".$time."\n", "successfulLogins");

    http_response_code(200);
    $_SESSION["user"]["user_id"] = $user_id;
    $result["general"] = "Successfully activated account";
    echo json_encode($result);
}
catch(PDOException $e){
    echoUnexpectedError($e);
}

?>