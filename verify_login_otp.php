<?php
session_start();

// DB Connection
$conn = new mysqli("localhost", "root", "", "sport_booking");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$error = "";

// Handle OTP submission
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $otp = $_POST['otp'];
    $user_id = $_SESSION['pending_user_id'];

    $stmt = $conn->prepare("SELECT otp_hash, expires_at FROM login_otps WHERE user_id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        $stmt->bind_result($otp_hash, $expires_at);
        $stmt->fetch();

        if (new DateTime() > new DateTime($expires_at)) {
            $error = "❌ OTP expired. Please log in again.";
        } elseif (password_verify($otp, $otp_hash)) {
            // OTP correct → login user
            $_SESSION['user_id'] = $user_id;
            $_SESSION['role'] = $_SESSION['pending_role'];

            unset($_SESSION['pending_user_id'], $_SESSION['pending_role']);
            $conn->query("DELETE FROM login_otps WHERE user_id = $user_id");

            if ($_SESSION['role'] === 'admin') {
                header("Location: admin_dashboard.php");
            } else {
                header("Location: index.php");
            }
            exit();
        } else {
            $error = "❌ Invalid OTP.";
        }
    } else {
        $error = "❌ No OTP found.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>OTP Verification</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
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
    .otp-card {
        background: white;
        padding: 40px;
        border-radius: 15px;
        box-shadow: 0px 4px 20px rgba(0,0,0,0.15);
        width: 100%;
        max-width: 380px;
        animation: fadeIn 0.4s ease-in-out;
    }
    .otp-card h2 {
        text-align: center;
        color: #002b5b;
        margin-bottom: 20px;
    }
    .otp-card p {
        text-align: center;
        font-size: 14px;
        color: #666;
        margin-bottom: 20px;
    }
    .otp-card input {
        width: 100%;
        padding: 12px;
        margin-top: 10px;
        border: 1px solid #ccc;
        border-radius: 8px;
        font-size: 16px;
        outline: none;
        transition: all 0.2s;
    }
    .otp-card input:focus {
        border-color: #005792;
        box-shadow: 0 0 5px rgba(0,87,146,0.4);
    }
    .otp-card button {
        width: 100%;
        padding: 14px;
        background: #ff6a00;
        color: white;
        font-size: 16px;
        font-weight: 600;
        border: none;
        border-radius: 8px;
        cursor: pointer;
        transition: background 0.3s;
        margin-top: 15px;
    }
    .otp-card button:hover {
        background: #e65c00;
    }
    .error {
        color: red;
        text-align: center;
        font-weight: bold;
        margin-bottom: 10px;
    }
    @keyframes fadeIn {
        from {opacity: 0; transform: translateY(-20px);}
        to {opacity: 1; transform: translateY(0);}
    }
</style>
</head>
<body>

<div class="otp-card">
    <h2>OTP Verification</h2>
    <p>We sent a 6-digit code to your email. Please enter it below.</p>
    <?php if(!empty($error)) echo "<p class='error'>$error</p>"; ?>
    <form method="POST">
        <input type="text" name="otp" placeholder="Enter 6-digit OTP" maxlength="6" required>
        <button type="submit">Verify OTP</button>
    </form>
</div>

</body>
</html>
