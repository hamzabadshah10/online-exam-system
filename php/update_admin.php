<?php
require_once '../config/db.php';

try {
    $email = 'admin@example.com';
    $password = 'admin123';
    $hash = password_hash($password, PASSWORD_BCRYPT);
    
    // Check if user exists
    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch();
    
    if ($user) {
        $pdo->query("UPDATE users SET password = '$hash' WHERE email = '$email'");
        echo "<h1>Success! Admin password updated to 'admin123'.</h1>";
    } else {
        $stmt = $pdo->prepare("INSERT INTO users (name, email, password, role) VALUES ('System Admin', ?, ?, 'admin')");
        $stmt->execute([$email, $hash]);
        echo "<h1>Success! Admin user created with password 'admin123'.</h1>";
    }
    
    echo "<p><a href='index.php'>Click here to go back to Login</a></p>";
    
} catch (Exception $e) {
    echo "<h1>Error</h1><p>" . $e->getMessage() . "</p>";
}
