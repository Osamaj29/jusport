<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'vendor/autoload.php';

function sendOTPEmail($toEmail, $otp) {
    $mail = new PHPMailer(true);

    try {
        // Server settings
        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com'; // SMTP server
        $mail->SMTPAuth   = true;
        $mail->Username   = 'osama0557562398@gmail.com'; // Your Gmail
        $mail->Password   = 'rqgt bxil vfpi lfpb
';    // Gmail App Password
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port       = 587;

        // Recipients
        $mail->setFrom('your_email@gmail.com', 'Sports Booking System');
        $mail->addAddress($toEmail);

        // Content
        $mail->isHTML(true);
        $mail->Subject = 'Your OTP Code';
        $mail->Body    = "<p>Your One-Time Password (OTP) is: <strong>$otp</strong></p><p>This code is valid for 10 minutes.</p>";

        $mail->send();
        return true;
    } catch (Exception $e) {
        error_log("OTP email failed: {$mail->ErrorInfo}");
        return false;
    }
}
?>
