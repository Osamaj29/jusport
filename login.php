<?php
session_start();
require 'otp_helper.php';
require 'mail_helper.php';


$conn = new mysqli("localhost", "root", "", "sport_booking");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$error = "";
$success = "";


if (isset($_SESSION['success'])) {
    $success = $_SESSION['success'];
    unset($_SESSION['success']);
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $email = $_POST['email'];
    $password = $_POST['password'];
    $role = $_POST['role'];

    $stmt = $conn->prepare("SELECT id, password, role FROM users WHERE email = ? AND role = ?");
    $stmt->bind_param("ss", $email, $role);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        $stmt->bind_result($id, $hashed_password, $user_role);
        $stmt->fetch();

        if (password_verify($password, $hashed_password)) {
            $otp = generateOTP(6);
            $otp_hash = password_hash($otp, PASSWORD_DEFAULT);
            $expires = date("Y-m-d H:i:s", strtotime('+10 minutes'));

            $conn->query("CREATE TABLE IF NOT EXISTS login_otps (
                id INT AUTO_INCREMENT PRIMARY KEY,
                user_id INT NOT NULL,
                otp_hash VARCHAR(255) NOT NULL,
                expires_at DATETIME NOT NULL,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
            )");

            
            $conn->query("DELETE FROM login_otps WHERE user_id = $id");

            
            $insert = $conn->prepare("INSERT INTO login_otps (user_id, otp_hash, expires_at) VALUES (?, ?, ?)");
            $insert->bind_param("iss", $id, $otp_hash, $expires);
            $insert->execute();

            
            if (sendOTPEmail($email, $otp)) {
                $_SESSION['pending_user_id'] = $id;
                $_SESSION['pending_role'] = $user_role;
                header("Location: verify_login_otp.php");
                exit();
            } else {
                $error = "Failed to send OTP email.";
            }
        } else {
            $error = "Incorrect password.";
        }
    } else {
        $error = "User not found or incorrect role.";
    }

    $stmt->close();
    $conn->close();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Login</title>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet" />
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
    input, select {
      width: 100%;
      padding: 12px;
      margin-bottom: 15px;
      border: 1px solid #ccc;
      border-radius: 8px;
      font-size: 14px;
    }
    .password-wrapper {
      position: relative;
    }
    .toggle-password {
      position: absolute;
      right: 12px;
      top: 50%;
      transform: translateY(-50%);
      cursor: pointer;
      color: #555;
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
    .link {
      text-align: center;
      margin-top: 10px;
    }
    .link a {
      color: #002b5b;
      font-weight: 600;
      text-decoration: none;
    }
    @keyframes fadeIn {
        from {opacity: 0; transform: translateY(-20px);}
        to {opacity: 1; transform: translateY(0);}
    }
  </style>
  <script>
    function togglePassword() {
      const passwordField = document.getElementById("password");
      const toggleIcon = document.getElementById("toggleIcon");
      if (passwordField.type === "password") {
        passwordField.type = "text";
        toggleIcon.textContent = "🙈"; 
      } else {
        passwordField.type = "password";
        toggleIcon.textContent = "👁️"; 
      }
    }
  </script>
</head>
<body>
  <div class="container">
    <h2>User Login</h2>
    <?php
      if (!empty($success)) echo "<p class='success'>$success</p>";
      if (!empty($error)) echo "<p class='error'>$error</p>";
    ?>
    <form method="POST">
      <label>Email</label>
      <input type="email" name="email" required />

      <label>Password</label>
      <div class="password-wrapper">
        <input type="password" name="password" id="password" required />
        <span id="toggleIcon" class="toggle-password" onclick="togglePassword()">👁️</span>
      </div>

      <label>Login As</label>
      <select name="role" required>
        <option value="user">User</option>
        <option value="admin">Admin</option>
      </select>

      <button type="submit">Login</button>
    </form>

    <form action="index.php" method="get">
      <button type="submit" style="margin-top: 10px; background-color: #3b1b91ff;">Back to Home</button>
    </form>
    <div class="link">
      <a href="forgot_password.php">Forgot Password?</a>
    </div>
  </div>
</body>
</html>
