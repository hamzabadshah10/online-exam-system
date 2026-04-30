<?php
// student/dashboard.php
require_once '../config/db.php';
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'student') { header('Location: ../index.php'); exit; }

$user_id = $_SESSION['user_id'];
$tab = $_GET['tab'] ?? 'dashboard';

// Upcoming Exams
$stmt_available = $pdo->prepare("SELECT ex.* FROM exams ex WHERE ex.id NOT IN (SELECT e.exam_id FROM enrollments e WHERE e.user_id = ?) ORDER BY ex.exam_date ASC LIMIT 4");
$stmt_available->execute([$user_id]);
$exams = $stmt_available->fetchAll(PDO::FETCH_ASSOC);

// My Enrollments
$stmt_enrolled = $pdo->prepare("SELECT e.id as enrollment_id, ex.id as exam_id, ex.title, ex.exam_date, r.status as result_status FROM enrollments e JOIN exams ex ON e.exam_id=ex.id LEFT JOIN results r ON r.enrollment_id=e.id WHERE e.user_id = ? ORDER BY ex.exam_date DESC");
$stmt_enrolled->execute([$user_id]);
$my_enrollments = $stmt_enrolled->fetchAll(PDO::FETCH_ASSOC);

// Fetch All Results for Cards
$all_scorecards = [];
$stmt_res = $pdo->prepare("SELECT ex.title, ex.subject, ex.duration_minutes, ex.exam_date, ex.start_time, ex.passing_marks, e.reg_number, r.status, r.total_score, r.total_correct, r.total_incorrect, e.id as enrollment_id FROM results r JOIN enrollments e ON r.enrollment_id=e.id JOIN exams ex ON e.exam_id=ex.id WHERE e.user_id = ? ORDER BY r.id DESC");
$stmt_res->execute([$user_id]);
$all_scorecards = $stmt_res->fetchAll(PDO::FETCH_ASSOC);

// Define SVG Icons (Matching Admin Theme)
$icons = [
    'dashboard'   => '<svg class="w-5 h-5 opacity-70" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"></path></svg>',
    'exams'       => '<svg class="w-5 h-5 opacity-70" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path></svg>',
    'enrollments' => '<svg class="w-5 h-5 opacity-70" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>',
    'results'     => '<svg class="w-5 h-5 opacity-70" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>'
];

function navLink($currentTab, $linkTab, $label, $icon) {
    if ($currentTab === $linkTab) {
        $activeClass = 'bg-blue-600 text-white shadow-md font-semibold';
        $iconClass = 'text-white opacity-100';
    } else {
        $activeClass = 'hover:bg-white/10 text-blue-100 font-medium';
        $iconClass = '';
    }
    return "<a href='?tab={$linkTab}' class='flex items-center space-x-3 {$activeClass} p-3 rounded-lg transition-all'>
                <div class='{$iconClass}'>{$icon}</div><span>{$label}</span>
            </a>";
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8"/>
<meta content="width=device-width, initial-scale=1.0" name="viewport"/>
<title>Student Portal</title>
<script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
<style data-purpose="typography">
    @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap');
    body {
        font-family: 'Inter', sans-serif;
    }
</style>
<script>
    tailwind.config = {
      theme: { extend: { 
        fontFamily: { sans: ['Inter', 'sans-serif'] },
        colors: { 'brand-dark': '#1e293b', 'brand-sidebar': '#1e3a5f', 'brand-bg': '#f3f4f6', 'brand-blue': '#3b82f6' } 
      } }
    }
    function openEnrollModal(id, title) {
        document.getElementById('m-exam-id').value = id;
        document.getElementById('m-exam-title').innerText = title;
        document.getElementById('enrollModal').classList.remove('hidden');
    }
    function closeEnrollModal() { document.getElementById('enrollModal').classList.add('hidden'); }
</script>
<style>.no-scrollbar::-webkit-scrollbar { display: none; } .no-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }</style>
</head>
<body class="bg-gray-100 font-sans text-gray-800 antialiased h-screen overflow-hidden">
<div class="w-full h-full flex flex-col">
<div class="flex-1 w-full flex flex-col overflow-hidden">

<section class="flex flex-col w-full h-full bg-white">
<div class="flex justify-between items-center px-6 py-4 bg-brand-sidebar border-b-2 border-white z-20 flex-shrink-0">
    <div class="flex items-center space-x-4 text-white">
        <div class="w-10 h-10 bg-blue-500 rounded-lg flex items-center justify-center font-black text-white tracking-widest text-md shadow-inner border border-blue-400">ST</div>
        <div class="flex flex-col">
            <div class="font-bold text-white tracking-wide leading-tight">Student Portal</div>
            <div class="text-[10px] text-blue-300 font-bold uppercase tracking-widest">Main Dashboard</div>
        </div>
    </div>
    <div class="flex items-center space-x-4">
        <a href="../api/auth.php?action=logout" class="bg-red-500 hover:bg-red-600 text-white px-6 py-1.5 rounded-md font-bold text-sm transition shadow-sm shadow-red-500/30">Logout</a>
    </div>
