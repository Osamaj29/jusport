<?php
$sport = $_GET['sport'] ?? 'unknown';

$details = [
  'tennis' => [
    'title' => 'Tennis Court',
    'desc' => '
      <p>Play on our professional-grade tennis courts, perfect for both beginners and advanced players.</p>
      <div class="features">
        <div>Evening lighting for night matches</div>
        <div>Certified coaches available upon request</div>
        <div>Shaded seating area for spectators</div>
        <div>Locker rooms with showers and changing space</div>
        <div>Available for friendly matches, coaching, or mini-tournaments</div>
      </div>',
    'img' => 'uploads/Tennis court.jpg',
  ],
  'football' => [
    'title' => 'Football Field',
    'desc' => '
      <p>Enjoy 11-a-side or full matches on a FIFA-approved artificial grass field, designed for both casual and competitive play.</p>
      <div class="features">
        <div>Night lighting for evening games</div>
        <div>Dedicated locker rooms with showers</div>
        <div>Spectator stands and seating</div>
        <div>On-site coaching and referee services available</div>
        <div>Bookable for training, tournaments, or weekend leagues</div>
      </div>',
    'img' => 'uploads/football court.jpg',
  ],
  'swimming' => [
    'title' => 'Swimming Pool',
    'desc' => '
      <p>Dive into our Olympic-size indoor swimming pool, ideal for all skill levels and events.</p>
      <div class="features">
        <div>Lane-based booking for lap swimming</div>
        <div>Private and group lessons available</div>
        <div>Temperature-controlled water</div>
        <div>Changing rooms and lockers</div>
        <div>Certified lifeguards on duty</div>
      </div>',
    'img' => 'uploads/swimming court.jpg',
  ]
];


$venue = $details[$sport] ?? null;
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title><?= $venue ? $venue['title'] : "Venue Not Found" ?></title>
  <style>
    * {
      box-sizing: border-box;
    }
    body {
      font-family: 'Inter', sans-serif;
      background: #f9f9f9;
      margin: 0;
      padding: 0;
      display: flex;
      justify-content: center;
      align-items: center;
      min-height: 100vh;
    }
    .container {
      max-width: 800px;
      width: 90%;
      background: #fff;
      padding: 30px;
      border-radius: 12px;
      box-shadow: 0 0 15px rgba(0,0,0,0.1);
      text-align: center;
    }
    img {
      width: 100%;
      max-height: 400px;
      object-fit: cover;
      border-radius: 10px;
      margin-bottom: 20px;
    }
    h1 {
      color: #002855;
      font-size: 28px;
      margin-bottom: 15px;
    }
    p {
      font-size: 16px;
      line-height: 1.7;
      color: #444;
    }
    .features {
      text-align: left;
      margin: 20px 0;
    }
    .features div {
      margin-bottom: 10px;
      position: relative;
      padding-left: 20px;
    }
    .features div::before {
      content: "•";
      position: absolute;
      left: 0;
      color: #ff6a00;
      font-weight: bold;
    }
    a.btn {
      display: inline-block;
      margin-top: 20px;
      background: #ff6a00;
      color: white;
      padding: 12px 22px;
      border-radius: 6px;
      text-decoration: none;
      font-weight: bold;
      transition: background 0.3s;
      margin-right: 10px;
    }
    a.btn:hover {
      background: #e55c00;
    }
    .btn-secondary {
      background: #444;
    }
    .btn-secondary:hover {
      background: #222;
    }
  </style>
</head>
<body>
  <div class="container">
    <?php if ($venue): ?>
      <img src="<?= $venue['img'] ?>" alt="<?= $venue['title'] ?>">
      <h1><?= $venue['title'] ?></h1>
      <?= $venue['desc'] ?>
      <a href="booking.php?sport=<?= $sport ?>" class="btn">Book Now</a>
      <a href="index.php" class="btn btn-secondary">Go Back Home</a>
    <?php else: ?>
      <h1>Venue Not Found</h1>
      <p>The sport or venue you're looking for doesn't exist.</p>
      <a href="index.php" class="btn">Back to Home</a>
    <?php endif; ?>
  </div>
</body>
</html>
