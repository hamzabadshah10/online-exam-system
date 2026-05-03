document.addEventListener('DOMContentLoaded', () => {
    const warningBox = document.getElementById('cheat-warning-box');
    const timerDisplay = document.getElementById('cheat-timer');
    const examForm = document.getElementById('exam-form');
    const submitFlag = document.getElementById('auto-submit-flag');
    
    if (!warningBox || !examForm) return;

    let warningTimer;
    let secondsLeft = 3; // Reduced to 3 seconds for stricter enforcement
    let isWarningActive = false;

    function triggerWarning(reason) {
        if (isWarningActive) return;
        console.warn('Anti-Cheat Triggered:', reason);
        isWarningActive = true;
        warningBox.classList.remove('hidden');
        secondsLeft = 3;
        timerDisplay.innerText = secondsLeft;
        
        warningTimer = setInterval(() => {
            secondsLeft--;
            timerDisplay.innerText = secondsLeft;
            if (secondsLeft <= 0) {
                clearInterval(warningTimer);
                if (submitFlag) submitFlag.value = '1';
                examForm.submit();
            }
        }, 1000);
    }

    function cancelWarning() {
        if (!isWarningActive) return;
        clearInterval(warningTimer);
        warningBox.classList.add('hidden');
        isWarningActive = false;
    }

    // Tab Visibility Check
    document.addEventListener('visibilitychange', () => {
        if (document.hidden) {
            triggerWarning('Tab Switch Detected');
        }
    });

    // Window Focus Check (Switching to other apps/windows)
    window.addEventListener('blur', () => {
        triggerWarning('Window Focus Lost');
    });

    // Window Focus Gain (Optional: allow cancellation if returned quickly)
    window.addEventListener('focus', () => {
        // cancelWarning(); // Disabled for stricter security - once you leave, it's flagged
    });

    // Prevent Right Click
    document.addEventListener('contextmenu', (e) => {
        e.preventDefault();
    });

    // Prevent Copy/Paste/Cut
    document.addEventListener('copy', (e) => e.preventDefault());
    document.addEventListener('paste', (e) => e.preventDefault());
    document.addEventListener('cut', (e) => e.preventDefault());

    // Window Resize Detection
    window.addEventListener('resize', () => {
        const threshold = 160;
        if (window.outerWidth - window.innerWidth > threshold || window.outerHeight - window.innerHeight > threshold) {
            triggerWarning('DevTools Resize Detected');
        }
    });
});
