
<?php
$host = 'localhost';           // Database host
$db_name = 'crime_report_db';     // Database name
$username = 'root';    // Database username
$password = '';    // Database password


// config_db.php
try {
    $conn = new PDO("mysql:host=localhost;dbname=crime_report_db", 'root', '');
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}
?>

