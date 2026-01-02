<?php
session_start();
require_once "db.php";

// Redirect if user not logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Priority facilities
$priorityNames = ['Football', 'Basketball', 'Futsal', 'Swimming', 'Pickleball', 'Tennis'];

// Default slots for system-defined facilities (8am – 6pm)
$defaultSlots = [
    "8:00 AM – 9:30 AM",
    "9:30 AM – 11:00 AM",
    "11:00 AM – 12:30 PM",
    "12:30 PM – 2:00 PM",
    "2:00 PM – 3:30 PM",
    "3:30 PM – 5:00 PM",
    "5:00 PM – 6:30 PM"
];

$fixedFacilities = [];
foreach ($priorityNames as $name) {
    $fixedFacilities[$name] = $defaultSlots;
}

// Load admin-added facilities from DB
$adminFacilities = [];
$sql = "SELECT f.id, f.name, f.description, f.image, fs.slot_time
        FROM facilities f
        LEFT JOIN facility_slots fs ON f.id = fs.facility_id
        ORDER BY f.id ASC, fs.slot_time ASC";
$result = $conn->query($sql);

if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $name = $row['name'];
        if (!isset($adminFacilities[$name])) {
            $adminFacilities[$name] = [
                'id' => $row['id'],
                'description' => $row['description'],
                'image' => $row['image'],
                'slots' => [],
            ];
        }
        if (!empty($row['slot_time'])) {
            $adminFacilities[$name]['slots'][] = $row['slot_time'];
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Browse Facilities</title>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
  <style>
    /* General Reset */
    * {
      box-sizing: border-box;
      margin: 0;
      padding: 0;
    }
    body {
      font-family: 'Inter', sans-serif;
      background: #f8f9fa;
      color: #333;
    }
    a {
      text-decoration: none;
    }

    .container {
      max-width: 1200px;
      margin: auto;
      padding: 40px 20px;
    }

    h2 {
      text-align: center;
      font-size: 36px;
      color: #1a202c;
      margin-bottom: 40px;
    }

    .facility-grid {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
      gap: 30px;
    }

    .facility-card {
      background: #fff;
      border-radius: 12px;
      overflow: hidden;
      box-shadow: 0 10px 20px rgba(0,0,0,0.08);
      transform: translateY(0);
      transition: all 0.3s ease;
      display: flex;
      flex-direction: column;
    }

    .facility-card:hover {
      transform: translateY(-10px);
      box-shadow: 0 20px 40px rgba(0,0,0,0.15);
    }

    .facility-card img {
      width: 100%;
      height: 200px;
      object-fit: cover;
      transition: transform 0.5s ease;
    }

    .facility-card:hover img {
      transform: scale(1.05);
    }

    .facility-card-content {
      padding: 20px;
      flex: 1;
      display: flex;
      flex-direction: column;
      justify-content: space-between;
    }

    .facility-card h3 {
      font-size: 22px;
      color: #1a202c;
      margin-bottom: 12px;
    }

    .facility-card p {
      font-size: 15px;
      color: #555;
      line-height: 1.6;
      margin-bottom: 15px;
    }

    .facility-card ul {
      padding-left: 20px;
      margin-bottom: 15px;
      color: #444;
    }

    .btn {
      background-color: #007bff;
      color: white;
      padding: 10px 16px;
      border-radius: 8px;
      text-align: center;
      font-weight: 600;
      transition: all 0.3s ease;
    }

    .btn:hover {
      background-color: #0056b3;
    }

    .back-btn {
      display: inline-block;
      background-color: #1a202c;
      color: white;
      padding: 12px 28px;
      border-radius: 8px;
      font-weight: 600;
      text-align: center;
      margin-top: 40px;
      transition: all 0.3s ease;
    }

    .back-btn:hover {
      background-color: #0d1117;
    }

    .footer {
      margin-top: 60px;
      text-align: center;
      font-size: 14px;
      color: #999;
    }

    /* Fade-in animation for cards */
    .facility-card {
      opacity: 0;
      transform: translateY(20px);
      animation: fadeInUp 0.6s forwards;
    }

    .facility-card:nth-child(1) { animation-delay: 0s; }
    .facility-card:nth-child(2) { animation-delay: 0.1s; }
    .facility-card:nth-child(3) { animation-delay: 0.2s; }
    .facility-card:nth-child(4) { animation-delay: 0.3s; }
    .facility-card:nth-child(5) { animation-delay: 0.4s; }
    .facility-card:nth-child(6) { animation-delay: 0.5s; }

    @keyframes fadeInUp {
      to {
        opacity: 1;
        transform: translateY(0);
      }
    }
  </style>
</head>
<body>

<div class="container">
  <h2>Browse Available Facilities</h2>
  <div class="facility-grid">

    <!-- System-defined priority facilities -->
    <?php foreach ($priorityNames as $name): ?>
      <?php if (isset($adminFacilities[$name])): ?>
        <?php $f = $adminFacilities[$name]; ?>
        <div class="facility-card">
          <img src="uploads/<?= htmlspecialchars($f['image']) ?>" alt="<?= htmlspecialchars($name) ?>">
          <div class="facility-card-content">
            <h3><?= htmlspecialchars($name) ?></h3>
            <p><?= htmlspecialchars($f['description']) ?></p>
            <?php if (!empty($f['slots'])): ?>
              <p><strong>Available Time Slots:</strong></p>
              <ul>
                <?php foreach ($f['slots'] as $slot): ?>
                  <li><?= htmlspecialchars($slot) ?></li>
                <?php endforeach; ?>
              </ul>
            <?php else: ?>
              <p>🕒 8:00 AM – 10:00 PM</p>
            <?php endif; ?>
            <a href="booking.php?facility=<?= urlencode($name) ?>" class="btn">Book Now</a>
          </div>
        </div>
        <?php unset($adminFacilities[$name]); ?>
      <?php else: ?>
        <div class="facility-card">
          <img src="uploads/<?= strtolower($name) ?>.jpg" alt="<?= htmlspecialchars($name) ?>">
          <div class="facility-card-content">
            <h3><?= htmlspecialchars($name) ?></h3>
            <p>Predefined facility by the system.</p>
            <p><strong>Available Time Slots:</strong></p>
            <ul>
              <?php foreach ($fixedFacilities[$name] as $slot): ?>
                <li><?= htmlspecialchars($slot) ?></li>
              <?php endforeach; ?>
            </ul>
            <a href="booking.php?facility=<?= urlencode($name) ?>" class="btn">Book Now</a>
          </div>
        </div>
      <?php endif; ?>
    <?php endforeach; ?>

    <!-- Admin-added facilities -->
    <?php foreach ($adminFacilities as $name => $f): ?>
      <div class="facility-card">
        <img src="uploads/<?= htmlspecialchars($f['image']) ?>" alt="<?= htmlspecialchars($name) ?>">
        <div class="facility-card-content">
          <h3><?= htmlspecialchars($name) ?></h3>
          <p><?= htmlspecialchars($f['description']) ?></p>
          <?php if (!empty($f['slots'])): ?>
            <p><strong>Available Time Slots:</strong></p>
            <ul>
              <?php foreach ($f['slots'] as $slot): ?>
                <li><?= htmlspecialchars($slot) ?></li>
              <?php endforeach; ?>
            </ul>
          <?php else: ?>
            <p>🕒 8:00 AM – 10:00 PM</p>
          <?php endif; ?>
          <a href="booking.php?facility=<?= urlencode($name) ?>" class="btn">Book Now</a>
        </div>
      </div>
    <?php endforeach; ?>

  </div>

  <div style="text-align: center;">
    <a href="dashboard.php" class="back-btn">Back to Dashboard</a>
  </div>
</div>



</body>
</html>
