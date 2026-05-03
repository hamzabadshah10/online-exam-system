<?php
// student/enroll.php
require_once '../config/db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'student') {
    header('Location: ../php/index.php');
    exit;
}

$exam_id = $_GET['id'] ?? null;
if (!$exam_id) {
    header('Location: dashboard.php');
    exit;
}

// Handle POST request
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $reg = trim($_POST['reg_number']);
    $dept = trim($_POST['department']);
    $prog = trim($_POST['program']);
    $uid = $_SESSION['user_id'];

    try {
        $stmt = $pdo->prepare("INSERT INTO enrollments (exam_id, user_id, reg_number, department, program) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([$exam_id, $uid, $reg, $dept, $prog]);
        $_SESSION['success'] = "Enrollment successful! You can now take the exam.";
        header('Location: dashboard.php');
        exit;
    } catch(PDOException $e) {
        $_SESSION['error'] = "Already enrolled or an error occurred: " . $e->getMessage();
        header('Location: dashboard.php');
        exit;
    }
}

// GET logic to verify exam
$stmt = $pdo->prepare("SELECT title FROM exams WHERE id = ?");
$stmt->execute([$exam_id]);
$exam = $stmt->fetch();
if (!$exam) {
    header('Location: dashboard.php');
    exit;
}

ob_start();
?>

<div class="d-flex justify-center align-center" style="min-height: calc(100vh - 200px); align-items: center; justify-content: center;">
    <div class="surface-card" style="width: 100%; max-width: 500px;">
        <div class="text-center mb-4">
            <h2 style="margin: 0 0 8px 0;">Enrollment Form</h2>
            <p style="color: var(--text-secondary); margin: 0; font-size: 0.95rem;">Exam: <span style="color: var(--primary-color);"><?php echo htmlspecialchars($exam['title']); ?></span></p>
        </div>

        <form action="enroll.php?id=<?php echo $exam_id; ?>" method="POST">
            <div class="form-group">
                <label class="form-label" for="reg_number">University Registration Number</label>
                <input type="text" class="form-control" name="reg_number" required placeholder="e.g. B23F0352SE016">
            </div>
            
            <div class="form-group">
                <label class="form-label" for="department">Department</label>
                <input type="text" class="form-control" name="department" required placeholder="e.g. IT & CS">
            </div>
            
            <div class="form-group mb-4">
                <label class="form-label" for="program">Program Details</label>
                <input type="text" class="form-control" name="program" required placeholder="e.g. Software Engineering (Blue)">
            </div>
            
            <button type="submit" class="btn btn-primary" style="width: 100%;">Confirm Enrollment</button>
            <div style="text-align: center; margin-top: 15px;">
                <a href="dashboard.php" style="color: var(--text-secondary); font-size: 0.9rem;">Cancel</a>
            </div>
        </form>
    </div>
</div>

<?php 
$content = ob_get_clean();
$base_url = '..';
include '../includes/header.php';
echo $content;
include '../includes/footer.php';
?>
