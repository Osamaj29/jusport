<?php
session_start();
require_once "db.php";

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$booking_id = intval($_GET['id'] ?? 0);
$success = '';
$error = '';

// Fetch booking and verify ownership
$stmt = $conn->prepare("SELECT id, facility_name AS facility, booking_date AS date, booking_time AS time 
                        FROM bookings 
                        WHERE id = ? AND user_id = ?");
$stmt->bind_param("ii", $booking_id, $user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows !== 1) {
    die("Booking not found or unauthorized access.");
}

$booking = $result->fetch_assoc();

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $new_date = $_POST['new_date'] ?? '';
    $new_time = $_POST['new_time'] ?? '';

    if ($new_date && $new_time) {
        $stmt = $conn->prepare("UPDATE bookings SET booking_date = ?, booking_time = ? WHERE id = ? AND user_id = ?");
        $stmt->bind_param("ssii", $new_date, $new_time, $booking_id, $user_id);
        if ($stmt->execute()) {
            $success = "Booking rescheduled successfully.";
            $booking['date'] = $new_date;
            $booking['time'] = $new_time;
        } else {
            $error = "Failed to reschedule. Please try again.";
        }
    } else {
        $error = "Please provide both a new date and time.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Reschedule Booking</title>
  <style>
    body {
      font-family: 'Inter', sans-serif;
      background-color: #f0f0f0;
      padding: 40px;
    }

    .container {
      background: white;
      max-width: 600px;
      margin: auto;
      padding: 30px;
      border-radius: 10px;
      box-shadow: 0 0 12px rgba(0,0,0,0.1);
    }

    h1 {
      color: #002855;
      margin-bottom: 20px;
    }

    label {
      display: block;
      margin-top: 15px;
      font-weight: 600;
    }

    input[type="date"],
    input[type="time"] {
      width: 100%;
      padding: 10px;
      margin-top: 5px;
      border-radius: 6px;
      border: 1px solid #ccc;
    }

    .btn {
      display: inline-block;
      margin-top: 20px;
      background-color: #f60;
      color: white;
      padding: 10px 20px;
      border-radius: 6px;
      text-decoration: none;
      border: none;
      cursor: pointer;
    }

    .btn:hover {
      background-color: #e05500;
    }

    .message {
      padding: 10px;
      margin-top: 15px;
      border-radius: 6px;
    }

    .success {
      background-color: #d4edda;
      color: #155724;
    }

    .error {
      background-color: #f8d7da;
      color: #721c24;
    }

    .footer-link {
      display: inline-block;
      margin-top: 20px;
      text-decoration: none;
      color: #002855;
    }
  </style>
</head>
<body>
  <div class="container">
    <h1>Reschedule Booking</h1>
    <p><strong>Facility:</strong> <?= htmlspecialchars($booking['facility']) ?></p>
    <p><strong>Current Date:</strong> <?= htmlspecialchars($booking['date']) ?></p>
    <p><strong>Current Time:</strong> <?= htmlspecialchars($booking['time']) ?></p>

    <?php if ($success): ?>
      <div class="message success"><?= $success ?></div>
    <?php elseif ($error): ?>
      <div class="message error"><?= $error ?></div>
    <?php endif; ?>

    <form method="POST">
      <label for="new_date">New Date:</label>
      <input type="date" name="new_date" id="new_date" required>

      <label for="new_time">New Time:</label>
      <input type="time" name="new_time" id="new_time" required>

      <button type="submit" class="btn">Submit Changes</button>
    </form>

    <a href="booking_management.php" class="footer-link">← Back to Booking Management</a>
  </div>
</body>
</html>
