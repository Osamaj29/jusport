<?php
session_start();
if (!isset($_SESSION['user_id']) || !isset($_SESSION['recent_bookings'])) {
  header("Location: facilities.php");
  exit();
}

$recent_bookings = $_SESSION['recent_bookings'];
unset($_SESSION['recent_bookings']); // Clear after showing
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Your Booking Confirmation</title>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet" />
  <style>
    body {
      font-family: 'Inter', sans-serif;
      background: #f4f4f4;
      margin: 0;
      padding: 0;
    }

    .container {
      max-width: 800px;
      margin: 50px auto;
      background: white;
      padding: 30px;
      border-radius: 10px;
      box-shadow: 0 4px 12px rgba(0,0,0,0.1);
    }

    h1 {
      color: #002855;
      text-align: center;
      margin-bottom: 30px;
    }

    table {
      width: 100%;
      border-collapse: collapse;
      margin-bottom: 30px;
    }

    th, td {
      border: 1px solid #ddd;
      padding: 12px;
      text-align: center;
    }

    th {
      background-color: #002855;
      color: white;
    }

    .btn {
      display: inline-block;
      padding: 10px 20px;
      background-color: #f60;
      color: white;
      text-decoration: none;
      border-radius: 5px;
      text-align: center;
    }

    .btn:hover {
      background-color: #e05500;
    }

    .footer {
      text-align: center;
      margin-top: 40px;
      color: #666;
    }
  </style>
</head>
<body>

<div class="container">
  <h1>Booking Confirmation</h1>

  <table>
    <thead>
      <tr>
        <th>Facility</th>
        <th>Date</th>
        <th>Time</th>
        <th>Status</th>
      </tr>
    </thead>
    <tbody>
      <?php foreach ($recent_bookings as $booking): ?>
        <tr>
          <td><?= htmlspecialchars($booking['facility']) ?></td>
          <td><?= htmlspecialchars($booking['date']) ?></td>
          <td><?= htmlspecialchars($booking['time']) ?></td>
          <td>Pending</td>
        </tr>
      <?php endforeach; ?>
    </tbody>
  </table>

  <a href="dashboard.php" class="btn">Back to Dashboard</a>
</div>

<div class="footer">
  &copy; 2025 Jeddah Universiti Sport Facilities
</div>

</body>
</html>
