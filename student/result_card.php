<?php
// student/result_card.php
require_once '../config/db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'student') {
    header('Location: ../php/index.php');
    exit;
}

$user_id = $_SESSION['user_id'];
$res_id_get = $_GET['id'] ?? null;

if (!$res_id_get) { header("Location: dashboard.php"); exit; }

$stmt = $pdo->prepare("
    SELECT r.*, e.reg_number, e.department, e.program, u.name, ex.title, ex.exam_date, ex.passing_marks, ex.total_marks 
    FROM results r 
    JOIN enrollments e ON r.enrollment_id = e.id 
    JOIN users u ON e.user_id = u.id 
    JOIN exams ex ON e.exam_id = ex.id 
    WHERE e.id = ? AND u.id = ?
");
$stmt->execute([$res_id_get, $user_id]);
$result = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$result) { header("Location: dashboard.php"); exit; }

$pct = ($result['total_marks'] > 0) ? round(($result['total_score'] / $result['total_marks']) * 100) : 0;
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8"/>
<meta content="width=device-width, initial-scale=1.0" name="viewport"/>
<title>Official Result - <?= htmlspecialchars($result['title']) ?></title>
<script src="https://cdn.tailwindcss.com"></script>
<style>
    @import url('https://fonts.googleapis.com/css2?family=Outfit:wght@100..900&display=swap');
    body { font-family: 'Outfit', sans-serif; background: #e2e8f0; }
    @media print {
        body { background: white !important; margin: 0; padding: 0; }
        .no-print { display: none !important; }
        .cert-container { box-shadow: none !important; border: 2px solid #2563eb !important; }
    }
</style>
</head>
<body class="p-8 flex flex-col items-center justify-center min-h-screen">

<div class="w-full max-w-4xl mb-4 flex justify-between no-print">
    <a href="dashboard.php" class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded shadow font-bold text-sm transition">&larr; Back to Dashboard</a>
    <button onclick="window.print()" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded shadow font-bold text-sm transition flex items-center space-x-2">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
        <span>Print Result Certificate</span>
    </button>
</div>

<div class="cert-container w-full max-w-4xl bg-white shadow-2xl rounded-2xl overflow-hidden border border-gray-200">
    <!-- Header -->
    <div class="bg-blue-900 border-b-8 border-blue-500 p-8 text-center flex flex-col items-center">
        <svg class="w-16 h-16 text-white mb-2" fill="currentColor" viewBox="0 0 24 24"><path d="M12 3L1 9l11 6 9-4.91V17h2V9L12 3zM3.89 9L12 4.57 20.11 9 12 13.43 3.89 9z"></path></svg>
        <h1 class="text-3xl font-black text-white uppercase tracking-widest">Global Gateway Education</h1>
        <p class="text-blue-200 font-bold mt-1 tracking-widest uppercase text-sm">Official Examination Transcript</p>
    </div>

    <!-- Student Details -->
    <div class="p-10 grid grid-cols-2 gap-8 border-b border-gray-100">
        <div class="space-y-4">
            <div>
                <p class="text-xs font-bold text-gray-400 uppercase">Student Name</p>
                <p class="text-xl font-bold text-gray-800"><?= htmlspecialchars($result['name']) ?></p>
            </div>
            <div>
                <p class="text-xs font-bold text-gray-400 uppercase">Registration ID</p>
                <p class="text-lg font-mono text-gray-700 bg-gray-100 py-1 px-3 inline-block rounded border"><?= htmlspecialchars($result['reg_number']) ?></p>
            </div>
            <div>
                <p class="text-xs font-bold text-gray-400 uppercase">Program</p>
                <p class="text-md font-semibold text-gray-700"><?= htmlspecialchars($result['program']) ?> (<?= htmlspecialchars($result['department']) ?>)</p>
            </div>
        </div>
        <div class="space-y-4 text-right">
            <div>
                <p class="text-xs font-bold text-gray-400 uppercase">Examination Subject</p>
                <p class="text-xl font-bold text-blue-800"><?= htmlspecialchars($result['title']) ?></p>
            </div>
            <div>
                <p class="text-xs font-bold text-gray-400 uppercase">Examination Date</p>
                <p class="text-md font-semibold text-gray-700"><?= date('F j, Y', strtotime($result['exam_date'])) ?></p>
            </div>
        </div>
    </div>

    <!-- Scores -->
    <div class="p-10 grid grid-cols-3 gap-6">
        <div class="bg-gray-50 border border-gray-200 p-6 rounded-xl text-center shadow-inner">
            <p class="text-sm font-bold text-gray-500 uppercase">Percentage</p>
            <p class="text-4xl font-black text-gray-800 mt-2"><?= $pct ?>%</p>
        </div>
        <div class="bg-blue-50 border border-blue-200 p-6 rounded-xl text-center shadow-inner">
            <p class="text-sm font-bold text-blue-600 uppercase">Total Score</p>
            <p class="text-4xl font-black text-blue-800 mt-2"><?= $result['total_score'] ?> / <?= $result['total_marks'] ?></p>
        </div>
        <div class="<?= $result['status']==='Pass'?'bg-green-50 border-green-200':'bg-red-50 border-red-200' ?> border p-6 rounded-xl text-center shadow-inner flex flex-col justify-center">
            <p class="text-sm font-bold <?= $result['status']==='Pass'?'text-green-600':'text-red-500' ?> uppercase">Final Status</p>
            <p class="text-4xl font-black <?= $result['status']==='Pass'?'text-green-700':'text-red-600' ?> mt-2 uppercase"><?= $result['status'] ?></p>
        </div>
    </div>

    <!-- Footer -->
    <div class="bg-gray-50 p-6 flex justify-between items-center text-xs text-gray-500 font-semibold border-t border-gray-200">
        <p>Auto-generated on: <?= date('Y-m-d H:i:s', strtotime($result['submitted_at'])) ?></p>
        <div class="text-right">
            <?php if($result['auto_submitted']): ?>
                <span class="text-red-500 font-bold border border-red-300 px-2 py-1 rounded">Flagged: Window Focus Penalty</span>
            <?php else: ?>
                <span>Verified System Processing</span>
            <?php endif; ?>
        </div>
    </div>
</div>

</body>
</html>
