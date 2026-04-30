<?php
// admin/create_exam.php
require_once '../config/db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../index.php');
    exit;
}

ob_start();
?>

<div class="mb-4 d-flex justify-between align-center">
    <div>
        <h2 style="margin: 0;">Create New Exam</h2>
        <p style="color: var(--text-secondary); margin-top: 5px;">Define the parameters for a new assessment.</p>
    </div>
    <a href="dashboard.php" class="btn" style="background-color: var(--border-color); color: var(--text-primary);">&larr; Back</a>
</div>

<div class="surface-card" style="max-width: 600px; margin: 0 auto;">
    <?php if (isset($_SESSION['error'])): ?>
        <div class="alert alert-error">
            <?php echo htmlspecialchars($_SESSION['error']); unset($_SESSION['error']); ?>
        </div>
    <?php endif; ?>

    <form action="../api/exam_manager.php" method="POST">
        <input type="hidden" name="action" value="create_exam">
        
        <div class="form-group mb-4">
            <label class="form-label">Exam Title</label>
            <input type="text" class="form-control" name="title" required placeholder="e.g. Midterm Software Engineering">
        </div>

        <div class="d-flex gap-4 mb-4">
            <div class="form-group" style="flex: 1;">
                <label class="form-label">Date</label>
                <input type="date" class="form-control" name="exam_date" required>
            </div>
            <div class="form-group" style="flex: 1;">
                <label class="form-label">Start Time</label>
                <input type="time" class="form-control" name="start_time" required>
            </div>
        </div>

        <div class="d-flex gap-4 mb-4">
            <div class="form-group" style="flex: 1;">
                <label class="form-label">Duration (Minutes)</label>
                <input type="number" class="form-control" name="duration_minutes" required min="5" value="60">
            </div>
            <div class="form-group" style="flex: 1;">
                <label class="form-label">Passing Marks</label>
                <input type="number" step="0.5" class="form-control" name="passing_marks" required min="1" value="50">
            </div>
        </div>

        <button type="submit" class="btn btn-primary" style="width: 100%;">Create Exam</button>
    </form>
</div>

<?php 
$content = ob_get_clean();
$base_url = '..';
include '../includes/header.php';
echo $content;
include '../includes/footer.php';
?>
