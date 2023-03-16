<?php
session_start();
if(isset($_SESSION["user"])){
    session_unset();
    http_response_code(302);
}
else{
    http_response_code(404);
    echo ("Error 404: Page not found");
}
?>