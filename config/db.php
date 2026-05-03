<?php
// config/db.php
session_start();
$host = 'localhost';
$dbname = 'online_exam_db';
$username = 'root';
$password = ''; // Default WAMP password

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    // Set PDO error mode to exception
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Auto-Sync Exam Statuses based on Time
    if (!function_exists('syncExamStatuses')) {
        function syncExamStatuses($pdo) {
            // 1. Mark as 'completed' if currentTime > (startTime + duration)
            $pdo->query("UPDATE exams 
                         SET status = 'completed' 
                         WHERE NOW() > DATE_ADD(CONCAT(exam_date, ' ', start_time), INTERVAL duration_minutes MINUTE) 
                         AND status != 'completed'");

            // 2. Mark as 'active' if currentTime is between startTime and (startTime + duration)
            $pdo->query("UPDATE exams 
                         SET status = 'active' 
                         WHERE NOW() BETWEEN CONCAT(exam_date, ' ', start_time) AND DATE_ADD(CONCAT(exam_date, ' ', start_time), INTERVAL duration_minutes MINUTE) 
                         AND status != 'active'");
        }
    }
    
    // Run sync on every global database request
    syncExamStatuses($pdo);

    // ── Remember Me Restoration ───────────────────────────────────────────
    if (!isset($_SESSION['user_id']) && isset($_COOKIE['remember_user'])) {
        $stmt_rem = $pdo->prepare("SELECT id, name, role FROM users WHERE id = ?");
        $stmt_rem->execute([$_COOKIE['remember_user']]);
        $rem_user = $stmt_rem->fetch(PDO::FETCH_ASSOC);
        if ($rem_user) {
            $_SESSION['user_id'] = $rem_user['id'];
            $_SESSION['name'] = $rem_user['name'];
            $_SESSION['role'] = $rem_user['role'];
        }
    }

} catch(PDOException $e) {
    die("Database Connection failed: " . $e->getMessage());
}
?>
