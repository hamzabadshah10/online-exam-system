<?php
require_once 'C:\wamp64\www\web_project_lab\online_exam_system\config\db.php';
try {
    $pdo->query("ALTER TABLE exams ADD COLUMN subject VARCHAR(255) AFTER title");
    echo "Column added successfully.\n";
} catch (Exception $e) {
    echo "Notice: " . $e->getMessage() . "\n";
}
