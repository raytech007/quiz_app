<?php echo view('student/layout/header'); ?>

<div class="row my-4">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0"><i class="fas fa-clipboard-list me-2"></i><?= esc($quiz['title']) ?></h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-8">
                        <div class="mb-4">
                            <h6 class="fw-bold">Description:</h6>
                            <div class="p-3 bg-light rounded">
                                <?= nl2br(esc($quiz['description'])) ?>
                            </div>
                        </div>

                        <div class="mb-4">
                            <h6 class="fw-bold">Quiz Details:</h6>
                            <table class="table table-bordered">
                                <tr>
                                    <th class="bg-light" style="width: 30%">Number of Questions</th>
                                    <td><?= $questionCount ?></td>
                                </tr>
                                <tr>
                                    <th class="bg-light">Time Limit</th>
                                    <td>
                                        <?php if ($quiz['time_limit'] > 0): ?>
                                            <span class="badge bg-warning text-dark"><?= $quiz['time_limit'] ?> minutes</span>
                                        <?php else: ?>
                                            <span class="badge bg-secondary">No time limit</span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                                <tr>
                                    <th class="bg-light">Pass Percentage</th>
                                    <td><?= $quiz['pass_percentage'] ?>%</td>
                                </tr>
                                <tr>
                                    <th class="bg-light">Maximum Attempts</th>
                                    <td>
                                        <?php if ($quiz['max_attempts'] == 0): ?>
                                            <span class="badge bg-info">Unlimited</span>
                                        <?php else: ?>
                                            <span class="badge bg-primary"><?= $quiz['max_attempts'] ?></span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                                <tr>
                                    <th class="bg-light">Show Results After Completion</th>
                                    <td>
                                        <?php if ($quiz['show_results']): ?>
                                            <span class="badge bg-success">Yes</span>
                                        <?php else: ?>
                                            <span class="badge bg-danger">No</span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                                <tr>
                                    <th class="bg-light">Show Correct Answers</th>
                                    <td>
                                        <?php if ($quiz['show_answers']): ?>
                                            <span class="badge bg-success">Yes</span>
                                        <?php else: ?>
                                            <span class="badge bg-danger">No</span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                                <tr>
                                    <th class="bg-light">Created By</th>
                                    <td><?= esc($quiz['first_name']) ?> <?= esc($quiz['last_name']) ?></td>
                                </tr>
                            </table>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="card mb-4">
                            <div class="card-header bg-success text-white">
                                <h6 class="mb-0">Ready to Begin?</h6>
                            </div>
                            <div class="card-body">
                                <?php if ($activeAttempt): ?>
                                    <div class="alert alert-warning">
                                        <i class="fas fa-exclamation-triangle me-2"></i>
                                        <strong>You have an unfinished attempt.</strong> You can resume it by clicking the button below.
                                    </div>
                                    <a href="<?= base_url('student/quizzes/take/' . $quiz['id']) ?>" class="btn btn-warning btn-lg w-100">
                                        <i class="fas fa-redo-alt me-2"></i>Resume Quiz
                                    </a>
                                <?php elseif ($canAttempt): ?>
                                    <div class="alert alert-info">
                                        <i class="fas fa-info-circle me-2"></i>
                                        <strong>Important:</strong> Make sure you have enough time to complete this quiz before starting.
                                        <?php if ($quiz['time_limit'] > 0): ?>
                                            You will have <strong><?= $quiz['time_limit'] ?> minutes</strong> to complete it.
                                        <?php endif; ?>
                                    </div>
                                    <a href="<?= base_url('student/quizzes/take/' . $quiz['id']) ?>" class="btn btn-success btn-lg w-100">
                                        <i class="fas fa-play me-2"></i>Start Quiz
                                    </a>
                                <?php else: ?>
                                    <div class="alert alert-danger">
                                        <i class="fas fa-exclamation-circle me-2"></i>
                                        <strong>Maximum attempts reached.</strong> You have used all your allowed attempts for this quiz.
                                    </div>
                                    <button class="btn btn-secondary btn-lg w-100" disabled>
                                        <i class="fas fa-lock me-2"></i>Cannot Attempt
                                    </button>
                                <?php endif; ?>
                            </div>
                        </div>

                        <div class="card">
                            <div class="card-header bg-info text-white">
                                <h6 class="mb-0">Your Attempts</h6>
                            </div>
                            <div class="card-body">
                                <?php if (empty($attempts)): ?>
                                    <p class="text-center text-muted">You haven't attempted this quiz yet.</p>
                                <?php else: ?>
                                    <ul class="list-group">
                                        <?php foreach ($attempts as $attempt): ?>
                                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                                <div>
                                                    <span class="badge bg-primary">#<?= count($attempts) - array_search($attempt, $attempts) ?></span>
                                                    <?= date('M d, Y H:i', strtotime($attempt['start_time'])) ?>
                                                    <?php if (!$attempt['is_completed']): ?>
                                                        <span class="badge bg-warning text-dark">In Progress</span>
                                                    <?php endif; ?>
                                                </div>

                                                <?php if ($attempt['is_completed']): ?>
                                                    <div>
                                                        <span class="badge bg-<?= $attempt['score_percentage'] >= $quiz['pass_percentage'] ? 'success' : 'danger' ?>">
                                                            <?= number_format($attempt['score_percentage'], 2) ?>%
                                                        </span>
                                                        <a href="<?= base_url('student/results/view/' . $attempt['id']) ?>" class="btn btn-sm btn-primary ms-2">
                                                            <i class="fas fa-eye"></i>
                                                        </a>
                                                    </div>
                                                <?php else: ?>
                                                    <a href="<?= base_url('student/quizzes/take/' . $quiz['id']) ?>" class="btn btn-sm btn-warning">
                                                        <i class="fas fa-redo-alt"></i> Resume
                                                    </a>
                                                <?php endif; ?>
                                            </li>
                                        <?php endforeach; ?>
                                    </ul>
                                <?php endif; ?>

                                <div class="mt-3">
                                    <p class="mb-0">
                                        <strong>Your attempts:</strong> <?= count($attempts) ?> /
                                        <?= $quiz['max_attempts'] == 0 ? '<span class="text-muted">Unlimited</span>' : $quiz['max_attempts'] ?>
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card-footer">
                <a href="<?= base_url('student/quizzes') ?>" class="btn btn-secondary">
                    <i class="fas fa-arrow-left me-2"></i>Back to Quizzes
                </a>
            </div>
        </div>
    </div>
</div>

<?php echo view('student/layout/footer'); ?>
