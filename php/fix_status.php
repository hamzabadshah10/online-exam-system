<?php
require_once '../config/db.php';
try {
    $stmt = $pdo->prepare("UPDATE exams SET status = 'completed' WHERE title LIKE ? AND exam_date = ?");
    $stmt->execute(['%Mid Term%', '2026-05-27']);
    echo "SUCCESS: Updated " . $stmt->rowCount() . " exams.";
} catch (Exception $e) {
    echo "ERROR: " . $e->getMessage();
}
unlink(__FILE__);
?>
