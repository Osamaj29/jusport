<?php
$to = "osama0557562398@gmail.com";
$subject = "Test Mail from XAMPP";
$body = "This is a test mail from XAMPP using Gmail SMTP.";
$headers = "From:osama0557562398@gmail.com";

if (mail($to, $subject, $body, $headers)) {
    echo "Mail sent successfully!";
} else {
    echo "Mail failed.";
}
?>
