<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$conn = new mysqli("localhost", "root", "", "sport_booking");
if ($conn->connect_error) die("Connection failed: " . $conn->connect_error);

// Fetch all bookings with venue name
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
<title>Booking Management | Jeddah University</title>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
<style>
body {
    font-family: 'Inter', sans-serif;
    background: linear-gradient(135deg, #002b5b, #005792);
    margin: 0;
    padding: 30px;
    min-height: 100vh;
    display: flex;
    justify-content: center;
    align-items: flex-start;
}
.card {
    background: #fff;
    padding: 30px;
    border-radius: 12px;
    box-shadow: 0 6px 16px rgba(0,0,0,0.1);
    width: 800px;
    max-width: 95%;
}
h2 {
    text-align: center;
    color: #002855;
    margin-bottom: 25px;
}
table {
    width: 100%;
    border-collapse: collapse;
    margin-top: 15px;
}
th, td {
    padding: 12px;
    border-bottom: 1px solid #ddd;
    text-align: center;
    font-size: 15px;
}
th {
    background: #002855;
    color: white;
    font-weight: 600;
}
.status.approved { color:#27ae60; font-weight:bold; }
.status.pending { color:#f39c12; font-weight:bold; }
.status.rejected { color:#c0392b; font-weight:bold; }

.action-btn {
    padding: 6px 12px;
    border: none;
    border-radius: 8px;
    color: white;
    font-weight: bold;
    cursor: pointer;
    margin: 2px;
    text-decoration: none;
    font-size: 14px;
}
.reschedule { background: #0056b3; }
.reschedule:hover { background: #004080; }
.cancel { background: #c0392b; }
.cancel:hover { background: #992d22; }

.back-btn {
    display: block;
    text-align: center;
    background-color: #002855;
    color: white;
    padding: 12px;
    text-decoration: none;
    border-radius: 8px;
    font-weight: bold;
    margin: 25px auto 0;
    width: fit-content;
    transition: background-color 0.3s ease;
}
.back-btn:hover { background-color: #001b40; }

@media(max-width:600px){
    .card { padding: 20px; }
    table { font-size: 14px; }
}
</style>
</head>
<body>
<div class="card">
    <h2>Booking Management</h2>

    <?php if (!empty($bookings)): ?>
    <table>
        <thead>
            <tr>
                <th>Facility</th>
                <th>Date</th>
                <th>Time</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach($bookings as $b): ?>
            <tr>
                <td><?= htmlspecialchars($b['venue_name']) ?></td>
                <td><?= htmlspecialchars($b['booking_date']) ?></td>
                <td><?= htmlspecialchars($b['booking_time']) ?></td>
                <td>
                    <a class="action-btn reschedule" href="reschedule.php?booking_id=<?= $b['id'] ?>">Reschedule</a>
                    <a class="action-btn cancel" href="cancel_booking.php?booking_id=<?= $b['id'] ?>">Cancel</a>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    <?php else: ?>
      <div style="text-align:center; margin:20px; color:#555;">No bookings found.</div>
    <?php endif; ?>

    <a href="dashboard.php" class="back-btn">← Back to Dashboard</a>
</div>
</body>
</html>
