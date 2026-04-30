<?php
require_once 'config/db.php';
try {
    $pdo->exec("ALTER TABLE results ADD COLUMN responses TEXT NULL");
    echo "Column 'responses' successfully added to 'results' table.";
} catch (Exception $e) {
    if (strpos($e->getMessage(), 'Duplicate column') !== false) {
        echo "Column 'responses' already exists.";
    } else {
        echo "Error: " . $e->getMessage();
    }
}
