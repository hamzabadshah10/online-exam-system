document.addEventListener('DOMContentLoaded', () => {
    const warningBox = document.getElementById('cheat-warning-box');
    const timerDisplay = document.getElementById('cheat-timer');
    const examForm = document.getElementById('exam-form');
    
    let warningTimer;
    let secondsLeft = 5;

    document.addEventListener('visibilitychange', () => {
        if (document.hidden) {
            // Tab is hidden, show warning
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
        } else {
            // Tab is visible again, cancel warning
            clearInterval(warningTimer);
            warningBox.classList.add('hidden');
        }
    });
});
