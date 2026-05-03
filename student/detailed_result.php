<?php
// student/detailed_result.php
require_once '../config/db.php';
if (!isset($_SESSION['user_id'])) { header('Location: ../php/index.php'); exit; }

$enrollment_id = $_GET['id'] ?? null;
if (!$enrollment_id) { die("Invalid request"); }

$stmt = $pdo->prepare("
    SELECT r.*, e.user_id, e.exam_id, e.reg_number, x.title, x.total_marks, x.subject
    FROM results r
    JOIN enrollments e ON r.enrollment_id = e.id
    JOIN exams x ON e.exam_id = x.id
    WHERE e.id = ?
");
$stmt->execute([$enrollment_id]);
$result_data = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$result_data) { die("Result not found"); }
if ($_SESSION['role'] === 'student' && $result_data['user_id'] !== $_SESSION['user_id']) {
    die("Unauthorized access");
}

$exam_id = $result_data['exam_id'];

// Calculate Rank
$stmt_rank = $pdo->prepare("SELECT r.id FROM results r JOIN enrollments e ON r.enrollment_id=e.id WHERE e.exam_id = ? ORDER BY r.total_score DESC, r.submitted_at ASC");
$stmt_rank->execute([$exam_id]);
$all_results = $stmt_rank->fetchAll(PDO::FETCH_ASSOC);
$rank = 0;
foreach($all_results as $index => $row) {
    if ($row['id'] === $result_data['id']) {
        $rank = $index + 1;
        break;
    }
}

$pct = ($result_data['total_marks'] > 0) ? round(($result_data['total_score'] / $result_data['total_marks']) * 100) : 0;

$responses = $result_data['responses'] ? json_decode($result_data['responses'], true) : [];

// Fetch questions
$stmt_q = $pdo->prepare("SELECT * FROM questions WHERE exam_id=?");
$stmt_q->execute([$exam_id]);
$questions = $stmt_q->fetchAll(PDO::FETCH_ASSOC);

$total_correct = 0;
$total_wrong = 0;
$total_skipped = 0;

foreach ($questions as $q) {
    $ans = $responses[$q['id']] ?? null;
    if ($ans === $q['correct_option']) { $total_correct++; }
    elseif ($ans !== null) { $total_wrong++; }
    else { $total_skipped++; }
}

// Sidebar Icons (Matching Dashboard)
$icons = [
    'dashboard'   => '<svg class="w-5 h-5 opacity-70" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"></path></svg>',
    'exams'       => '<svg class="w-5 h-5 opacity-70" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path></svg>',
    'enrollments' => '<svg class="w-5 h-5 opacity-70" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>',
    'results'     => '<svg class="w-5 h-5 opacity-70" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>'
];

$res_icons = [
    'score'   => '<svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 3.055A9.001 9.001 0 1020.945 13H11V3.055z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.488 9H15V3.512A9.025 9.025 0 0120.488 9z"></path></svg>',
    'status'  => '<svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>',
    'rank'    => '<svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"></path></svg>',
    'marks'   => '<svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>'
];
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8"/>
<meta content="width=device-width, initial-scale=1.0" name="viewport"/>
<title>Result Analysis - <?= htmlspecialchars($result_data['title']) ?></title>
<script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
<style>
    @import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap');
    body { font-family: 'Plus Jakarta Sans', sans-serif; }
    .no-scrollbar::-webkit-scrollbar { display: none; }
    .no-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }
    
    @keyframes pulse-soft {
        0%, 100% { opacity: 1; transform: scale(1); }
        50% { opacity: 0.8; transform: scale(1.05); }
    }
    .animate-pulse-soft { animation: pulse-soft 3s infinite ease-in-out; }
</style>
</head>
<body class="bg-[#f8fafc] font-sans text-slate-900 overflow-x-hidden selection:bg-indigo-100 selection:text-indigo-700">