</div>
<div class="bg-white overflow-hidden flex flex-1 relative">

<aside class="w-72 bg-brand-sidebar text-white flex-col hidden md:flex border-r border-gray-800/20 box-border z-10 transition-all">
<nav class="flex-1 px-5 py-6 space-y-2">
<?= navLink($tab, 'dashboard', 'Dashboard', $icons['dashboard']) ?>
<?= navLink($tab, 'available_exams', 'Available Exams', $icons['exams']) ?>
<?= navLink($tab, 'my_enrollments', 'My Enrollments', $icons['enrollments']) ?>
<?= navLink($tab, 'exam_results', 'Exam Results', $icons['results']) ?>
</nav>
</aside>

<main class="flex-1 bg-brand-bg p-8 overflow-y-auto no-scrollbar relative w-full overflow-hidden">
<div class="flex justify-between items-center mb-8 bg-white p-4 rounded-xl shadow-sm border border-gray-100">
<h3 class="text-2xl font-black text-slate-800 capitalize tracking-tight flex items-center space-x-2">
    <span class="bg-blue-600 w-2 h-8 rounded-full block mr-2"></span>
    <?php 
        if ($tab === 'dashboard') echo "Welcome, " . htmlspecialchars($_SESSION['name']) . "!";
        else if ($tab === 'available_exams') echo "Available Exams";
        else if ($tab === 'my_enrollments') echo "My Enrollments";
        else if ($tab === 'exam_results') echo "Exam Results";
    ?>
</h3>
<div class="flex items-center space-x-4">
<div class="bg-gray-100 border border-gray-200 px-4 py-2 rounded-full flex items-center space-x-3 shadow-inner">
<div class="w-8 h-8 bg-gradient-to-tr from-blue-400 to-blue-300 rounded-full border-2 border-white shadow-sm flex items-center justify-center text-[10px] font-bold text-white uppercase"><?= substr($_SESSION['name'], 0, 1) ?></div>
<span class="text-sm font-bold text-gray-700"><?php echo htmlspecialchars($_SESSION['name']); ?></span>
</div>
</div>
</div>

<?php if(isset($_SESSION['error'])): ?>
    <div class="bg-red-50 text-red-700 border-l-4 border-red-500 rounded p-4 mb-8 font-bold shadow-sm flex items-center">
        <svg class="w-5 h-5 mr-3" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path></svg>
        <?= htmlspecialchars($_SESSION['error']); unset($_SESSION['error']); ?>
    </div>
<?php endif; ?>
<?php if(isset($_SESSION['success'])): ?>
    <div class="bg-green-50 text-green-700 border-l-4 border-green-500 rounded p-4 mb-8 font-bold shadow-sm flex items-center">
        <svg class="w-5 h-5 mr-3" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path></svg>
        <?= htmlspecialchars($_SESSION['success']); unset($_SESSION['success']); ?>
    </div>
<?php endif; ?>

