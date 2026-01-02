<?php
session_start();
$conn = new mysqli("localhost", "root", "", "sport_booking");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$message = "";
$showForm = false;

if (isset($_GET['token'])) {
    $token = $_GET['token'];

    // Check token validity
    $stmt = $conn->prepare("SELECT email, expires_at FROM password_resets WHERE token = ?");
    $stmt->bind_param("s", $token);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        $stmt->bind_result($email, $expires);
        $stmt->fetch();

        if (strtotime($expires) > time()) {
            $showForm = true;
        } else {
            $message = "<p class='error'>❌ Reset link has expired.</p>";
        }
    } else {
        $message = "<p class='error'>❌ Invalid reset link.</p>";
    }
}

// Handle password update
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['reset_password'])) {
    $email = $_POST['email'];
    $newPassword = $_POST['password'];
    $confirmPassword = $_POST['confirm_password'];

    if ($newPassword !== $confirmPassword) {
        $message = "<p class='error'>❌ Passwords do not match.</p>";
        $showForm = true;
    } else {
        $hashed = password_hash($newPassword, PASSWORD_DEFAULT);

        // Update password
        $update = $conn->prepare("UPDATE users SET password = ? WHERE email = ?");
        $update->bind_param("ss", $hashed, $email);
        $update->execute();

        // Remove used token
        $delete = $conn->prepare("DELETE FROM password_resets WHERE email = ?");
        $delete->bind_param("s", $email);
        $delete->execute();

        $message = "<p class='success'>✅ Password changed successfully. You may now log in.</p>";
        $showForm = false;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Reset Password</title>
<style>
body {
  font-family: 'Inter', sans-serif;
  background: linear-gradient(135deg, #002b5b, #005792);
  display: flex;
  justify-content: center;
  align-items: center;
  height: 100vh;
}
.container {
  background: white;
  padding: 30px;
  border-radius: 12px;
  width: 400px;
  box-shadow: 0 4px 20px rgba(0,0,0,0.15);
}
h2 {
  text-align: center;
  color: #002b5b;
}
input {
  width: 100%;
  padding: 12px;
  margin-top: 10px;
  border-radius: 8px;
  border: 1px solid #ccc;
}
button {
  width: 100%;
  padding: 12px;
  background: #ff6a00;
  color: white;
  border: none;
  border-radius: 8px;
  margin-top: 15px;
  font-weight: bold;
}
button:hover {
  background: #e65c00;
}
.success {
  color: green;
  text-align: center;
}
.error {
  color: red;
  text-align: center;
}
</style>
</head>
<body>

<div class="container">
<h2>Reset Password</h2>
<?= $message ?>

<?php if ($showForm): ?>
<form method="POST">
    <input type="hidden" name="email" value="<?= htmlspecialchars($email) ?>">
    <input type="password" name="password" placeholder="New Password" required>
    <input type="password" name="confirm_password" placeholder="Confirm Password" required>
    <button type="submit" name="reset_password">Update Password</button>
</form>
<?php endif; ?>

<?php if (!$showForm && empty($message) === false): ?>
<div style="text-align:center;margin-top:15px;">
    <a href="login.php" style="color:#002b5b;font-weight:bold;">Back to Login</a>
</div>
<?php endif; ?>

</div>
</body>
</html>
