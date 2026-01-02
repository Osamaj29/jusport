<?php
session_start();
require 'config.php';
$message = '';
if (!isset($_SESSION['reset_email'])) {
    header('Location: request_reset.php'); exit;
}
$email = $_SESSION['reset_email'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $entered = trim($_POST['otp']);
    $stmt = $conn->prepare("SELECT otp_hash, expires_at FROM otp_table WHERE email = ? ORDER BY created_at DESC LIMIT 1");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->bind_result($otp_hash, $expires_at);
    if ($stmt->fetch()) {
        if (strtotime($expires_at) < time()) {
            $message = "OTP expired.";
        } elseif (password_verify($entered, $otp_hash)) {
            $_SESSION['otp_verified'] = true;
            $conn->query("DELETE FROM otp_table WHERE email = '".$conn->real_escape_string($email)."'");
            header('Location: set_new_password.php'); exit;
        } else {
            $message = "Invalid OTP.";
        }
    } else {
        $message = "No OTP found.";
    }
    $stmt->close();
}
?>
<!doctype html><html><head><meta charset="utf-8"><title>Verify OTP</title></head><body>
<div style="max-width:420px;margin:40px auto">
<h2>Enter OTP</h2>
<?php if($message) echo "<div style='color:red'>$message</div>"; ?>
<form method="post">
<input name="otp" placeholder="6-digit code" required>
<button>Verify</button>
</form>
</div></body></html>
