<?php
// admin/dashboard.php
require_once '../config/db.php';
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') { header('Location: ../php/index.php'); exit; }

$tab = $_GET['tab'] ?? 'dashboard';

// Dynamic Stats binding
$stats = [];
$stats['total_students'] = $pdo->query("SELECT COUNT(*) FROM users WHERE role='student'")->fetchColumn();
$stats['total_exams'] = $pdo->query("SELECT COUNT(*) FROM exams")->fetchColumn();
$stats['enrollments'] = $pdo->query("SELECT COUNT(*) FROM enrollments")->fetchColumn();

// Fetch enrollments
$stmt = $pdo->query("SELECT x.id as exam_id, subject, title as exam_title, exam_date, status, (SELECT COUNT(*) FROM enrollments e WHERE e.exam_id = x.id) as reg_count FROM exams x ORDER BY exam_date DESC LIMIT 5");
$enrollments = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Fetch all exams for dropdowns
$all_exams = $pdo->query("SELECT * FROM exams ORDER BY id DESC")->fetchAll(PDO::FETCH_ASSOC);

// Define SVG Icons
$icons = [
    'dashboard' => '<svg class="w-5 h-5 opacity-70" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"></path></svg>',
    'questions' => '<svg class="w-5 h-5 opacity-70" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>',
    'students'  => '<svg class="w-5 h-5 opacity-70" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>',
    'live'      => '<svg class="w-5 h-5 opacity-70" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>',
    'results'   => '<svg class="w-5 h-5 opacity-70" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>',
    'create'    => '<svg class="w-5 h-5 opacity-70" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v3m0 0v3m0-3h3m-3 0H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>'
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
<title>Admin Dashboard</title>
<script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
<style data-purpose="typography">
    @import url('https://fonts.googleapis.com/css2?family=Outfit:wght@100..900&display=swap');
    body {
        font-family: 'Outfit', sans-serif;
    }
</style>
<script>
    tailwind.config = {
      theme: { extend: { 
        fontFamily: { sans: ['Outfit', 'sans-serif'] },
        colors: { 'brand-dark': '#1e293b', 'brand-sidebar': '#1e3a5f', 'brand-bg': '#f3f4f6', 'card-total': '#3b82f6', 'card-active': '#64748b', 'card-pending': '#f87171', 'card-recent': '#10b981', } 
      } }
    }
</script>
<style>.no-scrollbar::-webkit-scrollbar { display: none; } .no-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }</style>
</head>
<body class="bg-gray-100 font-sans text-gray-800 antialiased h-screen overflow-hidden">
<div class="w-full h-full flex flex-col">
<div class="flex-1 w-full flex flex-col overflow-hidden">

<section class="flex flex-col w-full h-full bg-white">
<div class="flex justify-between items-center px-6 py-4 bg-brand-sidebar border-b-2 border-white z-20 flex-shrink-0">
    <div class="flex items-center space-x-4 text-white">
        <div class="w-10 h-10 bg-blue-500 rounded-lg flex items-center justify-center font-black text-white tracking-widest text-md shadow-inner border border-blue-400">AD</div>
        <div class="flex flex-col">
            <div class="font-bold text-white tracking-wide leading-tight">Administrator</div>
            <div class="text-[10px] text-blue-300 font-bold uppercase tracking-widest">Main settings</div>
        </div>
    </div>
    <div class="flex items-center space-x-4">
        <!-- Profile Pill (Top Bar) -->
        <div class="bg-white/5 border border-white/10 px-4 py-2 rounded-xl flex items-center space-x-3 shadow-inner group hover:bg-white/10 transition-all cursor-default">
            <div class="w-8 h-8 bg-gradient-to-tr from-indigo-400 to-blue-300 rounded-lg border border-white/20 shadow-md group-hover:rotate-12 transition-transform flex items-center justify-center text-white font-black text-xs">A</div>
            <span class="text-xs font-black text-white/90 tracking-tight"><?php echo htmlspecialchars($_SESSION['name']); ?></span>
        </div>
        <!-- Compact Premium Logout -->
        <a href="../api/auth.php?action=logout" class="flex items-center space-x-2 bg-rose-500 hover:bg-rose-600 text-white px-5 py-2.5 rounded-xl font-black text-[10px] uppercase tracking-widest transition-all shadow-lg shadow-rose-900/40 active:scale-95 group">
            <span>Logout</span>
            <svg class="w-4 h-4 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path></svg>
        </a>
    </div>
</div>
<div class="bg-white overflow-hidden flex flex-1 relative">

<aside class="w-72 bg-brand-sidebar text-white flex-col hidden md:flex border-r border-gray-800/20 box-border z-10 transition-all">
<nav class="flex-1 px-5 py-6 space-y-2">
<?= navLink($tab, 'dashboard', 'Dashboard', $icons['dashboard']) ?>
<button onclick="document.getElementById('modal-1').classList.remove('hidden')" class="w-full text-left flex items-center space-x-3 hover:bg-white/10 text-blue-100 font-medium p-3 rounded-lg transition-all focus:outline-none">
    <div><?= $icons['create'] ?></div>
    <span>Create New Exam</span>
</button>
<?= navLink($tab, 'questions', 'Question Bank', $icons['questions']) ?>
<?= navLink($tab, 'students', 'Student Records', $icons['students']) ?>
<?= navLink($tab, 'live', 'Live Exams', $icons['live']) ?>
<?= navLink($tab, 'results', 'Exam Results', $icons['results']) ?>
</nav>
</aside>

<main class="flex-1 bg-brand-bg p-8 overflow-y-auto no-scrollbar relative w-full overflow-hidden">
<div class="flex justify-between items-center mb-8 bg-white p-4 rounded-xl shadow-sm border border-gray-100">
<h3 class="text-2xl font-black text-slate-800 capitalize tracking-tight flex items-center space-x-2">
    <span class="bg-blue-600 w-2 h-8 rounded-full block mr-2"></span>
    <?= str_replace('_', ' ', htmlspecialchars($tab)) ?>
</h3>
    <div class="flex items-center space-x-2 text-[10px] font-black text-slate-600 uppercase tracking-widest bg-slate-50 px-4 py-2 rounded-xl border border-slate-100">
        <span class="w-2 h-2 bg-emerald-500 rounded-full animate-pulse"></span>
        <span>System Status: Online</span>
    </div>
</div>


