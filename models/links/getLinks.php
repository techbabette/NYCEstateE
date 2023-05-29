<?php
include("../functions/linkFunctions.php");
include("../functions/userFunctions.php");
require("../functions/generalFunctions.php");

$json_params = file_get_contents("php://input");

if (strlen($json_params) > 0 && isValidJSON($json_params)){
    $decoded_params = json_decode($json_params, true);
    $_POST = $decoded_params;
}

//Initialize access level as one
$accessLevel = 1;
$loggedIn = false;
$result;
$currentPage = $_POST["currentPage"];
$allowed = false;
session_start();
//If user is logged in, get their access level and set logged in to true
if(isset($_SESSION["user"])){
    $_SESSION["user"] = getUserInformation($_SESSION["user"]["user_id"]);
    $accessLevel = $_SESSION["user"]["level"];
    if($accessLevel != 1){
        $loggedIn = true;
    }
}
try{
    $result["general"]["links"] = getLinks($accessLevel, $loggedIn);
    $result["general"]["accessLevel"] = $accessLevel;
    //For every link found, check if it matches current page
    foreach($result["general"]["links"] as $link){
        if($link["href"] == $currentPage){
            $allowed = true;
            break;
        }
    }
    //If page is in list of pages available to user
    if($allowed){
        //Get data for new string
        $currDate = time();
        $user = $loggedIn ? $_SESSION["user"]["user_id"] : "/";
        $page = $currentPage;
        $email = $_SESSION["user"]["email"];
        $role = $_SESSION["user"]["role_name"];

        //Form new string
        $arrayOfData = array($user, $page, $currDate, $email, $role);
        $newLine = implode("::", $arrayOfData)."\n";

        //Add new string to activity log
        addLineToFile($newLine, "activity");

        //Return all links available to user
        http_response_code(200);
        echo json_encode($result);
        die();
    }
    //If page is not available to user, echo 401 or 403
    else{
        if($loggedIn){
            //Echo 403
            echoNoPermission("You are not permitted to view this page");
        }
        else{
            //Echo 401
            echoUnauthorized();
        }
    }
}
catch (PDOException $e){
    echoUnexpectedError();
}

?>