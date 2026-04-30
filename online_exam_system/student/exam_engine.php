<?php
// student/exam_engine.php
require_once '../config/db.php';
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'student') { header('Location: ../index.php'); exit; }

$exam_id = $_GET['exam_id'] ?? null;
if (!$exam_id) { header('Location: dashboard.php'); exit; }

$user_id = $_SESSION['user_id'];

// Verify enrollment and no past submission
$stmt_en = $pdo->prepare("SELECT id FROM enrollments WHERE user_id=? AND exam_id=?");
$stmt_en->execute([$user_id, $exam_id]);
if (!$stmt_en->fetch()) { header('Location: dashboard.php'); exit; }

$stmt_res = $pdo->prepare("SELECT id FROM results WHERE enrollment_id=(SELECT id FROM enrollments WHERE user_id=? AND exam_id=?)");
$stmt_res->execute([$user_id, $exam_id]);
if ($stmt_res->fetch()) { $_SESSION['error']="Exam already taken."; header('Location: dashboard.php'); exit; }

// Fetch Exam Metadata
$stmt_ex = $pdo->prepare("SELECT * FROM exams WHERE id=?");
$stmt_ex->execute([$exam_id]);
$exam = $stmt_ex->fetch(PDO::FETCH_ASSOC);

// Fetch Questions (Randomize Order per student)
$stmt_q = $pdo->prepare("SELECT id, question_text, option_a, option_b, option_c, option_d FROM questions WHERE exam_id=? ORDER BY RAND()");
$stmt_q->execute([$exam_id]);
$questions = $stmt_q->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8"/>
<meta content="width=device-width, initial-scale=1.0" name="viewport"/>
<title>Exam: <?= htmlspecialchars($exam['title']) ?></title>
<script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
<script>
    tailwind.config = { 
      theme: { extend: { 
        fontFamily: { sans: ['Inter', 'sans-serif'] },
        colors: { 
            'brand-dark': '#1e293b', 
            'brand-sidebar': '#1e3a5f', 
            'brand-bg': '#f3f4f6', 
            'brand-blue': '#3b82f6',
            'brand-accent': '#3b82f6'
        } 
      } } 
    }
    
    let currentQ = 0;
    const totalQ = <?= count($questions) ?>;
    
    function showQuestion(index) {
        for(let i=0; i<totalQ; i++) {
            document.getElementById('qblock-'+i).classList.add('hidden');
            document.getElementById('qnav-'+i).classList.remove('bg-blue-600', 'text-white', 'shadow-lg', 'scale-110');
            document.getElementById('qnav-'+i).classList.add('bg-white/10', 'text-blue-200');
        }
        document.getElementById('qblock-'+index).classList.remove('hidden');
        document.getElementById('qnav-'+index).classList.remove('bg-white/10', 'text-blue-200');
        document.getElementById('qnav-'+index).classList.add('bg-blue-600', 'text-white', 'shadow-lg', 'scale-110');
        currentQ = index;
        
        document.getElementById('q-counter').innerText = (index + 1) + ' of ' + totalQ;
    }
    
    function nextQ() { if(currentQ < totalQ-1) showQuestion(currentQ+1); }
    function prevQ() { if(currentQ > 0) showQuestion(currentQ-1); }
    function markQNav(index) {
        let qnav = document.getElementById('qnav-'+index);
        qnav.classList.add('ring-2', 'ring-orange-400', 'ring-offset-2', 'ring-offset-brand-sidebar');
    }
</script>
<style>
    @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap');
    body { font-family: 'Inter', sans-serif; }
    .no-scrollbar::-webkit-scrollbar { display: none; }
    .no-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }
</style>
</head>
<body class="bg-brand-bg h-screen overflow-hidden antialiased">