<?php if ($tab === 'dashboard'): ?>
<!-- Admin Dashboard Hero -->
<div class="relative bg-gradient-to-br from-brand-sidebar to-[#0f172a] rounded-[2rem] p-10 overflow-hidden shadow-2xl border border-white/10 group mb-10">
    <!-- Abstract Decorations -->
    <div class="absolute -right-20 -top-20 w-80 h-80 bg-blue-500/10 rounded-full blur-[100px] group-hover:bg-blue-400/20 transition-all duration-1000"></div>
    <div class="absolute -left-20 -bottom-20 w-80 h-80 bg-indigo-500/10 rounded-full blur-[80px]"></div>
    
    <div class="relative z-10 flex flex-col md:flex-row justify-between items-center">
        <div class="mb-8 md:mb-0">
            <span class="inline-block px-4 py-1.5 bg-blue-500/20 backdrop-blur-md border border-white/10 rounded-full text-blue-200 text-[10px] font-black uppercase tracking-widest mb-4">Administration Console</span>
            <h2 class="text-4xl md:text-5xl font-black text-white leading-tight mb-4">Welcome Back, Admin!</h2>
            <p class="text-blue-100/70 max-w-md font-medium leading-relaxed italic">"The secret of leadership is simple: Do what you believe in. Paint a picture of the future. Go there."</p>
            <div class="mt-8 flex items-center space-x-4">
                <button onclick="document.getElementById('modal-1').classList.remove('hidden')" class="bg-blue-600 text-white px-8 py-3.5 rounded-2xl font-black text-xs uppercase tracking-widest hover:bg-blue-500 transition-all shadow-xl shadow-blue-900/40 active:scale-95 flex items-center space-x-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path></svg>
                    <span>Create Exam</span>
                </button>
                <a href="?tab=live" class="bg-white/10 text-white border border-white/10 backdrop-blur-sm px-8 py-3.5 rounded-2xl font-black text-xs uppercase tracking-widest hover:bg-white/20 transition-all active:scale-95 flex items-center space-x-2">
                    <span class="w-2 h-2 bg-red-500 rounded-full animate-pulse mr-1"></span>
                    <span>Monitor Live</span>
                </a>
            </div>
        </div>
        
        <!-- High Level Stats -->
        <div class="grid grid-cols-2 gap-4 w-full md:w-auto">
            <div class="bg-white/5 backdrop-blur-xl border border-white/10 p-6 rounded-3xl text-center group/card hover:bg-white/10 transition-all">
                <p class="text-4xl font-black text-white mb-1"><?= $stats['total_students'] ?></p>
                <p class="text-[9px] font-black text-blue-300 uppercase tracking-widest">Active Students</p>
            </div>
            <div class="bg-white/5 backdrop-blur-xl border border-white/10 p-6 rounded-3xl text-center group/card hover:bg-white/10 transition-all">
                <p class="text-4xl font-black text-white mb-1"><?= $stats['total_exams'] ?></p>
                <p class="text-[9px] font-black text-blue-300 uppercase tracking-widest">Total Exams</p>
            </div>
        </div>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-8 mb-10">
    <!-- Main Overview Table -->
    <div class="lg:col-span-2">
        <div class="bg-white rounded-[2rem] shadow-sm border border-gray-100 overflow-hidden">
            <div class="px-8 py-7 border-b border-gray-50 flex justify-between items-center">
                <h4 class="font-black text-slate-800 text-lg uppercase tracking-tight flex items-center space-x-3">
                    <div class="p-2 bg-blue-50 text-blue-600 rounded-xl"><?= $icons['dashboard'] ?></div>
                    <span>Recent Examinations</span>
                </h4>
                <a href="?tab=results" class="text-xs font-black text-blue-600 hover:underline uppercase tracking-widest">View All Results</a>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm whitespace-nowrap">
                    <thead>
                        <tr class="bg-gradient-to-r from-[#1e3a5f] to-[#2563eb] text-white uppercase text-[11px] font-black tracking-widest">
                            <th class="px-8 py-6">Subject & Title</th>
                            <th class="px-8 py-6 text-center">Registrations</th>
                            <th class="px-8 py-6">Status</th>
                            <th class="px-8 py-6 text-center">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        <?php if(count($enrollments)===0): ?>
                            <tr><td colspan="4" class="p-10 text-center text-gray-400 font-bold uppercase tracking-widest text-xs">No exams found</td></tr>
                        <?php else: ?>
                            <?php foreach($enrollments as $enr): ?>
                                <tr class="hover:bg-indigo-50/20 transition-all duration-300 group">
                                    <td class="px-8 py-7">
                                        <p class="font-black text-slate-900 text-lg leading-tight group-hover:text-indigo-600 transition-colors"><?= htmlspecialchars($enr['exam_title']) ?></p>
                                        <p class="text-[12px] font-black text-slate-700 uppercase tracking-widest mt-1.5"><?= htmlspecialchars($enr['subject']) ?> â€¢ <?= date('M d, Y', strtotime($enr['exam_date'])) ?></p>
                                    </td>
                                    <td class="px-8 py-7 text-center">
                                        <span class="bg-blue-50 text-blue-700 px-4 py-2.5 rounded-xl text-[13px] font-black border border-blue-100 shadow-sm"><?= $enr['reg_count'] ?></span>
                                    </td>
                                    <td class="px-8 py-7">
                                        <span class="px-4 py-1.5 rounded-full text-[11px] font-black tracking-widest uppercase border shadow-sm
                                        <?= $enr['status']==='active'?'bg-emerald-50 text-emerald-700 border-emerald-300':($enr['status']==='completed'?'bg-blue-600 text-white border-blue-700':'bg-amber-50 text-amber-700 border-amber-300'); ?>
                                        "><?= $enr['status'] ?></span>
                                    </td>
                                    <td class="px-8 py-7 text-center">
                                        <div class="flex items-center justify-center space-x-3">
                                            <?php if($enr['status'] !== 'completed'): ?>
                                                <form action="../api/exam_manager.php" method="POST">
                                                    <input type="hidden" name="action" value="update_status">
                                                    <input type="hidden" name="exam_id" value="<?= $enr['exam_id'] ?>">
                                                    <input type="hidden" name="status" value="completed">
                                                    <button type="submit" class="p-3 bg-indigo-50 hover:bg-indigo-600 text-indigo-600 hover:text-white border border-indigo-100 hover:border-indigo-600 rounded-2xl transition-all duration-300 shadow-sm active:scale-95 group/check" title="Mark as Completed">
                                                        <svg class="w-4 h-4 group-hover/check:scale-110" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg>
                                                    </button>
                                                </form>
                                            <?php endif; ?>
                                            <form action="../api/delete_record.php" method="POST" onsubmit="return confirm('Delete this exam?');">
                                                <input type="hidden" name="type" value="exam">
                                                <input type="hidden" name="id" value="<?= $enr['exam_id'] ?>">
                                                <button type="submit" class="p-3 bg-rose-50 hover:bg-rose-600 text-rose-500 hover:text-white border border-rose-100 hover:border-rose-600 rounded-2xl transition-all duration-300 shadow-sm active:scale-95 group/del" title="Delete Exam">
                                                    <svg class="w-4 h-4 group-hover/del:rotate-12 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
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
        </div>
    </div>

    <!-- Quick Stats Sidecards -->
    <div class="space-y-8">
        <div class="bg-white p-8 rounded-[2rem] shadow-xl shadow-slate-200/50 border border-gray-100 relative overflow-hidden">
            <div class="flex items-center space-x-4 mb-6">
                <div class="p-3 bg-amber-100 text-amber-600 rounded-2xl shadow-inner"><?= $icons['students'] ?></div>
                <h5 class="font-black text-slate-800 text-sm uppercase tracking-widest">Platform Reach</h5>
            </div>
            <p class="text-5xl font-black text-slate-900 mb-2 tracking-tighter"><?= $stats['enrollments'] ?></p>
            <div class="inline-block bg-slate-50 border border-slate-100 px-3 py-1 rounded-lg">
                <p class="text-[10px] font-black text-slate-700 uppercase tracking-widest">Total Enrollments</p>
            </div>
            <div class="mt-8 pt-8 border-t border-slate-50">
                <div class="flex justify-between items-center text-[10px] font-black uppercase tracking-widest mb-3">
                    <span class="text-slate-800">System Load</span>
                    <span class="text-blue-600">Optimal (Low)</span>
                </div>
                <div class="w-full bg-slate-100 h-2 rounded-full overflow-hidden border border-slate-200 shadow-inner">
                    <div class="bg-gradient-to-r from-blue-500 to-indigo-600 h-full w-[65%] rounded-full shadow-[0_0_12px_rgba(37,99,235,0.4)]"></div>
                </div>
            </div>
        </div>

        <div class="bg-[#1e3a5f] p-8 rounded-[2rem] shadow-2xl relative overflow-hidden group">
            <div class="absolute -right-10 -bottom-10 w-40 h-40 bg-white/5 rounded-full blur-3xl group-hover:scale-150 transition-all duration-700"></div>
            <h5 class="font-black text-white text-lg uppercase tracking-tight mb-4 relative z-10">Quick Actions</h5>
            <div class="grid grid-cols-2 gap-3 relative z-10">
                <a href="?tab=questions" class="bg-white/10 hover:bg-white/20 border border-white/10 p-4 rounded-2xl text-center transition-all">
                    <div class="text-blue-300 mb-2 flex justify-center"><?= $icons['questions'] ?></div>
                    <p class="text-[10px] font-black text-white uppercase tracking-widest">Question Bank</p>
                </a>
                <a href="?tab=students" class="bg-white/10 hover:bg-white/20 border border-white/10 p-4 rounded-2xl text-center transition-all">
                    <div class="text-indigo-300 mb-2 flex justify-center"><?= $icons['students'] ?></div>
                    <p class="text-[10px] font-black text-white uppercase tracking-widest">Students</p>
                </a>
            </div>
        </div>
    </div>
