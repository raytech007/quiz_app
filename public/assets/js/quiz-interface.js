/**
 * Quiz Interface JavaScript
 * Handles the interface for taking quizzes including navigation, saving answers, and submission
 */
(function() {
    document.addEventListener('DOMContentLoaded', function() {
        // Check if we're on a quiz taking page
        if (!document.getElementById('questionContainer')) return;

        // Elements
        const questionContainer = document.getElementById('questionContainer');
        const prevBtn = document.getElementById('prevBtn');
        const nextBtn = document.getElementById('nextBtn');
        const saveBtn = document.getElementById('saveBtn');
        const submitQuizBtn = document.getElementById('submitQuizBtn');
        const confirmSubmitBtn = document.getElementById('confirmSubmitBtn');
        const questionBtns = document.querySelectorAll('.question-btn');
        const currentQuestionNumber = document.getElementById('currentQuestionNumber');
        const questionTypeDisplay = document.getElementById('questionTypeDisplay');
        const answeredQuestionsCount = document.getElementById('answeredQuestionsCount');
        const unansweredQuestionsCount = document.getElementById('unansweredQuestionsCount');

        // Quiz state
        let currentQuestionIndex = 0;
        let currentQuestion = null;
        let answeredQuestions = new Set();

        // Initialize
        function init() {
            // Count answered questions from initial data
            for (const key in quizAnswers) {
                answeredQuestions.add(key);
            }

            updateQuestionCounters();
            showQuestion(0);
            setupEventListeners();
        }

        // Setup event listeners
        function setupEventListeners() {
            // Navigation buttons
            if (prevBtn) prevBtn.addEventListener('click', showPreviousQuestion);
            if (nextBtn) nextBtn.addEventListener('click', showNextQuestion);
            if (saveBtn) saveBtn.addEventListener('click', saveCurrentAnswer);

            // Question palette buttons
            questionBtns.forEach(btn => {
                btn.addEventListener('click', function() {
                    const index = parseInt(this.dataset.questionIndex);
                    showQuestion(index);
                });
            });

            // Submit button
            if (submitQuizBtn) {
                submitQuizBtn.addEventListener('click', function(e) {
                    e.preventDefault();
                    const modal = new bootstrap.Modal(document.getElementById('submitConfirmModal'));
                    modal.show();
                });
            }

            // Confirm submit button
            if (confirmSubmitBtn) {
                confirmSubmitBtn.addEventListener('click', function() {
                    document.getElementById('quizForm').submit();
                });
            }
        }

        // Show a specific question
        function showQuestion(index) {
            // Validate index
            if (index < 0) index = 0;
            if (index >= quizQuestions.length) index = quizQuestions.length - 1;

            // Save current answer before changing question
            if (currentQuestion) {
                saveCurrentAnswer();
            }

            currentQuestionIndex = index;
            currentQuestion = quizQuestions[index];

            // Update question number display
            if (currentQuestionNumber) {
                currentQuestionNumber.textContent = index + 1;
            }

            // Update question type display
            if (questionTypeDisplay) {
                questionTypeDisplay.textContent = getQuestionTypeLabel(currentQuestion.question_type);
            }

            // Clear previous question
            questionContainer.innerHTML = '';

            // Render question based on type
            renderQuestion(currentQuestion);

            // Handle navigation button states
            updateNavigationButtons();

            // Update palette button states
            updatePaletteButtons();

            // Initialize MathJax rendering for the new content
            if (window.MathJax) {
                MathJax.typesetPromise([questionContainer]);
            }
        }

        // Render question based on type
        function renderQuestion(question) {
            const questionDiv = document.createElement('div');
            questionDiv.className = 'question-display';

            // Question content
            const contentDiv = document.createElement('div');
            contentDiv.className = 'question-content mb-4';
            contentDiv.innerHTML = `
                <div class="card">
                    <div class="card-header bg-light">
                        <strong>Question ${currentQuestionIndex + 1}:</strong>
                    </div>
                    <div class="card-body">
                        ${question.content}
                    </div>
                </div>
            `;
            questionDiv.appendChild(contentDiv);

            // Answer section
            const answerDiv = document.createElement('div');
            answerDiv.className = 'answer-section';

            switch (question.question_type_id) {
                case 1: // Multiple choice
                    renderMultipleChoiceOptions(answerDiv, question);
                    break;
                case 2: // True/False
                    renderTrueFalseOptions(answerDiv, question);
                    break;
                case 3: // Fill in the blank
                    renderFillBlankInput(answerDiv, question);
                    break;
                default:
                    answerDiv.innerHTML = '<div class="alert alert-danger">Unsupported question type</div>';
            }

            questionDiv.appendChild(answerDiv);
            questionContainer.appendChild(questionDiv);
        }

        // Render multiple choice options
        function renderMultipleChoiceOptions(container, question) {
            const userAnswer = quizAnswers[question.id] ? quizAnswers[question.id].user_answer : null;

            // Fetch options via AJAX
            fetch(`/admin/questions/get-options/${question.id}`)
                .then(response => response.json())
                .then(options => {
                    const optionsHtml = options.map((option, index) => {
                        const isChecked = userAnswer == option.id;
                        return `
                            <div class="form-check mb-3">
                                <input class="form-check-input" type="radio" name="answer-${question.id}" 
                                       id="option-${option.id}" value="${option.id}" ${isChecked ? 'checked' : ''}>
                                <label class="form-check-label" for="option-${option.id}">
                                    ${option.option_text}
                                </label>
                            </div>
                        `;
                    }).join('');

                    container.innerHTML = `
                        <div class="card">
                            <div class="card-header bg-light">
                                <strong>Select the correct answer:</strong>
                            </div>
                            <div class="card-body">
                                ${optionsHtml}
                            </div>
                        </div>
                    `;
                })
                .catch(error => {
                    console.error('Error loading options:', error);
                    container.innerHTML = '<div class="alert alert-danger">Error loading options</div>';
                });
        }

        // Render true/false options
        function renderTrueFalseOptions(container, question) {
            const userAnswer = quizAnswers[question.id] ? quizAnswers[question.id].user_answer : null;

            container.innerHTML = `
                <div class="card">
                    <div class="card-header bg-light">
                        <strong>Select True or False:</strong>
                    </div>
                    <div class="card-body">
                        <div class="form-check mb-3">
                            <input class="form-check-input" type="radio" name="answer-${question.id}" 
                                   id="tf-true-${question.id}" value="true" ${userAnswer === 'true' ? 'checked' : ''}>
                            <label class="form-check-label" for="tf-true-${question.id}">
                                True
                            </label>
                        </div>
                        <div class="form-check mb-3">
                            <input class="form-check-input" type="radio" name="answer-${question.id}" 
                                   id="tf-false-${question.id}" value="false" ${userAnswer === 'false' ? 'checked' : ''}>
                            <label class="form-check-label" for="tf-false-${question.id}">
                                False
                            </label>
                        </div>
                    </div>
                </div>
            `;
        }

        // Render fill in the blank input
        function renderFillBlankInput(container, question) {
            const userAnswer = quizAnswers[question.id] ? quizAnswers[question.id].user_answer : '';

            container.innerHTML = `
                <div class="card">
                    <div class="card-header bg-light">
                        <strong>Enter your answer:</strong>
                    </div>
                    <div class="card-body">
                        <div class="form-floating">
                            <input type="text" class="form-control" id="fill-blank-${question.id}" 
                                   name="answer-${question.id}" value="${userAnswer}" placeholder="Your answer">
                            <label for="fill-blank-${question.id}">Your answer</label>
                        </div>
                    </div>
                </div>
            `;
        }

        // Show previous question
        function showPreviousQuestion() {
            if (currentQuestionIndex > 0) {
                showQuestion(currentQuestionIndex - 1);
            }
        }

        // Show next question
        function showNextQuestion() {
            if (currentQuestionIndex < quizQuestions.length - 1) {
                showQuestion(currentQuestionIndex + 1);
            }
        }

        // Save the current answer
        function saveCurrentAnswer() {
            if (!currentQuestion) return;

            let answer = '';

            // Get answer based on question type
            const answerInput = document.querySelector(`input[name="answer-${currentQuestion.id}"]:checked`) ||
                               document.querySelector(`input[name="answer-${currentQuestion.id}"]`);

            if (answerInput) {
                answer = answerInput.value.trim();
            }

            // If answer is not empty, save it
            if (answer) {
                saveAnswer(currentQuestion.id, answer);
                answeredQuestions.add(currentQuestion.id.toString());
                updateQuestionCounters();
                updatePaletteButtons();
            }
        }

        // Save answer to server
        function saveAnswer(questionId, answer) {
            const formData = new FormData();
            formData.append('attempt_id', attemptId);
            formData.append('question_id', questionId);
            formData.append('answer', answer);
            formData.append(csrfName, csrfHash);

            fetch(saveAnswerUrl, {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (!data.success) {
                    console.error('Error saving answer:', data.message);
                }
            })
            .catch(error => {
                console.error('Error saving answer:', error);
            });
        }

        // Update navigation buttons
        function updateNavigationButtons() {
            if (prevBtn) prevBtn.disabled = currentQuestionIndex === 0;
            if (nextBtn) nextBtn.disabled = currentQuestionIndex === quizQuestions.length - 1;
        }

        // Update palette buttons
        function updatePaletteButtons() {
            questionBtns.forEach(btn => {
                const questionId = btn.dataset.questionId;
                const index = parseInt(btn.dataset.questionIndex);

                // Remove all classes
                btn.classList.remove('btn-secondary', 'btn-success', 'btn-primary');

                // Add appropriate class
                if (index === currentQuestionIndex) {
                    btn.classList.add('btn-primary'); // Current question
                } else if (answeredQuestions.has(questionId)) {
                    btn.classList.add('btn-success'); // Answered
                } else {
                    btn.classList.add('btn-secondary'); // Unanswered
                }
            });
        }

        // Update question counters
        function updateQuestionCounters() {
            const answered = answeredQuestions.size;
            const total = quizQuestions.length;
            const unanswered = total - answered;

            if (answeredQuestionsCount) answeredQuestionsCount.textContent = answered;
            if (unansweredQuestionsCount) unansweredQuestionsCount.textContent = unanswered;
        }

        // Helper function to get question type label
        function getQuestionTypeLabel(type) {
            switch (type) {
                case 'multiple_choice':
                    return 'Multiple Choice';
                case 'true_false':
                    return 'True/False';
                case 'fill_blank':
                    return 'Fill in the Blank';
                default:
                    return type;
            }
        }

        // Initialize the quiz interface
        init();
    });
})();