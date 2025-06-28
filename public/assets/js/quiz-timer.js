/**
 * Quiz Timer JavaScript
 * Handles the countdown timer for quizzes with time limits
 */
(function() {
    document.addEventListener('DOMContentLoaded', function() {
        // Get timer element
        const timerElement = document.getElementById('timer');

        // If there's no timer element, exit
        if (!timerElement) return;

        // Get time remaining in seconds and attempt ID
        let timeRemaining = parseInt(timerElement.dataset.timeRemaining);
        const attemptId = timerElement.dataset.attemptId;

        // If no time limit, exit
        if (timeRemaining <= 0) return;

        // Update timer display
        function updateTimerDisplay() {
            const minutes = Math.floor(timeRemaining / 60);
            const seconds = timeRemaining % 60;
            timerElement.textContent = `${minutes}:${seconds.toString().padStart(2, '0')}`;

            // Change color when time is running low
            if (timeRemaining <= 60) { // Last minute
                timerElement.classList.remove('bg-light', 'text-dark', 'bg-warning');
                timerElement.classList.add('bg-danger', 'text-white');
            } else if (timeRemaining <= 300) { // Last 5 minutes
                timerElement.classList.remove('bg-light', 'text-dark');
                timerElement.classList.add('bg-warning', 'text-dark');
            }
        }

        // Timer function
        function startTimer() {
            const timerId = setInterval(function() {
                timeRemaining--;

                if (timeRemaining <= 0) {
                    clearInterval(timerId);
                    handleTimeUp();
                } else {
                    updateTimerDisplay();
                }

                // Every 30 seconds, check with server for the actual time remaining
                if (timeRemaining % 30 === 0) {
                    syncTimerWithServer();
                }
            }, 1000);
        }

        // Sync with server to ensure accurate time
        function syncTimerWithServer() {
            fetch(`/api/quiz/get-time/${attemptId}`)
                .then(response => response.json())
                .then(data => {
                    if (data.success && data.timeRemaining !== null) {
                        // Only update if there's a significant difference (more than 5 seconds)
                        if (Math.abs(timeRemaining - data.timeRemaining) > 5) {
                            timeRemaining = data.timeRemaining;
                            updateTimerDisplay();
                        }

                        // If time is up according to server
                        if (data.timeRemaining <= 0) {
                            handleTimeUp();
                        }
                    }
                })
                .catch(error => console.error('Error syncing timer:', error));
        }

        // Handle time up
        function handleTimeUp() {
            timerElement.textContent = '0:00';
            timerElement.classList.remove('bg-light', 'text-dark', 'bg-warning');
            timerElement.classList.add('bg-danger', 'text-white');

            // Show alert
            alert('Time is up! Your quiz will be automatically submitted.');

            // Submit the form
            document.getElementById('quizForm').submit();
        }

        // Start the timer
        updateTimerDisplay();
        startTimer();
    });
})();