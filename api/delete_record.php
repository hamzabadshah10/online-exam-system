<?php
// api/delete_record.php
require_once '../config/db.php';
session_start();

if (!isset($_SESSION['user_id'])) { 
    header('Location: ../php/index.php'); 
    exit; 
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $type = $_POST['type'] ?? '';
    $id = $_POST['id'] ?? '';
    $user_id = $_SESSION['user_id'];
    $role = $_SESSION['role'];

    if ($type === 'student' && $id && $role === 'admin') {
        $stmt = $pdo->prepare("DELETE FROM users WHERE id = ? AND role = 'student'");
        $stmt->execute([$id]);
        $_SESSION['success'] = "Student deleted successfully.";
        header('Location: ../admin/dashboard.php?tab=students');
        exit;
    } elseif ($type === 'result' && $id) {
        // Admin can delete any result, student can only delete their own
        if ($role === 'admin') {
            $stmt = $pdo->prepare("DELETE FROM results WHERE id = ?");
            $stmt->execute([$id]);
        } else {
            $stmt = $pdo->prepare("DELETE r FROM results r JOIN enrollments e ON r.enrollment_id = e.id WHERE r.id = ? AND e.user_id = ?");
            $stmt->execute([$id, $user_id]);
        }
        $_SESSION['success'] = "Result record deleted successfully.";
        header('Location: ' . ($_SERVER['HTTP_REFERER'] ?? '../student/dashboard.php?tab=exam_results'));
        exit;
    } elseif ($type === 'result_by_enrollment' && $id) {
        if ($role === 'admin') {
            $stmt = $pdo->prepare("DELETE FROM results WHERE enrollment_id = ?");
            $stmt->execute([$id]);
        } else {
            $stmt = $pdo->prepare("DELETE r FROM results r JOIN enrollments e ON r.enrollment_id = e.id WHERE e.id = ? AND e.user_id = ?");
            $stmt->execute([$id, $user_id]);
        }
        $_SESSION['success'] = "Result record deleted successfully.";
        header('Location: ' . ($_SERVER['HTTP_REFERER'] ?? '../student/dashboard.php?tab=exam_results'));
        exit;
    } elseif ($type === 'exam_results_only' && $id && $role === 'admin') {
        $stmt = $pdo->prepare("DELETE r FROM results r JOIN enrollments e ON r.enrollment_id = e.id WHERE e.exam_id = ?");
        $stmt->execute([$id]);
        $_SESSION['success'] = "All results for this exam have been cleared.";
        header('Location: ' . ($_SERVER['HTTP_REFERER'] ?? '../admin/dashboard.php?tab=results'));
        exit;
    } elseif ($type === 'enrollment' && $id) {
        if ($role === 'admin') {
            $stmt = $pdo->prepare("DELETE FROM enrollments WHERE id = ?");
            $stmt->execute([$id]);
        } else {
            $stmt = $pdo->prepare("DELETE FROM enrollments WHERE id = ? AND user_id = ?");
            $stmt->execute([$id, $user_id]);
        }
        $_SESSION['success'] = "Enrollment deleted successfully.";
        header('Location: ' . ($_SERVER['HTTP_REFERER'] ?? '../student/dashboard.php?tab=my_enrollments'));
        exit;
    } elseif ($type === 'exam' && $id && $role === 'admin') {
        $stmt = $pdo->prepare("DELETE FROM exams WHERE id = ?");
        $stmt->execute([$id]);
        $_SESSION['success'] = "Exam deleted successfully.";
        header('Location: ' . ($_SERVER['HTTP_REFERER'] ?? '../admin/dashboard.php'));
        exit;
    }
}
header('Location: ../php/index.php');
