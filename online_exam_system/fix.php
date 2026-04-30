<?php
// fix.php
require_once 'config/db.php';

try {
    $hash = password_hash('admin123', PASSWORD_BCRYPT);
    $pdo->query("UPDATE users SET password_hash = '$hash' WHERE email = 'admin@example.com'");
    echo "<div style='font-family:sans-serif; text-align:center; padding-top:50px;'>";
    echo "<h1 style='color:green;'>System Override Successful!</h1>";
    echo "<h3>Admin password has been reset to: <strong>admin123</strong></h3>";
    echo "<a href='index.php' style='display:inline-block; padding:10px 20px; background:blue; color:white; text-decoration:none; border-radius:5px;'>Click to Login</a>";
    echo "</div>";
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
