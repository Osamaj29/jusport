<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'vendor/autoload.php';
session_start();
$conn = new mysqli("localhost", "root", "", "sport_booking");

$message = "";

// Handle sending reset link
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['request_reset'])) {
    $email = $_POST['email'];

    $stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        $token = bin2hex(random_bytes(16)); // secure reset token
        $expires = date("Y-m-d H:i:s", strtotime("+15 minutes"));

        // Store token in DB
        $insert = $conn->prepare("INSERT INTO password_resets (email, token, expires_at) VALUES (?, ?, ?)");
        $insert->bind_param("sss", $email, $token, $expires);
        $insert->execute();

        // Send email with reset link
        $mail = new PHPMailer(true);
        try {
            $mail->isSMTP();
            $mail->Host       = 'smtp.gmail.com';
            $mail->SMTPAuth   = true;
            $mail->Username   = 'osama0557562398@gmail.com'; // your Gmail
            $mail->Password   = 'erffxywngpqjxayo'; // Gmail App Password
            $mail->SMTPSecure = 'tls';
            $mail->Port       = 587;

            $mail->setFrom('osama0557562398@gmail.com', 'Sports Booking System');
            $mail->addAddress($email);

            $resetLink = "http://localhost/SportFacilities/reset_password.php?token=$token";

            $mail->isHTML(true);
            $mail->Subject = 'Reset Your Password';
            $mail->Body = "
              <h3>Password Reset Request</h3>
              <p>We received a request to reset your password. Click the link below to reset it:</p>
              <p><a href='$resetLink' style='background-color:#002b5b;color:white;padding:10px 15px;text-decoration:none;border-radius:5px;'>Reset Password</a></p>
              <p>This link will expire in 15 minutes.</p>
              <p>If you did not request this, please ignore this email.</p>
            ";

            $mail->send();
            $message = "<p class='success'>✅ A password reset link has been sent to your email.</p>";
        } catch (Exception $e) {
            $message = "<p class='error'>❌ Email sending failed: {$mail->ErrorInfo}</p>";
        }
    } else {
        $message = "<p class='error'>❌ No user found with that email.</p>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Forgot Password</title>
  <style>
    body {
      font-family: 'Inter', sans-serif;
      background: linear-gradient(135deg, #002b5b, #005792);
      display: flex;
      justify-content: center;
      align-items: center;
      height: 100vh;
      margin: 0;
    }
    .container {
      background: white;
      padding: 30px;
      border-radius: 12px;
      box-shadow: 0px 4px 20px rgba(0,0,0,0.15);
      max-width: 400px;
      width: 100%;
      animation: fadeIn 0.4s ease-in-out;
    }
    h2 {
      text-align: center;
      margin-bottom: 20px;
      color: #002b5b;
    }
    label {
      font-weight: bold;
      display: block;
      margin-bottom: 5px;
    }
    input[type="email"] {
      width: 100%;
      padding: 12px;
      margin-bottom: 15px;
      border: 1px solid #ccc;
      border-radius: 8px;
      font-size: 14px;
    }
    button {
      width: 100%;
      padding: 12px;
      background-color: #ff6a00;
      color: white;
      border: none;
      border-radius: 8px;
      font-weight: bold;
      font-size: 15px;
      cursor: pointer;
    }
    button:hover {
      background-color: #e65c00;
    }
    .success {
      color: green;
      text-align: center;
      margin-bottom: 10px;
    }
    .error {
      color: red;
      text-align: center;
      margin-bottom: 10px;
    }
    @keyframes fadeIn {
        from {opacity: 0; transform: translateY(-20px);}
        to {opacity: 1; transform: translateY(0);}
    }
  </style>
</head>
<body>
  <div class="container">
    <h2>Forgot Password</h2>
    <?php echo $message; ?>
    <form method="POST">
      <label for="email">Enter your registered email:</label>
      <input type="email" name="email" placeholder="example@email.com" required>
      <button type="submit" name="request_reset">Send Reset Link</button>
    </form>
    <form action="login.php" method="get">
      <button type="submit" style="margin-top: 10px; background-color: #3b1b91ff;">Back to Login</button>
    </form>
  </div>
</body>
</html>
