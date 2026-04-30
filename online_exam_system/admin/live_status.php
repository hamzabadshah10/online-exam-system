<?php
// admin/live_status.php
require_once '../config/db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../index.php');
    exit;
}

// Fetch active enrollments
$stmt = $pdo->query("SELECT e.*, u.name, u.email, ex.title as exam_title 
                     FROM enrollments e 
                     JOIN users u ON e.user_id = u.id 
                     JOIN exams ex ON e.exam_id = ex.id 
                     ORDER BY e.enrolled_at DESC");
$enrollments = $stmt->fetchAll(PDO::FETCH_ASSOC);

ob_start();
?>

<div class="mb-4">
    <h2 style="margin: 0;">Live Enrollment Status</h2>
    <p style="color: var(--text-secondary); margin-top: 5px;">Monitor active test-takers and recent registrations.</p>
</div>

<div class="surface-card">
    <?php if (count($enrollments) > 0): ?>
        <table style="width: 100%; border-collapse: collapse; text-align: left;">
            <thead>
                <tr style="border-bottom: 2px solid var(--border-color); color: var(--text-secondary);">
                    <th style="padding: 12px 0;">Student Name</th>
                    <th style="padding: 12px 0;">Reg Number</th>
                    <th style="padding: 12px 0;">Exam Title</th>
                    <th style="padding: 12px 0;">Enrolled At</th>
                    <th style="padding: 12px 0;">Status</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($enrollments as $enr): ?>
                    <tr style="border-bottom: 1px solid var(--border-color);">
                        <td style="padding: 12px 0;"><?php echo htmlspecialchars($enr['name']); ?></td>
                        <td style="padding: 12px 0;"><?php echo htmlspecialchars($enr['reg_number']); ?></td>
                        <td style="padding: 12px 0; color: var(--primary-color); font-weight: 500;"><?php echo htmlspecialchars($enr['exam_title']); ?></td>
                        <td style="padding: 12px 0;"><?php echo htmlspecialchars($enr['enrolled_at']); ?></td>
                        <td style="padding: 12px 0;">
                            <span style="color: var(--success);">
                                <!-- Since tracking live socket state is out of scope without node, we show registered -->
                                Registered
                            </span>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p style="color: var(--text-secondary);">No enrollments found.</p>
    <?php endif; ?>
</div>

<?php 
$content = ob_get_clean();
$base_url = '..';
include '../includes/header.php';
echo $content;
include '../includes/footer.php';
?>
