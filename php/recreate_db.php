<?php
/**
 * RECREATE DATABASE SCRIPT
 * WARNING: This will DELETE all existing exams, students, and results!
 */
$host = 'localhost';
$username = 'root';
$password = '';
$dbName = 'online_exam_db';

try {
    // Connect without DB selected
    $pdo = new PDO("mysql:host=$host;charset=utf8mb4", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    echo "Cleaning up existing database...<br>";
    $pdo->exec("DROP DATABASE IF EXISTS `$dbName` ");
    
    echo "Creating fresh database...<br>";
    $pdo->exec("CREATE DATABASE `$dbName` ");
    $pdo->exec("USE `$dbName` ");

    echo "Applying schema from database.sql...<br>";
    $sql = file_get_contents('database.sql');
    
    // Remove the CREATE DATABASE/USE lines from SQL string if they exist to avoid conflicts
    // (Though our DROP/CREATE above is handled, executing the full SQL is fine if it matches)
    $pdo->exec($sql);

    echo "<h2 style='color: green;'>Database recreated successfully!</h2>";
    echo "<p>Admin Login: <b>admin@example.com</b> / <b>admin123</b></p>";
    echo "<a href='index.php'>Go to Login Page</a>";

} catch(PDOException $e) {
    echo "<h2 style='color: red;'>Critical Error:</h2> " . $e->getMessage();
}
?>
