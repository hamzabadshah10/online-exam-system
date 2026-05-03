function initTimer(durationMinutes) {
    let timeRemaining = durationMinutes * 60;
    const timerDisplay = document.getElementById('timer-display');
    const examForm = document.getElementById('exam-form');

    const updateTimer = setInterval(() => {
        const min = Math.floor(timeRemaining / 60);
        const sec = timeRemaining % 60;
        
        timerDisplay.innerText = 
            (min < 10 ? '0' : '') + min + ':' + 
            (sec < 10 ? '0' : '') + sec;

        if (timeRemaining <= 0) {
            clearInterval(updateTimer);
            document.getElementById('auto-submit-flag').value = '1';
            examForm.submit();
        }
        timeRemaining--;
    }, 1000);
}
