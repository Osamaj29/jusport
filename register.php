<?php
session_start();

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "sport_booking";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$message = "";

function isPasswordStrong($password) {
    return preg_match('/^(?=.*[A-Z])(?=.*[a-z])(?=.*\d)(?=.*[\W_]).{8,}$/', $password);
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $name = htmlspecialchars($_POST['name']);
    $email = htmlspecialchars($_POST['email']);
    $phone = htmlspecialchars($_POST['phone']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    // Check password match
    if ($password !== $confirm_password) {
        $message = "<p class='error'>❌ Passwords do not match.</p>";
    } 
    // Check password strength
    elseif (!isPasswordStrong($password)) {
        $message = "<p class='error'>❌ Password must be at least 8 characters long and include uppercase, lowercase, number, and special character.</p>";
    } 
    else {
        // Check if email already exists
        $stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            $message = "<p class='error'>❌ Email already registered. Please use another.</p>";
        } else {
            $hashedPassword = password_hash($password, PASSWORD_BCRYPT);
            $insert = $conn->prepare("INSERT INTO users (name, email, phone, password, role) VALUES (?, ?, ?, ?, 'user')");
            $insert->bind_param("ssss", $name, $email, $phone, $hashedPassword);

            if ($insert->execute()) {
                $_SESSION['success'] = "✅ Registration successful! You can now login.";
                header("Location: login.php");
                exit();
            } else {
                $message = "<p class='error'>❌ Error: " . $conn->error . "</p>";
            }
            $insert->close();
        }

        $stmt->close();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>User Registration</title>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet" />
  <style>
    body {
      font-family: 'Inter', sans-serif;
      background: linear-gradient(135deg, #002b5b, #005792);
      margin: 0;
      display: flex;
      flex-direction: column;
      min-height: 100vh;
    }
    .navbar {
      background-color: #002b5b;
      padding: 15px 0;
      text-align: center;
    }
    .navbar a {
      color: white;
      text-decoration: none;
      margin: 0 15px;
      font-weight: 600;
    }
    .navbar a:hover { text-decoration: underline; }

    .container {
      background: white;
      padding: 30px;
      border-radius: 12px;
      box-shadow: 0px 4px 20px rgba(0,0,0,0.15);
      max-width: 400px;
      width: 100%;
      margin: auto;
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
    input {
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
    button:hover { background-color: #e65c00; }
    .error, .success {
      text-align: center;
      font-weight: 600;
      margin-bottom: 10px;
    }
    .error { color: red; }
    .success { color: green; }

    @keyframes fadeIn {
      from {opacity: 0; transform: translateY(-20px);}
      to {opacity: 1; transform: translateY(0);}
    }
  </style>
</head>
<body>

  <div class="navbar">
    <a href="index.php">Home</a>
    <a href="facilities.php">Facilities</a>
    <a href="about.php">About</a>
    <a href="contact.php">Contact</a>
    <a href="dashboard.php">Dashboard</a>
    <a href="login.php">Login</a>
    <a href="register.php">Register</a>
  </div>

  <div class="container">
    <h2>Create Account</h2>
    <?php if (!empty($message)) echo $message; ?>
    <form method="POST" onsubmit="return validatePassword();">
      <label>Full Name</label>
      <input type="text" name="name" required />

      <label>Email</label>
      <input type="email" name="email" required />

      <label>Phone Number</label>
      <input type="text" name="phone" required />

      <label>Password</label>
      <input type="password" name="password" id="password" required placeholder="At least 8 chars, 1 uppercase, 1 lowercase, 1 number, 1 symbol"/>

      <label>Confirm Password</label>
      <input type="password" name="confirm_password" id="confirm_password" required />

      <button type="submit">Register</button>
    </form>

    <form action="login.php" method="get">
      <button type="submit" style="margin-top: 10px; background-color: #3b1b91ff;">Back to Login</button>
    </form>
  </div>

  <script>
    function validatePassword() {
      var pass = document.getElementById("password").value;
      var confirm = document.getElementById("confirm_password").value;
      var strongRegex = /^(?=.*[A-Z])(?=.*[a-z])(?=.*\d)(?=.*[\W_]).{8,}$/;

      if (pass !== confirm) {
        alert("❌ Passwords do not match.");
        return false;
      }
      if (!strongRegex.test(pass)) {
        alert("❌ Password must be at least 8 characters long and include uppercase, lowercase, number, and special character.");
        return false;
      }
      return true;
    }
  </script>
</body>
</html>