<div class="flex min-h-screen">
    <!-- Sidebar -->
    <aside class="fixed left-0 top-0 h-screen w-80 bg-indigo-950 border-r border-white/5 z-50 transition-all duration-500 overflow-y-auto hidden lg:block shadow-2xl">
        <div class="p-10">
            <div class="flex items-center space-x-4 mb-14 group">
                <div class="p-4 bg-blue-600 rounded-2xl shadow-xl shadow-blue-900/20 text-white transform group-hover:rotate-12 transition-transform duration-500">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0012 18.75c-1.03 0-1.9-.4-2.59-1.177L8.863 17z"></path></svg>
                </div>
                <div>
                    <h1 class="text-2xl font-black tracking-tighter uppercase text-white">EduQuest</h1>
                    <p class="text-[10px] font-black text-blue-400 uppercase tracking-[0.3em]">Student Portal</p>
                </div>
            </div>

            <nav class="space-y-3">
                <a href="dashboard.php?tab=dashboard" class="flex items-center space-x-4 p-5 rounded-2xl text-white hover:bg-white/10 transition-all duration-300 font-black text-xs uppercase tracking-widest group">
                    <span class="group-hover:scale-110 transition-transform"><?= $icons['dashboard'] ?></span>
                    <span>Dashboard</span>
                </a>
                <a href="dashboard.php?tab=available_exams" class="flex items-center space-x-4 p-5 rounded-2xl text-white hover:bg-white/10 transition-all duration-300 font-black text-xs uppercase tracking-widest group">
                    <span class="group-hover:scale-110 transition-transform"><?= $icons['exams'] ?></span>
                    <span>Browse Exams</span>
                </a>
                <a href="dashboard.php?tab=my_enrollments" class="flex items-center space-x-4 p-5 rounded-2xl text-white hover:bg-white/10 transition-all duration-300 font-black text-xs uppercase tracking-widest group">
                    <span class="group-hover:scale-110 transition-transform"><?= $icons['enrollments'] ?></span>
                    <span>Enrollments</span>
                </a>
                <a href="dashboard.php?tab=exam_results" class="flex items-center space-x-4 p-5 rounded-2xl bg-blue-600 text-white shadow-2xl shadow-blue-900/50 font-black text-xs uppercase tracking-widest transition-all duration-300 group">
                    <span class="group-hover:rotate-12 transition-transform"><?= $icons['results'] ?></span>
                    <span>Analytics</span>
                </a>
            </nav>
        </div>
    </aside>

    <!-- Main Content -->
    <main class="flex-1 lg:ml-80 min-h-screen p-8 lg:p-14 relative overflow-hidden">
        <!-- Background Accents -->
        <div class="absolute top-[-10%] right-[-5%] w-[40rem] h-[40rem] bg-indigo-50 rounded-full blur-[120px] opacity-60 -z-10"></div>
        <div class="absolute bottom-[-10%] left-[-5%] w-[30rem] h-[30rem] bg-blue-50 rounded-full blur-[100px] opacity-40 -z-10"></div>

        <!-- Header -->
        <header class="flex flex-col md:flex-row justify-between md:items-center mb-14 gap-8">
            <div class="flex items-center space-x-6">
                <a href="dashboard.php?tab=exam_results" class="p-4 bg-white border border-slate-100 rounded-2xl text-slate-600 hover:text-indigo-600 hover:border-indigo-100 transition-all shadow-sm hover:shadow-lg active:scale-95 group">
                    <svg class="w-6 h-6 group-hover:-translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7"></path></svg>
                </a>
                <div>
                    <h2 class="text-4xl font-black text-slate-900 tracking-tighter uppercase leading-none"><?= htmlspecialchars($result_data['title']) ?></h2>
                    <p class="text-[11px] font-black text-indigo-400 uppercase tracking-[0.3em] mt-3 flex items-center">
                        <span class="w-2 h-2 bg-indigo-500 rounded-full mr-2"></span>
                        Performance Overview
                    </p>
                </div>
            </div>
            
            <div class="flex items-center space-x-4">
                <div class="bg-white border border-slate-100 p-3 rounded-2xl shadow-sm flex items-center space-x-4 pr-6">
                    <div class="w-12 h-12 bg-indigo-50 text-indigo-600 rounded-xl flex items-center justify-center font-black">
                        ID
                    </div>
                    <div class="flex flex-col">
                        <span class="text-[10px] font-black text-slate-600 uppercase tracking-widest">Student Index</span>
                        <span class="text-sm font-black text-slate-900"><?= htmlspecialchars($result_data['reg_number']) ?></span>
                    </div>
                </div>
                <?php if ($_SESSION['role'] === 'admin'): ?>
                    <form action="../api/delete_record.php" method="POST" onsubmit="return confirm('DANGER: Permanent deletion of record requested. Proceed?');">
                        <input type="hidden" name="type" value="result">
                        <input type="hidden" name="id" value="<?= $result_data['id'] ?>">
                        <button type="submit" class="bg-rose-50 text-rose-500 hover:bg-rose-600 hover:text-white px-8 py-4 rounded-2xl text-xs font-black uppercase tracking-widest border-2 border-rose-100 transition-all shadow-sm hover:shadow-xl active:scale-95">Purge Record</button>
                    </form>
                <?php endif; ?>
            </div>
        </header>

        <!-- Metrics Grid -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-8 mb-14">
            <div class="bg-blue-50/50 p-10 rounded-[2.5rem] shadow-xl shadow-blue-100/50 border-2 border-blue-100/50 flex flex-col relative overflow-hidden group hover:-translate-y-2 transition-all duration-500">
                <div class="p-4 bg-blue-600 text-white rounded-2xl w-fit mb-6 shadow-lg shadow-blue-200"><?= $res_icons['score'] ?></div>
                <p class="text-5xl font-black text-blue-700 tracking-tighter group-hover:scale-110 transition-transform origin-left duration-500"><?= $pct ?>%</p>
                <p class="text-[11px] font-black text-blue-400 uppercase tracking-[0.2em] mt-2">Total Score</p>
                <div class="absolute -right-8 -bottom-8 p-12 opacity-[0.03] scale-150 rotate-12 group-hover:rotate-0 transition-transform duration-700 text-blue-900">
                    <svg class="w-24 h-24" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"></path></svg>
                </div>
            </div>

            <?php 
                $isPass = ($result_data['status'] === 'Pass');
                $statusBg = $isPass ? 'bg-emerald-50/50 border-emerald-100/50 shadow-emerald-100/50 text-emerald-700' : 'bg-rose-50/50 border-rose-100/50 shadow-rose-100/50 text-rose-600';
                $statusIcon = $isPass ? 'bg-emerald-500' : 'bg-rose-500';
            ?>
            <div class="<?= $statusBg ?> p-10 rounded-[2.5rem] shadow-xl border-2 flex flex-col relative overflow-hidden group hover:-translate-y-2 transition-all duration-500">
                <div class="p-4 <?= $statusIcon ?> text-white rounded-2xl w-fit mb-6 shadow-lg"><?= $res_icons['status'] ?></div>
                <p class="text-5xl font-black tracking-tighter group-hover:scale-110 transition-transform origin-left duration-500"><?= $result_data['status'] ?></p>
                <p class="text-[11px] font-black opacity-60 uppercase tracking-[0.2em] mt-2">Pass / Fail Status</p>
            </div>

            <div class="bg-indigo-50/50 p-10 rounded-[2.5rem] shadow-xl shadow-indigo-100/50 border-2 border-indigo-100/50 flex flex-col relative overflow-hidden group hover:-translate-y-2 transition-all duration-500">
                <div class="p-4 bg-indigo-600 text-white rounded-2xl w-fit mb-6 shadow-lg shadow-indigo-200"><?= $res_icons['rank'] ?></div>
                <p class="text-5xl font-black text-indigo-700 tracking-tighter group-hover:scale-110 transition-transform origin-left duration-500">#<?= $rank ?></p>
                <p class="text-[11px] font-black text-indigo-400 uppercase tracking-[0.2em] mt-2">Class Rank</p>
            </div>

            <div class="bg-orange-50/50 p-10 rounded-[2.5rem] shadow-xl shadow-orange-100/50 border-2 border-orange-100/50 flex flex-col relative overflow-hidden group hover:-translate-y-2 transition-all duration-500">
                <div class="p-4 bg-orange-500 text-white rounded-2xl w-fit mb-6 shadow-lg shadow-orange-200"><?= $res_icons['marks'] ?></div>
                <p class="text-5xl font-black text-orange-700 tracking-tighter group-hover:scale-110 transition-transform origin-left duration-500"><?= (int)$result_data['total_score'] ?></p>
                <p class="text-[11px] font-black text-orange-400 uppercase tracking-[0.2em] mt-2">Marks Obtained (<?= (int)$result_data['total_marks'] ?>)</p>
            </div>
        </div>

        <!-- Breakdown & Detail Area -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-10 mb-14">
            <!-- Breakdown Grid -->
            <div class="lg:col-span-1 space-y-8">
                <div class="bg-gradient-to-br from-emerald-600 to-teal-500 p-6 rounded-3xl shadow-xl shadow-emerald-100 text-white relative overflow-hidden group">
                    <div class="absolute -right-4 -top-4 p-8 opacity-10 group-hover:scale-125 transition-transform duration-700"><?= $res_icons['status'] ?></div>
                    <div class="relative z-10">
                        <p class="text-[10px] font-black uppercase tracking-[0.2em] opacity-80 mb-2">Correct Answers</p>
                        <p class="text-4xl font-black mb-1"><?= $total_correct ?></p>
                        <p class="text-[10px] font-black uppercase tracking-widest opacity-90">Total Correct</p>
                    </div>
                </div>
                <div class="bg-gradient-to-br from-rose-600 to-pink-500 p-6 rounded-3xl shadow-xl shadow-rose-100 text-white relative overflow-hidden group">
                    <div class="absolute -right-4 -top-4 p-8 opacity-10 group-hover:scale-125 transition-transform duration-700"><?= $res_icons['marks'] ?></div>
                    <div class="relative z-10">
                        <p class="text-[10px] font-black uppercase tracking-[0.2em] opacity-80 mb-2">Wrong Answers</p>
                        <p class="text-4xl font-black mb-1"><?= $total_wrong ?></p>
                        <p class="text-[10px] font-black uppercase tracking-widest opacity-90">Total Incorrect</p>
                    </div>
                </div>
                <div class="bg-gradient-to-br from-slate-900 to-slate-700 p-6 rounded-3xl shadow-xl shadow-slate-200 text-white relative overflow-hidden group">
                    <div class="absolute -right-4 -top-4 p-8 opacity-10 group-hover:scale-125 transition-transform duration-700"><?= $res_icons['score'] ?></div>
                    <div class="relative z-10">
                        <p class="text-[10px] font-black uppercase tracking-[0.2em] opacity-80 mb-2">Skipped Questions</p>
                        <p class="text-4xl font-black mb-1"><?= $total_skipped ?></p>
                        <p class="text-[10px] font-black uppercase tracking-widest opacity-90">Not Answered</p>
                    </div>
                </div>
            </div>

            <!-- Detailed Table -->
            <div class="lg:col-span-2">
                <div class="bg-white rounded-[3rem] shadow-2xl shadow-slate-200/50 border border-gray-100 overflow-hidden h-full flex flex-col">
                    <div class="px-10 py-10 bg-gradient-to-r from-blue-600 to-indigo-700 text-white flex justify-between items-center shadow-lg relative overflow-hidden">
                        <!-- Decorative Header Pattern -->
                        <div class="absolute right-0 top-0 opacity-10 translate-x-1/4 -translate-y-1/4">
                            <svg class="w-64 h-64" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"></path></svg>
                        </div>
                        
                        <div class="flex items-center space-x-5 relative z-10">
                            <div class="p-4 bg-white/20 backdrop-blur-md text-white rounded-2xl border border-white/30 shadow-inner">
                                <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"></path></svg>
                            </div>
                            <div>
                                <h4 class="font-black text-white text-2xl tracking-tighter uppercase">Answer Review</h4>
                                <p class="text-[11px] font-black text-blue-200 uppercase tracking-[0.2em] mt-1">Detailed Question Breakdown</p>
                            </div>
                        </div>
                        <div class="bg-white/20 backdrop-blur-md border border-white/30 px-6 py-3 rounded-2xl text-[11px] font-black text-white uppercase tracking-widest relative z-10 shadow-lg">
                            <?= count($questions) ?> Total Questions
                        </div>
                    </div>
                    <div class="overflow-x-auto flex-1 no-scrollbar">
                        <table class="w-full text-left text-sm whitespace-nowrap">
                            <thead>
                                <tr class="bg-indigo-50/30 text-indigo-900 uppercase text-[11px] font-black tracking-[0.25em] border-b border-indigo-100">
                                    <th class="px-10 py-6">Index</th>
                                    <th class="px-10 py-6">Question Details</th>
                                    <th class="px-10 py-6 text-center">Your Result</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-50 bg-white">
                                <?php foreach ($questions as $idx => $q): 
                                    $ans = $responses[$q['id']] ?? null;
                                    $is_correct = ($ans === $q['correct_option']);
                                    $is_skipped = ($ans === null);
                                ?>
                                <tr class="hover:bg-indigo-50/20 transition-all duration-300 group">
                                    <td class="px-10 py-8">
                                        <div class="w-10 h-10 bg-slate-50 text-slate-600 rounded-xl flex items-center justify-center font-black text-xs group-hover:bg-indigo-600 group-hover:text-white transition-all">
                                            <?= str_pad($idx + 1, 2, '0', STR_PAD_LEFT) ?>
                                        </div>
                                    </td>
                                    <td class="px-10 py-8">
                                        <div class="flex flex-col max-w-xl">
                                            <p class="font-black text-slate-900 text-base leading-snug tracking-tight mb-2 group-hover:text-indigo-600 transition-colors"><?= htmlspecialchars($q['question_text']) ?></p>
                                            <div class="flex items-center space-x-4">
                                                <span class="text-[10px] font-black text-blue-600 uppercase tracking-widest bg-blue-50 px-3 py-1 rounded-md border border-blue-100 shadow-[0_0_15px_rgba(59,130,246,0.2)] group-hover:shadow-[0_0_25px_rgba(59,130,246,0.4)] transition-all">Attempted Answer: <span class="text-slate-900 ml-1"><?= $ans ?: 'N/A' ?></span></span>
                                                <span class="text-[10px] font-black text-emerald-500 uppercase tracking-widest bg-emerald-50 px-3 py-1 rounded-md border border-emerald-100 shadow-[0_0_15px_rgba(16,185,129,0.1)]">Target: <span class="ml-1 text-slate-900"><?= $q['correct_option'] ?></span></span>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-10 py-8 text-center">
                                        <?php if ($is_skipped): ?>
                                            <div class="inline-flex items-center space-x-2 bg-amber-50 text-amber-600 border-2 border-amber-100 px-5 py-2.5 rounded-full text-[10px] font-black uppercase tracking-widest">
                                                <span class="w-1.5 h-1.5 bg-amber-500 rounded-full animate-pulse"></span>
                                                <span>Skipped</span>
                                            </div>
                                        <?php elseif ($is_correct): ?>
                                            <div class="inline-flex items-center space-x-2 bg-emerald-50 text-emerald-600 border-2 border-emerald-100 px-5 py-2.5 rounded-full text-[10px] font-black uppercase tracking-widest shadow-sm">
                                                <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 24 24"><path d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41L9 16.17z"></path></svg>
                                                <span>Correct</span>
                                            </div>
                                        <?php else: ?>
                                            <div class="inline-flex items-center space-x-2 bg-rose-50 text-rose-500 border-2 border-rose-100 px-5 py-2.5 rounded-full text-[10px] font-black uppercase tracking-widest shadow-sm">
                                                <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 24 24"><path d="M19 6.41L17.59 5 12 10.59 6.41 5 5 6.41 10.59 12 5 17.59 6.41 19 12 13.41 17.59 19 19 17.59 13.41 12 19 6.41z"></path></svg>
                                                <span>Wrong</span>
                                            </div>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>

                </div>
            </div>
        </div>
    </main>
</div>

</body>
</html>
