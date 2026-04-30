<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8"/>
<meta content="width=device-width, initial-scale=1.0" name="viewport"/>
<title>EduQuest - Premium Examination Engine</title>
<script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
<style>
    @import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap');
    body { font-family: 'Plus Jakarta Sans', sans-serif; }
    
    @keyframes float {
        0%, 100% { transform: translateY(0px) rotate(0deg); }
        50% { transform: translateY(-20px) rotate(5deg); }
    }
    .animate-float { animation: float 6s ease-in-out infinite; }
</style>
</head>
<body class="bg-[#f8fafc] text-slate-900 selection:bg-indigo-100 selection:text-indigo-700 min-h-screen overflow-x-hidden">

<div class="relative min-h-screen flex flex-col">
    <!-- Background Accents -->
    <div class="absolute top-[-10%] left-[-10%] w-[50rem] h-[50rem] bg-indigo-50 rounded-full blur-[120px] opacity-60 -z-10 animate-float"></div>
    <div class="absolute bottom-[-10%] right-[-10%] w-[40rem] h-[40rem] bg-blue-50 rounded-full blur-[100px] opacity-40 -z-10" style="animation-delay: -3s"></div>

    <!-- Navigation -->
    <nav class="container mx-auto px-8 py-10 flex justify-between items-center z-10">
        <div class="flex items-center space-x-4 group">
            <div class="p-3 bg-indigo-600 rounded-2xl shadow-xl shadow-indigo-100 text-white transform group-hover:rotate-12 transition-transform">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0012 18.75c-1.03 0-1.9-.4-2.59-1.177L8.863 17z"></path></svg>
            </div>
            <div>
                <h1 class="text-2xl font-black tracking-tighter uppercase text-slate-900 leading-none">EduQuest</h1>
                <p class="text-[10px] font-black text-indigo-400 uppercase tracking-[0.3em] mt-1">Intelligent Assessment</p>
            </div>
        </div>
        <div class="hidden md:flex items-center space-x-8">
            <a href="register.php" class="bg-indigo-600 text-white px-8 py-3 rounded-2xl text-xs font-black uppercase tracking-widest hover:bg-indigo-700 transition-all shadow-lg shadow-indigo-100 active:scale-95">Register</a>
        </div>
    </nav>

    <?php if(isset($_SESSION['error'])): ?>
        <div class="container mx-auto px-8 mb-8">
            <div class="bg-rose-50 border border-rose-100 text-rose-500 px-8 py-4 rounded-2xl text-xs font-black uppercase tracking-widest text-center shadow-sm">
                <?= htmlspecialchars($_SESSION['error']); unset($_SESSION['error']); ?>
            </div>
        </div>
    <?php endif; ?>
    <?php if(isset($_SESSION['success'])): ?>
        <div class="container mx-auto px-8 mb-8">
            <div class="bg-emerald-50 border border-emerald-100 text-emerald-600 px-8 py-4 rounded-2xl text-xs font-black uppercase tracking-widest text-center shadow-sm">
                <?= htmlspecialchars($_SESSION['success']); unset($_SESSION['success']); ?>
            </div>
        </div>
    <?php endif; ?>

    <!-- Main Content -->
    <main class="container mx-auto px-8 flex-1 flex flex-col lg:flex-row items-center gap-20 py-10 z-10">
        <!-- Left: Hero Text -->
        <div class="flex-1 text-center lg:text-left">
            <div class="inline-flex items-center space-x-3 bg-gradient-to-r from-indigo-600 to-blue-500 border border-indigo-400/30 px-6 py-2.5 rounded-full shadow-[0_10px_25px_rgba(79,70,229,0.3)] mb-10 group/pill">
                <span class="w-2.5 h-2.5 bg-emerald-400 rounded-full animate-pulse shadow-[0_0_12px_rgba(52,211,153,0.8)]"></span>
                <span class="text-[10px] font-black text-white uppercase tracking-[0.25em] drop-shadow-sm">Now processing 150k+ daily certifications</span>
            </div>
            <h2 class="text-6xl lg:text-8xl font-black text-slate-900 tracking-tighter leading-[0.9] mb-10">
                The Future of <br/>
                <span class="text-transparent bg-clip-text bg-gradient-to-r from-indigo-600 to-blue-500">Academic</span> <br/>
                Excellence.
            </h2>
            <p class="text-lg text-slate-600 font-medium max-w-lg mb-12 leading-relaxed mx-auto lg:mx-0">
                A high-fidelity examination ecosystem designed for elite institutions. Secure, scalable, and beautifully intuitive.
            </p>
            <div class="flex flex-col sm:flex-row items-center justify-center lg:justify-start gap-6">
                <a href="register.php" class="group relative bg-slate-900 text-white px-10 py-5 rounded-[2rem] text-sm font-black uppercase tracking-widest shadow-2xl shadow-slate-300 hover:-translate-y-1 hover:shadow-indigo-200 transition-all active:scale-95 flex items-center space-x-3 overflow-hidden">
                    <span class="relative z-10">Get Started</span>
                    <svg class="w-5 h-5 relative z-10 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M17 8l4 4m0 0l-4 4m4-4H3"></path></svg>
                    <div class="absolute inset-0 bg-indigo-600 translate-y-full group-hover:translate-y-0 transition-transform duration-500"></div>
                </a>
            </div>
        </div>

        <!-- Right: Login Portals -->
        <div class="flex-1 w-full max-w-2xl grid grid-cols-1 md:grid-cols-2 gap-8 relative">
            <!-- Decorative Elements behind cards -->
            <div class="absolute -right-10 top-1/2 -translate-y-1/2 w-80 h-80 border-2 border-indigo-100 rounded-full opacity-40 -z-10"></div>
            
            <!-- Student Login -->
            <div class="bg-white p-10 rounded-[3rem] shadow-[0_20px_50px_rgba(79,70,229,0.15)] border border-gray-100 relative overflow-hidden group hover:-translate-y-2 transition-all duration-500">
                <div class="relative z-10">
                    <div class="w-14 h-14 bg-indigo-50 text-indigo-600 rounded-2xl flex items-center justify-center mb-8 group-hover:bg-indigo-600 group-hover:text-white transition-colors">
                        <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 14l9-5-9-5-9 5 9 5zm0 0l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14zm-4 6v-7.5l4-2.222"></path></svg>
                    </div>
                    <h3 class="text-2xl font-black text-slate-900 tracking-tighter uppercase mb-6">Student Access</h3>
                    <form action="api/auth.php" method="POST" class="space-y-5">
                        <input type="hidden" name="action" value="login_student">
                        <div class="space-y-2">
                            <label class="text-[10px] font-black text-slate-600 uppercase tracking-widest ml-1">Email Address</label>
                            <input name="email" type="email" placeholder="student@example.com" class="w-full bg-slate-50 border-2 border-slate-100 rounded-2xl p-4 text-sm font-bold focus:bg-white focus:border-indigo-600 focus:ring-0 transition-all" required/>
                        </div>
                        <div class="space-y-2">
                            <label class="text-[10px] font-black text-slate-600 uppercase tracking-widest ml-1">Password</label>
                            <div class="relative">
                                <input name="password" type="password" placeholder="Enter your password" class="w-full bg-slate-50 border-2 border-slate-100 rounded-2xl p-4 pr-12 text-sm font-bold focus:bg-white focus:border-indigo-600 focus:ring-0 transition-all" required/>
                                <button type="button" onclick="togglePassword(this)" class="absolute right-4 top-1/2 -translate-y-1/2 text-slate-600 hover:text-indigo-600 transition-colors">
                                    <svg class="w-5 h-5 eye-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                </button>
                            </div>
                        </div>
                        <div class="flex items-center justify-between ml-1">
                            <label class="flex items-center space-x-3 cursor-pointer group/check">
                                <div class="relative flex items-center justify-center">
                                    <input type="checkbox" name="remember" class="peer h-5 w-5 cursor-pointer appearance-none rounded-lg border-2 border-slate-100 bg-slate-50 transition-all checked:bg-indigo-600 checked:border-indigo-600 focus:ring-0 focus:ring-offset-0"/>
                                    <svg class="absolute h-3 w-3 text-white opacity-0 peer-checked:opacity-100 transition-opacity pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="4" d="M5 13l4 4L19 7"></path></svg>
                                </div>
                                <span class="text-[10px] font-black text-slate-600 uppercase tracking-widest group-hover/check:text-indigo-600 transition-colors">Remember Me</span>
                            </label>
                        </div>
                        <button type="submit" class="w-full bg-indigo-600 text-white py-4 rounded-2xl text-xs font-black uppercase tracking-widest shadow-lg shadow-indigo-100 hover:bg-indigo-700 active:scale-95 transition-all">Login</button>
                        <p class="text-[9px] font-black text-slate-600 text-center uppercase tracking-widest mt-4">
                            New user? <a href="register.php" class="text-indigo-600 hover:underline">Register Now</a>
                        </p>
                    </form>
                </div>
            </div>

            <!-- Admin Login -->
            <div class="bg-slate-900 p-10 rounded-[3rem] shadow-[0_20px_50px_rgba(30,58,95,0.4)] border border-slate-800 relative overflow-hidden group hover:-translate-y-2 transition-all duration-500">
                <div class="relative z-10 text-white">
                    <div class="w-14 h-14 bg-white/10 text-white rounded-2xl flex items-center justify-center mb-8 group-hover:bg-white group-hover:text-slate-900 transition-colors">
                        <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path></svg>
                    </div>
                    <h3 class="text-2xl font-black tracking-tighter uppercase mb-6">Admin Access</h3>
                    <form action="api/auth.php" method="POST" class="space-y-5">
                        <input type="hidden" name="action" value="login_admin">
                        <div class="space-y-2">
                            <label class="text-[10px] font-black text-white/70 uppercase tracking-widest ml-1">Admin Email</label>
                            <input name="email" type="email" value="admin@example.com" class="w-full bg-white/5 border-2 border-white/10 rounded-2xl p-4 text-sm font-bold text-white placeholder-white/50 focus:bg-white/10 focus:border-white focus:ring-0 transition-all" required/>
                        </div>
                        <div class="space-y-2">
                            <label class="text-[10px] font-black text-white/70 uppercase tracking-widest ml-1">Admin Password</label>
                            <div class="relative">
                                <input name="password" type="password" value="admin123" placeholder="Enter your password" class="w-full bg-white/5 border-2 border-white/10 rounded-2xl p-4 pr-12 text-sm font-bold text-white placeholder-white/50 focus:bg-white/10 focus:border-white focus:ring-0 transition-all" required/>
                                <button type="button" onclick="togglePassword(this)" class="absolute right-4 top-1/2 -translate-y-1/2 text-white/70 hover:text-white transition-colors">
                                    <svg class="w-5 h-5 eye-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                </button>
                            </div>
                        </div>
                        <button type="submit" class="w-full bg-white text-slate-900 py-4 rounded-2xl text-xs font-black uppercase tracking-widest shadow-xl hover:bg-slate-100 active:scale-95 transition-all">Admin Login</button>

                    </form>
                </div>
            </div>
        </div>
    </main>

    <!-- Footer -->
    <footer class="container mx-auto px-8 py-14 flex flex-col md:flex-row justify-between items-center gap-8 border-t border-slate-100 mt-20">
        <p class="text-[10px] font-black text-slate-600 uppercase tracking-widest">© 2026 EduQuest Intelligence Corp. All rights reserved.</p>
        <div class="flex items-center space-x-8">
            <a href="#" class="text-[10px] font-black text-slate-600 hover:text-indigo-600 uppercase tracking-widest transition-colors">Privacy Policy</a>
            <a href="#" class="text-[10px] font-black text-slate-600 hover:text-indigo-600 uppercase tracking-widest transition-colors">Security</a>
            <a href="#" class="text-[10px] font-black text-slate-600 hover:text-indigo-600 uppercase tracking-widest transition-colors">Support</a>
        </div>
    </footer>
</div>

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