<!-- Content Area -->
<div class="space-y-10">

    <?php if ($tab === 'dashboard'): ?>
    <!-- Dashboard Hero / Welcome Section -->
    <div class="relative bg-gradient-to-br from-brand-sidebar to-blue-900 rounded-[2rem] p-10 overflow-hidden shadow-2xl border border-white/10 group">
        <!-- Abstract Decorations -->
        <div class="absolute -right-20 -top-20 w-80 h-80 bg-blue-500/20 rounded-full blur-[100px] group-hover:bg-blue-400/30 transition-all duration-1000"></div>
        <div class="absolute -left-20 -bottom-20 w-80 h-80 bg-indigo-500/10 rounded-full blur-[80px]"></div>
        
        <div class="relative z-10 flex flex-col md:flex-row justify-between items-center">
            <div class="mb-8 md:mb-0">
                <span class="inline-block px-4 py-1.5 bg-blue-500/20 backdrop-blur-md border border-white/10 rounded-full text-blue-200 text-[10px] font-black uppercase tracking-widest mb-4">Student Dashboard</span>
                <h2 class="text-4xl md:text-5xl font-black text-white leading-tight mb-4">Hello, <?= explode(' ', $_SESSION['name'])[0] ?>! 👋</h2>
                <p class="text-blue-100/70 max-w-md font-medium leading-relaxed">Ready to advance your career? Track your progress, manage your certifications, and conquer your next exam today.</p>
                <div class="mt-8 flex items-center space-x-4">
                    <a href="?tab=available_exams" class="bg-white text-blue-900 px-8 py-3.5 rounded-2xl font-black text-xs uppercase tracking-widest hover:bg-blue-50 transition-all shadow-xl shadow-blue-900/40 active:scale-95">Explore Exams</a>
                    <a href="?tab=my_enrollments" class="bg-blue-500/30 text-white border border-white/10 backdrop-blur-sm px-8 py-3.5 rounded-2xl font-black text-xs uppercase tracking-widest hover:bg-blue-500/40 transition-all active:scale-95">My Schedule</a>
                </div>
            </div>
            
            <!-- Quick Stats in Hero -->
            <div class="grid grid-cols-2 gap-4 w-full md:w-auto">
                <div class="bg-white/5 backdrop-blur-xl border border-white/10 p-6 rounded-3xl text-center group/card hover:bg-white/10 transition-all">
                    <p class="text-3xl font-black text-white mb-1"><?= count($all_scorecards) ?></p>
                    <p class="text-[9px] font-black text-blue-300 uppercase tracking-widest">Completed</p>
                </div>
                <div class="bg-white/5 backdrop-blur-xl border border-white/10 p-6 rounded-3xl text-center group/card hover:bg-white/10 transition-all">
                    <p class="text-3xl font-black text-white mb-1"><?= count($my_enrollments) ?></p>
                    <p class="text-[9px] font-black text-blue-300 uppercase tracking-widest">Ongoing</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Dashboard Content Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Main Activity Area -->
        <div class="lg:col-span-2 space-y-8">
            <!-- Recent Enrollments -->
            <section>
                <div class="flex justify-between items-center mb-6">
                    <h4 class="font-black text-slate-800 text-lg uppercase tracking-tight">Active Enrollments</h4>
                    <a href="?tab=my_enrollments" class="text-xs font-black text-blue-600 hover:underline uppercase tracking-widest">See All</a>
                </div>
                <div class="space-y-4">
                    <?php if(count($my_enrollments) > 0): ?>
                        <?php foreach(array_slice($my_enrollments, 0, 3) as $en): ?>
                            <div class="bg-white border border-gray-100 p-6 rounded-3xl shadow-sm hover:shadow-md transition-all flex items-center justify-between group">
                                <div class="flex items-center space-x-4">
                                    <div class="w-12 h-12 bg-indigo-50 text-indigo-600 rounded-2xl flex items-center justify-center shadow-sm">
                                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path></svg>
                                    </div>
                                    <div>
                                        <h5 class="font-black text-slate-800 group-hover:text-blue-600 transition-colors"><?= htmlspecialchars($en['title']) ?></h5>
                                        <p class="text-[10px] font-bold text-slate-600 uppercase tracking-widest mt-1">Exam Date: <?= date("d M Y", strtotime($en['exam_date'])) ?></p>
                                    </div>
                                </div>
                                <div class="flex items-center space-x-3">
                                    <?php if (!$en['result_status']): ?>
                                        <a href="exam_engine.php?exam_id=<?= $en['exam_id'] ?>" class="px-5 py-2.5 bg-blue-600 text-white rounded-xl text-[10px] font-black uppercase tracking-widest shadow-lg shadow-blue-100 hover:-translate-y-0.5 transition-all">Start Now</a>
                                    <?php else: ?>
                                        <span class="px-5 py-2.5 bg-emerald-50 text-emerald-600 rounded-xl text-[10px] font-black uppercase tracking-widest border border-emerald-100">Completed</span>
                                    <?php endif; ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div class="bg-gray-50 border-2 border-dashed border-gray-200 p-10 rounded-[2rem] text-center">
                            <p class="text-gray-400 font-bold text-sm">You haven't enrolled in any exams yet.</p>
                            <a href="?tab=available_exams" class="mt-4 inline-block text-blue-600 font-black text-xs uppercase tracking-widest">Browse Exams</a>
                        </div>
                    <?php endif; ?>
                </div>
            </section>
        </div>

        <!-- Sidebar Area -->
        <div class="space-y-8">
            <!-- Performance Insights -->
            <section class="bg-white border border-gray-100 p-8 rounded-[2rem] shadow-sm">
                <h4 class="font-black text-slate-800 text-lg uppercase tracking-tight mb-8">Quick Insights</h4>
                
                <div class="space-y-6">
                    <!-- Average Score -->
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-[10px] font-black text-slate-600 uppercase tracking-widest mb-1">Average Score</p>
                            <h5 class="text-2xl font-black text-slate-800">
                                <?php 
                                    if(count($all_scorecards) > 0) {
                                        $avg = array_sum(array_column($all_scorecards, 'total_score')) / count($all_scorecards);
                                        echo round($avg) . '%';
                                    } else {
                                        echo 'N/A';
                                    }
                                ?>
                            </h5>
                        </div>
                        <div class="w-12 h-12 bg-emerald-100 text-emerald-600 rounded-2xl flex items-center justify-center">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path></svg>
                        </div>
                    </div>

                    <!-- Pass Rate -->
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-[10px] font-black text-slate-600 uppercase tracking-widest mb-1">Passing Rate</p>
                            <h5 class="text-2xl font-black text-slate-800">
                                <?php 
                                    if(count($all_scorecards) > 0) {
                                        $passed = count(array_filter($all_scorecards, fn($c) => $c['status'] === 'Pass'));
                                        echo round(($passed / count($all_scorecards)) * 100) . '%';
                                    } else {
                                        echo 'N/A';
                                    }
                                ?>
                            </h5>
                        </div>
                        <div class="w-12 h-12 bg-blue-100 text-blue-600 rounded-2xl flex items-center justify-center">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        </div>
                    </div>

                    <div class="pt-6 border-t border-gray-100">
                        <p class="text-[10px] font-black text-slate-600 uppercase tracking-widest mb-4">Target Progress</p>
                        <div class="w-full bg-gray-100 h-2 rounded-full overflow-hidden mb-2">
                            <div class="bg-blue-600 h-full rounded-full" style="width: <?= count($all_scorecards) > 0 ? min(100, count($all_scorecards) * 10) : 0 ?>%"></div>
                        </div>
                        <div class="flex justify-between text-[9px] font-black text-slate-600 uppercase tracking-widest">
                            <span>Level 1</span>
                            <span><?= count($all_scorecards) ?> / 10 Exams</span>
                        </div>
                    </div>
                </div>
            </section>

        </div>
    </div>
    <?php endif; ?>

    <?php if ($tab === 'available_exams'): ?>
    <!-- Upcoming Exams -->
    <section class="animate-in fade-in slide-in-from-bottom-6 duration-700">
        <div class="flex items-center space-x-4 mb-10">
            <div class="p-3 bg-indigo-600 text-white rounded-2xl shadow-lg shadow-indigo-100"><?= $icons['exams'] ?></div>
            <div>
                <h4 class="font-black text-slate-900 text-2xl tracking-tighter uppercase">Upcoming Exams</h4>
                <p class="text-[11px] font-black text-slate-600 uppercase tracking-widest mt-1">Available Examination Catalog</p>
            </div>
        </div>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-8">
            <?php if(count($exams)===0): ?>
                <div class="col-span-full bg-white p-20 rounded-[2.5rem] shadow-2xl shadow-slate-200/50 border border-gray-100 text-center">
                    <div class="w-20 h-20 bg-indigo-50 rounded-full flex items-center justify-center mx-auto mb-6 text-indigo-200">
                        <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    </div>
                    <p class="text-slate-700 font-black text-lg">No active exams available</p>
                    <p class="text-slate-600 text-sm mt-1">Check back later for newly scheduled sessions.</p>
                </div>
            <?php else: ?>
                <?php foreach($exams as $ex): ?>
                    <div class="bg-white p-8 rounded-[2.5rem] shadow-xl shadow-slate-200/40 border border-gray-100 hover:shadow-2xl hover:shadow-indigo-100/50 transition-all duration-500 flex flex-col group relative overflow-hidden">
                        <!-- Decorative Background Element -->
                        <div class="absolute -right-10 -top-10 w-32 h-32 bg-indigo-50 rounded-full opacity-50 group-hover:scale-150 transition-transform duration-700"></div>
                        
                        <div class="mb-6 relative z-10">
                            <span class="text-[10px] font-black text-indigo-600 uppercase tracking-[0.2em] bg-indigo-50 border-2 border-indigo-100 px-3 py-1 rounded-full mb-4 inline-block shadow-sm"><?= htmlspecialchars($ex['subject']); ?></span>
                            <h3 class="font-black text-slate-900 text-2xl leading-tight tracking-tight group-hover:text-indigo-600 transition-colors h-20 overflow-hidden line-clamp-3"><?= htmlspecialchars($ex['title']); ?></h3>
                            <div class="flex items-center text-slate-600 space-x-2 mt-4">
                                <svg class="w-4 h-4 text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                <span class="text-[12px] font-black uppercase tracking-widest text-slate-600"><?= date("M d, Y", strtotime($ex['exam_date'])); ?></span>
                            </div>
                        </div>

                        <!-- Info Strip -->
                        <div class="grid grid-cols-2 gap-4 py-6 border-t border-gray-50 text-xs font-black text-slate-700 relative z-10">
                            <div class="flex items-center space-x-3 bg-slate-50/50 p-2.5 rounded-xl border border-slate-100">
                                <svg class="w-4 h-4 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                <span><?= date('h:i A', strtotime($ex['start_time'])); ?></span>
                            </div>
                            <div class="flex items-center space-x-3 bg-slate-50/50 p-2.5 rounded-xl border border-slate-100">
                                <svg class="w-4 h-4 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                                <span><?= $ex['duration_minutes']; ?> Min</span>
                            </div>
                        </div>

                        <button onclick="openEnrollModal(<?= $ex['id'] ?>, '<?= htmlspecialchars($ex['title']) ?>')" class="mt-4 w-full bg-indigo-600 hover:bg-indigo-700 text-white py-5 rounded-[1.5rem] text-xs font-black uppercase tracking-[0.2em] transition-all shadow-xl shadow-indigo-100 hover:shadow-indigo-200 group-hover:-translate-y-2 relative z-10 active:scale-95">Enroll Now</button>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </section>
    <?php endif; ?>

    <?php if ($tab === 'my_enrollments'): ?>
    <!-- My Enrollments -->
    <section class="animate-in fade-in slide-in-from-bottom-6 duration-700">
        <div class="bg-white rounded-[2.5rem] shadow-2xl shadow-slate-200/60 border border-gray-100 overflow-hidden transition-all duration-500">
            <!-- Section Header -->
            <div class="px-10 py-9 border-b border-indigo-50 bg-white flex flex-col md:flex-row justify-between md:items-center gap-6 relative">
                <div class="flex items-center space-x-5">
                    <div class="p-4 bg-gradient-to-br from-indigo-600 to-blue-600 shadow-xl shadow-indigo-200 text-white rounded-2xl transform -rotate-3 hover:rotate-0 transition-transform duration-500">
                        <?= $icons['enrollments'] ?>
                    </div>
                    <div>
                        <h4 class="font-black text-slate-900 text-3xl tracking-tighter uppercase">My Enrollments</h4>
                        <p class="text-[11px] font-black text-indigo-400 uppercase tracking-[0.2em] mt-1.5 flex items-center">
                            <span class="w-2 h-2 bg-indigo-500 rounded-full mr-2 animate-pulse"></span>
                            Active Academic Tracking
                        </p>
                    </div>
                </div>
                <div class="flex items-center">
                    <div class="bg-indigo-50/50 border-2 border-indigo-100 px-6 py-3 rounded-2xl shadow-sm">
                        <span class="text-indigo-900 text-sm font-black tracking-tight"><?= count($my_enrollments) ?> Total Registrations</span>
                    </div>
                </div>
            </div>

            <!-- Table Container -->
            <div class="overflow-x-auto w-full">
                <table class="w-full text-left text-sm whitespace-nowrap">
                    <thead>
                        <tr class="bg-indigo-50/30 text-indigo-900 uppercase text-[11px] font-black tracking-[0.25em] border-b border-indigo-100">
                            <th class="px-10 py-6">Examination Identity</th>
                            <th class="px-10 py-6 text-center">Current Status</th>
                            <th class="px-10 py-6 text-center">Operations</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50 bg-white">
                        <?php if(count($my_enrollments)===0): ?>
                            <tr>
                                <td colspan="3" class="p-24 text-center">
                                    <div class="flex flex-col items-center">
                                        <div class="w-20 h-20 bg-slate-50 rounded-full flex items-center justify-center mb-6 text-slate-200">
                                            <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path></svg>
                                        </div>
                                        <p class="text-slate-600 font-black uppercase tracking-[0.2em] text-sm">No Enrollment Data Detected</p>
                                        <a href="?tab=available_exams" class="mt-6 inline-flex items-center space-x-2 bg-indigo-600 text-white px-8 py-3 rounded-xl font-black text-xs uppercase tracking-widest hover:bg-indigo-700 transition-all shadow-lg shadow-indigo-100">
                                            <span>Browse Available Exams</span>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach($my_enrollments as $en): ?>
                                <tr class="hover:bg-indigo-50/20 transition-all duration-300 group border-b border-gray-50 last:border-0">
                                    <td class="px-10 py-8">
                                        <div class="flex items-center space-x-6">
                                            <div class="w-2 h-14 bg-indigo-100 rounded-full group-hover:bg-indigo-600 group-hover:h-16 transition-all duration-500 shadow-sm"></div>
                                            <div>
                                                <p class="font-black text-slate-900 text-lg leading-tight tracking-tight group-hover:text-indigo-600 transition-colors"><?= htmlspecialchars($en['title']) ?></p>
                                                <div class="flex items-center space-x-3 mt-2">
                                                    <span class="flex items-center text-[10px] font-black text-slate-600 uppercase tracking-widest">
                                                        <svg class="w-3.5 h-3.5 mr-1.5 text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                                        Registered: <?= date("d M Y", strtotime($en['exam_date'])) ?>
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-10 py-8 text-center">
                                        <?php if ($en['result_status']): ?>
                                            <div class="inline-flex items-center space-x-2 bg-indigo-50 text-indigo-700 border-2 border-indigo-100 px-6 py-2.5 rounded-full text-[10px] font-black uppercase tracking-[0.15em] shadow-sm">
                                                <span class="w-2 h-2 bg-indigo-500 rounded-full"></span>
                                                <span>Completed</span>
                                            </div>
                                        <?php else: ?>
                                            <div class="inline-flex items-center space-x-2 bg-emerald-50 text-emerald-700 border-2 border-emerald-100 px-6 py-2.5 rounded-full text-[10px] font-black uppercase tracking-[0.15em] shadow-sm">
                                                <span class="w-2 h-2 bg-emerald-500 rounded-full animate-pulse"></span>
                                                <span>Enrolled</span>
                                            </div>
                                        <?php endif; ?>
                                    </td>
                                    <td class="px-10 py-8 text-center">
                                        <div class="flex items-center justify-center space-x-4">
                                            <?php if (!$en['result_status']): ?>
                                                <a href="exam_engine.php?exam_id=<?= $en['exam_id'] ?>" class="group/btn relative inline-flex items-center space-x-3 bg-indigo-600 hover:bg-indigo-700 text-white px-8 py-4 rounded-2xl text-xs font-black uppercase tracking-widest shadow-xl shadow-indigo-100 hover:shadow-indigo-200 transition-all hover:-translate-y-1 active:scale-95 overflow-hidden">
                                                    <div class="absolute inset-0 bg-white/10 translate-x-[-100%] group-hover/btn:translate-x-[100%] transition-transform duration-700"></div>
                                                    <svg class="w-4 h-4 relative z-10" fill="currentColor" viewBox="0 0 24 24"><path d="M8 5v14l11-7z"></path></svg>
                                                    <span class="relative z-10">Launch Exam</span>
                                                </a>
                                            <?php else: ?>
                                                <div class="inline-flex items-center space-x-3 text-emerald-800 font-black text-[11px] uppercase tracking-[0.2em] bg-emerald-50/80 py-4 px-8 rounded-2xl border-2 border-emerald-100 shadow-sm">
                                                    <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg>
                                                    <span>Session Finished</span>
                                                </div>
                                            <?php endif; ?>

                                            <form action="../api/delete_record.php" method="POST" onsubmit="return confirm('CRITICAL: Un-enroll from this exam? This will permanently delete your registration and any existing results.');" class="shrink-0">
                                                <input type="hidden" name="type" value="enrollment">
                                                <input type="hidden" name="id" value="<?= $en['enrollment_id'] ?>">
                                                <button type="submit" class="p-4 bg-rose-50 hover:bg-rose-600 text-rose-500 hover:text-white border-2 border-rose-100 hover:border-rose-600 rounded-2xl transition-all duration-300 shadow-sm hover:shadow-lg hover:shadow-rose-100 group/del">
                                                    <svg class="w-5 h-5 group-hover/del:rotate-12 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
            <!-- Table Footer Decorative -->
            <div class="bg-indigo-50/20 px-10 py-4 border-t border-indigo-50 flex justify-center">
                <p class="text-[9px] font-black text-indigo-300 uppercase tracking-[0.4em]">Integrated Student Portal</p>
            </div>
        </div>
    </section>
    <?php endif; ?>

    <?php if ($tab === 'exam_results'): ?>
    <!-- Exam Results -->
    <?php if (count($all_scorecards) > 0): ?>
    <section class="animate-in fade-in slide-in-from-bottom-6 duration-700">
        <div class="flex items-center space-x-4 mb-10">
            <div class="p-3 bg-emerald-600 text-white rounded-2xl shadow-lg shadow-emerald-100"><?= $icons['results'] ?></div>
            <div>
                <h4 class="font-black text-slate-900 text-2xl tracking-tighter uppercase">Academic Results</h4>
                <p class="text-[11px] font-black text-slate-600 uppercase tracking-widest mt-1">Official certification performance logs</p>
            </div>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
            <?php foreach($all_scorecards as $card): ?>
                <div class="bg-white border border-gray-100 rounded-[2.5rem] shadow-2xl shadow-slate-200/50 hover:shadow-indigo-100 transition-all duration-500 overflow-hidden flex flex-col group">
                    <!-- Blue Gradient Header -->
                    <div class="bg-gradient-to-br from-indigo-900 via-indigo-700 to-blue-600 p-8 text-white relative">
                        <div class="absolute top-[-2rem] right-[-2rem] p-12 opacity-10 scale-150 rotate-12">
                            <svg class="w-32 h-32" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"></path></svg>
                        </div>
                        <div class="flex justify-between items-start relative z-10">
                            <div class="pr-4">
                                <p class="text-[10px] font-black uppercase tracking-[0.25em] text-blue-200 mb-2"><?= htmlspecialchars($card['subject']) ?></p>
                                <h3 class="font-black text-2xl leading-tight tracking-tight h-16 line-clamp-2"><?= htmlspecialchars($card['title']) ?></h3>
                            </div>
                            <?php 
                                $statusBadge = $card['status'] === 'Pass' ? 'bg-emerald-400/20 text-emerald-100 border-emerald-400/30' : 'bg-rose-400/20 text-rose-100 border-rose-400/30';
                            ?>
                            <span class="<?= $statusBadge ?> backdrop-blur-xl border-2 px-6 py-2 rounded-full text-[10px] font-black uppercase tracking-widest shadow-xl shrink-0"><?= $card['status'] ?></span>
                        </div>

                        <!-- Performance Visualizer -->
                        <div class="mt-10 relative z-10">
                            <div class="flex justify-between text-[11px] font-black uppercase tracking-widest mb-3 text-blue-100">
                                <span class="flex items-center"><span class="w-2 h-2 bg-blue-300 rounded-full mr-2"></span>Achieved Score</span>
                                <span class="text-white text-lg leading-none tracking-tighter"><?= (int)$card['total_score'] ?>%</span>
                            </div>
                            <div class="w-full bg-white/10 rounded-full h-3 overflow-hidden border border-white/5 p-0.5">
                                <div class="bg-gradient-to-r from-blue-300 to-white h-full rounded-full shadow-[0_0_20px_rgba(255,255,255,0.4)] transition-all duration-1000" style="width: <?= (int)$card['total_score'] ?>%"></div>
                            </div>
                        </div>
                    </div>

                    <!-- Details Content -->
                    <div class="p-8 flex-1 flex flex-col">
                        <div class="grid grid-cols-2 gap-y-5 gap-x-6 mb-10">
                            <div class="flex items-center space-x-3">
                                <div class="p-2.5 bg-indigo-50 text-indigo-600 rounded-xl"><?= $icons['dashboard'] ?></div>
                                <div class="flex flex-col">
                                    <span class="text-[9px] font-black text-slate-600 uppercase tracking-widest">Exam Date</span>
                                    <span class="text-[13px] font-black text-slate-700"><?= date('d M Y', strtotime($card['exam_date'])) ?></span>
                                </div>
                            </div>
                            <div class="flex items-center space-x-3">
                                <div class="p-2.5 bg-indigo-50 text-indigo-600 rounded-xl"><?= $icons['results'] ?></div>
                                <div class="flex flex-col">
                                    <span class="text-[9px] font-black text-slate-600 uppercase tracking-widest">Duration</span>
                                    <span class="text-[13px] font-black text-slate-700"><?= $card['duration_minutes'] ?> Mins</span>
                                </div>
                            </div>
                            <div class="flex items-center space-x-3">
                                <div class="p-2.5 bg-indigo-50 text-indigo-600 rounded-xl">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                </div>
                                <div class="flex flex-col">
                                    <span class="text-[9px] font-black text-slate-600 uppercase tracking-widest">Started At</span>
                                    <span class="text-[13px] font-black text-slate-700"><?= date('h:i A', strtotime($card['start_time'])) ?></span>
                                </div>
                            </div>
                            <div class="flex items-center space-x-3">
                                <div class="p-2.5 bg-indigo-50 text-indigo-600 rounded-xl">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                </div>
                                <div class="flex flex-col">
                                    <span class="text-[9px] font-black text-slate-600 uppercase tracking-widest">Pass Thresh</span>
                                    <span class="text-[13px] font-black text-slate-700"><?= (int)$card['passing_marks'] ?>%</span>
                                </div>
                            </div>
                        </div>

                        <!-- Action Controls -->
                        <div class="mt-auto flex items-center space-x-4">
                            <a href="detailed_result.php?id=<?= $card['enrollment_id'] ?>" class="flex-1 flex items-center justify-center space-x-3 bg-indigo-50 hover:bg-indigo-600 border-2 border-indigo-100 hover:border-indigo-600 text-indigo-600 hover:text-white font-black py-4 rounded-2xl transition-all duration-300 text-[11px] uppercase tracking-[0.15em] group/view shadow-sm hover:shadow-xl hover:shadow-indigo-100 active:scale-95">
                                <svg class="w-4 h-4 group-hover/view:scale-110 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                                <span>Report Analysis</span>
                            </a>
                            <form action="../api/delete_record.php" method="POST" onsubmit="return confirm('DANGER: This will permanently delete your results for this exam. Proceed?');" class="shrink-0">
                                <input type="hidden" name="type" value="result_by_enrollment">
                                <input type="hidden" name="id" value="<?= $card['enrollment_id'] ?>">
                                <button type="submit" class="p-4 bg-rose-50 hover:bg-rose-600 text-rose-500 hover:text-white border-2 border-rose-100 hover:border-rose-600 rounded-2xl transition-all duration-300 shadow-sm hover:shadow-lg hover:shadow-rose-100 group/del">
                                    <svg class="w-5 h-5 group-hover/del:rotate-12 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </section>
    <?php else: ?>
    <div class="bg-white p-24 rounded-[3rem] shadow-2xl shadow-slate-200/50 border border-gray-100 text-center">
        <div class="w-24 h-24 bg-slate-50 rounded-full flex items-center justify-center mx-auto mb-8 text-slate-200">
            <svg class="w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
        </div>
        <p class="text-slate-700 font-black text-xl uppercase tracking-widest">No Result Data Found</p>
        <p class="text-slate-600 text-sm mt-2">Complete an exam to see your performance metrics here.</p>
    </div>
    <?php endif; ?>
    <?php endif; ?>

