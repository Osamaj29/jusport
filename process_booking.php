<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$venue_id = $_POST['venue'] ?? '';
$date = $_POST['date'] ?? '';
$time = $_POST['time'] ?? '';
$duration = $_POST['duration'] ?? '';

if (!$venue_id || !$date || !$time || !$duration) {
    header("Location: booking.php");
    exit();
}

$conn = new mysqli("localhost", "root", "", "sport_booking");
if ($conn->connect_error) die("Connection failed: " . $conn->connect_error);

$stmt = $conn->prepare("INSERT INTO bookings (user_id, venue, booking_date, booking_time, duration, status) VALUES (?, ?, ?, ?, ?, 'pending')");
$stmt->bind_param("issss", $user_id, $venue_id, $date, $time, $duration);
$stmt->execute();
$stmt->close();
$conn->close();

header("Location: my_orders.php");
exit();
?>
