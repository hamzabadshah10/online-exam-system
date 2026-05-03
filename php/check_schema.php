<?php
require_once '../config/db.php';

try {
    $q = $pdo->query("DESCRIBE users");
    $columns = $q->fetchAll(PDO::FETCH_ASSOC);
    echo "<h3>Users Table Columns:</h3><pre>";
    print_r($columns);
    echo "</pre>";
    
    $q = $pdo->query("SELECT * FROM users WHERE email='admin@example.com'");
    $user = $q->fetch(PDO::FETCH_ASSOC);
    echo "<h3>Admin User Data:</h3><pre>";
    print_r($user);
    echo "</pre>";

} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
?>
