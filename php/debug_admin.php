<?php
require_once '../config/db.php';
$stmt = $pdo->query("SELECT * FROM users WHERE role='admin'");
$admin = $stmt->fetch(PDO::FETCH_ASSOC);
if ($admin) {
    echo "Admin Found: " . $admin['email'] . "<br>";
    echo "Hash: " . $admin['password_hash'] . "<br>";
} else {
    echo "Admin NOT found in database!";
}
?>
