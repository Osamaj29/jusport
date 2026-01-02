<?php
session_start();
require_once 'db.php';

// Only admin can access
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

if (!isset($_GET['id'])) {
    die("❌ No facility selected.");
}

$facility_id = intval($_GET['id']);
$success = false;

$stmt = $conn->prepare("SELECT * FROM facilities WHERE id = ?");
$stmt->bind_param("i", $facility_id);
$stmt->execute();
$result = $stmt->get_result();
$facility = $result->fetch_assoc();

if (!$facility) {
    die("❌ Facility not found.");
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'];
    $venue = $_POST['venue'];
    $description = $_POST['description'];

    $image = $facility['image']; 
    if (!empty($_FILES['image']['name'])) {
        $targetDir = "uploads/";
        $image = time() . "_" . basename($_FILES['image']['name']);
        $targetFile = $targetDir . $image;
        move_uploaded_file($_FILES['image']['tmp_name'], $targetFile);
    }

    $update = $conn->prepare("UPDATE facilities SET name=?, venue_name=?, description=?, image=? WHERE id=?");
    $update->bind_param("ssssi", $name, $venue, $description, $image, $facility_id);

    if ($update->execute()) {
        $success = true;
        header("Location: edit_facilities.php?updated=1");
        exit();
    } else {
        echo "❌ Error updating facility.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Edit Facility</title>
  <style>
    body {
      font-family: 'Segoe UI', sans-serif;
      background: #f4f4f9;
      margin: 0;
      padding: 40px;
    }
    .container {
      max-width: 700px;
      margin: auto;
      background: white;
      padding: 30px;
      border-radius: 10px;
      box-shadow: 0 4px 12px rgba(0,0,0,0.1);
    }
    h2 {
      text-align: center;
      color: #002855;
      margin-bottom: 20px;
    }
    form label {
      font-weight: bold;
      display: block;
      margin-top: 15px;
    }
    form input, form textarea {
      width: 100%;
      padding: 10px;
      margin-top: 6px;
      border-radius: 6px;
      border: 1px solid #ccc;
      font-size: 14px;
    }
    form input[type="file"] {
      border: none;
    }
    .btn {
      margin-top: 20px;
      display: inline-block;
      padding: 12px 24px;
      background-color: #002855;
      color: white;
      border: none;
      border-radius: 6px;
      font-weight: bold;
      cursor: pointer;
      text-align: center;
    }
    .btn:hover {
      background-color: #004080;
    }
    .back {
      display: inline-block;
      margin-top: 15px;
      text-decoration: none;
      color: #002855;
      font-weight: bold;
    }
    img.preview {
      display: block;
      margin-top: 10px;
      max-width: 150px;
      border-radius: 6px;
    }
  </style>
</head>
<body>
  <div class="container">
    <h2>Edit Facility</h2>
    
    <?php if ($success): ?>
      <p style="color:green; text-align:center;">✅ Facility updated successfully!</p>
    <?php endif; ?>

    <form method="POST" enctype="multipart/form-data">
      <label for="name">Facility Name:</label>
      <input type="text" name="name" value="<?= htmlspecialchars($facility['name']) ?>" required>

      <label for="venue">Venue:</label>
      <input type="text" name="venue" value="<?= htmlspecialchars($facility['venue_name'] ?? '') ?>">

      <label for="description">Description:</label>
      <textarea name="description" rows="4" required><?= htmlspecialchars($facility['description']) ?></textarea>

      <label for="image">Image:</label>
      <input type="file" name="image" accept="image/*">
      <?php if ($facility['image']): ?>
        <img src="uploads/<?= htmlspecialchars($facility['image']) ?>" class="preview">
      <?php endif; ?>

      <button type="submit" class="btn">💾 Update Facility</button>
    </form>

    <a href="edit_facilities.php" class="back">⬅ Back to Facilities</a>
  </div>
</body>
</html>
