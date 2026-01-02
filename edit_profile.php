<?php
session_start();
require_once "db.php";

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$message = "";

// Fetch current user data
$stmt = $conn->prepare("SELECT username, email, nickname, age, address, profile_pic FROM users WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
if ($result->num_rows === 1) {
    $user = $result->fetch_assoc();
} else {
    die("User not found.");
}

// Update profile when submitted
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $username = $_POST['username'] ?? '';
    $email = $_POST['email'] ?? '';
    $nickname = $_POST['nickname'] ?? '';
    $age = $_POST['age'] ?? null;
    $address = $_POST['address'] ?? '';

    $profile_pic = $user['profile_pic'];

    if (!empty($_FILES['profile_pic']['name'])) {
        $targetDir = "uploads/";
        $fileName = basename($_FILES["profile_pic"]["name"]);
        $targetFile = $targetDir . $fileName;
        $imageFileType = strtolower(pathinfo($targetFile, PATHINFO_EXTENSION));
        $validExtensions = ['jpg','jpeg','png','gif'];

        if (in_array($imageFileType, $validExtensions)) {
            if (move_uploaded_file($_FILES["profile_pic"]["tmp_name"], $targetFile)) {
                $profile_pic = $fileName;
            } else {
                $message = "Failed to upload image.";
            }
        } else {
            $message = "Invalid image format.";
        }
    }

    $update = $conn->prepare("UPDATE users SET username=?, email=?, nickname=?, age=?, address=?, profile_pic=? WHERE id=?");
    $update->bind_param("sssissi", $username, $email, $nickname, $age, $address, $profile_pic, $user_id);
    
    if ($update->execute()) {
        $message = "Profile updated successfully.";
        header("Location: dashboard.php");
        exit();
    } else {
        $message = "Failed to update profile.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Edit Profile | Jeddah University</title>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
<style>
body {
    font-family:'Inter',sans-serif;
    background: linear-gradient(135deg,#002b5b,#005792);
    margin:0;
    padding:30px;
    min-height:100vh;
    display:flex;
    justify-content:center;
}
.card {
    background:#fff;
    padding:30px;
    border-radius:12px;
    box-shadow:0 6px 16px rgba(0,0,0,0.1);
    width:500px;
    max-width:95%;
}
h2 {
    text-align:center;
    color:#002855;
    margin-bottom:25px;
}
label {
    display:block;
    margin-bottom:5px;
    font-weight:bold;
}
input, textarea {
    width:100%;
    padding:10px;
    margin-bottom:15px;
    border-radius:6px;
    border:1px solid #ccc;
    font-size:14px;
}
button {
    background-color:#ff6a00;
    color:white;
    padding:12px;
    border:none;
    border-radius:6px;
    font-size:16px;
    cursor:pointer;
    width:100%;
    font-weight:bold;
}
button:hover { background-color:#e05500; }
.profile-preview {
    text-align:center;
    margin-bottom:20px;
}
.profile-preview img {
    width:120px;
    height:120px;
    border-radius:50%;
    object-fit:cover;
    border:3px solid #002855;
}
.message {
    text-align:center;
    margin-bottom:20px;
    font-weight:bold;
    color:green;
}
.back-btn {
    display:block;
    text-align:center;
    background-color:#002855;
    color:white;
    padding:12px;
    text-decoration:none;
    border-radius:6px;
    font-weight:bold;
    margin-top:20px;
    box-shadow:0 2px 6px rgba(0,0,0,0.1);
}
.back-btn:hover { background-color:#001b40; }
</style>
</head>
<body>
<div class="card">
<h2>Edit Your Profile</h2>

<?php if ($user['profile_pic']): ?>
<div class="profile-preview">
    <img src="uploads/<?= htmlspecialchars($user['profile_pic']) ?>" alt="Profile Picture">
</div>
<?php endif; ?>

<?php if (!empty($message)): ?>
<div class="message"><?= htmlspecialchars($message) ?></div>
<?php endif; ?>

<form method="POST" enctype="multipart/form-data">
    <label>Username</label>
    <input type="text" name="username" value="<?= htmlspecialchars($user['username']) ?>" required>

    <label>Email</label>
    <input type="email" name="email" value="<?= htmlspecialchars($user['email']) ?>" required>

    <label>Nickname</label>
    <input type="text" name="nickname" value="<?= htmlspecialchars($user['nickname']) ?>">

    <label>Age</label>
    <input type="number" name="age" value="<?= htmlspecialchars($user['age']) ?>">

    <label>Address</label>
    <textarea name="address"><?= htmlspecialchars($user['address']) ?></textarea>

    <label>Change Profile Picture</label>
    <input type="file" name="profile_pic" accept="image/*">

    <button type="submit">Save Changes</button>
</form>

<a href="dashboard.php" class="back-btn">← Back to Dashboard</a>
</div>
</body>
</html>