</div>
<?php endif; ?>

<?php if ($tab === 'questions'): ?>
<div class="bg-white rounded-[2rem] p-10 shadow-2xl shadow-slate-200/50 border border-gray-100 w-full min-h-[calc(100vh-180px)] flex flex-col animate-in fade-in slide-in-from-bottom-4 duration-500">
    <div class="flex justify-between items-center mb-10 border-b border-indigo-50 pb-8">
        <div class="flex items-center space-x-4">
            <div class="w-2 h-12 bg-indigo-600 rounded-full"></div>
            <div>
                <h4 class="font-black text-4xl text-slate-900 tracking-tight">Question Repository</h4>
                <p class="text-sm text-slate-500 font-black uppercase tracking-widest mt-1">Bulk import examination data via standardized CSV</p>
            </div>
        </div>
        <div class="bg-indigo-600 px-6 py-3 rounded-2xl text-white text-[10px] font-black uppercase tracking-widest shadow-xl shadow-indigo-200">
            Secure Upload Channel
        </div>
    </div>
    
    <div class="bg-amber-50 border-2 border-amber-100 p-6 mb-10 rounded-[1.5rem] flex items-start space-x-4">
        <div class="flex-shrink-0 p-2 bg-amber-200 text-amber-700 rounded-xl shadow-inner">
            <svg class="h-6 w-6" viewBox="0 0 20 20" fill="currentColor">
                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
            </svg>
        </div>
        <div>
            <p class="text-sm text-amber-800 font-black uppercase tracking-widest mb-1.5">Mcqs Format</p>
            <code class="text-xs font-mono bg-white px-4 py-2 inline-block rounded-xl border border-amber-200 text-amber-700 font-bold shadow-sm">Question, Option A, Option B, Option C, Option D, Correct Option (A/B/C/D)</code>
        </div>
    </div>
    
    <form action="../api/csv_parser.php" method="POST" enctype="multipart/form-data" class="flex-1 flex flex-col space-y-10" onsubmit="handleUpload(event)">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-10">
            <div class="space-y-4">
                <label class="block text-[11px] font-black text-slate-600 tracking-widest uppercase ml-2">1. Select Target Examination</label>
                <div class="relative group">
                    <select name="exam_id" class="w-full bg-white border-2 border-indigo-50 rounded-[1.5rem] text-base p-5 font-black text-slate-700 shadow-xl shadow-indigo-100/30 focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 transition-all cursor-pointer" required>
                        <option value="">-- Click to Select Exam --</option>
                        <?php foreach($all_exams as $ex): ?>
                            <option value="<?= $ex['id'] ?>"><?= htmlspecialchars($ex['subject'].' - '.$ex['title']) ?> (<?= $ex['exam_date'] ?>)</option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>

            <div class="space-y-4">
                <label class="block text-[11px] font-black text-slate-600 tracking-widest uppercase ml-2">Data Upload Here</label>
                <div id="drop-zone" class="bg-white border-2 border-dashed border-indigo-200 rounded-[1.5rem] p-10 text-center hover:bg-indigo-50/30 hover:border-indigo-500 transition-all duration-500 cursor-pointer relative group flex flex-col items-center justify-center min-h-[220px] shadow-xl shadow-indigo-100/20">
                    <input type="file" name="csv_file" id="csv-file-input" accept=".csv" required class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10">
                    
                    <div id="file-idle-ui" class="space-y-4 transition-all duration-300">
                        <div class="w-20 h-20 bg-indigo-50 text-indigo-600 rounded-[1.5rem] mx-auto flex items-center justify-center group-hover:scale-110 group-hover:rotate-6 transition-all duration-500 shadow-lg shadow-indigo-100 border border-indigo-100">
                            <svg class="h-10 w-10" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path></svg>
                        </div>
                        <div>
                            <h5 class="text-xl font-black text-slate-800 tracking-tight">Deploy CSV File</h5>
                            <p class="mt-2 text-xs text-slate-600 font-black uppercase tracking-widest">Drop here or tap to browse</p>
                        </div>
                    </div>

                    <div id="file-selected-ui" class="hidden space-y-4 animate-in fade-in zoom-in duration-500">
                        <div class="w-20 h-20 bg-emerald-50 text-emerald-600 rounded-[1.5rem] mx-auto flex items-center justify-center shadow-lg shadow-emerald-100 border border-emerald-100">
                            <svg class="h-10 w-10" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        </div>
                        <div>
                            <h5 id="selected-file-name" class="text-xl font-black text-emerald-700 tracking-tight">filename.csv</h5>
                            <p class="text-[10px] text-emerald-500 font-black uppercase tracking-widest mt-1">Ready for initialization</p>
                        </div>
                        <button type="button" id="change-file-btn" class="text-[10px] bg-slate-100 text-slate-700 px-4 py-2 rounded-lg font-black uppercase tracking-widest hover:bg-slate-200 transition-colors z-20 relative">Reset Selection</button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Progress Bar Container -->
        <div id="upload-progress-container" class="hidden mt-10 p-10 bg-slate-50 rounded-[2rem] border border-slate-100 shadow-inner">
            <div id="upload-success-msg" class="hidden max-w-2xl mx-auto mb-8 bg-emerald-600 p-6 rounded-[1.5rem] flex items-center space-x-6 animate-in fade-in slide-in-from-top-4 duration-500 shadow-2xl shadow-emerald-200">
                <div class="w-14 h-14 bg-white/20 text-white rounded-xl flex items-center justify-center shadow-inner backdrop-blur-md">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="4" d="M5 13l4 4L19 7"></path></svg>
                </div>
                <div>
                    <h5 class="text-white font-black uppercase tracking-widest text-sm">Initialization Successful!</h5>
                    <p class="text-emerald-100 text-xs font-bold mt-1">Question bank has been synchronized with the selected exam.</p>
                </div>
            </div>

            <div class="max-w-2xl mx-auto">
                <div class="flex justify-between items-center mb-6">
                    <div class="flex items-center space-x-4">
                        <div class="flex space-x-1.5">
                            <span class="w-2.5 h-2.5 bg-indigo-600 rounded-full animate-bounce" style="animation-delay: 0.1s"></span>
                            <span class="w-2.5 h-2.5 bg-indigo-600 rounded-full animate-bounce" style="animation-delay: 0.2s"></span>
                            <span class="w-2.5 h-2.5 bg-indigo-600 rounded-full animate-bounce" style="animation-delay: 0.3s"></span>
                        </div>
                        <span class="text-xs font-black text-indigo-900 uppercase tracking-widest">Processing Secure Data...</span>
                    </div>
                    <span id="upload-percentage" class="text-2xl font-black text-indigo-600 tracking-tighter">0%</span>
                </div>
                <div class="w-full bg-indigo-100 rounded-full h-8 shadow-inner overflow-hidden border-2 border-indigo-200 p-1.5">
                    <div id="upload-progress-bar" class="bg-gradient-to-r from-indigo-500 via-blue-600 to-indigo-800 h-full rounded-full transition-all duration-700 shadow-[0_0_20px_rgba(79,70,229,0.5)]" style="width: 0%"></div>
                </div>
                <p class="text-center text-[10px] text-slate-600 font-black uppercase tracking-widest mt-8 italic">Encrypted Transmission in Progress. Do not terminate.</p>
            </div>
        </div>

        <div class="pt-6">
            <button type="submit" class="w-full bg-indigo-600 text-white font-black uppercase tracking-widest py-6 rounded-[1.5rem] hover:bg-indigo-700 hover:shadow-[0_20px_50px_rgba(79,70,229,0.4)] hover:-translate-y-1 transition-all duration-300 shadow-2xl flex items-center justify-center space-x-4 group overflow-hidden relative">
                <div class="absolute inset-0 bg-white/10 translate-x-[-100%] group-hover:translate-x-[100%] transition-transform duration-1000"></div>
                <span class="relative z-10 text-lg">Upload</span>
                <svg class="w-6 h-6 group-hover:translate-x-2 transition-transform relative z-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M13 7l5 5m0 0l-5 5m5-5H6"></path></svg>
            </button>
        </div>
    </form>
