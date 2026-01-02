<?php
session_start();
require_once 'db.php';

// Only admin can access
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

// Show success message after update
$updated = isset($_GET['updated']) ? true : false;

// Predefined facility order
$fixedFacilityNames = ['Football', 'Basketball', 'Futsal', 'Swimming', 'Pickleball', 'Tennis'];

// Fetch all admin-added facilities
$query = "SELECT * FROM facilities ORDER BY name ASC";
$result = $conn->query($query);

$adminFacilities = [];
if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $adminFacilities[] = $row;
    }
}

// Split into fixed and others
$fixedFacilities = [];
$otherFacilities = [];

foreach ($fixedFacilityNames as $fixedName) {
    foreach ($adminFacilities as $facility) {
        if ($facility['name'] === $fixedName) {
            $fixedFacilities[] = $facility;
        }
    }
}

foreach ($adminFacilities as $facility) {
    if (!in_array($facility['name'], $fixedFacilityNames)) {
        $otherFacilities[] = $facility;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Edit Facilities</title>
  <style>
    body {
      font-family: 'Segoe UI', sans-serif;
      background: linear-gradient(135deg, #002b5b, #005792);
      margin: 0;
      padding: 30px;
      min-height: 100vh;
      box-sizing: border-box;
    }
    .container {
      max-width: 1100px;
      margin: auto;
      background: white;
      padding: 30px;
      border-radius: 10px;
      box-shadow: 0 0 15px rgba(0,0,0,0.1);
    }
    h2 {
      text-align: center;
      color: #002855;
      margin-bottom: 30px;
    }
    .success-msg {
      text-align: center;
      color: green;
      margin-bottom: 15px;
      font-weight: bold;
    }
    .btn {
      display: inline-block;
      padding: 8px 14px;
      background-color: #004080;
      color: white;
      border-radius: 5px;
      text-decoration: none;
      font-size: 14px;
      margin: 4px 6px 0 0;
      transition: background-color 0.3s ease;
    }
    .btn:hover {
      background-color: #0066cc;
    }
    .delete-btn {
      background-color: #d11a2a;
    }
    .delete-btn:hover {
      background-color: #a10f1f;
    }
    .add-btn {
      background-color: #28a745;
      margin-bottom: 20px;
    }
    .facility-grid {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(320px, 1fr));
      gap: 30px;
      margin-top: 30px;
    }
    .facility-card {
      background: white;
      padding: 20px;
      border-radius: 10px;
      box-shadow: 0 4px 10px rgba(0,0,0,0.1);
      transition: transform 0.3s ease;
    }
    .facility-card:hover {
      transform: translateY(-5px);
    }
    .facility-card img {
      width: 100%;
      height: 200px;
      object-fit: cover;
      border-radius: 8px;
      margin-bottom: 15px;
    }
    .facility-card h3 {
      color: #002855;
      font-size: 20px;
      margin-bottom: 10px;
    }
    .facility-card p {
      color: #555;
      font-size: 15px;
      line-height: 1.5;
      margin-bottom: 10px;
    }
    .footer {
      text-align: center;
      margin-top: 60px;
      font-size: 14px;
      color: #777;
    }
  </style>
</head>
<body>

<div class="container">
  <h2>Edit or Remove Facilities</h2>
  <?php if ($updated): ?>
    <div class="success-msg">✅ Facility updated successfully.</div>
  <?php endif; ?>

  <a href="manage_facilities.php" class="btn add-btn">➕ Add New Facility</a>

  <div class="facility-grid">
    <!-- Show fixed facilities in predefined order -->
    <?php foreach ($fixedFacilities as $facility): ?>
      <div class="facility-card">
        <img src="uploads/<?= htmlspecialchars($facility['image']) ?>" alt="<?= htmlspecialchars($facility['name']) ?>">
        <h3><?= htmlspecialchars($facility['name']) ?></h3>
        <p><?= htmlspecialchars($facility['description']) ?></p>
        <div>
          <a href="edit_facility1.php?id=<?= $facility['id'] ?>" class="btn">✏️ Edit</a>
          <a href="delete_facility.php?id=<?= $facility['id'] ?>" class="btn delete-btn" onclick="return confirm('Are you sure you want to delete this facility?');">🗑️ Delete</a>
        </div>
      </div>
    <?php endforeach; ?>

    <!-- Show other facilities after -->
    <?php foreach ($otherFacilities as $facility): ?>
      <div class="facility-card">
        <img src="uploads/<?= htmlspecialchars($facility['image']) ?>" alt="<?= htmlspecialchars($facility['name']) ?>">
        <h3><?= htmlspecialchars($facility['name']) ?></h3>
        <p><?= htmlspecialchars($facility['description']) ?></p>
        <div>
          <a href="edit_facility1.php?id=<?= $facility['id'] ?>" class="btn">✏️ Edit</a>
          <a href="delete_facility.php?id=<?= $facility['id'] ?>" class="btn delete-btn" onclick="return confirm('Are you sure you want to delete this facility?');">🗑️ Delete</a>
        </div>
      </div>
    <?php endforeach; ?>
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
</div>



</body>
</html>
