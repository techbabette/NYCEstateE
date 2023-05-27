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
function sendActivationLink($email, $link){
    require("../../../emailSetup.php");

    try{
        $mail = createNewBlankEmail();

        $mail->addAddress($email, "New user");
    
        $mail->Subject = "Your NYCEstate activation link";
    
        $href = $linkPrefix."login.html&activation=$link";
    
        $mail->Body = "<a href=\"$href\">Activate your account</a>";
    
        $mail->send();
    }
    catch (Exception $e){
        return 0;
    }

    $mail->smtpClose();
    return 1;
}
?>