</div>
<?php endif; ?>

<?php if ($tab === 'live'): ?>
    
    <div class="bg-white rounded-[2rem] shadow-2xl shadow-slate-200/50 border border-gray-100 overflow-hidden transform transition duration-500">
    <!-- Tab Header -->
    <div class="p-8 border-b border-indigo-50 bg-white flex flex-col sm:flex-row sm:justify-between sm:items-center gap-6">
        <div class="flex items-center space-x-4">
            <div class="relative">
                <span class="w-4 h-4 bg-red-500 rounded-full block animate-ping absolute inset-0"></span>
                <span class="w-4 h-4 bg-red-500 rounded-full block relative shadow-[0_0_15px_rgba(239,68,68,0.8)]"></span>
            </div>
            <h4 class="font-black text-slate-800 text-2xl tracking-tight uppercase">Live Exam Monitor</h4>
        </div>
        
        <div class="relative w-full sm:w-80">
            <input type="text" id="live-search" placeholder="Search activity..." class="w-full bg-white border-2 border-indigo-50 rounded-2xl text-sm pl-6 pr-14 py-3.5 focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 shadow-xl shadow-indigo-100/50 transition-all font-black text-slate-700 placeholder:text-slate-500">
            <div class="absolute right-4 top-1/2 -translate-y-1/2 p-2 bg-indigo-600 text-white rounded-xl shadow-lg shadow-indigo-200">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
            </div>
        </div>
    </div>

    <!-- Live Table -->
    <div class="overflow-x-auto w-full">
        <table class="w-full text-left text-sm whitespace-nowrap">
            <thead>
                <tr class="bg-gradient-to-r from-[#1e3a5f] to-[#2563eb] text-white uppercase text-[11px] font-black tracking-widest">
                    <th class="px-8 py-6">Candidate Details</th>
                    <th class="px-8 py-6">Examination Identity</th>
                    <th class="px-8 py-6 text-right">Activity Status</th>
                    <th class="px-8 py-6 text-center">Action</th>
                </tr>
            </thead>
            <tbody id="live-tbody" class="divide-y divide-gray-50">
            <?php 
            $live_stmt = $pdo->query("SELECT r.id as result_id, u.name, ex.subject, ex.title, r.status, r.auto_submitted, r.submitted_at FROM results r JOIN enrollments e ON r.enrollment_id=e.id JOIN users u ON e.user_id=u.id JOIN exams ex ON e.exam_id=ex.id ORDER BY r.submitted_at DESC LIMIT 50");
            $live_logs = $live_stmt->fetchAll(PDO::FETCH_ASSOC);
            if(count($live_logs)===0): ?>
                <tr><td colspan="4" class="p-16 text-center text-gray-400 font-bold uppercase tracking-widest text-xs">No active sessions monitored</td></tr>
            <?php else: ?>
                <?php foreach($live_logs as $log): ?>
                <tr class="hover:bg-indigo-50/20 transition-all duration-300 group">
                    <td class="px-8 py-7">
                        <div class="flex items-center space-x-4">
                            <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-indigo-600 to-blue-500 text-white flex items-center justify-center font-black shadow-lg shadow-indigo-100">
                                <?= strtoupper(substr($log['name'],0,1)) ?>
                            </div>
                            <div>
                                <p class="font-black text-slate-900 text-lg leading-tight group-hover:text-indigo-600 transition-colors"><?= htmlspecialchars($log['name']) ?></p>
                                <p class="text-[12px] font-black text-slate-600 uppercase tracking-widest mt-1">Verified Session</p>
                            </div>
                        </div>
                    </td>
                    <td class="px-8 py-7">
                        <p class="font-black text-indigo-700 text-base leading-tight"><?= htmlspecialchars($log['subject'] ?? '') ?></p>
                        <p class="text-[12px] font-black text-slate-600 uppercase tracking-widest mt-1"><?= htmlspecialchars($log['title']) ?></p>
                    </td>
                    <td class="px-8 py-7 text-right">
                        <?php if($log['auto_submitted']): ?>
                            <div class="inline-flex items-center space-x-2 text-white font-black tracking-widest text-[11px] bg-rose-600 border-2 border-rose-400 px-5 py-2 rounded-full uppercase shadow-xl shadow-rose-200 animate-pulse ring-4 ring-rose-500/20 mb-1">
                                <span class="w-2 h-2 bg-white rounded-full animate-ping"></span>
                                <span class="flex items-center">
                                    <svg class="w-3.5 h-3.5 mr-1.5" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2L1 21h22L12 2zm0 3.45l8.28 14.1H3.72L12 5.45zM11 16h2v2h-2v-2zm0-7h2v5h-2V9z"/></svg>
                                    Security Breach: Auto-Submitted
                                </span>
                            </div>
                        <?php else: ?>
                            <div class="inline-flex items-center space-x-2 text-emerald-700 font-black tracking-widest text-[11px] bg-emerald-50 border-2 border-emerald-100 px-4 py-1.5 rounded-full uppercase shadow-sm mb-1">
                                <span class="w-1.5 h-1.5 bg-emerald-500 rounded-full animate-pulse"></span>
                                <span>Normal Submission</span>
                            </div>
                        <?php endif; ?>
                        <p class="text-[13px] text-slate-900 font-black tracking-tighter mt-1"><?= date('H:i:s', strtotime($log['submitted_at'])) ?></p>
                    </td>
                    <td class="px-8 py-7 text-center">
                        <form action="../api/delete_record.php" method="POST" onsubmit="return confirm('Delete this live record?');">
                            <input type="hidden" name="type" value="result">
                            <input type="hidden" name="id" value="<?= $log['result_id'] ?>">
                            <button type="submit" class="p-3.5 bg-rose-50 hover:bg-rose-600 text-rose-500 hover:text-white border border-rose-100 hover:border-rose-600 rounded-2xl transition-all duration-300 shadow-sm active:scale-95">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                            </button>
                        </form>
                    </td>
                </tr>
                <?php endforeach; ?>
            <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
<?php endif; ?>

