<?php echo view('student/layout/header'); ?>

<div class="row my-4">
    <!-- Available Quizzes Card -->
    <div class="col-lg-6 col-md-6 mb-4">
        <div class="card bg-primary text-white h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div class="me-3">
                        <div class="text-white-75 small">Available Quizzes</div>
                        <div class="text-lg fw-bold"><?= count($availableQuizzes) ?></div>
                    </div>
                    <i class="fas fa-clipboard-list fa-2x text-white-50"></i>
                </div>
            </div>
            <div class="card-footer d-flex align-items-center justify-content-between">
                <a class="small text-white stretched-link" href="<?= base_url('student/quizzes') ?>">View Available Quizzes</a>
                <div class="small text-white"><i class="fas fa-angle-right"></i></div>
            </div>
        </div>
    </div>

    <!-- My Classes Card -->
    <div class="col-lg-6 col-md-6 mb-4">
        <div class="card bg-success text-white h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div class="me-3">
                        <div class="text-white-75 small">My Classes</div>
                        <div class="text-lg fw-bold"><?= count($classes) ?></div>
                    </div>
                    <i class="fas fa-users fa-2x text-white-50"></i>
                </div>
            </div>
            <div class="card-footer d-flex align-items-center justify-content-between">
                <a class="small text-white stretched-link" href="#classes-section">View My Classes</a>
                <div class="small text-white"><i class="fas fa-angle-right"></i></div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <!-- Available Quizzes -->
    <div class="col-md-12 mb-4">
        <div class="card">
            <div class="card-header bg-primary text-white">
                <i class="fas fa-clipboard-list me-1"></i>
                Available Quizzes
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
    <!-- Recent Attempts -->
    <div class="col-md-6 mb-4">
        <div class="card">
            <div class="card-header bg-info text-white">
                <i class="fas fa-history me-1"></i>
                Recent Quiz Attempts
            </div>
            <div class="card-body">
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
                                    <th>Date</th>
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
                                                <span class="badge bg-<?= $attempt['score_percentage'] >= 60 ? 'success' : 'danger' ?>">
                                                    <?= number_format($attempt['score_percentage'], 2) ?>%
                                                </span>
                                            <?php else: ?>
                                                <span class="badge bg-warning">In Progress</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <a href="<?= base_url('student/results/view/' . $attempt['id']) ?>" class="btn btn-sm btn-primary">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                    <div class="text-end mt-2">
                        <a href="<?= base_url('student/results') ?>" class="btn btn-sm btn-primary">View All Results</a>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Upcoming Quizzes -->
    <div class="col-md-6 mb-4">
        <div class="card">
            <div class="card-header bg-warning text-dark">
                <i class="fas fa-calendar-alt me-1"></i>
                Upcoming Quizzes
            </div>
            <div class="card-body">
                <?php if (empty($upcomingQuizzes)): ?>
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle me-2"></i>You have no upcoming quizzes scheduled.
                    </div>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th>Quiz</th>
                                    <th>Class</th>
                                    <th>Start Date</th>
                                    <th>End Date</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($upcomingQuizzes as $quiz): ?>
                                    <tr>
                                        <td><?= esc($quiz['quiz_title']) ?></td>
                                        <td><?= esc($quiz['class_name']) ?></td>
                                        <td><?= date('M d, Y H:i', strtotime($quiz['start_time'])) ?></td>
                                        <td><?= date('M d, Y H:i', strtotime($quiz['end_time'])) ?></td>
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

<!-- My Classes Section -->
<div class="row" id="classes-section">
    <div class="col-md-12 mb-4">
        <div class="card">
            <div class="card-header bg-success text-white">
                <i class="fas fa-users me-1"></i>
                My Classes
            </div>
            <div class="card-body">
                <?php if (empty($classes)): ?>
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle me-2"></i>You are not enrolled in any classes yet.
                    </div>
                <?php else: ?>
                    <div class="row">
                        <?php foreach ($classes as $class): ?>
                            <div class="col-md-4 mb-3">
                                <div class="card h-100 border-success">
                                    <div class="card-header bg-light">
                                        <h5 class="card-title mb-0"><?= esc($class['name']) ?></h5>
                                    </div>
                                    <div class="card-body">
                                        <p class="card-text">
                                            <?= nl2br(esc(character_limiter($class['description'], 100))) ?>
                                        </p>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?php echo view('student/layout/footer'); ?>
