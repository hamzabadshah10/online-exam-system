<?php
// api/csv_parser.php
require_once '../config/db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    http_response_code(403);
    exit("Unauthorized");
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['csv_file'])) {
    $exam_id = (int)$_POST['exam_id'];
    
    if (empty($exam_id)) {
        $_SESSION['error'] = "You must select a target exam first.";
    } else {
        $file = $_FILES['csv_file']['tmp_name'];
        if (($handle = fopen($file, "r")) !== FALSE) {
            // Skip header row
            fgetcsv($handle, 1000, ",");
            
            $insertedCount = 0;
            $total_marks = 0; // Assuming 1 mark per question for calculation
            
            try {
                $pdo->beginTransaction();
                $stmt = $pdo->prepare("INSERT INTO questions (exam_id, question_text, option_a, option_b, option_c, option_d, correct_option) VALUES (?, ?, ?, ?, ?, ?, ?)");
                
                while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
                    // Ensure array has 6 specific columns: Question, A, B, C, D, Correct
                    if (count($data) >= 6) {
                        $qText = trim($data[0]);
                        $oA = trim($data[1]);
                        $oB = trim($data[2]);
                        $oC = trim($data[3]);
                        $oD = trim($data[4]);
                        $correct = strtoupper(trim($data[5])); // A, B, C, or D
                        
                        if (!empty($qText) && in_array($correct, ['A','B','C','D'])) {
                            $stmt->execute([$exam_id, $qText, $oA, $oB, $oC, $oD, $correct]);
                            $insertedCount++;
                            $total_marks += 1;
                        }
                    }
                }
                
                // Update the exams table to reflect total marks (1 mark per question uploaded)
                $stmtUpdate = $pdo->prepare("UPDATE exams SET total_marks = (total_marks + ?) WHERE id = ?");
                $stmtUpdate->execute([$total_marks, $exam_id]);
                
                $pdo->commit();
                fclose($handle);
                
                $_SESSION['success'] = "Successfully uploaded {$insertedCount} MCQs into the system!";
            } catch(Exception $e) {
                $pdo->rollBack();
                $_SESSION['error'] = "Error during ingest: " . $e->getMessage();
            }
        } else {
            $_SESSION['error'] = "Failed to process the CSV file.";
        }
    }

    if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') {
        header('Content-Type: application/json');
        echo json_encode([
            'success' => !isset($_SESSION['error']),
            'message' => $_SESSION['success'] ?? '',
            'error' => $_SESSION['error'] ?? ''
        ]);
        unset($_SESSION['success'], $_SESSION['error']);
        exit;
    }
    
    header('Location: ../admin/dashboard.php?tab=students');
    exit;
};
?>