<?php if ($tab === 'results'): ?>
<?php
// â”€â”€ Fetch all results with full exam details â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€
$res_stmt = $pdo->query("
    SELECT r.id as result_id, e.reg_number, u.name as student_name,
           ex.id as exam_id, ex.subject, ex.title as exam_title,
           ex.total_marks, ex.passing_marks, ex.duration_minutes,
           ex.exam_date, ex.start_time, ex.status as exam_status,
           r.total_score, r.status as result_status
    FROM results r
    JOIN enrollments e ON r.enrollment_id = e.id
    JOIN users u ON e.user_id = u.id
    JOIN exams ex ON e.exam_id = ex.id
    ORDER BY ex.id DESC, r.id DESC
");
$all_results = $res_stmt->fetchAll(PDO::FETCH_ASSOC);

// â”€â”€ Group results by exam â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€
$exams_grouped = [];
foreach ($all_results as $rs) {
    $eid = $rs['exam_id'];
    if (!isset($exams_grouped[$eid])) {
        $exams_grouped[$eid] = [
            'exam_id'          => $rs['exam_id'],
            'subject'          => $rs['subject'],
            'exam_title'       => $rs['exam_title'],
            'total_marks'      => $rs['total_marks'],
            'passing_marks'    => $rs['passing_marks'],
            'duration_minutes' => $rs['duration_minutes'],
            'exam_date'        => $rs['exam_date'],
            'start_time'       => $rs['start_time'],
            'exam_status'      => $rs['exam_status'],
            'results'          => [],
        ];
    }
    $exams_grouped[$eid]['results'][] = $rs;
}
?>

<div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
    <!-- Header -->
    <div class="p-6 border-b border-gray-100 bg-gray-50/50 flex flex-col sm:flex-row sm:justify-between sm:items-center space-y-4 sm:space-y-0">
        <h4 class="font-black text-gray-800 text-lg tracking-tight uppercase flex items-center space-x-3">
            <svg class="w-6 h-6 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
            <span>All Student Results</span>
        </h4>
        <div class="relative">
            <input type="text" id="results-search" placeholder="Search Subject or Exam..." class="bg-white border-2 border-indigo-50 rounded-2xl text-sm pl-5 pr-12 py-3 focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 shadow-xl shadow-indigo-100/50 w-full sm:w-80 transition-all font-bold text-slate-700 placeholder:text-slate-500">
            <div class="absolute right-4 top-1/2 -translate-y-1/2 p-1.5 bg-indigo-600 text-white rounded-lg shadow-lg shadow-indigo-200">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
            </div>
        </div>
    </div>

    <?php if (count($exams_grouped) === 0): ?>
        <div class="p-16 text-center text-gray-400">
            <svg class="w-16 h-16 mx-auto mb-4 text-gray-200" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
            <p class="font-bold text-lg">No results found</p>
            <p class="text-sm mt-1">Results will appear here once students complete exams.</p>
        </div>
    <?php else: ?>

    <!-- â”€â”€ Exam Cards Grid â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€ -->
    <div id="exam-cards-view" class="p-6 grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 gap-6">
        <?php foreach ($exams_grouped as $eid => $eg):
            $total    = count($eg['results']);
            $passed   = count(array_filter($eg['results'], fn($r) => $r['result_status'] === 'Pass'));
            $failed   = $total - $passed;
            $passRate = $total > 0 ? round(($passed / $total) * 100) : 0;

            $statusColor = match($eg['exam_status']) {
                'active'    => 'bg-green-100 text-green-700 border-green-300',
                'completed' => 'bg-gray-200 text-gray-600 border-gray-300',
                default     => 'bg-orange-100 text-orange-700 border-orange-300',
            };
        ?>
        <div class="exam-card bg-white border border-gray-200 rounded-2xl shadow-sm hover:shadow-xl transition-all duration-300 flex flex-col overflow-hidden group cursor-pointer"
             data-exam-subject="<?= strtolower(htmlspecialchars($eg['subject'])) ?>"
             data-exam-title="<?= strtolower(htmlspecialchars($eg['exam_title'])) ?>"
             onclick="showExamResults(<?= $eid ?>)">

            <!-- Gradient header -->
            <div class="bg-gradient-to-br from-[#1e3a5f] to-[#2563eb] p-5 relative overflow-hidden">
                <div class="absolute -right-5 -top-5 w-24 h-24 bg-white/5 rounded-full pointer-events-none"></div>
                <div class="absolute -right-2 bottom-[-1.5rem] w-32 h-32 bg-white/5 rounded-full pointer-events-none"></div>
                <div class="flex justify-between items-start relative z-10">
                    <div class="flex-1 pr-3">
                        <p class="text-blue-200 text-[10px] font-black uppercase tracking-widest mb-1"><?= htmlspecialchars($eg['subject']) ?></p>
                        <h3 class="text-white font-black text-base leading-snug"><?= htmlspecialchars($eg['exam_title']) ?></h3>
                    </div>
                    <span class="px-2.5 py-1 rounded-full text-[10px] font-black tracking-widest uppercase border <?= $statusColor ?> shrink-0 bg-white/90">
                        <?= ucfirst($eg['exam_status']) ?>
                    </span>
                </div>
                <!-- Pass rate bar -->
                <div class="mt-4 relative z-10">
                    <div class="flex justify-between text-[10px] font-bold text-blue-200 mb-1.5">
                        <span>Pass Rate</span><span><?= $passRate ?>%</span>
                    </div>
                    <div class="h-1.5 bg-white/20 rounded-full overflow-hidden">
                        <div class="h-full bg-emerald-400 rounded-full" style="width:<?= $passRate ?>%"></div>
                    </div>
                </div>
            </div>

            <!-- Card body: exam meta -->
            <div class="p-5 flex-1 space-y-3 text-sm text-gray-600">
                <div class="grid grid-cols-2 gap-y-2 gap-x-3">
                    <div class="flex items-center space-x-2">
                        <svg class="w-4 h-4 text-blue-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                        <span><?= date('d M Y', strtotime($eg['exam_date'])) ?></span>
                    </div>
                    <div class="flex items-center space-x-2">
                        <svg class="w-4 h-4 text-blue-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        <span><?= $eg['duration_minutes'] ?> mins</span>
                    </div>
                    <div class="flex items-center space-x-2">
                        <svg class="w-4 h-4 text-blue-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        <span><?= date('h:i A', strtotime($eg['start_time'])) ?></span>
                    </div>
                    <div class="flex items-center space-x-2">
                        <svg class="w-4 h-4 text-blue-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        <span>Pass: <strong class="text-gray-800"><?= $eg['passing_marks'] ?></strong> marks</span>
                    </div>
                </div>

                <!-- Stats counts -->
                <div class="border-t border-gray-100 pt-3 grid grid-cols-3 gap-2 text-center">
                    <div class="bg-blue-50 rounded-xl p-2">
                        <div class="text-xl font-black text-blue-700"><?= $total ?></div>
                        <div class="text-[10px] font-bold text-blue-400 uppercase tracking-wider mt-0.5">Total</div>
                    </div>
                    <div class="bg-green-50 rounded-xl p-2">
                        <div class="text-xl font-black text-green-600"><?= $passed ?></div>
                        <div class="text-[10px] font-bold text-green-400 uppercase tracking-wider mt-0.5">Passed</div>
                    </div>
                    <div class="bg-red-50 rounded-xl p-2">
                        <div class="text-xl font-black text-red-500"><?= $failed ?></div>
                        <div class="text-[10px] font-bold text-red-400 uppercase tracking-wider mt-0.5">Failed</div>
                    </div>
                </div>
            </div>

            <!-- Card footer CTA -->
            <div class="px-5 pb-5 flex items-center space-x-3">
                <button onclick="event.stopPropagation(); showExamResults(<?= $eid ?>)"
                    class="flex-1 bg-indigo-50 hover:bg-indigo-600 text-indigo-600 hover:text-white border border-indigo-100 hover:border-indigo-600 font-black py-3 rounded-2xl transition-all duration-200 flex items-center justify-center space-x-2 text-xs uppercase tracking-widest group-hover:shadow-lg group-hover:shadow-indigo-100">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                    <span>View Results</span>
                </button>
                <form action="../api/delete_record.php" method="POST" onsubmit="return confirm('Delete all results for this exam?');" class="shrink-0" onclick="event.stopPropagation()">
                    <input type="hidden" name="type" value="exam_results_only">
                    <input type="hidden" name="id" value="<?= $eid ?>">
                    <button type="submit" class="p-3 bg-rose-50 hover:bg-rose-600 text-rose-500 hover:text-white border border-rose-100 hover:border-rose-600 rounded-2xl transition-all duration-200 shadow-sm" title="Clear All Results">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                    </button>
                </form>
            </div>
        </div>
        <?php endforeach; ?>
    </div>

    <!-- â”€â”€ Per-Exam Detail Panel (hidden by default) â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€ -->
    <div id="exam-detail-panel" class="hidden px-6 pb-6">
        <div class="bg-gray-50 border border-gray-200 rounded-2xl overflow-hidden shadow-md">
            <!-- Panel header -->
            <div class="bg-gradient-to-r from-[#1e3a5f] to-[#2563eb] px-6 py-4 flex items-center justify-between">
                <div>
                    <p id="detail-subject" class="text-blue-200 text-[10px] font-black uppercase tracking-widest mb-0.5"></p>
                    <h3 id="detail-title" class="text-white font-black text-lg leading-tight"></h3>
                </div>
                <button onclick="closeExamResults()" title="Close"
                    class="text-blue-200 hover:text-white bg-white/10 hover:bg-white/20 p-2 rounded-lg transition-all">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>
            <!-- Panel stats strip -->
            <div id="detail-stats" class="flex flex-wrap items-center gap-4 px-6 py-3 bg-white border-b border-gray-200 text-sm text-gray-600"></div>
            <!-- Panel table -->
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm whitespace-nowrap">
                    <thead>
                    <tr class="bg-gradient-to-r from-[#1e3a5f] to-[#2563eb] text-white uppercase text-[11px] font-black tracking-widest">
                        <th class="p-4 font-bold">Student Name</th>
                        <th class="p-4 font-bold">Roll Number</th>
                        <th class="p-4 font-bold text-center">Score</th>
                        <th class="p-4 font-bold text-center">Result</th>
                        <th class="p-4 font-bold text-center">Action</th>
                    </tr>
                    </thead>
                    <tbody id="detail-tbody" class="divide-y divide-gray-100 text-gray-700"></tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Embedded exam data for JS -->
    <script>
    const examData = <?= json_encode(array_values($exams_grouped), JSON_HEX_TAG) ?>;

    function showExamResults(examId) {
        const eg = examData.find(e => e.exam_id == examId);
        if (!eg) return;

        document.getElementById('detail-subject').textContent = eg.subject;
        document.getElementById('detail-title').textContent   = eg.exam_title;

        const passed   = eg.results.filter(r => r.result_status === 'Pass').length;
        const failed   = eg.results.length - passed;
        const passRate = eg.results.length > 0 ? Math.round((passed / eg.results.length) * 100) : 0;
        const d        = new Date(eg.exam_date);
        const dateStr  = d.toLocaleDateString('en-GB', {day:'2-digit', month:'short', year:'numeric'});

        document.getElementById('detail-stats').innerHTML = `
            <span class="flex items-center space-x-1.5">
                <svg class="w-4 h-4 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                <span>${dateStr}</span>
            </span>
            <span class="flex items-center space-x-1.5">
                <svg class="w-4 h-4 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                <span>${eg.duration_minutes} mins</span>
            </span>
            <span class="flex items-center space-x-1.5">
                <svg class="w-4 h-4 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                <span>Passing: <strong>${eg.passing_marks}</strong> / ${eg.total_marks ?? 'â€”'} marks</span>
            </span>
            <span class="ml-auto flex items-center space-x-2 text-xs font-bold flex-wrap gap-y-1">
                <span class="bg-blue-100 text-blue-700 px-3 py-1 rounded-full">${eg.results.length} Total</span>
                <span class="bg-green-100 text-green-700 px-3 py-1 rounded-full">${passed} Passed</span>
                <span class="bg-red-100 text-red-700 px-3 py-1 rounded-full">${failed} Failed</span>
                <span class="bg-indigo-100 text-indigo-700 px-3 py-1 rounded-full">${passRate}% Rate</span>
            </span>`;

        const tbody = document.getElementById('detail-tbody');
        if (eg.results.length === 0) {
            tbody.innerHTML = `<tr><td colspan="5" class="p-8 text-center text-gray-400 font-medium">No student results for this exam yet.</td></tr>`;
        } else {
            tbody.innerHTML = eg.results.map(rs => {
                const pass   = rs.result_status === 'Pass';
                const border = pass ? 'border-green-400' : 'border-red-400';
                const badge  = pass ? 'bg-green-100 text-green-700 border-green-200' : 'bg-red-100 text-red-700 border-red-200';
                const initial = (rs.student_name || '?').charAt(0).toUpperCase();
                return `
                <tr class="hover:bg-slate-50 transition-colors">
                    <td class="p-4 border-l-4 ${border}">
                        <div class="flex items-center space-x-3">
                            <div class="w-8 h-8 rounded-md bg-gradient-to-tr from-blue-600 to-blue-400 text-white flex items-center justify-center font-bold shadow-inner text-sm">${initial}</div>
                            <span class="font-bold text-gray-800">${rs.student_name}</span>
                        </div>
                    </td>
                    <td class="p-4">
                        <span class="font-mono text-xs font-bold text-white bg-[#6b58ab] px-3 py-1 rounded-lg tracking-wider shadow-sm">${rs.reg_number}</span>
                    </td>
                    <td class="p-4 text-center">
                        <span class="text-xl font-black text-blue-600">${rs.total_score}</span>
                        <span class="text-gray-400 font-bold mx-1">/</span>
                        <span class="text-gray-600 font-bold">${rs.total_marks}</span>
                    </td>
                    <td class="p-4 text-center">
                        <span class="px-3 py-1 rounded-full text-[10px] font-black tracking-widest uppercase border ${badge} shadow-sm">${rs.result_status}</span>
                    </td>
                    <td class="p-4 text-center">
                        <form action="../api/delete_record.php" method="POST"
                              onsubmit="return confirm('Are you sure you want to delete this result?');">
                            <input type="hidden" name="type" value="result">
                            <input type="hidden" name="id"   value="${rs.result_id}">
                            <button type="submit"
                                class="text-red-500 hover:text-white bg-red-50 hover:bg-red-500 px-3 py-1.5 rounded-lg text-xs font-bold transition-all border border-red-200 hover:border-red-500 shadow-sm">
                                Delete
                            </button>
                        </form>
                    </td>
                </tr>`;
            }).join('');
        }

        document.getElementById('exam-detail-panel').classList.remove('hidden');
        document.getElementById('exam-detail-panel').scrollIntoView({ behavior: 'smooth', block: 'start' });
    }

    function closeExamResults() {
        document.getElementById('exam-detail-panel').classList.add('hidden');
    }

    // Search filters exam cards by subject / title
    document.addEventListener('DOMContentLoaded', () => {
        const input = document.getElementById('results-search');
        if (!input) return;
        input.addEventListener('input', function () {
            const q = this.value.toLowerCase();
            document.querySelectorAll('.exam-card').forEach(card => {
                const match = (card.dataset.examSubject + ' ' + card.dataset.examTitle).includes(q);
                card.style.display = match ? '' : 'none';
            });
        });
    });
    </script>

    <?php endif; // count($exams_grouped) ?>
</div>
<?php endif; // tab === results ?>

<?php if ($tab === 'students'): ?>
<?php 
$stu_stmt = $pdo->query("SELECT id, name, email, created_at FROM users WHERE role='student' ORDER BY id DESC");
$students = $stu_stmt->fetchAll(PDO::FETCH_ASSOC);
$total_stu = count($students);
?>

<div class="space-y-8 animate-in fade-in slide-in-from-bottom-4 duration-500">
    <!-- Student Header & Search -->
    <div class="bg-white rounded-[2rem] p-8 shadow-sm border border-gray-100 flex flex-col md:flex-row md:items-center justify-between gap-6">
        <div>
            <div class="flex items-center space-x-3 mb-2">
                <div class="p-2 bg-indigo-50 text-indigo-600 rounded-xl">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                </div>
                <h4 class="font-black text-slate-800 text-2xl tracking-tight">Student Directory</h4>
            </div>
            <p class="text-sm text-slate-600 font-bold uppercase tracking-widest pl-1">Total Registered: <span class="text-indigo-600"><?= $total_stu ?> Students</span></p>
        </div>
        
        <div class="relative flex-1 max-w-md">
            <input type="text" id="student-search" placeholder="Search by name or email..." class="w-full bg-white border-2 border-indigo-50 rounded-2xl text-sm pl-6 pr-14 py-4 focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 shadow-xl shadow-indigo-100/50 transition-all font-black text-slate-700 placeholder:text-slate-500">
            <div class="absolute right-4 top-1/2 -translate-y-1/2 p-2 bg-indigo-600 text-white rounded-xl shadow-lg shadow-indigo-200">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
            </div>
        </div>
    </div>

    <!-- Student Table -->
    <div class="bg-white rounded-[2rem] shadow-2xl shadow-slate-200/50 border border-gray-100 overflow-hidden">
        <div class="overflow-x-auto w-full">
            <table class="w-full text-left text-sm whitespace-nowrap">
                <thead>
                    <tr class="bg-gradient-to-r from-[#1e3a5f] to-[#2563eb] text-white uppercase text-[11px] font-black tracking-widest">
                        <th class="px-8 py-6">Student Identity</th>
                        <th class="px-8 py-6">System ID</th>
                        <th class="px-8 py-6">Access Credentials</th>
                        <th class="px-8 py-6 text-right">Registration Date</th>
                        <th class="px-8 py-6 text-center">Action</th>
                    </tr>
                </thead>
                <tbody id="student-tbody" class="divide-y divide-gray-50">
                    <?php if($total_stu === 0): ?>
                        <tr><td colspan="5" class="p-16 text-center text-gray-400 font-bold uppercase tracking-widest text-xs">No students found</td></tr>
                    <?php else: ?>
                        <?php foreach($students as $st): ?>
                            <tr class="student-row hover:bg-indigo-50/30 transition-all duration-300 group">
                                <td class="px-8 py-7">
                                    <div class="flex items-center space-x-5">
                                        <div class="w-14 h-14 rounded-2xl bg-gradient-to-br from-indigo-600 via-blue-600 to-indigo-700 text-white flex items-center justify-center font-black text-xl shadow-xl shadow-indigo-200 group-hover:scale-110 group-hover:rotate-3 transition-all duration-500">
                                            <?= strtoupper(substr($st['name'],0,1)) ?>
                                        </div>
                                        <div>
                                            <p class="font-black text-slate-900 text-xl leading-tight tracking-tight group-hover:text-indigo-600 transition-colors"><?= htmlspecialchars($st['name']) ?></p>
                                            <div class="flex items-center space-x-2 mt-1.5">
                                                <span class="w-2 h-2 bg-emerald-500 rounded-full animate-pulse"></span>
                                                <p class="text-[12px] font-black text-emerald-600 uppercase tracking-widest">Active Member</p>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-8 py-7">
                                    <div class="bg-indigo-50 border border-indigo-100 px-4 py-2 rounded-xl inline-flex items-center space-x-2">
                                        <span class="text-[12px] font-black text-indigo-300 uppercase tracking-tighter">ID:</span>
                                        <span class="font-mono text-base font-black text-indigo-600">#<?= str_pad(htmlspecialchars($st['id']), 4, "0", STR_PAD_LEFT) ?></span>
                                    </div>
                                </td>
                                <td class="px-8 py-7">
                                    <div class="flex flex-col">
                                        <div class="flex items-center space-x-2 text-slate-700 font-black text-base">
                                            <svg class="w-4 h-4 text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                                            <span><?= htmlspecialchars($st['email']) ?></span>
                                        </div>
                                        <p class="text-[11px] font-black text-indigo-500 uppercase tracking-widest mt-1 ml-6 bg-indigo-50 px-2 py-0.5 rounded inline-block w-fit">Verified Student Account</p>
                                    </div>
                                </td>
                                <td class="px-8 py-7 text-right">
                                    <p class="font-black text-slate-800 text-sm uppercase tracking-widest"><?= date('d M Y', strtotime($st['created_at'])) ?></p>
                                    <p class="text-[12px] text-indigo-400 font-bold mt-0.5"><?= date('h:i A', strtotime($st['created_at'])) ?></p>
                                </td>
                                <td class="px-8 py-7 text-center">
                                    <form action="../api/delete_record.php" method="POST" onsubmit="return confirm('DANGER: Delete this student?');" onclick="event.stopPropagation()">
                                        <input type="hidden" name="type" value="student">
                                        <input type="hidden" name="id" value="<?= $st['id'] ?>">
                                        <button type="submit" class="p-3.5 bg-rose-50 hover:bg-rose-600 text-rose-500 hover:text-white border border-rose-100 hover:border-rose-600 rounded-2xl transition-all duration-300 shadow-sm hover:shadow-lg hover:shadow-rose-100 active:scale-95">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const searchInput = document.getElementById('student-search');
    const studentRows = document.querySelectorAll('.student-row');
    
    if (searchInput) {
        searchInput.addEventListener('input', function() {
            const query = this.value.toLowerCase();
            studentRows.forEach(row => {
                const text = row.innerText.toLowerCase();
                row.style.display = text.includes(query) ? '' : 'none';
            });
        });
    }
});
</script>
<?php endif; ?>

