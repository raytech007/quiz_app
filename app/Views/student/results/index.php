<?php echo view('student/layout/header'); ?>

<div class="row my-4">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0"><i class="fas fa-chart-bar me-2"></i>My Quiz Results</h5>
            </div>
            <div class="card-body">
                <?php if (empty($attempts)): ?>
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle me-2"></i>You haven't attempted any quizzes yet.
                    </div>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover" id="resultsTable">
                            <thead class="table-light">
                                <tr>
                                    <th>Quiz</th>
                                    <th>Attempt #</th>
                                    <th>Started On</th>
                                    <th>Completed On</th>
                                    <th>Time Taken</th>
                                    <th>Status</th>
                                    <th>Score</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                // Group attempts by quiz
                                $attemptsByQuiz = [];
                                foreach ($attempts as $attempt) {
                                    if (!isset($attemptsByQuiz[$attempt['quiz_id']])) {
                                        $attemptsByQuiz[$attempt['quiz_id']] = [];
                                    }
                                    $attemptsByQuiz[$attempt['quiz_id']][] = $attempt;
                                }

                                // Count attempts for each quiz
                                foreach ($attemptsByQuiz as $quizId => $quizAttempts) {
                                    $attemptCount = count($quizAttempts);

                                    // Sort attempts by start time, newest first
                                    usort($quizAttempts, function($a, $b) {
                                        return strtotime($b['start_time']) - strtotime($a['start_time']);
                                    });

                                    foreach ($quizAttempts as $index => $attempt):
                                        $attemptNumber = $attemptCount - $index;

                                        // Calculate time taken
                                        $timeTaken = 'N/A';
                                        if ($attempt['is_completed'] && !empty($attempt['end_time'])) {
                                            $startTime = strtotime($attempt['start_time']);
                                            $endTime = strtotime($attempt['end_time']);
                                            $duration = $endTime - $startTime;

                                            $hours = floor($duration / 3600);
                                            $minutes = floor(($duration % 3600) / 60);
                                            $seconds = $duration % 60;

                                            if ($hours > 0) {
                                                $timeTaken = "{$hours}h {$minutes}m {$seconds}s";
                                            } else {
                                                $timeTaken = "{$minutes}m {$seconds}s";
                                            }
                                        }
                                ?>
                                    <tr>
                                        <td><?= esc($attempt['quiz_title']) ?></td>
                                        <td><?= $attemptNumber ?></td>
                                        <td><?= date('M d, Y H:i', strtotime($attempt['start_time'])) ?></td>
                                        <td>
                                            <?php if ($attempt['is_completed'] && !empty($attempt['end_time'])): ?>
                                                <?= date('M d, Y H:i', strtotime($attempt['end_time'])) ?>
                                            <?php else: ?>
                                                <span class="text-muted">Not completed</span>
                                            <?php endif; ?>
                                        </td>
                                        <td><?= $timeTaken ?></td>
                                        <td>
                                            <?php if ($attempt['is_completed']): ?>
                                                <span class="badge bg-success">Completed</span>
                                            <?php else: ?>
                                                <span class="badge bg-warning text-dark">In Progress</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <?php if ($attempt['is_completed']): ?>
                                                <span class="badge bg-<?= $attempt['score_percentage'] >= 60 ? 'success' : 'danger' ?> p-2">
                                                    <?= number_format($attempt['score_percentage'], 2) ?>%
                                                </span>
                                            <?php else: ?>
                                                <span class="text-muted">N/A</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <?php if ($attempt['is_completed']): ?>
                                                <a href="<?= base_url('student/results/view/' . $attempt['id']) ?>" class="btn btn-sm btn-primary">
                                                    <i class="fas fa-eye"></i> View
                                                </a>
                                            <?php else: ?>
                                                <a href="<?= base_url('student/quizzes/take/' . $attempt['quiz_id']) ?>" class="btn btn-sm btn-warning">
                                                    <i class="fas fa-redo-alt"></i> Resume
                                                </a>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php
                                    endforeach;
                                }
                                ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<div class="row mt-4">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header bg-info text-white">
                <h5 class="mb-0"><i class="fas fa-trophy me-2"></i>Your Performance Summary</h5>
            </div>
            <div class="card-body">
                <?php if (empty($attempts)): ?>
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle me-2"></i>No data available yet. Take some quizzes to see your performance summary.
                    </div>
                <?php else: ?>
                    <?php
                        // Calculate performance metrics
                        $totalAttempts = count($attempts);
                        $completedAttempts = 0;
                        $totalScore = 0;
                        $passedQuizzes = 0;

                        foreach ($attempts as $attempt) {
                            if ($attempt['is_completed']) {
                                $completedAttempts++;
                                $totalScore += $attempt['score_percentage'];

                                if ($attempt['score_percentage'] >= 60) {
                                    $passedQuizzes++;
                                }
                            }
                        }

                        $averageScore = $completedAttempts > 0 ? $totalScore / $completedAttempts : 0;
                        $passRate = $completedAttempts > 0 ? ($passedQuizzes / $completedAttempts) * 100 : 0;
                    ?>

                    <div class="row">
                        <div class="col-md-3 mb-3">
                            <div class="card bg-light">
                                <div class="card-body text-center">
                                    <h6 class="card-title">Total Attempts</h6>
                                    <h2 class="display-4"><?= $totalAttempts ?></h2>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-3 mb-3">
                            <div class="card bg-light">
                                <div class="card-body text-center">
                                    <h6 class="card-title">Completed Quizzes</h6>
                                    <h2 class="display-4"><?= $completedAttempts ?></h2>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-3 mb-3">
                            <div class="card bg-light">
                                <div class="card-body text-center">
                                    <h6 class="card-title">Average Score</h6>
                                    <h2 class="display-4"><?= number_format($averageScore, 1) ?>%</h2>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-3 mb-3">
                            <div class="card bg-light">
                                <div class="card-body text-center">
                                    <h6 class="card-title">Pass Rate</h6>
                                    <h2 class="display-4"><?= number_format($passRate, 1) ?>%</h2>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Add sorting functionality to the results table if DataTables is available
    if (typeof $.fn.DataTable !== 'undefined') {
        $('#resultsTable').DataTable({
            "order": [[2, "desc"]], // Sort by start date descending
            "pageLength": 10,
            "language": {
                "emptyTable": "No quiz attempts found"
            }
        });
    }
});
</script>

<?php echo view('student/layout/footer'); ?>
