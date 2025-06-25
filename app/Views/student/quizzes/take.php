<?php echo view('student/layout/header'); ?>

<div class="quiz-container mb-4">
    <!-- Quiz Header -->
    <div class="card mb-4">
        <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
            <h5 class="mb-0"><i class="fas fa-clipboard-list me-2"></i><?= esc($quiz['title']) ?></h5>
            <div>
                <?php if($quiz['time_limit'] > 0): ?>
                <div class="timer">
                    <i class="fas fa-clock me-1"></i>Time Remaining:
                    <span id="timer" data-time-remaining="<?= $timeRemaining ?>" data-attempt-id="<?= $attempt['id'] ?>"
                        class="badge bg-light text-dark p-2">
                        <?= floor($timeRemaining / 60) ?>:<?= str_pad($timeRemaining % 60, 2, '0', STR_PAD_LEFT) ?>
                    </span>
                </div>
                <?php endif; ?>
            </div>
        </div>
        <div class="card-body">
            <div class="alert alert-info">
                <i class="fas fa-info-circle me-2"></i>
                <strong>Instructions:</strong> Answer all questions to the best of your ability. Use the question palette to navigate between questions.
                Your answers are automatically saved when you click the "Save" button or navigate to another question.
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Question Palette -->
        <div class="col-md-3 mb-4">
            <div class="card">
                <div class="card-header bg-secondary text-white">
                    <h5 class="mb-0">Question Palette</h5>
                </div>
                <div class="card-body">
                    <div class="palette-container">
                        <?php foreach($questions as $index => $question): ?>
                            <?php
                                $buttonClass = "btn-secondary"; // Default gray for unattempted
                                if(isset($answers[$question['id']])) {
                                    $buttonClass = "btn-success"; // Green for answered
                                }
                                $questionNumber = $index + 1;
                            ?>
                            <button class="btn <?= $buttonClass ?> question-btn m-1"
                                data-question-index="<?= $index ?>"
                                data-question-id="<?= $question['id'] ?>">
                                <?= $questionNumber ?>
                            </button>
                        <?php endforeach; ?>
                    </div>

                    <div class="palette-legend mt-3">
                        <div class="d-flex align-items-center mb-2">
                            <div class="btn-secondary btn-sm me-2" style="width: 20px; height: 20px;"></div>
                            <small>Not Answered</small>
                        </div>
                        <div class="d-flex align-items-center">
                            <div class="btn-success btn-sm me-2" style="width: 20px; height: 20px;"></div>
                            <small>Answered</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Question Display -->
        <div class="col-md-9">
            <div class="card">
                <div class="card-header bg-info text-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0 question-header">Question <span id="currentQuestionNumber">1</span> of <?= count($questions) ?></h5>
                    <div id="questionTypeDisplay" class="badge bg-light text-dark"></div>
                </div>
                <div class="card-body">
                    <form id="quizForm" action="<?= base_url('student/quizzes/submit') ?>" method="post">
                        <?= csrf_field() ?>
                        <input type="hidden" name="attempt_id" value="<?= $attempt['id'] ?>">

                        <div id="questionContainer">
                            <!-- Questions will be dynamically shown here -->
                        </div>

                        <div class="question-navigation mt-4 d-flex justify-content-between">
                            <button type="button" id="prevBtn" class="btn btn-secondary">
                                <i class="fas fa-arrow-left me-1"></i> Previous
                            </button>

                            <button type="button" id="saveBtn" class="btn btn-primary">
                                <i class="fas fa-save me-1"></i> Save
                            </button>

                            <button type="button" id="nextBtn" class="btn btn-primary">
                                Next <i class="fas fa-arrow-right ms-1"></i>
                            </button>

                            <button type="submit" id="submitQuizBtn" class="btn btn-danger">
                                <i class="fas fa-stop-circle me-1"></i> Submit Quiz
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Question Templates (Hidden) -->
<div id="questionTemplates" style="display: none;">
    <!-- Multiple Choice Template -->
    <div id="template-multiple-choice" class="question-template">
        <div class="question-content mb-3"></div>
        <div class="options-container"></div>
    </div>

    <!-- True/False Template -->
    <div id="template-true-false" class="question-template">
        <div class="question-content mb-3"></div>
        <div class="options-container">
            <div class="form-check mb-2">
                <input class="form-check-input" type="radio" name="tf-answer" id="tf-true" value="true">
                <label class="form-check-label" for="tf-true">True</label>
            </div>
            <div class="form-check mb-2">
                <input class="form-check-input" type="radio" name="tf-answer" id="tf-false" value="false">
                <label class="form-check-label" for="tf-false">False</label>
            </div>
        </div>
    </div>

    <!-- Fill in the Blank Template -->
    <div id="template-fill-blank" class="question-template">
        <div class="question-content mb-3"></div>
        <div class="input-container">
            <div class="form-floating mb-3">
                <input type="text" class="form-control" id="fill-blank-answer" placeholder="Your answer">
                <label for="fill-blank-answer">Your answer</label>
            </div>
        </div>
    </div>
</div>

<!-- Confirmation Modal -->
<div class="modal fade" id="submitConfirmModal" tabindex="-1" aria-labelledby="submitConfirmModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title" id="submitConfirmModalLabel">Confirm Submission</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to submit this quiz?</p>
                <p><strong>Warning:</strong> Once submitted, you cannot return to this quiz attempt.</p>

                <div class="alert alert-warning">
                    <strong>Summary:</strong>
                    <ul>
                        <li>Total Questions: <span id="totalQuestionsCount"><?= count($questions) ?></span></li>
                        <li>Answered Questions: <span id="answeredQuestionsCount">0</span></li>
                        <li>Unanswered Questions: <span id="unansweredQuestionsCount"><?= count($questions) ?></span></li>
                    </ul>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" id="confirmSubmitBtn" class="btn btn-danger">Submit Quiz</button>
            </div>
        </div>
    </div>
</div>

<!-- Store questions data for JavaScript -->
<script>
    const quizQuestions = <?= json_encode($questions) ?>;
    const quizAnswers = <?= json_encode($answers) ?>;
    const attemptId = <?= $attempt['id'] ?>;
    const saveAnswerUrl = "<?= base_url('api/quiz/save-answer') ?>";
    const csrfName = "<?= csrf_token() ?>";
    const csrfHash = "<?= csrf_hash() ?>";
</script>

<?php echo view('student/layout/footer'); ?>
