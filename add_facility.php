<?php
session_start();
require_once 'db.php';

// Restrict to admin only
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

$success = "";
$error = "";

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $name = trim($_POST["name"]);
    $description = trim($_POST["description"]);
    $image = $_FILES['image']['name'];
    $image_tmp = $_FILES['image']['tmp_name'];

    if (!empty($name) && !empty($description)) {
        $imagePath = "";

        // Upload image if selected
        if (!empty($image)) {
            $imagePath = "uploads/" . basename($image);
            move_uploaded_file($image_tmp, $imagePath);
        }

        // Insert into database
        $stmt = $conn->prepare("INSERT INTO facilities (name, description, image) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $name, $description, $image);

        if ($stmt->execute()) {
            $success = "✅ Facility added successfully!";
        } else {
            $error = "❌ Failed to add facility.";
        }
    } else {
        $error = "⚠️ Please fill in all required fields.";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Add Sport Facility</title>
    <style>
        body {
            font-family: 'Inter', sans-serif;
            background-color: #f0f0f0;
            padding: 40px;
        }
        .container {
            max-width: 600px;
            margin: auto;
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        h1 { color: #002855; }
        label { display: block; margin-top: 15px; font-weight: bold; }
        input[type="text"], textarea, input[type="file"] {
            width: 100%; padding: 10px; margin-top: 5px; border: 1px solid #ccc; border-radius: 6px;
        }
        .btn {
            margin-top: 20px;
            background-color: #f60;
            color: white;
            border: none;
            padding: 12px 20px;
            border-radius: 6px;
            cursor: pointer;
        }
        .btn:hover { background-color: #e05500; }
        .message {
            margin-top: 15px;
            padding: 10px;
            border-radius: 6px;
        }
        .success { background-color: #d4edda; color: #155724; }
        .error { background-color: #f8d7da; color: #721c24; }
    </style>
</head>
<body>
    <div class="container">
        <h1>Add Sport Facility</h1>

        <?php if ($success): ?><div class="message success"><?= $success ?></div><?php endif; ?>
        <?php if ($error): ?><div class="message error"><?= $error ?></div><?php endif; ?>

        <form method="POST" enctype="multipart/form-data">
            <label for="name">Facility Name</label>
            <input type="text" name="name" id="name" required>

            <label for="description">Description</label>
            <textarea name="description" id="description" rows="4" required></textarea>

            <label for="image">Upload Image</label>
            <input type="file" name="image" id="image" accept="image/*">

            <button type="submit" class="btn">Add Facility</button>
        </form>
    </div>
<div style="text-align: center; margin: 20px 0;">
  <a href="admin_dashboard.php" style="
    display: inline-block;
    background-color: #002855;
    color: white;
    padding: 12px 24px;
    text-decoration: none;
    border-radius: 6px;
    font-weight: bold;
    box-shadow: 0 2px 6px rgba(0,0,0,0.1);
  ">
    🏠 Back to Home
  </a>
</div>

</body>

</html>
