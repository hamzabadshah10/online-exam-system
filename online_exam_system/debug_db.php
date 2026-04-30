<?php
require_once 'config/db.php';

echo "<h2>Database Debug Information</h2>";

try {
    $stmt = $pdo->query("SELECT id, name, email, role, password_hash FROM users");
    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "<table border='1' cellpadding='10'>";
    echo "<tr><th>ID</th><th>Name</th><th>Email</th><th>Role</th><th>Hash (First 10 chars)</th><th>Verify 'admin123'</th></tr>";
    
    foreach ($users as $user) {
        $verify = password_verify('admin123', $user['password_hash']) ? "MATCH" : "FAIL";
        $hash_start = substr($user['password_hash'], 0, 10) . "...";
        echo "<tr>";
        echo "<td>{$user['id']}</td>";
        echo "<td>{$user['name']}</td>";
        echo "<td>{$user['email']}</td>";
        echo "<td>{$user['role']}</td>";
        echo "<td>$hash_start</td>";
        echo "<td>$verify</td>";
        echo "</tr>";
    }
    echo "</table>";

} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
?>
