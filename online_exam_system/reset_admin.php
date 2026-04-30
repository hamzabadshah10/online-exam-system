<?php
require_once 'config/db.php';

try {
    $email = 'admin@example.com';
    $password = 'admin123';
    $hash = password_hash($password, PASSWORD_BCRYPT);
    
    // 1. Delete existing admin to avoid conflicts
    $pdo->prepare("DELETE FROM users WHERE email = ?")->execute([$email]);
    
    // 2. Insert fresh admin record
    $stmt = $pdo->prepare("INSERT INTO users (name, email, password_hash, role) VALUES ('System Admin', ?, ?, 'admin')");
    $stmt->execute([$email, $hash]);
    
    echo "<div style='font-family:sans-serif; text-align:center; padding:50px;'>";
    echo "<h1 style='color:blue;'>Admin Account Hard Reset Successful!</h1>";
    echo "<p><strong>Email:</strong> $email</p>";
    echo "<p><strong>Password:</strong> $password</p>";
    echo "<br><a href='index.php' style='background:blue; color:white; padding:10px 20px; text-decoration:none; border-radius:5px;'>Go to Login Page</a>";
    echo "</div>";

} catch (Exception $e) {
    echo "<h1>Error</h1><p>" . $e->getMessage() . "</p>";
}
?>
