<?php
session_start();
require 'config.php';
if (!isset($_SESSION['reset_email']) || !isset($_SESSION['otp_verified'])) {
    header('Location: request_reset.php'); exit;
}
$email = $_SESSION['reset_email'];
$msg = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $pw = $_POST['password'];
    $pw2 = $_POST['password_confirm'];
    if ($pw !== $pw2) $msg = "Passwords do not match.";
    elseif (strlen($pw) < 6) $msg = "Use at least 6 chars.";
    else {
        $hash = password_hash($pw, PASSWORD_DEFAULT);
        $upd = $conn->prepare("UPDATE users SET password = ? WHERE email = ?");
        $upd->bind_param("ss", $hash, $email);
        $upd->execute();
        $conn->query("DELETE FROM otp_table WHERE email = '".$conn->real_escape_string($email)."'");
        unset($_SESSION['reset_email'], $_SESSION['otp_verified']);
        $msg = "Password reset complete. <a href='login.php'>Login</a>";
    }
}
?>
<!doctype html><html><head><meta charset="utf-8"><title>Reset Password</title></head><body>
<div style="max-width:420px;margin:40px auto">
<h2>Reset Password</h2>
<?php if($msg) echo "<div>$msg</div>"; ?>
<form method="post">
<input type="password" name="password" placeholder="New password" required><br>
<input type="password" name="password_confirm" placeholder="Confirm password" required><br>
<button>Reset Password</button>
</form>
</div></body></html>
