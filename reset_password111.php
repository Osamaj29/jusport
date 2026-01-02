<?php
session_start();
require_once "db.php";

// Ensure user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$success = "";
$error = "";

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $current_password = $_POST['current_password'] ?? '';
    $new_password = $_POST['new_password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';

    if (empty($current_password) || empty($new_password) || empty($confirm_password)) {
        $error = "All fields are required.";
    } elseif ($new_password !== $confirm_password) {
        $error = "New passwords do not match.";
    } else {
        // Check current password
        $stmt = $conn->prepare("SELECT password FROM users WHERE id = ?");
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $stmt->bind_result($hashed_password);
        $stmt->fetch();
        $stmt->close();

        if (!password_verify($current_password, $hashed_password)) {
            $error = "Current password is incorrect.";
        } else {
            // Update new password
            $new_hashed = password_hash($new_password, PASSWORD_DEFAULT);
            $stmt = $conn->prepare("UPDATE users SET password = ? WHERE id = ?");
            $stmt->bind_param("si", $new_hashed, $user_id);

            if ($stmt->execute()) {
                $success = "Password updated successfully.";
            } else {
                $error = "Failed to update password.";
            }
            $stmt->close();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0"/>
<title>Reset Password | Jeddah University</title>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
<style>
body {
    font-family: 'Inter', sans-serif;
    background: linear-gradient(135deg, #002b5b, #005792);
    margin: 0;
    padding: 30px;
    min-height: 100vh;
    display: flex;
    justify-content: center;
    align-items: center;
}
.card {
    background: #fff;
    padding: 30px;
    border-radius: 12px;
    box-shadow: 0 6px 16px rgba(0,0,0,0.1);
    width: 420px;
}
h2 {
    text-align: center;
    color: #002855;
    margin-bottom: 25px;
}
label {
    font-weight: 600;
    margin-top: 12px;
    display: block;
    color: #002855;
}
input[type="password"] {
    width: 100%;
    padding: 10px;
    margin-top: 6px;
    border-radius: 8px;
    border: 1px solid #ccc;
    font-size: 15px;
}
.btn {
    display: block;
    width: 100%;
    text-align: center;
    background: #002855;
    color: white;
    padding: 12px;
    border-radius: 8px;
    text-decoration: none;
    font-weight: bold;
    margin-top: 20px;
    border: none;
    cursor: pointer;
    transition: background-color 0.3s ease;
}
.btn:hover { background: #001b40; }

.message {
    margin-top: 15px;
    padding: 12px;
    border-radius: 8px;
    font-size: 15px;
    text-align: center;
}
.success { background-color: #d4edda; color: #155724; }
.error { background-color: #f8d7da; color: #721c24; }

.back-link {
    display: block;
    text-align: center;
    margin-top: 15px;
    font-weight: bold;
    color: #002855;
    text-decoration: none;
}
.back-link:hover { text-decoration: underline; }

@media(max-width:480px){
    .card{width:90%; padding:20px;}
}
</style>
</head>
<body>
<div class="card">
    <h2>Reset Password</h2>

    <?php if ($success): ?>
      <div class="message success"><?= htmlspecialchars($success) ?></div>
    <?php elseif ($error): ?>
      <div class="message error"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <form method="POST">
      <label for="current_password">Current Password</label>
      <input type="password" name="current_password" id="current_password" required>

      <label for="new_password">New Password</label>
      <input type="password" name="new_password" id="new_password" required>

      <label for="confirm_password">Confirm New Password</label>
      <input type="password" name="confirm_password" id="confirm_password" required>

      <button type="submit" class="btn">Update Password</button>
    </form>

    <a href="dashboard.php" class="back-link">← Back to Dashboard</a>
</div>
</body>
</html>
