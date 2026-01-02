<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$conn = new mysqli("localhost", "root", "", "sport_booking");
if ($conn->connect_error) die("Connection failed: " . $conn->connect_error);

// Get booking_id from URL
$booking_id = $_GET['booking_id'] ?? 0;
$booking_id = intval($booking_id);

// Fetch booking details with venue name and user name
$stmt = $conn->prepare("
    SELECT b.booking_date, b.booking_time, b.duration, b.status, f.name AS venue_name, u.name AS user_name
    FROM bookings b
    JOIN facilities f ON b.venue = f.id
    JOIN users u ON b.user_id = u.id
    WHERE b.id = ? AND b.user_id = ?
");
$stmt->bind_param("ii", $booking_id, $user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    die("Booking not found.");
}

$booking = $result->fetch_assoc();
$stmt->close();
$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0"/>
<title>Booking Summary | Jeddah University</title>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
<style>
body {
    font-family:'Inter',sans-serif;
    background: linear-gradient(135deg, #002b5b, #005792);
    margin:0;
    padding:30px;
    box-sizing:border-box;
    min-height:100vh;
    display:flex;
    justify-content:center;
    align-items:center;
}
.card {
    background:#fff;
    padding:30px;
    border-radius:12px;
    box-shadow:0 6px 16px rgba(0,0,0,0.1);
    width:420px;
}
h2 {
    text-align:center;
    color:#002855;
    margin-bottom:25px;
}
.info {
    margin:12px 0;
    font-size:16px;
}
.info strong {
    display:inline-block;
    width:120px;
    color:#002855;
}
.status {
    font-weight:bold;
    text-transform:capitalize;
}
.status.approved { color:#27ae60; }
.status.pending { color:#f39c12; }
.status.rejected { color:#c0392b; }
.btn {
    display:block;
    text-align:center;
    background:#002855;
    color:white;
    padding:12px;
    border-radius:8px;
    text-decoration:none;
    font-weight:bold;
    margin-top:20px;
    transition: background-color 0.3s ease;
}
.btn:hover { background:#001b40; }

/* Optional: responsive */
@media(max-width:480px){
    .card{width:90%; padding:20px;}
    .info strong{width:100px;}
}
</style>
</head>
<body>
<div class="card">
    <h2>Booking Summary</h2>
    <div class="info"><strong>Name:</strong> <?= htmlspecialchars($booking['user_name']) ?></div>
    <div class="info"><strong>Facility:</strong> <?= htmlspecialchars($booking['venue_name']) ?></div>
    <div class="info"><strong>Date:</strong> <?= htmlspecialchars($booking['booking_date']) ?></div>
    <div class="info"><strong>Time:</strong> <?= htmlspecialchars($booking['booking_time']) ?></div>
    <div class="info"><strong>Duration:</strong> <?= htmlspecialchars($booking['duration']) ?> hours</div>
    <div class="info"><strong>Status:</strong> 
        <span class="status <?= strtolower($booking['status']) ?>"><?= ucfirst($booking['status']) ?></span>
    </div>
    <a href="my_orders.php" class="btn">← Back to My Bookings</a>
</div>
</body>
</html>
