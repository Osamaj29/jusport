<?php
// config.php
// Database
$dbHost = 'localhost';
$dbUser = 'root';
$dbPass = '';
$dbName = 'sport_booking';
$conn = new mysqli($dbHost, $dbUser, $dbPass, $dbName);
if ($conn->connect_error) { die("DB conn error: " . $conn->connect_error); }

// Site / mail settings
define('SITE_URL', 'http://localhost/SportFacilities'); // change for production
define('SMTP_HOST','smtp.gmail.com');
define('SMTP_USER','osama0557562398@gmail.com');       // your sender email
define('SMTP_PASS','pdbe qeeq fexw fena');    // use app password if Gmail
define('SMTP_PORT',587);
