<?php
if(isset($_POST["createNewUser"])){
    session_start();
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
    
    $errors = 0;
    $greska = "";
    if(!preg_match($reName, $name) || !preg_match($reName, $lastName))
    {
        $errors++;
        $greska .= "Username";
    }
    if(!preg_match($rePass1, $pass) || !preg_match($rePass2, $pass)
    || !preg_match($rePass3, $pass) || !preg_match($rePass4, $pass)
    || !preg_match($rePass5, $pass))
    {
        $errors++;
        $greska .= "Password";
    }
    if(!filter_var($email, FILTER_VALIDATE_EMAIL)){
        $errors++;
        $greska .= "Email";
    }
    if($errors != 0){
        http_response_code(422);
        echo $greska;
        die();
    }
    require("../DataAccess/functions.php");
    try{
        createNewUser($email, $pass, $name, $lastName);
        http_response_code(201);
        Header("Location: ../pages/login.html");
    }
    catch(PDOException $e){
        http_response_code(500);
        echo $e;
    }
}
else{
    http_response_code(404);
    echo "Error 404: Page not found";
}

?>