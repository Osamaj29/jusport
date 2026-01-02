<?php
session_start();
require_once 'db.php';

// Only admin can access
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

// Delete logic
if (isset($_GET['id'])) {
    $id = intval($_GET['id']);
    $stmt = $conn->prepare("DELETE FROM facilities WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    header("Location: delete_facility.php?deleted=1");
    exit();
}

// Fixed order list
$fixedOrder = ['Football', 'Basketball', 'Futsal', 'Swimming', 'Pickleball', 'Tennis'];

// Fetch all facilities
$sql = "SELECT * FROM facilities";
$result = $conn->query($sql);

// Separate facilities
$fixedFacilities = [];
$otherFacilities = [];

if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        if (in_array($row['name'], $fixedOrder)) {
            $fixedFacilities[$row['name']] = $row;
        } else {
            $otherFacilities[] = $row;
        }
    }
}

// Final display list: fixed (in order) then others
$displayFacilities = [];
foreach ($fixedOrder as $name) {
    if (isset($fixedFacilities[$name])) {
        $displayFacilities[] = $fixedFacilities[$name];
    }
}
$displayFacilities = array_merge($displayFacilities, $otherFacilities);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>Delete Facility</title>
  <style>
    body {
      font-family: 'Segoe UI', sans-serif;
      background: linear-gradient(135deg, #002b5b, #005792);
      padding: 40px;
      min-height: 100vh;
      box-sizing: border-box;
      margin: 0;
    }
    h1 {
      text-align: center;
      color: #fff;
      margin-bottom: 20px;
      text-shadow: 1px 1px 3px rgba(0,0,0,0.6);
    }
    .facility-table {
      width: 100%;
      max-width: 900px;
      margin: 0 auto 40px auto;
      background: white;
      border-collapse: collapse;
      border-radius: 10px;
      overflow: hidden;
      box-shadow: 0 0 8px rgba(0,0,0,0.1);
    }
    th, td {
      padding: 12px 15px;
      text-align: left;
      border-bottom: 1px solid #ddd;
      vertical-align: middle;
    }
    th {
      background: #002855;
      color: white;
    }
    tr:hover {
      background-color: #f9f9f9;
    }
    .delete-btn {
      background: #d9534f;
      color: white;
      padding: 6px 10px;
      text-decoration: none;
      border-radius: 4px;
      font-weight: bold;
      transition: background-color 0.3s ease;
      display: inline-block;
    }
    .delete-btn:hover {
      background: #c9302c;
    }
    .back-btn-container {
      text-align: center;
      margin: 30px 0 0 0;
    }
    .back-btn {
      background-color: #002855;
      color: white;
      padding: 12px 24px;
      text-decoration: none;
      border-radius: 6px;
      font-weight: bold;
      box-shadow: 0 2px 6px rgba(0,0,0,0.1);
      display: inline-block;
      transition: background-color 0.3s ease;
    }
    .back-btn:hover {
      background-color: #004080;
    }
    p.success-msg {
      text-align: center;
      color: #d4edda;
      background-color: #155724;
      padding: 10px 15px;
      border-radius: 6px;
      font-weight: bold;
      max-width: 900px;
      margin: 0 auto 20px auto;
      box-shadow: 0 0 5px rgba(21,87,36,0.5);
    }
  </style>
</head>
<body>

<h1>Delete Facilities</h1>

<?php if (isset($_GET['deleted'])): ?>
  <p class="success-msg">Facility deleted successfully.</p>
<?php endif; ?>

<table class="facility-table">
  <thead>
    <tr>
      <th>Name</th>
      <th>Description</th>
      <th>Image</th>
      <th>Action</th>
    </tr>
  </thead>
  <tbody>
    <?php if (!empty($displayFacilities)): ?>
      <?php foreach ($displayFacilities as $row): ?>
        <tr>
          <td><?= htmlspecialchars($row['name']) ?></td>
          <td><?= htmlspecialchars($row['description']) ?></td>
          <td>
            <?php if (!empty($row['image'])): ?>
              <img src="uploads/<?= htmlspecialchars($row['image']) ?>" alt="Image" style="width: 80px; height: 60px; object-fit: cover; border-radius: 4px;">
            <?php else: ?>
              No image
            <?php endif; ?>
          </td>
          <td>
            <a href="?id=<?= $row['id'] ?>" class="delete-btn" onclick="return confirm('Are you sure you want to delete this facility?');">Delete</a>
          </td>
        </tr>
      <?php endforeach; ?>
    <?php else: ?>
      <tr>
        <td colspan="4" style="text-align: center;">No facilities found.</td>
      </tr>
    <?php endif; ?>
  </tbody>
</table>

<div class="back-btn-container">
  <a href="admin_dashboard.php" class="back-btn">🏠 Back to Home</a>
</div>

</body>
</html>
