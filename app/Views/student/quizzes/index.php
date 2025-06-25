<?php echo view('student/layout/header'); ?>

<div class="row my-4">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0"><i class="fas fa-clipboard-list me-2"></i>Available Quizzes</h5>
            </div>
            <div class="card-body">
                <?php if (empty($availableQuizzes)): ?>
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle me-2"></i>You have no available quizzes at this moment.
                    </div>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th>Quiz Title</th>
                                    <th>Class</th>
                                    <th>Due Date</th>
                                    <th>Time Limit</th>
                                    <th>Attempts</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($availableQuizzes as $quiz): ?>
                                    <tr>
                                        <td><?= esc($quiz['quiz_title']) ?></td>
                                        <td><?= esc($quiz['class_name']) ?></td>
                                        <td><?= date('M d, Y H:i', strtotime($quiz['end_time'])) ?></td>
                                        <td>
                                            <?php if ($quiz['time_limit'] > 0): ?>
                                                <?= $quiz['time_limit'] ?> minutes
                                            <?php else: ?>
                                                <span class="text-muted">No time limit</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <?php if ($quiz['max_attempts'] == 0): ?>
                                                <?= $quiz['attempts'] ?> / <span class="text-muted">Unlimited</span>
                                            <?php else: ?>
                                                <?= $quiz['attempts'] ?> / <?= $quiz['max_attempts'] ?>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <a href="<?= base_url('student/quizzes/view/' . $quiz['quiz_id']) ?>" class="btn btn-sm btn-info">
                                                <i class="fas fa-info-circle"></i> Details
                                            </a>
                                            <?php if ($quiz['can_attempt']): ?>
                                                <a href="<?= base_url('student/quizzes/take/' . $quiz['quiz_id']) ?>" class="btn btn-sm btn-success">
                                                    <i class="fas fa-play"></i> Take Quiz
                                                </a>
                                            <?php else: ?>
                                                <button class="btn btn-sm btn-secondary" disabled>
                                                    <i class="fas fa-ban"></i> Max Attempts Reached
                                                </button>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header bg-success text-white">
                <h5 class="mb-0"><i class="fas fa-history me-2"></i>Your Recent Quiz Attempts</h5>
            </div>
            <div class="card-body">
                <div class="text-end mb-3">
                    <a href="<?= base_url('student/results') ?>" class="btn btn-primary">
                        <i class="fas fa-chart-bar me-1"></i>View All Results
                    </a>
                </div>

                <?php
                // Get recent attempts from database
                $db = \Config\Database::connect();
                $userId = session()->get('id');

                $recentAttempts = $db->table('quiz_attempts')
                    ->select('quiz_attempts.*, quizzes.title as quiz_title')
                    ->join('quizzes', 'quizzes.id = quiz_attempts.quiz_id')
                    ->where('quiz_attempts.user_id', $userId)
                    ->orderBy('quiz_attempts.start_time', 'DESC')
                    ->limit(5)
                    ->get()
                    ->getResultArray();
                ?>

                <?php if (empty($recentAttempts)): ?>
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle me-2"></i>You haven't attempted any quizzes yet.
                    </div>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th>Quiz</th>
                                    <th>Started On</th>
                                    <th>Status</th>
                                    <th>Score</th>
                                    <th>View</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($recentAttempts as $attempt): ?>
                                    <tr>
                                        <td><?= esc($attempt['quiz_title']) ?></td>
                                        <td><?= date('M d, Y H:i', strtotime($attempt['start_time'])) ?></td>
                                        <td>
                                            <?php if ($attempt['is_completed']): ?>
                                                <span class="badge bg-success">Completed</span>
                                            <?php else: ?>
                                                <span class="badge bg-warning text-dark">In Progress</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <?php if ($attempt['is_completed']): ?>
                                                <span class="badge bg-<?= $attempt['score_percentage'] >= 60 ? 'success' : 'danger' ?>">
                                                    <?= number_format($attempt['score_percentage'], 2) ?>%
                                                </span>
                                            <?php else: ?>
                                                <span class="text-muted">N/A</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <a href="<?= base_url('student/results/view/' . $attempt['id']) ?>" class="btn btn-sm btn-primary">
                                                <i class="fas fa-eye"></i> View
                                            </a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?php echo view('student/layout/footer'); ?>
