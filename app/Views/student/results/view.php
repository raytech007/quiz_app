<?php echo view('student/layout/header'); ?>

<div class="row my-4">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                <h5 class="mb-0"><i class="fas fa-chart-bar me-2"></i>Quiz Results</h5>
                <?php if ($attempt['is_completed']): ?>
                    <span class="badge bg-<?= $attempt['score_percentage'] >= 60 ? 'success' : 'danger' ?> p-2 fs-6">
                        Score: <?= number_format($attempt['score_percentage'], 2) ?>%
                    </span>
                <?php endif; ?>
            </div>
            <div class="card-body">
                <?php if (isset($resultsHidden) && $resultsHidden): ?>
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle me-2"></i>
                        <strong>Results are hidden.</strong> The instructor has configured this quiz to hide detailed results after completion.
                    </div>

                    <div class="text-center my-5">
                        <i class="fas fa-lock fa-5x text-muted mb-3"></i>
                        <h3>Results are not available for viewing</h3>
                        <p class="text-muted">Please contact your instructor if you need more information about your performance.</p>
                    </div>
                <?php else: ?>
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <h6 class="fw-bold">Quiz Information</h6>
                            <table class="table table-bordered">
                                <tr>
                                    <th class="bg-light" style="width: 30%">Quiz Title</th>
                                    <td><?= esc($quiz['title']) ?></td>
                                </tr>
                                <tr>
                                    <th class="bg-light">Started On</th>
                                    <td><?= date('M d, Y H:i:s', strtotime($attempt['start_time'])) ?></td>
                                </tr>
                                <?php if ($attempt['is_completed'] && !empty($attempt['end_time'])): ?>
                                    <tr>
                                        <th class="bg-light">Completed On</th>
                                        <td><?= date('M d, Y H:i:s', strtotime($attempt['end_time'])) ?></td>
                                    </tr>
                                    <tr>
                                        <th class="bg-light">Time Taken</th>
                                        <td>
                                            <?php
                                                $startTime = strtotime($attempt['start_time']);
                                                $endTime = strtotime($attempt['end_time']);
                                                $duration = $endTime - $startTime;

                                                $hours = floor($duration / 3600);
                                                $minutes = floor(($duration % 3600) / 60);
                                                $seconds = $duration % 60;

                                                if ($hours > 0) {
                                                    echo "{$hours}h {$minutes}m {$seconds}s";
                                                } else {
                                                    echo "{$minutes}m {$seconds}s";
                                                }
                                            ?>
                                        </td>
                                    </tr>
                                <?php endif; ?>
                            </table>
                        </div>

                        <div class="col-md-6">
                            <h6 class="fw-bold">Result Summary</h6>
                            <?php if ($attempt['is_completed']): ?>
                                <table class="table table-bordered">
                                    <tr>
                                        <th class="bg-light" style="width: 30%">Total Questions</th>
                                        <td><?= count($answers) ?></td>
                                    </tr>
                                    <tr>
                                        <th class="bg-light">Correct Answers</th>
                                        <td>
                                            <?php
                                                $correctCount = 0;
                                                foreach ($answers as $answer) {
                                                    if ($answer['is_correct']) {
                                                        $correctCount++;
                                                    }
                                                }
                                                echo $correctCount;
                                            ?>
                                        </td>
                                    </tr>
                                    <tr>
                                        <th class="bg-light">Score</th>
                                        <td><?= number_format($attempt['score_percentage'], 2) ?>%</td>
                                    </tr>
                                    <tr>
                                        <th class="bg-light">Status</th>
                                        <td>
                                            <?php if ($attempt['score_percentage'] >= $quiz['pass_percentage']): ?>
                                                <span class="badge bg-success">Pass</span>
                                            <?php else: ?>
                                                <span class="badge bg-danger">Fail</span>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                </table>
                            <?php else: ?>
                                <div class="alert alert-warning">
                                    <i class="fas fa-exclamation-triangle me-2"></i>
                                    <strong>Quiz is not completed.</strong> This attempt is still in progress.
                                </div>
                                <a href="<?= base_url('student/quizzes/take/' . $quiz['id']) ?>" class="btn btn-warning">
                                    <i class="fas fa-redo-alt me-2"></i>Resume Quiz
                                </a>
                            <?php endif; ?>
                        </div>
                    </div>

                    <?php if ($attempt['is_completed']): ?>
                        <div class="mb-4">
                            <h6 class="fw-bold">Detailed Results</h6>

                            <div class="accordion" id="resultsAccordion">
                                <?php foreach ($answers as $index => $answer): ?>
                                    <div class="accordion-item">
                                        <h2 class="accordion-header" id="heading<?= $index ?>">
                                            <button class="accordion-button <?= $index > 0 ? 'collapsed' : '' ?>" type="button"
                                                data-bs-toggle="collapse" data-bs-target="#collapse<?= $index ?>"
                                                aria-expanded="<?= $index == 0 ? 'true' : 'false' ?>" aria-controls="collapse<?= $index ?>">
                                                <div class="d-flex align-items-center w-100">
                                                    <span class="me-3">Question <?= $index + 1 ?></span>
                                                    <?php if ($answer['is_correct']): ?>
                                                        <span class="badge bg-success ms-auto me-2">Correct</span>
                                                    <?php else: ?>
                                                        <span class="badge bg-danger ms-auto me-2">Incorrect</span>
                                                    <?php endif; ?>
                                                </div>
                                            </button>
                                        </h2>
                                        <div id="collapse<?= $index ?>" class="accordion-collapse collapse <?= $index == 0 ? 'show' : '' ?>"
                                            aria-labelledby="heading<?= $index ?>" data-bs-parent="#resultsAccordion">
                                            <div class="accordion-body">
                                                <div class="question-content mb-3">
                                                    <div class="card">
                                                        <div class="card-header bg-light">
                                                            <strong>Question:</strong>
                                                        </div>
                                                        <div class="card-body">
                                                            <?= $answer['question']['content'] ?>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="your-answer mb-3">
                                                    <div class="card">
                                                        <div class="card-header bg-light">
                                                            <strong>Your Answer:</strong>
                                                        </div>
                                                        <div class="card-body">
                                                            <?php if ($answer['question']['question_type_id'] == 1): // Multiple choice ?>
                                                                <?php foreach ($answer['options'] as $option): ?>
                                                                    <div class="form-check mb-2">
                                                                        <input class="form-check-input" type="radio"
                                                                            <?= ($answer['user_answer'] == $option['id']) ? 'checked' : '' ?> disabled>
                                                                        <label class="form-check-label <?= ($answer['user_answer'] == $option['id']) ? ($option['is_correct'] ? 'text-success fw-bold' : 'text-danger fw-bold') : '' ?>">
                                                                            <?= $option['option_text'] ?>
                                                                            <?php if ($option['is_correct'] && $showAnswers): ?>
                                                                                <i class="fas fa-check-circle text-success ms-1"></i>
                                                                            <?php endif; ?>
                                                                        </label>
                                                                    </div>
                                                                <?php endforeach; ?>
                                                            <?php elseif ($answer['question']['question_type_id'] == 2): // True/False ?>
                                                                <div class="form-check mb-2">
                                                                    <input class="form-check-input" type="radio"
                                                                        <?= $answer['user_answer'] == 'true' ? 'checked' : '' ?> disabled>
                                                                    <label class="form-check-label <?= ($answer['user_answer'] == 'true') ? ($answer['is_correct'] ? 'text-success fw-bold' : 'text-danger fw-bold') : '' ?>">
                                                                        True
                                                                    </label>
                                                                </div>
                                                                <div class="form-check mb-2">
                                                                    <input class="form-check-input" type="radio"
                                                                        <?= $answer['user_answer'] == 'false' ? 'checked' : '' ?> disabled>
                                                                    <label class="form-check-label <?= ($answer['user_answer'] == 'false') ? ($answer['is_correct'] ? 'text-success fw-bold' : 'text-danger fw-bold') : '' ?>">
                                                                        False
                                                                    </label>
                                                                </div>
                                                            <?php elseif ($answer['question']['question_type_id'] == 3): // Fill in the blank ?>
                                                                <div class="input-group">
                                                                    <input type="text" class="form-control" value="<?= esc($answer['user_answer']) ?>"
                                                                        readonly>
                                                                    <span class="input-group-text">
                                                                        <?php if ($answer['is_correct']): ?>
                                                                            <i class="fas fa-check text-success"></i>
                                                                        <?php else: ?>
                                                                            <i class="fas fa-times text-danger"></i>
                                                                        <?php endif; ?>
                                                                    </span>
                                                                </div>
                                                            <?php endif; ?>
                                                        </div>
                                                    </div>
                                                </div>

                                                <?php if ($showAnswers && !$answer['is_correct']): ?>
                                                    <div class="correct-answer mb-3">
                                                        <div class="card">
                                                            <div class="card-header bg-success text-white">
                                                                <strong>Correct Answer:</strong>
                                                            </div>
                                                            <div class="card-body">
                                                                <?php if ($answer['question']['question_type_id'] == 1): // Multiple choice ?>
                                                                    <?php foreach ($answer['options'] as $option): ?>
                                                                        <?php if ($option['is_correct']): ?>
                                                                            <div class="text-success fw-bold"><?= $option['option_text'] ?></div>
                                                                        <?php endif; ?>
                                                                    <?php endforeach; ?>
                                                                <?php elseif ($answer['question']['question_type_id'] == 2): // True/False ?>
                                                                    <div class="text-success fw-bold">
                                                                        <?= $answer['correct_answer']['option_text'] ?>
                                                                    </div>
                                                                <?php elseif ($answer['question']['question_type_id'] == 3): // Fill in the blank ?>
                                                                    <ul class="list-group">
                                                                        <?php foreach ($answer['correct_answers'] as $correct): ?>
                                                                            <li class="list-group-item text-success">
                                                                                <?= esc($correct['answer_text']) ?>
                                                                                <?php if ($correct['is_case_sensitive']): ?>
                                                                                    <small class="text-muted">(case sensitive)</small>
                                                                                <?php endif; ?>
                                                                            </li>
                                                                        <?php endforeach; ?>
                                                                    </ul>
                                                                <?php endif; ?>
                                                            </div>
                                                        </div>
                                                    </div>
                                                <?php endif; ?>

                                                <?php if (!empty($answer['question']['explanation'])): ?>
                                                    <div class="explanation">
                                                        <div class="card">
                                                            <div class="card-header bg-info text-white">
                                                                <strong>Explanation:</strong>
                                                            </div>
                                                            <div class="card-body">
                                                                <?= $answer['question']['explanation'] ?>
                                                            </div>
                                                        </div>
                                                    </div>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    <?php endif; ?>
                <?php endif; ?>
            </div>
            <div class="card-footer">
                <a href="<?= base_url('student/results') ?>" class="btn btn-secondary">
                    <i class="fas fa-arrow-left me-2"></i>Back to Results
                </a>

                <?php if ($attempt['is_completed'] && $quiz['max_attempts'] > 0): ?>
                    <?php
                    // Count user's attempts for this quiz
                    $db = \Config\Database::connect();
                    $userId = session()->get('id');
                    $attemptsCount = $db->table('quiz_attempts')
                        ->where('quiz_id', $quiz['id'])
                        ->where('user_id', $userId)
                        ->countAllResults();

                    // Check if user can attempt again
                    $canAttemptAgain = $attemptsCount < $quiz['max_attempts'];
                    ?>

                    <?php if ($canAttemptAgain): ?>
                        <a href="<?= base_url('student/quizzes/take/' . $quiz['id']) ?>" class="btn btn-primary float-end">
                            <i class="fas fa-redo me-2"></i>Attempt Again
                        </a>
                    <?php endif; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Initialize MathJax rendering if present
    if (typeof MathJax !== 'undefined') {
        MathJax.typesetPromise();
    }
});
</script>

<?php echo view('student/layout/footer'); ?>