</div>

</main>
</div>
</section>
</div>
</div>

<!-- Enroll Modal -->
<div id="enrollModal" class="fixed inset-0 z-[100] bg-slate-900/40 backdrop-blur-sm hidden items-center justify-center p-4 flex drop-shadow-2xl">
    <form action="../api/exam_manager.php" method="POST" class="bg-white rounded-2xl shadow-[0_20px_50px_rgba(0,0,0,0.5)] w-full max-w-[450px] overflow-hidden border border-gray-200 transform scale-100 transition-all">
        <input type="hidden" name="action" value="enroll">
        <input type="hidden" name="exam_id" id="m-exam-id">
        
        <div class="bg-brand-sidebar text-white px-6 py-4 flex justify-between items-center border-b border-slate-700/50">
            <h3 class="text-md font-bold tracking-wider flex items-center space-x-2">
                <svg class="w-5 h-5 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                <span>Enroll in Exam</span>
            </h3>
            <button type="button" class="text-slate-400 hover:text-white transition-colors bg-slate-800 hover:bg-slate-700 p-1 rounded-md" onclick="closeEnrollModal()">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M6 18L18 6M6 6l12 12" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"></path></svg>
            </button>
        </div>

        <div class="p-8 space-y-6 bg-gray-50/50">
            <!-- Target Exam Display -->
            <div class="bg-indigo-50 border border-indigo-100 p-4 rounded-xl">
                <p class="text-[10px] font-black text-indigo-400 uppercase tracking-widest mb-1">Target Examination</p>
                <p class="text-lg font-black text-slate-900 leading-tight" id="m-exam-title">---</p>
            </div>

            <div>
                <label class="text-[11px] tracking-widest uppercase font-black text-slate-700 mb-2 block">Registration Identity:</label>
                <input type="text" name="reg_number" required class="w-full border-gray-300 rounded-lg text-sm p-3 bg-white shadow-inner font-semibold text-gray-800 focus:ring-blue-500 focus:border-blue-500 transition-all" placeholder="e.g. B23F0352SE016">
            </div>
            
            <div>
                <label class="text-[11px] tracking-widest uppercase font-black text-slate-700 mb-2 block">Department:</label>
                <input type="text" name="department" required class="w-full border-gray-300 rounded-lg text-sm p-3 bg-white shadow-inner font-semibold text-gray-800 focus:ring-blue-500 focus:border-blue-500 transition-all" placeholder="IT & CS">
            </div>
            
            <div>
                <label class="text-[11px] tracking-widest uppercase font-black text-slate-700 mb-2 block">Academic Program:</label>
                <div class="relative">
                    <select name="program" class="w-full border-gray-300 rounded-lg text-sm p-3 bg-white shadow-inner font-semibold text-gray-800 focus:ring-blue-500 focus:border-blue-500 transition-all cursor-pointer appearance-none">
                        <option value="Software Engineering">Software Engineering</option>
                        <option value="Computer Science">Computer Science</option>
                        <option value="Information Technology">Information Technology</option>
                    </select>
                    <div class="absolute right-3 top-1/2 -translate-y-1/2 pointer-events-none text-gray-400">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                    </div>
                </div>
            </div>
        </div>

        <div class="p-5 bg-white flex justify-end space-x-3 border-t border-gray-200 shadow-inner">
            <button type="button" class="px-5 py-2.5 border border-gray-300 text-gray-600 rounded-lg text-sm bg-gray-50 hover:bg-gray-100 hover:text-gray-800 font-bold tracking-wide transition-all shadow-sm" onclick="closeEnrollModal()">Cancel</button>
            <button type="submit" class="px-6 py-2.5 bg-indigo-600 text-white rounded-lg text-sm hover:bg-indigo-700 font-black tracking-widest uppercase shadow-lg shadow-indigo-200 hover:-translate-y-0.5 transition-all">Confirm Enrollment</button>
        </div>
    </form>
</div>
<script>
    document.getElementById('enrollModal').classList.add('flex');
    document.getElementById('enrollModal').style.display = '';
</script>

</body>
</html>
