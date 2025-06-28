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
        <div class="col-lg-3 col-md-4 mb-4">
            <div class="card question-palette">
                <div class="card-header bg-secondary text-white">
                    <h6 class="mb-0"><i class="fas fa-th me-2"></i>Question Palette</h6>
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
                            <button class="btn <?= $buttonClass ?> question-btn"
                                data-question-index="<?= $index ?>"
                                data-question-id="<?= $question['id'] ?>"
                                title="Question <?= $questionNumber ?>">
                                <?= $questionNumber ?>
                            </button>
                        <?php endforeach; ?>
                    </div>

                    <div class="palette-legend mt-3">
                        <div class="d-flex align-items-center mb-2">
                            <div class="btn btn-secondary btn-sm me-2" style="width: 20px; height: 20px; padding: 0;"></div>
                            <small>Not Answered</small>
                        </div>
                        <div class="d-flex align-items-center mb-2">
                            <div class="btn btn-success btn-sm me-2" style="width: 20px; height: 20px; padding: 0;"></div>
                            <small>Answered</small>
                        </div>
                        <div class="d-flex align-items-center">
                            <div class="btn btn-primary btn-sm me-2" style="width: 20px; height: 20px; padding: 0;"></div>
                            <small>Current Question</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Question Display -->
        <div class="col-lg-9 col-md-8">
            <div class="card">
                <div class="card-header bg-info text-white d-flex justify-content-between align-items-center">
                    <h6 class="mb-0">Question <span id="currentQuestionNumber">1</span> of <?= count($questions) ?></h6>
                    <div id="questionTypeDisplay" class="badge bg-light text-dark"></div>
                </div>
                <div class="card-body">
                    <form id="quizForm" action="<?= base_url('student/quizzes/submit') ?>" method="post">
                        <?= csrf_field() ?>
                        <input type="hidden" name="attempt_id" value="<?= $attempt['id'] ?>">

                        <div id="questionContainer">
                            <!-- Questions will be dynamically shown here -->
                        </div>

                        <div class="question-navigation mt-4">
                            <button type="button" id="prevBtn" class="btn btn-secondary">
                                <i class="fas fa-arrow-left me-1"></i> Previous
                            </button>

                            <button type="button" id="saveBtn" class="btn btn-primary">
                                <i class="fas fa-save me-1"></i> Save Answer
                            </button>

                            <button type="button" id="nextBtn" class="btn btn-primary">
                                Next <i class="fas fa-arrow-right ms-1"></i>
                            </button>

                            <button type="button" id="submitQuizBtn" class="btn btn-danger">
                                <i class="fas fa-stop-circle me-1"></i> Submit Quiz
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Confirmation Modal -->
<div class="modal fade" id="submitConfirmModal" tabindex="-1" aria-labelledby="submitConfirmModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title" id="submitConfirmModalLabel">
                    <i class="fas fa-exclamation-triangle me-2"></i>Confirm Submission
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="alert alert-warning">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    <strong>Warning:</strong> Once submitted, you cannot return to this quiz attempt.
                </div>

                <p class="mb-3">Are you sure you want to submit this quiz?</p>

                <div class="card">
                    <div class="card-header bg-light">
                        <strong>Submission Summary:</strong>
                    </div>
                    <div class="card-body">
                        <div class="row text-center">
                            <div class="col-4">
                                <div class="border-end">
                                    <h4 class="text-primary mb-1" id="totalQuestionsCount"><?= count($questions) ?></h4>
                                    <small class="text-muted">Total Questions</small>
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="border-end">
                                    <h4 class="text-success mb-1" id="answeredQuestionsCount">0</h4>
                                    <small class="text-muted">Answered</small>
                                </div>
                            </div>
                            <div class="col-4">
                                <h4 class="text-danger mb-1" id="unansweredQuestionsCount"><?= count($questions) ?></h4>
                                <small class="text-muted">Unanswered</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="fas fa-times me-1"></i>Cancel
                </button>
                <button type="button" id="confirmSubmitBtn" class="btn btn-danger">
                    <i class="fas fa-check me-1"></i>Submit Quiz
                </button>
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