</main>
<!-- END: AdminMainContent -->

<!-- BEGIN: CreateExamModal -->
<div id="modal-1" class="absolute inset-0 bg-slate-900/40 backdrop-blur-sm hidden items-center justify-center p-4 z-50 flex drop-shadow-2xl" data-purpose="modal-overlay">
<form action="../api/exam_manager.php" method="POST" class="bg-white rounded-2xl shadow-[0_20px_50px_rgba(0,0,0,0.5)] w-full max-w-[450px] overflow-hidden border border-gray-200 transform scale-100 transition-all">
<input type="hidden" name="action" value="create_exam">
<div class="bg-brand-sidebar text-white px-6 py-4 flex justify-between items-center border-b border-slate-700/50">
<h3 class="text-md font-bold tracking-wider flex items-center space-x-2"><svg class="w-5 h-5 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v3m0 0v3m0-3h3m-3 0H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg> <span>Create New Exam</span></h3>
<button type="button" class="text-slate-600 hover:text-white transition-colors bg-slate-800 hover:bg-slate-700 p-1 rounded-md" onclick="document.getElementById('modal-1').classList.add('hidden')">
<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M6 18L18 6M6 6l12 12" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"></path></svg>
</button>
</div>
<div class="p-8 space-y-6 bg-gray-50/50">
<div>
<label class="text-[11px] tracking-widest uppercase font-black text-slate-700 mb-2 block">Subject Name:</label>
<input name="subject" placeholder="e.g. Software Engineering" class="w-full border-gray-300 rounded-lg text-sm p-3 bg-white shadow-inner font-semibold text-gray-800 focus:ring-blue-500 focus:border-blue-500 transition-all" type="text" required/>
</div>
<div>
<label class="text-[11px] tracking-widest uppercase font-black text-slate-700 mb-2 block">Exam Title:</label>
<input name="title" placeholder="e.g. Midterm Spring 2026" class="w-full border-gray-300 rounded-lg text-sm p-3 bg-white shadow-inner font-semibold text-gray-800 focus:ring-blue-500 focus:border-blue-500 transition-all" type="text" required/>
</div>
<div class="flex space-x-4">
<div class="flex-1">
<label class="text-[11px] tracking-widest uppercase font-black text-slate-700 mb-2 block">Exam Date:</label>
<input type="date" name="exam_date" class="w-full border-gray-300 rounded-lg text-sm p-3 bg-white shadow-inner font-semibold text-gray-800 focus:ring-blue-500 focus:border-blue-500 transition-all" required>
</div>
<div class="flex-1">
<label class="text-[11px] tracking-widest uppercase font-black text-slate-700 mb-2 block">Start Time:</label>
<input type="time" name="start_time" class="w-full border-gray-300 rounded-lg text-sm p-3 bg-white shadow-inner font-semibold text-gray-800 focus:ring-blue-500 focus:border-blue-500 transition-all" required>
</div>
</div>
<div class="flex space-x-4">
<div class="flex-1">
<label class="text-[11px] tracking-widest uppercase font-black text-slate-700 mb-2 block">Duration (min):</label>
<input name="duration_minutes" value="60" class="w-full border-gray-300 rounded-lg text-sm p-3 bg-white shadow-inner font-bold text-blue-600 focus:ring-blue-500 focus:border-blue-500 transition-all" type="number" required/>
</div>
<div class="flex-1">
<label class="text-[11px] tracking-widest uppercase font-black text-slate-700 mb-2 block">Passing Marks:</label>
<input name="passing_marks" value="50" class="w-full border-gray-300 rounded-lg text-sm p-3 bg-white shadow-inner font-bold text-blue-600 focus:ring-blue-500 focus:border-blue-500 transition-all border-l-4 border-l-orange-400" type="number" required/>
</div>
</div>
</div>
<div class="p-5 bg-white flex justify-end space-x-3 border-t border-gray-200 shadow-inner">
<button type="button" class="px-5 py-2.5 border border-gray-300 text-gray-600 rounded-lg text-sm bg-gray-50 hover:bg-gray-100 hover:text-gray-800 font-bold tracking-wide transition-all shadow-sm" onclick="document.getElementById('modal-1').classList.add('hidden')">Cancel</button>
<button type="submit" class="px-6 py-2.5 bg-blue-600 text-white rounded-lg text-sm hover:bg-blue-700 font-black tracking-widest uppercase shadow-[0_4px_14px_0_rgb(37,99,235,0.39)] hover:shadow-[0_6px_20px_rgba(37,99,235,0.23)] hover:-translate-y-0.5 transition-all">Save Exam</button>
</div>
</form>
</div>
<!-- END: CreateExamModal -->

