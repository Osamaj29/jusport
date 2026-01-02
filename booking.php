<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// DB connection
$conn = new mysqli("localhost", "root", "", "sport_booking");
if ($conn->connect_error) die("Connection failed: " . $conn->connect_error);

// Fetch all venues
$venues = [];
$result = $conn->query("SELECT id, name FROM facilities");
while ($row = $result->fetch_assoc()) {
    $venues[] = $row;
}
$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0"/>
<title>Book Venue - Jeddah University</title>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet" />
<style>
body {
  font-family: 'Inter', sans-serif;
  margin: 0;
  display: flex;
  min-height: 100vh;
  background: #f4f4f4;
}
.sidebar {
  width: 220px;
  background: #002855;
  color: white;
  padding: 20px;
}
.sidebar h2 {
  font-size: 20px;
  margin-bottom: 10px;
}
.sidebar ul {
  list-style: none;
  padding: 0;
}
.sidebar ul li {
  margin: 30px 0;
}
.sidebar ul li a {
  color: white;
  text-decoration: none;
  font-weight: 600;
}
.main-content {
  flex: 1;
  padding: 40px;
  background: #fff;
}
h1 {
  color: #002855;
}
form {
  margin-top: 30px;
  max-width: 600px;
}
label {
  display: block;
  margin-bottom: 5px;
  font-weight: 600;
}
input, select {
  width: 100%;
  padding: 12px;
  margin-bottom: 20px;
  border-radius: 6px;
  border: 1px solid #ccc;
  font-size: 15px;
  box-sizing: border-box;
  height: 45px; /* Ensures equal height for input & select */
}
button {
  background: #f60;
  color: white;
  padding: 12px 20px;
  border: none;
  border-radius: 6px;
  font-size: 16px;
  cursor: pointer;
}
button:hover {
  background: #e05500;
}
.back-btn {
  display: inline-block;
  background: #002855;
  color: white;
  padding: 12px 24px;
  text-decoration: none;
  border-radius: 6px;
  font-weight: bold;
  box-shadow: 0 2px 6px rgba(0,0,0,0.1);
  margin-right: 10px;
}
</style>
</head>
<body>
<div class="sidebar">
  <h2>Quick Links</h2>
  <ul>
    <li><a href="dashboard.php">Dashboard</a></li>
    <li><a href="facilities.php">All Facilities</a></li>
    <li><a href="logout.php">Logout</a></li>
  </ul>
</div>
<div class="main-content">
  <h1>Booking Details</h1>
  <form method="POST" action="process_booking.php">
    <label for="venue">Select Venue</label>
    <select name="venue" id="venue" required>
      <option value="">-- Choose a venue --</option>
      <?php foreach($venues as $v): ?>
      <option value="<?= $v['id'] ?>"><?= htmlspecialchars($v['name']) ?></option>
      <?php endforeach; ?>
    </select>

    <label for="date">Booking Date</label>
    <input type="date" name="date" id="date" required min="<?= date('Y-m-d') ?>">

    <label for="time">Booking Time</label>
    <input type="time" name="time" id="time" required min="08:00" max="22:00" step="1800">

    <label for="duration">Duration (hours)</label>
    <select name="duration" id="duration" required>
      <option value="1">1 hour</option>
      <option value="2">2 hours</option>
      <option value="3">3 hours</option>
    </select>

    <a href="index.php" class="back-btn">🏠 Back to Home</a>
    <button type="submit">Confirm Booking</button>
  </form>
</div>
</body>
</html>
