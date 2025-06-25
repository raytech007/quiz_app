<?php echo view('student/layout/header'); ?>

<div class="row my-4">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                <h5 class="mb-0"><i class="fas fa-chart-bar me-2"></i>Quiz Results</h5>
                <?php if ($attempt['is_completed']): ?>
                    <span class="badge bg-<?= $attempt['score_percentage'] >= $quiz['pass_percentage'] ? 'success' : 'danger' ?> p-2 fs-6">
                        Status: <?= $attempt['score_percentage'] >= $quiz['pass_percentage'] ? 'Pass' : 'Fail' ?>
                    </span>
                <?php endif; ?>
            </div>
            <div class="card-body">
                <div class="alert alert-info">
                    <i class="fas fa-info-circle me-2"></i>
                    <strong>Results are hidden.</strong> The instructor has configured this quiz to hide detailed results after completion.
                </div>

                <div class="text-center my-5">
                    <i class="fas fa-lock fa-5x text-muted mb-3"></i>
                    <h3>Detailed results are not available for viewing</h3>
                    <p class="text-muted">Please contact your instructor if you need more information about your performance.</p>
                </div>

                <div class="row mb-4">
                    <div class="col-md-6 mx-auto">
                        <div class="card">
                            <div class="card-header bg-light">
                                <h6 class="mb-0">Quiz Information</h6>
                            </div>
                            <div class="card-body">
                                <table class="table table-bordered">
                                    <tr>
                                        <th class="bg-light" style="width: 40%">Quiz Title</th>
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
                                    <?php if ($attempt['is_completed']): ?>
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
                                    <?php endif; ?>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
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

<?php echo view('student/layout/footer'); ?>
