<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require '../vendor/autoload.php';

function sendOTP($email, $otp) {
    $mail = new PHPMailer(true);

    try {
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'raidbgm@gmail.com';
        $mail->Password = 'fxjxzsgdwzoupbbs';
        $mail->SMTPSecure = 'tls';
        $mail->Port = 587;

        $mail->setFrom('raidbgm@gmail.com', 'Matrimonial Site');
        $mail->addAddress($email);
        $mail->isHTML(true);

        $mail->Subject = 'Your OTP Code';
        $mail->Body = "Your OTP is: <b>$otp</b>";

        $mail->send();
        return true;
    } catch (Exception $e) {
        return false;
    }
}
?>
