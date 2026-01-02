<?php
session_start();
require_once 'db.php';

// Priority display order
$priorityOrder = ['Football', 'Basketball', 'Futsal', 'Swimming', 'Pickleball', 'Tennis'];

// Fetch all facilities with their slots
$facilities = [];
$sql = "SELECT * FROM facilities";
$result = $conn->query($sql);

if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $facility_id = $row['id'];
        $slot_query = $conn->prepare("SELECT slot_time FROM facility_slots WHERE facility_id = ?");
        $slot_query->bind_param("i", $facility_id);
        $slot_query->execute();
        $slot_result = $slot_query->get_result();
        $slots = [];
        while ($slot_row = $slot_result->fetch_assoc()) {
            $slots[] = $slot_row['slot_time'];
        }
        if (empty($slots)) $slots = ["8:00 AM - 10:00 PM"];
        $row['slots'] = $slots;
        $facilities[] = $row;
    }
}

// Sort facilities by priority
usort($facilities, function($a, $b) use ($priorityOrder) {
    $posA = array_search($a['name'], $priorityOrder);
    $posB = array_search($b['name'], $priorityOrder);
    $posA = $posA !== false ? $posA : 999;
    $posB = $posB !== false ? $posB : 999;
    return $posA <=> $posB;
});
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0"/>
<title>Sports Venues | Jeddah University</title>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet"/>
<style>
* { box-sizing: border-box; margin:0; padding:0; }
body { font-family: 'Inter', sans-serif; background-color: #f4f4f4; color: #333; }

/* Header & Navbar */
.header-bar { background: #002b5b; color: white; padding: 10px 30px; display:flex; justify-content: space-between; align-items:center; flex-wrap:wrap; }
.header-bar h1 { font-size: 18px; font-weight:600; }

.navbar { background: rgba(228,228,228,0.71); }
.navbar .container { display:flex; justify-content:center; align-items:center; flex-wrap:wrap; padding:10px 30px; }
nav { display:flex; flex-wrap:wrap; gap:16px; }

/* Normal navbar links */
nav a { 
    color:black; 
    text-decoration:none; 
    font-weight:600; 
    padding:20px 10px; 
    transition:all 0.3s ease; 
}
nav a:hover { 
    background-color: rgba(79,150,216,0.86); 
    border-radius:4px; 
}

/* Navbar buttons fit text exactly and center text */
nav a.btn,
nav a.btn-secondary {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    border-radius: 4px;
    padding: 4px 12px;     /* fits text */
    font-size: 13px;
    white-space: nowrap;
    text-align: center;
    transition: all 0.3s ease;
}

/* Primary button */
nav a.btn {
    background: orange;
    color: white;
}
nav a.btn:hover {
    transform: scale(1.05);
}

/* Secondary button (Logout/Register) */
nav a.btn-secondary {
    background: gray;
    color: white;
    border: 1px solid #555;
}
nav a.btn-secondary:hover {
    transform: scale(1.05);
}

/* Container */
.container { max-width:1200px; margin:0 auto; padding:2px 1px; }

/* Intro Box */
.intro-box {
    background: #9eb3d2ff;
    padding: 20px 15px;
    text-align:center;
    border-radius:20px;
    box-shadow:0 4px 12px rgba(0,0,0,0.1);
    margin-bottom: 40px;
    opacity:0;
    transform: translateY(-20px);
    animation: fadeInDown 1s ease forwards;
}
.intro-box .icon { font-size: 40px; margin-bottom:10px; }
.intro-box h1 { font-size:2rem; color:#002855; margin-bottom:8px; }
.intro-box p { font-size:1rem; color:#002855; }

/* Facilities Grid */
.sports-grid { display:grid; grid-template-columns: repeat(3, 1fr); gap:15px; }
.sport-card {
    display:flex; flex-direction:column; 
    background:#fff; border-radius:10px; overflow:hidden;
    box-shadow:0 3px 8px rgba(0,0,0,0.1); 
    padding:15px; text-align:center;
    min-height: 420px;
    opacity:0; transform: translateY(20px); 
    animation: fadeInUp 0.7s ease forwards;
}
.sport-card img { width:100%; height:200px; object-fit:cover; border-radius:8px; margin-bottom:12px; }
.sport-card h3 { font-size:1.2rem; color:#002855; margin-bottom:8px; }
.sport-card p { font-size:0.95rem; color:#555; margin-bottom:10px; flex-grow:1; }
.sport-card ul { list-style:none; padding:0; margin:0 0 10px 0; }
.sport-card ul li { font-size:0.85rem; color:#333; margin-bottom:3px; }

/* Book Now button at bottom */
.book-btn { 
    display:inline-block; 
    background:#f60; 
    color:white; 
    padding:8px 14px; 
    text-decoration:none; 
    border-radius:6px; 
    font-weight:600; 
    font-size:13px; 
    margin-top:auto; 
    transition:background 0.3s ease; 
}
.book-btn:hover { background:#e55b00; }

@keyframes fadeInDown { from { opacity:0; transform: translateY(-20px); } to { opacity:1; transform: translateY(0); } }
@keyframes fadeInUp { from { opacity:0; transform: translateY(20px); } to { opacity:1; transform: translateY(0); } }

/* Footer */
.footer { background:#002b5b; color:#fff; text-align:center; padding:15px 0; margin-top:40px; }

/* Responsive */
@media(max-width:768px){
    .navbar .container { flex-direction:column; align-items:flex-start; }
    nav { justify-content:flex-start; gap:10px; margin-top:10px; }
    .header-bar { flex-direction:column; align-items:flex-start; gap:10px; }
    .sports-grid { grid-template-columns: 1fr; }
}
</style>
</head>
<body>

<!-- Header -->
<div class="header-bar">
    <h1>Jeddah University Sport Facilities Booking System</h1>
</div>

<!-- Navbar -->
<header class="navbar">
    <div class="container">
        <nav>
            <a href="index.php">Home</a>
            <a href="facilities.php">Facilities</a>
            <a href="about.php">About</a>
            <a href="contact.php">Contact</a>
            <a href="dashboard.php">Dashboard</a>
            <?php if(isset($_SESSION['user_id'])): ?>
                <a href="logout.php" class="btn btn-secondary">Logout</a>
            <?php else: ?>
                <a href="login.php" class="btn">Login</a>
                <a href="register.php" class="btn btn-secondary">Register</a>
            <?php endif; ?>
        </nav>
    </div>
</header>

<!-- Intro -->
<section class="container">
    <div class="intro-box">
        <div class="icon">🏟️</div>
        <h1>Our Sports Venues</h1>
        <p>Explore the sports available for booking at Jeddah University.</p>
    </div>

    <!-- Popular Sports -->
    <div class="popular-sports">
        <h2 style="text-align:center; margin-bottom:20px;">Popular Sports</h2>
        <div class="sports-grid">
            <?php if(!empty($facilities)): ?>
                <?php foreach($facilities as $facility): ?>
                    <div class="sport-card">
                        <img src="uploads/<?= htmlspecialchars($facility['image']) ?>" alt="<?= htmlspecialchars($facility['name']) ?>">
                        <h3><?= htmlspecialchars($facility['name']) ?></h3>
                        <p><?= htmlspecialchars($facility['description']) ?></p>
                        <ul>
                            <?php foreach($facility['slots'] as $slot): ?>
                                <li>🕒 <?= htmlspecialchars($slot) ?></li>
                            <?php endforeach; ?>
                        </ul>
                        <a href="booking.php?facility=<?= urlencode($facility['name']) ?>" class="book-btn">Book Now</a>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p style="text-align:center;">No facilities available at the moment.</p>
            <?php endif; ?>
        </div>
    </div>
</section>

<!-- Footer -->
<div class="footer">
    &copy; 2025 Jeddah University Sport Facilities Booking System
</div>

</body>
</html>