<div class="flex flex-col h-full w-full">
    <!-- Header Bar -->
    <header class="bg-brand-sidebar h-16 flex items-center justify-between px-6 border-b border-white/10 z-30 shrink-0">
        <div class="flex items-center space-x-4 text-white">
            <div class="w-10 h-10 bg-blue-500 rounded-lg flex items-center justify-center font-black text-white tracking-widest text-md shadow-inner border border-blue-400">ST</div>
            <div class="flex flex-col">
                <div class="font-bold text-white tracking-wide leading-tight text-sm"><?= htmlspecialchars($exam['title']) ?></div>
                <div class="text-[10px] text-blue-300 font-bold uppercase tracking-widest">Ongoing Examination</div>
            </div>
        </div>

        <div class="flex items-center space-x-6">
            <div class="bg-white/10 backdrop-blur-md px-4 py-2 rounded-full flex items-center gap-3 border border-white/20">
                <svg class="w-4 h-4 text-orange-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                <span id="timer-display" class="text-white font-black text-sm tracking-widest">--:--</span>
            </div>
            <a href="dashboard.php?tab=my_enrollments" onclick="return confirm('Leaving this page might pause or submit your exam depending on rules. Are you sure you want to go back to dashboard?')" class="bg-white/10 hover:bg-white/20 text-white px-4 py-2 rounded-lg font-bold text-xs transition border border-white/20 flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path></svg>
                Back to Dashboard
            </a>
        </div>
    </header>

    <div class="flex flex-1 overflow-hidden">
        <!-- Question Navigation Sidebar -->
        <aside class="w-72 bg-brand-sidebar border-r border-white/5 flex flex-col z-20 shrink-0">
            <div class="p-6 border-b border-white/5">
                <h3 class="text-blue-300 font-black text-[10px] uppercase tracking-[0.2em] mb-4">Question Navigator</h3>
                <div class="grid grid-cols-4 gap-2">
                    <?php foreach($questions as $index => $q): ?>
                        <div id="qnav-<?= $index ?>" onclick="showQuestion(<?= $index ?>)" class="cursor-pointer aspect-square flex items-center justify-center bg-white/10 text-blue-200 text-xs rounded-lg font-black hover:bg-blue-500 hover:text-white transition-all duration-200 border border-white/5">
                            <?= $index+1 ?>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
            <div class="p-6 mt-auto">
                <div class="bg-blue-900/40 rounded-xl p-4 border border-blue-400/20">
                    <p class="text-[10px] font-black text-blue-400 uppercase tracking-widest mb-1">Student</p>
                    <p class="text-white font-bold text-sm truncate"><?= htmlspecialchars($_SESSION['name']) ?></p>
                </div>
            </div>
        </aside>

        <!-- Main Content Area -->
        <main class="flex-1 bg-brand-bg overflow-y-auto no-scrollbar flex flex-col p-8 lg:p-12">
            
            <!-- Warning Box (Anti-Cheat) -->
            <div id="cheat-warning-box" class="hidden bg-orange-500 text-white rounded-2xl p-6 mb-8 shadow-xl shadow-orange-500/20 animate-pulse border-4 border-white">
                <div class="flex items-center gap-4">
                    <div class="bg-white/20 p-3 rounded-xl">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                    </div>
                    <div>
                        <p class="text-lg font-black uppercase tracking-tight leading-none">Security Alert: Window Inactive!</p>
                        <p class="text-sm font-bold opacity-90 mt-1">Return immediately. Auto-submission in <span id="cheat-timer" class="text-2xl font-black underline">5</span> seconds.</p>
                    </div>
                </div>
            </div>

            <div class="max-w-4xl mx-auto w-full flex flex-col h-full">
                <!-- Question Header -->
                <div class="flex justify-between items-center mb-8">
                    <h2 class="text-3xl font-black text-slate-800 tracking-tight">Question <span id="q-counter" class="text-blue-600">1 of <?= count($questions) ?></span></h2>
                </div>

                <form id="exam-form" action="../api/exam_manager.php" method="POST" class="flex flex-col flex-1">
                    <input type="hidden" name="action" value="submit_exam">
                    <input type="hidden" name="exam_id" value="<?= $exam_id ?>">
                    <input type="hidden" name="auto_submitted" id="auto-submit-flag" value="0">

                    <div class="bg-white rounded-3xl shadow-[0_20px_50px_rgb(0,0,0,0.04)] border border-gray-100 p-8 lg:p-12 mb-10">
                        <?php if(count($questions) === 0): ?>
                            <div class="flex flex-col items-center py-20 text-center">
                                <div class="bg-red-50 p-6 rounded-full text-red-500 mb-6">
                                    <svg class="w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                                </div>
                                <p class="text-red-600 font-black text-xl uppercase tracking-widest">No Questions Available</p>
                                <p class="text-gray-400 mt-2 font-bold">Please contact your administrator regarding this exam.</p>
                            </div>
                        <?php else: ?>
                            <?php foreach($questions as $index => $q): ?>
                            <div id="qblock-<?= $index ?>" class="<?= $index===0?'':'hidden' ?>">
                                <p class="text-2xl font-black text-slate-700 leading-snug mb-10"><?= htmlspecialchars($q['question_text']) ?></p>
                                
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <?php foreach(['A'=>$q['option_a'], 'B'=>$q['option_b'], 'C'=>$q['option_c'], 'D'=>$q['option_d']] as $opt_key => $opt_val): ?>
                                    <label class="group relative flex items-center p-6 border-2 border-gray-100 rounded-2xl cursor-pointer hover:border-blue-500 hover:bg-blue-50/30 transition-all duration-300" onclick="markQNav(<?= $index ?>)">
                                        <input class="hidden peer" name="q_<?= $q['id'] ?>" value="<?= $opt_key ?>" type="radio"/>
                                        <div class="w-6 h-6 border-2 border-gray-300 rounded-full flex items-center justify-center peer-checked:border-blue-600 peer-checked:bg-blue-600 transition-all group-hover:scale-110">
                                            <div class="w-2 h-2 bg-white rounded-full opacity-0 peer-checked:opacity-100"></div>
                                        </div>
                                        <span class="ml-4 text-lg font-bold text-slate-600 peer-checked:text-blue-700 transition-colors"><?= htmlspecialchars($opt_val) ?></span>
                                    </label>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>

                    <!-- Centered Navigation Buttons -->
                    <div class="flex justify-center items-center gap-4 mb-12">
                        <button type="button" onclick="prevQ()" class="bg-white text-slate-700 border-2 border-gray-200 px-10 py-4 rounded-2xl font-black uppercase tracking-widest hover:bg-gray-50 hover:border-gray-300 transition-all active:scale-95 shadow-sm">
                            Previous
                        </button>
                        <button type="button" onclick="nextQ()" class="bg-blue-600 text-white border-2 border-blue-600 px-12 py-4 rounded-2xl font-black uppercase tracking-widest hover:bg-blue-700 hover:border-blue-700 transition-all active:scale-95 shadow-lg shadow-blue-200">
                            Next
                        </button>
                    </div>

                    <!-- Submit Footer -->
                    <div class="mt-auto flex justify-center pb-8">
                        <button type="submit" onclick="return confirm('Are you sure you want to completely finish and submit?')" class="group flex items-center space-x-3 bg-red-500 hover:bg-red-600 text-white px-12 py-5 rounded-2xl font-black uppercase tracking-[0.2em] shadow-xl shadow-red-200 transition-all hover:-translate-y-1 active:translate-y-0">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            <span>Finish & Submit Exam</span>
                        </button>
                    </div>
                </form>
            </div>
        </main>
    </div>
</div>

<script src="../assets/js/timer.js"></script>
<script src="../assets/js/anti-cheat.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', () => {
        initTimer(<?= $exam['duration_minutes'] ?>);
        showQuestion(0);
    });
</script>
</body>
</html>

