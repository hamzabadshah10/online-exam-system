<?php
// api/exam_manager.php
require_once '../config/db.php';

if (!isset($_SESSION['user_id'])) {
    http_response_code(403); exit("Unauthorized");
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    // Admin Action: Create Exam
    if ($action === 'create_exam' && $_SESSION['role'] === 'admin') {
        $title = trim($_POST['title']);
        $subject = trim($_POST['subject']);
        $date = trim($_POST['exam_date']);
        $time = trim($_POST['start_time']);
        $duration = (int)$_POST['duration_minutes'];
        $passing = (float)$_POST['passing_marks'];

        try {
            $stmt = $pdo->prepare("INSERT INTO exams (title, subject, exam_date, start_time, duration_minutes, passing_marks, status) VALUES (?, ?, ?, ?, ?, ?, 'pending')");
            $stmt->execute([$title, $subject, $date, $time, $duration, $passing]);
            $_SESSION['success'] = "Exam '{$title}' created successfully!";
        } catch(PDOException $e) {
            $_SESSION['error'] = "Failed to create exam: " . $e->getMessage();
        }
        header('Location: ../admin/dashboard.php');
        exit;
    }

    // Student Action: Enroll in Exam
    if ($action === 'enroll' && $_SESSION['role'] === 'student') {
        $exam_id = (int)$_POST['exam_id'];
        $reg_number = trim($_POST['reg_number']);
        $dept = trim($_POST['department']);
        $prog = trim($_POST['program']);
        $uid = $_SESSION['user_id'];

        try {
            $stmt = $pdo->prepare("INSERT INTO enrollments (exam_id, user_id, reg_number, department, program) VALUES (?, ?, ?, ?, ?)");
            $stmt->execute([$exam_id, $uid, $reg_number, $dept, $prog]);
            $_SESSION['success'] = "Successfully enrolled in the exam!";
        } catch(PDOException $e) {
            $_SESSION['error'] = "Already enrolled or database error.";
        }
        header('Location: ../student/dashboard.php?tab=my_enrollments');
        exit;
    }

    // Student Action: Submit Exam
    if ($action === 'submit_exam' && $_SESSION['role'] === 'student') {
        $exam_id = (int)$_POST['exam_id'];
        $uid = $_SESSION['user_id'];
        $auto_submitted = isset($_POST['auto_submitted']) && $_POST['auto_submitted'] == '1' ? 1 : 0;
        
        // Find enrollment
        $stmt_e = $pdo->prepare("SELECT id FROM enrollments WHERE user_id=? AND exam_id=?");
        $stmt_e->execute([$uid, $exam_id]);
        $enrollment = $stmt_e->fetch(PDO::FETCH_ASSOC);
        if(!$enrollment) exit("Not enrolled");
        $enrollment_id = $enrollment['id'];

        // Grade MCQs
        $score = 0;
        $correct = 0;
        $incorrect = 0;
        
        // Fetch specific exam passing marks & questions
        $stmt_x = $pdo->prepare("SELECT passing_marks FROM exams WHERE id=?");
        $stmt_x->execute([$exam_id]);
        $ex = $stmt_x->fetch(PDO::FETCH_ASSOC);
        $passing_marks = $ex['passing_marks'];

        $stmt_q = $pdo->prepare("SELECT id, correct_option FROM questions WHERE exam_id=?");
        $stmt_q->execute([$exam_id]);
        $responses = [];
        while($q = $stmt_q->fetch(PDO::FETCH_ASSOC)) {
            $ans = $_POST['q_'.$q['id']] ?? null;
            $responses[$q['id']] = $ans;
            if ($ans === $q['correct_option']) {
                $score += 1; // 1 mark per question
                $correct++;
            } else if ($ans !== null) {
                $incorrect++;
            }
        }
        
        $status = ($score >= $passing_marks) ? 'Pass' : 'Fail';
        $responses_json = json_encode($responses);

        $stmt_r = $pdo->prepare("INSERT INTO results (enrollment_id, total_score, total_correct, total_incorrect, status, auto_submitted, responses) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt_r->execute([$enrollment_id, $score, $correct, $incorrect, $status, $auto_submitted, $responses_json]);

        header("Location: ../student/dashboard.php");
        exit;
    }
}
?>
