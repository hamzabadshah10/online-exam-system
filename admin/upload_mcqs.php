<?php
// admin/upload_mcqs.php
require_once '../config/db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../php/index.php');
    exit;
}

$exam_id = $_GET['exam_id'] ?? 0;
// Verify exam exists
$stmt = $pdo->prepare("SELECT title FROM exams WHERE id = ?");
$stmt->execute([$exam_id]);
$exam = $stmt->fetch();

if (!$exam) {
    header('Location: dashboard.php');
    exit;
}

ob_start();
?>

<div class="mb-4 d-flex justify-between align-center">
    <div>
        <h2 style="margin: 0;">Upload MCQs</h2>
        <p style="color: var(--text-secondary); margin-top: 5px;">Exam: <strong style="color: var(--primary-color);"><?php echo htmlspecialchars($exam['title']); ?></strong></p>
    </div>
    <a href="dashboard.php" class="btn" style="background-color: var(--border-color); color: var(--text-primary);">&larr; Back</a>
</div>

<div class="surface-card" style="max-width: 600px; margin: 0 auto;">
    <div class="alert alert-info" style="background-color: rgba(43, 108, 238, 0.1); color: var(--primary-color); border: 1px solid rgba(43, 108, 238, 0.2);">
        <strong>CSV Format Required:</strong> No Header Row. Order must be: 
        <em>Question Text, Option A, Option B, Option C, Option D, Correct (A/B/C/D)</em>
    </div>

    <?php if (isset($_SESSION['error'])): ?>
        <div class="alert alert-error">
            <?php echo htmlspecialchars($_SESSION['error']); unset($_SESSION['error']); ?>
        </div>
    <?php endif; ?>
    <?php if (isset($_SESSION['success'])): ?>
        <div class="alert alert-success">
            <?php echo htmlspecialchars($_SESSION['success']); unset($_SESSION['success']); ?>
        </div>
    <?php endif; ?>

    <form action="../api/csv_parser.php" method="POST" enctype="multipart/form-data">
        <input type="hidden" name="exam_id" value="<?php echo htmlspecialchars($exam_id); ?>">
        
        <div class="form-group mb-4">
            <label class="form-label" for="csv_file">Select CSV File</label>
            <input type="file" class="form-control" name="csv_file" id="csv_file" accept=".csv" required style="padding: 60px 20px; border-style: dashed; text-align: center;">
        </div>

        <button type="submit" class="btn btn-primary" style="width: 100%;">Process and Upload</button>
    </form>
</div>

<?php 
$content = ob_get_clean();
$base_url = '..';
include '../includes/header.php';
echo $content;
include '../includes/footer.php';
?>
