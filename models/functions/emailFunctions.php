<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

require_once "../../vendor/autoload.php";
function createNewBlankEmail(){
    require("../../../emailSetup.php");
    $mail = new PHPMailer(true);
    try{
        $mail->SMTPDebug = SMTP::DEBUG_OFF; //SMTP::DEBUG_SERVER; //Enable verbose debug output
        $mail->SMTPOptions = array('ssl' => array('verify_peer' => false, 'verify_peer_name' => false,
        'allow_self_signed' => true));
        $mail->CharSet = 'utf-8';
        $mail->isSMTP(); //Send using SMTP
        $mail->Host = 'smtp.gmail.com'; //'smtp.example.com'; //Set the SMTP server to send through
        $mail->Port = 465; //Google port
        $mail->SMTPSecure = "ssl";
        $mail->SMTPAuth = true; //Enable SMTP authentication
        $mail->Username = $username; //SMTP username
        $mail->Password = $password; //SMTP password
        $mail->setFrom('NYCEstateTESTMAIL@gmail.com', 'NYCEstate');
        $mail->isHTML(true);
    }
    catch (Exception $e) {
        return "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
    }

    return $mail;

}
function sendActivationLink($email, $link, $type = "Register"){
    require("../../../emailSetup.php");

    try{
        $mail = createNewBlankEmail();

        $subject = "";
        $body = "";

        $mail->addAddress($email, "New user");

        if($type == "Register"){
            $subject = "Your NYCEstate activation link";
            $body = "<h2>Thank you for registering</h2><h3>Here's your activation link</h3>";
        }

        if($type == "Reactivate"){
            $subject = "Your NYCEstate reactivation link";
            $body = "<h2>Your account has been disabled</h2><h3>Reactivate it by clicking the link below</h3>";
        }
    
        $mail->Subject = $subject;
    
        $href = $linkPrefix."login.html&activation=$link";
    
        $mail->Body = $body."<a href=\"$href\">Activate your account</a>";
    
        $mail->send();
    }
    catch (Exception $e){
        return 0;
    }

    $mail->smtpClose();
    return 1;
}
?>