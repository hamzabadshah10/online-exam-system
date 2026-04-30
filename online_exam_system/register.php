<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8"/>
    <meta content="width=device-width, initial-scale=1.0" name="viewport"/>
    <title>Registry - EduQuest Certification Portal</title>
    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap');
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
    </style>
</head>
<body class="bg-[#f8fafc] text-slate-900 selection:bg-indigo-100 selection:text-indigo-700 min-h-screen flex items-center justify-center p-8 relative overflow-hidden">

    <!-- Background Accents -->
    <div class="absolute top-[-10%] right-[-10%] w-[40rem] h-[40rem] bg-indigo-50 rounded-full blur-[120px] opacity-60 -z-10"></div>
    <div class="absolute bottom-[-10%] left-[-10%] w-[30rem] h-[30rem] bg-blue-50 rounded-full blur-[100px] opacity-40 -z-10"></div>

    <main class="w-full max-w-xl bg-white p-12 lg:p-16 rounded-[3rem] shadow-2xl shadow-slate-200/50 border border-gray-100 relative z-10">
        <!-- Brand Header -->
        <div class="flex items-center space-x-4 mb-12 group">
            <a href="index.php" class="p-3 bg-indigo-600 rounded-2xl shadow-xl shadow-indigo-100 text-white transform hover:rotate-12 transition-transform">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 14l9-5-9-5-9 5 9 5zm0 0l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14zm-4 6v-7.5l4-2.222"></path></svg>
            </a>
            <div>
                <h1 class="text-2xl font-black tracking-tighter uppercase text-slate-900 leading-none">EduQuest</h1>
                <p class="text-[10px] font-black text-indigo-400 uppercase tracking-[0.3em] mt-1">User Registration</p>
            </div>
        </div>

        <div class="mb-10">
            <h2 class="text-4xl font-black text-slate-900 tracking-tighter uppercase">Create Your Account</h2>
            <p class="text-sm font-medium text-slate-600 mt-2">Enter your details to create an account.</p>
        </div>

        <?php if (isset($_SESSION['error'])): ?>
            <div class="bg-rose-50 border border-rose-100 text-rose-500 px-6 py-4 rounded-2xl text-[11px] font-black uppercase tracking-widest mb-8 animate-pulse">
                <?= htmlspecialchars($_SESSION['error']); unset($_SESSION['error']); ?>
            </div>
        <?php endif; ?>

        <form action="api/auth.php" method="POST" class="space-y-6">
            <input type="hidden" name="action" value="register">
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="space-y-2">
                    <label class="text-[10px] font-black text-slate-600 uppercase tracking-widest ml-1">Full Name</label>
                    <input name="name" type="text" placeholder="John Doe" class="w-full bg-slate-50 border-2 border-slate-100 rounded-2xl p-4 text-sm font-bold focus:bg-white focus:border-indigo-600 focus:ring-0 transition-all" required/>
                </div>
                
                <div class="space-y-2">
                    <label class="text-[10px] font-black text-slate-600 uppercase tracking-widest ml-1">Email Address</label>
                    <input name="email" type="email" placeholder="name@example.com" class="w-full bg-slate-50 border-2 border-slate-100 rounded-2xl p-4 text-sm font-bold focus:bg-white focus:border-indigo-600 focus:ring-0 transition-all" required/>
                </div>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="space-y-2">
                    <label class="text-[10px] font-black text-slate-600 uppercase tracking-widest ml-1">Password</label>
                    <div class="relative">
                        <input name="password" type="password" placeholder="Enter your password" class="w-full bg-slate-50 border-2 border-slate-100 rounded-2xl p-4 pr-12 text-sm font-bold focus:bg-white focus:border-indigo-600 focus:ring-0 transition-all" required/>
                        <button type="button" onclick="togglePassword(this)" class="absolute right-4 top-1/2 -translate-y-1/2 text-slate-600 hover:text-indigo-600 transition-colors">
                            <svg class="w-5 h-5 eye-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                        </button>
                    </div>
                </div>
                <div class="space-y-2">
                    <label class="text-[10px] font-black text-slate-600 uppercase tracking-widest ml-1">Confirm Password</label>
                    <div class="relative">
                        <input name="confirm_password" type="password" placeholder="Enter your password" class="w-full bg-slate-50 border-2 border-slate-100 rounded-2xl p-4 pr-12 text-sm font-bold focus:bg-white focus:border-indigo-600 focus:ring-0 transition-all" required/>
                        <button type="button" onclick="togglePassword(this)" class="absolute right-4 top-1/2 -translate-y-1/2 text-slate-600 hover:text-indigo-600 transition-colors">
                            <svg class="w-5 h-5 eye-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                        </button>
                    </div>
                </div>
            </div>
            
            <div class="pt-6">
                <button type="submit" class="w-full bg-indigo-600 text-white py-5 rounded-[2rem] text-sm font-black uppercase tracking-widest shadow-2xl shadow-indigo-100 hover:bg-indigo-700 hover:-translate-y-1 active:scale-95 transition-all">
                    Create Account
                </button>
            </div>
            
            <div class="mt-10 pt-10 border-t border-slate-50 text-center">
                <p class="text-[11px] font-black text-slate-600 uppercase tracking-widest">
                    Already have an account? <a href="index.php" class="text-indigo-600 hover:underline ml-1">Login Here</a>
                </p>
            </div>
        </form>
    </main>


<script>
function togglePassword(btn) {
    const input = btn.previousElementSibling;
    const type = input.getAttribute('type') === 'password' ? 'text' : 'password';
    input.setAttribute('type', type);
    
    // Toggle Eye Icon
    if (type === 'text') {
        btn.innerHTML = `<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l18 18"/></svg>`;
    } else {
        btn.innerHTML = `<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>`;
    }
}
</script>
</body>
</html>
