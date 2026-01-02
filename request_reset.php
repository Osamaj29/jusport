<?php
session_start();
require 'config.php';
require 'otp_helper.php';
require 'mail_helper.php';

$message = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email']);

    $stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows == 0) {
        $message = "No user found with that email.";
    } else {
        $otp = generateOTP(6);
        $otp_hash = password_hash($otp, PASSWORD_DEFAULT);
        $expires = date("Y-m-d H:i:s", strtotime('+15 minutes'));

        $conn->query("DELETE FROM otp_table WHERE email = '".$conn->real_escape_string($email)."'");
        $ins = $conn->prepare("INSERT INTO otp_table (email, otp_hash, expires_at) VALUES (?, ?, ?)");
        $ins->bind_param("sss", $email, $otp_hash, $expires);
        $ins->execute();

        if (sendOTPEmail($email, $otp)) {
            $_SESSION['reset_email'] = $email;
            $message = "OTP sent to your email.";
        } else {
            $message = "Failed to send email.";
        }
    }
    $stmt->close();
}
?>
<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>Request Password Reset</title>
<style>
.container{max-width:420px;margin:40px auto;padding:20px;background:#fff;border-radius:8px;box-shadow:0 6px 18px rgba(0,0,0,.08);}
input{width:100%;padding:10px;margin:8px 0;border:1px solid #ccc;border-radius:5px}
button{width:100%;padding:10px;background:#002b5b;color:#fff;border:none;border-radius:5px}
.msg{padding:8px;margin-bottom:8px}
</style>
</head>
<body>
<div class="container">
<h2>Forgot Password</h2>
<?php if($message) echo "<div class='msg'>$message</div>"; ?>
<form method="post">
<label>Registered Email</label>
<input type="email" name="email" required />
<button type="submit">Send OTP</button>
</form>
</div>
</body>
</html>
