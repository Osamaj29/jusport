<?php
session_start();
require_once "db.php";

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$current_date = date("Y-m-d");

// Fetch booking history with venue name
$sql = "
    SELECT b.id, b.booking_date, b.booking_time, b.status, f.name AS venue_name
    FROM bookings b
    JOIN facilities f ON b.venue = f.id
    WHERE b.user_id = ?
    ORDER BY b.booking_date DESC, b.booking_time DESC
";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$bookings = $result->fetch_all(MYSQLI_ASSOC);
$stmt->close();
$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0"/>
<title>Booking History | Jeddah University</title>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
<style>
body {
    font-family: 'Inter', sans-serif;
    background: linear-gradient(135deg, #002b5b, #005792);
    margin: 0;
    padding: 40px 20px;
    min-height: 100vh;
    display: flex;
    justify-content: center;
    align-items: flex-start;
}
.card {
    background: #fff;
    padding: 40px;
    border-radius: 14px;
    box-shadow: 0 8px 20px rgba(0,0,0,0.15);
    width: 950px;
    max-width: 95%;
}
h2 {
    text-align: center;
    color: #002855;
    margin-bottom: 30px;
    font-size: 2rem;
}
table {
    width: 100%;
    border-collapse: collapse;
    margin-top: 15px;
    border-radius: 10px;
    overflow: hidden;
    box-shadow: 0 4px 12px rgba(0,0,0,0.1);
}
th, td {
    padding: 14px 16px;
    border-bottom: 1px solid #ddd;
    text-align: center;
    font-size: 16px;
}
th {
    background: #002855;
    color: white;
    font-weight: 600;
}
tr:nth-child(even) {
    background-color: #f9f9f9;
}

/* Status colors (text only) */
.status-approved {
    color: green;
    font-weight: 700;
}
.status-rejected {
    color: red;
    font-weight: 700;
}
.status-pending {
    color: orange;
    font-weight: 700;
}
.status-completed {
    color: blue;
    font-weight: 700;
}
.status-default {
    color: black;
    font-weight: 700;
}

.message {
    text-align: center;
    margin: 20px 0;
    font-size: 16px;
    color: #555;
}

.btn {
    display: block;
    text-align: center;
    background: #002855;
    color: white;
    padding: 12px 20px;
    border-radius: 8px;
    text-decoration: none;
    font-weight: bold;
    margin: 25px auto 0;
    width: fit-content;
    transition: background-color 0.3s ease;
}
.btn:hover { background: #001b40; }

@media(max-width:600px){
    .card { padding: 25px; }
    table { font-size: 14px; }
    th, td { padding: 10px; }
}
</style>
</head>
<body>
<div class="card">
    <h2>Booking History</h2>

    <?php if (!empty($bookings)): ?>
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
          <?php foreach ($bookings as $booking): 
            $is_past = $booking['booking_date'] < $current_date;
            $status = strtolower($booking['status']);
            if ($is_past) {
                $statusClass = "status-completed";
                $statusLabel = "Completed";
            } else {
                $statusClass = match ($status) {
                    'approved' => 'status-approved',
                    'rejected' => 'status-rejected',
                    'pending'  => 'status-pending',
                    default    => 'status-default'
                };
                $statusLabel = ucfirst($booking['status']);
            }
          ?>
            <tr>
              <td><?= htmlspecialchars($booking['venue_name']) ?></td>
              <td><?= htmlspecialchars($booking['booking_date']) ?></td>
              <td><?= htmlspecialchars($booking['booking_time']) ?></td>
              <td class="<?= $statusClass ?>"><?= htmlspecialchars($statusLabel) ?></td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    <?php else: ?>
      <div class="message">No bookings found.</div>
    <?php endif; ?>

    <a href="dashboard.php" class="btn">← Back to Dashboard</a>
</div>
</body>
</html>
