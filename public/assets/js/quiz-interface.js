/**
 * Quiz Interface JavaScript
 * Handles the interface for taking quizzes including navigation, saving answers, and submission
 */
(function() {
    document.addEventListener('DOMContentLoaded', function() {
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

            // Set up event listeners
            setupEventListeners();
        }

        // Setup event listeners
        function setupEventListeners() {
            // Navigation buttons
            prevBtn.addEventListener('click', showPreviousQuestion);
            nextBtn.addEventListener('click', showNextQuestion);
            saveBtn.addEventListener('click', saveCurrentAnswer);

            // Question palette buttons
            questionBtns.forEach(btn => {
                btn.addEventListener('click', function() {
                    const index = parseInt(this.dataset.questionIndex);
                    showQuestion(index);
                });
            });

            // Submit button
            submitQuizBtn.addEventListener('click', function(e) {
                e.preventDefault();
                $('#submitConfirmModal').modal('show');
            });

            // Confirm submit button
            confirmSubmitBtn.addEventListener('click', function() {
                document.getElementById('quizForm').submit();
            });
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
            currentQuestionNumber.textContent = index + 1;

            // Update question type display
            questionTypeDisplay.textContent = getQuestionTypeLabel(currentQuestion.question_type);

            // Clear previous question
            questionContainer.innerHTML = '';

            // Get the appropriate template based on question type
            let template;
            switch (currentQuestion.question_type) {
                case 'multiple_choice':
                    template = document.getElementById('template-multiple-choice').cloneNode(true);
                    renderMultipleChoiceQuestion(template, currentQuestion);
                    break;
                case 'true_false':
                    template = document.getElementById('template-true-false').cloneNode(true);
                    renderTrueFalseQuestion(template, currentQuestion);
                    break;
                case 'fill_blank':
                    template = document.getElementById('template-fill-blank').cloneNode(true);
                    renderFillBlankQuestion(template, currentQuestion);
                    break;
                default:
                    template = document.createElement('div');
                    template.innerHTML = '<div class="alert alert-danger">Unsupported question type</div>';
            }

            // Show the template
            questionContainer.appendChild(template);

            // Handle navigation button states
            updateNavigationButtons();

            // Initialize MathJax rendering for the new content
            if (window.MathJax) {
                MathJax.typesetPromise([questionContainer]);
            }
        }

        // Render multiple choice question
        function renderMultipleChoiceQuestion(template, question) {
            // Set question content
            template.querySelector('.question-content').innerHTML = question.content;

            // Get options container
            const optionsContainer = template.querySelector('.options-container');

            // Get the user's previous answer if any
            const userAnswer = quizAnswers[question.id] ? quizAnswers[question.id].user_answer : null;

            // Get options from AJAX
            fetch(`/admin/questions/get-options/${question.id}`)
                .then(response => response.json())
                .then(options => {
                    // Create option elements
                    options.forEach((option, index) => {
                        const optionId = `option-${question.id}-${index}`;
                        const isChecked = userAnswer == option.id;

                        const optionDiv = document.createElement('div');
                        optionDiv.className = 'form-check mb-2';
                        optionDiv.innerHTML = `
                            <input class="form-check-input" type="radio" name="answer-${question.id}"
                                id="${optionId}" value="${option.id}" ${isChecked ? 'checked' : ''}>
                            <label class="form-check-label" for="${optionId}">
                                ${option.option_text}
                            </label>
                        `;

                        optionsContainer.appendChild(optionDiv);
                    });
                })
                .catch(error => {
                    console.error('Error loading options:', error);
                    optionsContainer.innerHTML = '<div class="alert alert-danger">Error loading options</div>';
                });
        }

        // Render true/false question
        function renderTrueFalseQuestion(template, question) {
            // Set question content
            template.querySelector('.question-content').innerHTML = question.content;

            // Get the user's previous answer if any
            const userAnswer = quizAnswers[question.id] ? quizAnswers[question.id].user_answer : null;

            // Set the correct option if previously answered
            if (userAnswer) {
                const value = userAnswer === 'true' ? 'true' : 'false';
                const input = template.querySelector(`input[value="${value}"]`);
                if (input) input.checked = true;
            }
        }

        // Render fill in the blank question
        function renderFillBlankQuestion(template, question) {
            // Set question content
            template.querySelector('.question-content').innerHTML = question.content;

            // Get the user's previous answer if any
            const userAnswer = quizAnswers[question.id] ? quizAnswers[question.id].user_answer : '';

            // Set the input value
            const input = template.querySelector('#fill-blank-answer');
            input.value = userAnswer;
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
            switch (currentQuestion.question_type) {
                case 'multiple_choice':
                    const mcSelected = document.querySelector(`input[name="answer-${currentQuestion.id}"]:checked`);
                    answer = mcSelected ? mcSelected.value : '';
                    break;
                case 'true_false':
                    const tfSelected = document.querySelector('input[name="tf-answer"]:checked');
                    answer = tfSelected ? tfSelected.value : '';
                    break;
                case 'fill_blank':
                    const fbInput = document.querySelector('#fill-blank-answer');
                    answer = fbInput ? fbInput.value.trim() : '';
                    break;
            }

            // If answer is not empty, save it
            if (answer) {
                saveAnswer(currentQuestion.id, answer);
                answeredQuestions.add(currentQuestion.id.toString());

                // Update the question button to green
                const btn = document.querySelector(`.question-btn[data-question-id="${currentQuestion.id}"]`);
                if (btn) {
                    btn.classList.remove('btn-secondary');
                    btn.classList.add('btn-success');
                }

                updateQuestionCounters();
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

        // Update navigation buttons (prev/next/submit)
        function updateNavigationButtons() {
            // Disable prev button on first question
            prevBtn.disabled = currentQuestionIndex === 0;

            // Disable next button on last question
            nextBtn.disabled = currentQuestionIndex === quizQuestions.length - 1;
        }

        // Update question counters in the submission modal
        function updateQuestionCounters() {
            const answered = answeredQuestions.size;
            const total = quizQuestions.length;
            const unanswered = total - answered;

            answeredQuestionsCount.textContent = answered;
            unansweredQuestionsCount.textContent = unanswered;
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
