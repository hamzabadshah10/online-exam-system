<?php
require_once '../config/db.php';
try {
    $q = $pdo->query("DESCRIBE results");
    $columns = $q->fetchAll(PDO::FETCH_ASSOC);
    echo "<h3>Results Table Columns:</h3><pre>";
    print_r($columns);
    echo "</pre>";
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
?>
