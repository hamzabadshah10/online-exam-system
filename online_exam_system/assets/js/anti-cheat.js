document.addEventListener('DOMContentLoaded', () => {
    const warningBox = document.getElementById('cheat-warning-box');
    const timerDisplay = document.getElementById('cheat-timer');
    const examForm = document.getElementById('exam-form');
    
    let warningTimer;
    let secondsLeft = 5;
    let isWarningActive = false;

    function triggerWarning() {
        if (isWarningActive) return;
        isWarningActive = true;
        warningBox.classList.remove('hidden');
        secondsLeft = 5;
        timerDisplay.innerText = secondsLeft;
        
        warningTimer = setInterval(() => {
            secondsLeft--;
            timerDisplay.innerText = secondsLeft;
            if (secondsLeft <= 0) {
                clearInterval(warningTimer);
                document.getElementById('auto-submit-flag').value = '1';
                examForm.submit();
            }
        }, 1000);
    }

    function cancelWarning() {
        clearInterval(warningTimer);
        warningBox.classList.add('hidden');
        isWarningActive = false;
    }

    // Detect Tab Switch
    document.addEventListener('visibilitychange', () => {
        if (document.hidden) {
            triggerWarning();
        } else {
            // Optional: Don't cancel if you want to be strict
            // cancelWarning(); 
        }
    });

    // Detect Window Focus Loss (e.g. switching to another app)
    window.addEventListener('blur', () => {
        triggerWarning();
    });

    // Detect Window Focus Gain
    window.addEventListener('focus', () => {
        cancelWarning();
    });

    // Prevent Right Click (Inspect Element attempt)
    document.addEventListener('contextmenu', (e) => {
        e.preventDefault();
        alert('Security Restriction: Right-click is disabled during examination.');
    });

    // Block Common DevTools Shortcuts
    document.addEventListener('keydown', (e) => {
        // F12, Ctrl+Shift+I, Ctrl+Shift+J, Ctrl+U
        if (
            e.keyCode === 123 || 
            (e.ctrlKey && e.shiftKey && (e.keyCode === 73 || e.keyCode === 74)) ||
            (e.ctrlKey && e.keyCode === 85)
        ) {
            e.preventDefault();
            triggerWarning();
            alert('Security Alert: Developer Tools detected. Your exam will be auto-submitted.');
        }
    });
});
