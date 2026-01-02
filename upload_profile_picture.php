<?php
session_start();
require_once 'db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Handle image upload
if (isset($_FILES['profile_picture']) && $_FILES['profile_picture']['error'] === UPLOAD_ERR_OK) {
    $file = $_FILES['profile_picture'];
    $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    $allowed = ['jpg', 'jpeg', 'png', 'gif'];

    if (in_array($ext, $allowed)) {
        $newName = "admin_" . $user_id . "_" . time() . "." . $ext;
        $uploadPath = "uploads/" . $newName;

        if (move_uploaded_file($file['tmp_name'], $uploadPath)) {
            $stmt = $conn->prepare("UPDATE users SET profile_picture = ? WHERE id = ?");
            $stmt->bind_param("si", $newName, $user_id);
            $stmt->execute();

            header("Location: admin_dashboard.php?upload=success");
            exit();
        } else {
            echo "❌ Failed to move uploaded file.";
        }
    } else {
        echo "❌ Invalid file type.";
    }
} else {
    echo "❌ No file uploaded or upload error.";
}
?>