</div>
</section>
</div>
</div>

<script>
function handleUpload(event) {
    event.preventDefault();
    const form = event.target;
    const formData = new FormData(form);
    const xhr = new XMLHttpRequest();
    const progressContainer = document.getElementById('upload-progress-container');
    const progressBar = document.getElementById('upload-progress-bar');
    const progressText = document.getElementById('upload-percentage');
    const submitBtn = form.querySelector('button[type="submit"]');

    // Show progress UI
    progressContainer.classList.remove('hidden');
    progressContainer.scrollIntoView({ behavior: 'smooth', block: 'end' });
    submitBtn.disabled = true;
    submitBtn.classList.add('opacity-50', 'cursor-not-allowed');

    xhr.upload.addEventListener('progress', (e) => {
        if (e.lengthComputable) {
            const percentComplete = Math.round((e.loaded / e.total) * 100);
            progressBar.style.width = percentComplete + '%';
            progressText.innerText = percentComplete + '%';
        }
    });

    xhr.onload = function() {
        if (xhr.status === 200) {
            try {
                const response = JSON.parse(xhr.responseText);
                if (response.success) {
                    progressBar.style.width = '100%';
                    progressText.innerText = '100%';
                    
                    // Show success message
                    const successMsg = document.getElementById('upload-success-msg');
                    if (successMsg) {
                        successMsg.classList.remove('hidden');
                        successMsg.scrollIntoView({ behavior: 'smooth', block: 'center' });
                    }

                    setTimeout(() => {
                        window.location.href = '?tab=students';
                    }, 2000); // Wait longer to show the success message
                } else {
                    alert('Error: ' + (response.error || 'Upload failed'));
                    progressContainer.classList.add('hidden');
                    submitBtn.disabled = false;
                    submitBtn.classList.remove('opacity-50', 'cursor-not-allowed');
                }
            } catch (e) {
                console.error('Error parsing response', e);
                window.location.reload();
            }
        } else {
            alert('Server error occurred during upload.');
            progressContainer.classList.add('hidden');
            submitBtn.disabled = false;
            submitBtn.classList.remove('opacity-50', 'cursor-not-allowed');
        }
    };

    xhr.open('POST', form.action, true);
    xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
    xhr.send(formData);
}

