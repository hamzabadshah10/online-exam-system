<?php
require_once 'config/db.php';

try {
    $email = 'admin@example.com';
    $password = 'admin123';
    $hash = password_hash($password, PASSWORD_BCRYPT);
    
    // 1. Ensure the 'users' table has the correct column name
    // Some scripts used 'password', some used 'password_hash'. We will ensure it's 'password_hash'.
    try {
        $pdo->query("ALTER TABLE users CHANGE password password_hash VARCHAR(255)");
    } catch(Exception $e) {
        // Column might already be named correctly
    }

    // 2. Re-create the admin user to be 100% sure
    $pdo->prepare("DELETE FROM users WHERE email = ?")->execute([$email]);
    $stmt = $pdo->prepare("INSERT INTO users (name, email, password_hash, role) VALUES ('System Admin', ?, ?, 'admin')");
    $stmt->execute([$email, $hash]);

    // 3. Manually set the session to log you in right now
    $stmt = $pdo->prepare("SELECT id, name, role FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['name'] = $user['name'];
        $_SESSION['role'] = $user['role'];
        
        echo "<h1 style='color:green; text-align:center;'>Repair Successful! Redirecting to Dashboard...</h1>";
        echo "<script>setTimeout(function(){ window.location.href = 'admin/dashboard.php'; }, 1000);</script>";
    } else {
        echo "<h1>Critical Error: Could not create admin session.</h1>";
    }

} catch (Exception $e) {
    echo "<h1>Error during repair:</h1><p>" . $e->getMessage() . "</p>";
}
?>
