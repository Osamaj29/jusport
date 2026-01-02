<?php
session_start();
require_once 'db.php';

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
    $slots = array_filter($_POST["slots"]); // Remove empty slot inputs

    $image = $_FILES['image']['name'];
    $imagePath = '';

    if ($image) {
        $imagePath = "uploads/" . basename($image);
        move_uploaded_file($_FILES['image']['tmp_name'], $imagePath);
    }

    if (!empty($name) && !empty($description)) {
        $stmt = $conn->prepare("INSERT INTO facilities (name, description, image) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $name, $description, $image);
        if ($stmt->execute()) {
            $facility_id = $stmt->insert_id;

            // Insert time slots
            $slot_stmt = $conn->prepare("INSERT INTO facility_slots (facility_id, slot_time) VALUES (?, ?)");
            foreach ($slots as $slot) {
                $slot_stmt->bind_param("is", $facility_id, $slot);
                $slot_stmt->execute();
            }

            $success = "Facility added successfully!";
        } else {
            $error = "Error inserting facility.";
        }
    } else {
        $error = "Please fill all required fields.";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Manage Facilities</title>
    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
            background: linear-gradient(135deg, #002b5b, #005792);
            padding: 40px;
            margin: 0;
            min-height: 100vh;
            box-sizing: border-box;
            display: flex;
            justify-content: center;
            align-items: flex-start;
        }
        .container {
            max-width: 650px;
            margin: auto;
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        h2 {
            text-align: center;
            color: #002855;
        }
        label {
            margin-top: 15px;
            display: block;
            font-weight: bold;
        }
        input[type="text"], textarea, input[type="file"], input[type="time"] {
            width: 100%;
            padding: 10px;
            margin-top: 6px;
            border: 1px solid #ccc;
            border-radius: 6px;
        }
        .btn {
            background: #f60;
            color: white;
            padding: 12px 20px;
            border: none;
            margin-top: 20px;
            border-radius: 6px;
            cursor: pointer;
            font-weight: bold;
            font-size: 16px;
            transition: background-color 0.3s ease;
        }
        .btn:hover {
            background: #e05500;
        }
        .message {
            margin-top: 15px;
            padding: 10px;
            border-radius: 6px;
            font-weight: bold;
            font-size: 14px;
        }
        .success { background: #d4edda; color: #155724; }
        .error { background: #f8d7da; color: #721c24; }
        .slot-inputs input { margin-bottom: 10px; }

        /* Fixed bottom Back to Home button styling */
        .back-home {
            position: fixed;
            bottom: 1px;
            left: 50%;
            transform: translateX(-50%);
            z-index: 999;
        }
        .back-home a {
            display: inline-block;
            background-color: #002855;
            color: white;
            padding: 12px 24px;
            text-decoration: none;
            border-radius: 6px;
            font-weight: bold;
            box-shadow: 0 2px 6px rgba(0,0,0,0.1);
            transition: background-color 0.3s ease;
        }
        .back-home a:hover {
            background-color: #004080;
        }
    </style>
</head>
<body>

<div class="container">
    <h2>Add New Sport Facility</h2>

    <?php if ($success): ?>
        <div class="message success"><?= htmlspecialchars($success) ?></div>
    <?php endif; ?>
    <?php if ($error): ?>
        <div class="message error"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <form method="POST" enctype="multipart/form-data">
        <label for="name">Facility Name</label>
        <input type="text" name="name" id="name" required>

        <label for="description">Description</label>
        <textarea name="description" id="description" required rows="4"></textarea>

        <label for="image">Upload Image</label>
        <input type="file" name="image" id="image" accept="image/*">

        <label>Available Time Slots (Between 8:00 AM - 10:00 PM)</label>
        <div class="slot-inputs">
            <input type="time" name="slots[]" min="08:00" max="22:00" required>
            <input type="time" name="slots[]" min="08:00" max="22:00">
            <label>Optional Slot</label>
            <input type="time" name="slots[]" min="08:00" max="22:00">
        </div>

        <button type="submit" class="btn">➕ Add Facility</button>
    </form>
</div>

<div class="back-home">
  <a href="admin_dashboard.php">🏠 Back to Home</a>
</div>

</body>
</html>