function setupTableSearch(inputId, tbodyId) {
    const input = document.getElementById(inputId);
    const tbody = document.getElementById(tbodyId);
    if (!input || !tbody) return;

    input.addEventListener('input', function() {
        const q = this.value.toLowerCase();
        const rows = tbody.getElementsByTagName('tr');
        for (let row of rows) {
            const text = row.textContent.toLowerCase();
            row.style.display = text.includes(q) ? '' : 'none';
        }
    });
}

function initUploadUI() {
    const fileInput = document.getElementById('csv-file-input');
    const dropZone = document.getElementById('drop-zone');
    const idleUI = document.getElementById('file-idle-ui');
    const selectedUI = document.getElementById('file-selected-ui');
    const fileNameDisplay = document.getElementById('selected-file-name');
    const changeFileBtn = document.getElementById('change-file-btn');

    if (!fileInput) return;

    fileInput.addEventListener('change', (e) => {
        if (fileInput.files.length > 0) {
            const file = fileInput.files[0];
            fileNameDisplay.textContent = file.name;
            idleUI.classList.add('hidden');
            selectedUI.classList.remove('hidden');
            dropZone.classList.add('border-emerald-400', 'bg-emerald-50/10');
            dropZone.classList.remove('border-blue-400', 'bg-blue-50/20');
        }
    });

    changeFileBtn.addEventListener('click', (e) => {
        e.stopPropagation();
        fileInput.value = '';
        idleUI.classList.remove('hidden');
        selectedUI.classList.add('hidden');
        dropZone.classList.remove('border-emerald-400', 'bg-emerald-50/10');
        dropZone.classList.add('border-blue-400', 'bg-blue-50/20');
    });

    // Visual Drag feedback
    ['dragenter', 'dragover'].forEach(eventName => {
        dropZone.addEventListener(eventName, (e) => {
            e.preventDefault();
            e.stopPropagation();
            dropZone.classList.add('bg-blue-100', 'border-blue-600', 'scale-[1.02]');
        }, false);
    });

    ['dragleave', 'drop'].forEach(eventName => {
        dropZone.addEventListener(eventName, (e) => {
            e.preventDefault();
            e.stopPropagation();
            dropZone.classList.remove('bg-blue-100', 'border-blue-600', 'scale-[1.02]');
        }, false);
    });
}

document.addEventListener('DOMContentLoaded', () => {
    setupTableSearch('live-search', 'live-tbody');
    initUploadUI();
    
    // Attach upload handler
    const uploadForm = document.querySelector('form[action="../api/csv_parser.php"]');
    if (uploadForm) {
        uploadForm.addEventListener('submit', handleUpload);
    }

    // Auto-refresh for Live Monitor
    const currentTab = new URLSearchParams(window.location.search).get('tab');
    if (currentTab === 'live') {
        setInterval(() => {
            // We fetch the current page but only replace the table body to avoid flicker
            fetch(window.location.href)
                .then(response => response.text())
                .then(html => {
                    const parser = new DOMParser();
                    const doc = parser.parseFromString(html, 'text/html');
                    const newTbody = doc.getElementById('live-tbody');
                    if (newTbody) {
                        document.getElementById('live-tbody').innerHTML = newTbody.innerHTML;
                    }
                });
        }, 5000); // Refresh every 5 seconds
    }
});
</script>

</body>
</html>
