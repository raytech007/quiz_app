<?php echo view('teacher/layout/header'); ?>

<div class="row my-4">
    <!-- Total Quizzes Card -->
    <div class="col-lg-4 col-md-6 mb-4">
        <div class="card bg-primary text-white h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div class="me-3">
                        <div class="text-white-75 small">My Quizzes</div>
                        <div class="text-lg fw-bold"><?= $totalQuizzes ?></div>
                    </div>
                    <i class="fas fa-clipboard-list fa-2x text-white-50"></i>
                </div>
            </div>
            <div class="card-footer d-flex align-items-center justify-content-between">
                <a class="small text-white stretched-link" href="<?= base_url('teacher/quizzes') ?>">View Details</a>
                <div class="small text-white"><i class="fas fa-angle-right"></i></div>
            </div>
        </div>
    </div>

    <!-- Total Questions Card -->
    <div class="col-lg-4 col-md-6 mb-4">
        <div class="card bg-success text-white h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div class="me-3">
                        <div class="text-white-75 small">My Questions</div>
                        <div class="text-lg fw-bold"><?= $totalQuestions ?></div>
                    </div>
                    <i class="fas fa-question-circle fa-2x text-white-50"></i>
                </div>
            </div>
            <div class="card-footer d-flex align-items-center justify-content-between">
                <a class="small text-white stretched-link" href="<?= base_url('teacher/questions') ?>">View Details</a>
                <div class="small text-white"><i class="fas fa-angle-right"></i></div>
            </div>
        </div>
    </div>

    <!-- Total Classes Card -->
    <div class="col-lg-4 col-md-6 mb-4">
        <div class="card bg-info text-white h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div class="me-3">
                        <div class="text-white-75 small">My Classes</div>
                        <div class="text-lg fw-bold"><?= $totalClasses ?></div>
                    </div>
                    <i class="fas fa-users fa-2x text-white-50"></i>
                </div>
            </div>
            <div class="card-footer d-flex align-items-center justify-content-between">
                <a class="small text-white stretched-link" href="<?= base_url('teacher/classes') ?>">View Details</a>
                <div class="small text-white"><i class="fas fa-angle-right"></i></div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <!-- Recent Quiz Attempts -->
    <div class="col-md-6 mb-4">
        <div class="card">
            <div class="card-header">
                <i class="fas fa-history me-1"></i>
                Recent Quiz Attempts
            </div>
            <div class="card-body">
                <?php if (empty($recentAttempts)): ?>
                    <p class="text-center">No recent quiz attempts found.</p>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover">
                            <thead>
                                <tr>
                                    <th>Student</th>
                                    <th>Quiz</th>
                                    <th>Score</th>
                                    <th>Date</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($recentAttempts as $attempt): ?>
                                    <tr>
                                        <td><?= esc($attempt['first_name']) ?> <?= esc($attempt['last_name']) ?></td>
                                        <td><?= esc($attempt['quiz_title']) ?></td>
                                        <td>
                                            <?php if ($attempt['is_completed']): ?>
                                                <?= number_format($attempt['score_percentage'], 2) ?>%
                                            <?php else: ?>
                                                <span class="badge bg-warning">In Progress</span>
                                            <?php endif; ?>
                                        </td>
                                        <td><?= date('M d, Y H:i', strtotime($attempt['start_time'])) ?></td>
                                        <td>
                                            <a href="<?= base_url('teacher/results/view/' . $attempt['id']) ?>" class="btn btn-sm btn-primary">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                    <div class="text-end mt-2">
                        <a href="<?= base_url('teacher/results') ?>" class="btn btn-sm btn-primary">View All Results</a>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Upcoming Assignments -->
    <div class="col-md-6 mb-4">
        <div class="card">
            <div class="card-header">
                <i class="fas fa-calendar-alt me-1"></i>
                Upcoming Quiz Assignments
            </div>
            <div class="card-body">
                <?php if (empty($upcomingAssignments)): ?>
                    <p class="text-center">No upcoming quiz assignments found.</p>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover">
                            <thead>
                                <tr>
                                    <th>Quiz</th>
                                    <th>Class</th>
                                    <th>Start Date</th>
                                    <th>End Date</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($upcomingAssignments as $assignment): ?>
                                    <tr>
                                        <td><?= esc($assignment['quiz_title']) ?></td>
                                        <td><?= esc($assignment['class_name']) ?></td>
                                        <td><?= date('M d, Y H:i', strtotime($assignment['start_time'])) ?></td>
                                        <td><?= date('M d, Y H:i', strtotime($assignment['end_time'])) ?></td>
                                        <td>
                                            <a href="<?= base_url('teacher/assignments/edit/' . $assignment['id']) ?>" class="btn btn-sm btn-primary">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                    <div class="text-end mt-2">
                        <a href="<?= base_url('teacher/assignments/add') ?>" class="btn btn-sm btn-success">
                            <i class="fas fa-plus"></i> New Assignment
                        </a>
                        <a href="<?= base_url('teacher/assignments') ?>" class="btn btn-sm btn-primary">View All</a>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- Teacher's Classes -->
<div class="row">
    <div class="col-md-12 mb-4">
        <div class="card">
            <div class="card-header">
                <i class="fas fa-users me-1"></i>
                My Classes
            </div>
            <div class="card-body">
                <?php if (empty($classes)): ?>
                    <p class="text-center">No classes found.</p>
                <?php else: ?>
                    <div class="row">
                        <?php foreach ($classes as $class): ?>
                            <div class="col-md-4 mb-3">
                                <div class="card h-100">
                                    <div class="card-body">
                                        <h5 class="card-title"><?= esc($class['name']) ?></h5>
                                        <p class="card-text text-muted small">
                                            <?= nl2br(esc(character_limiter($class['description'], 100))) ?>
                                        </p>
                                    </div>
                                    <div class="card-footer">
                                        <a href="<?= base_url('teacher/classes/students/' . $class['id']) ?>" class="btn btn-sm btn-primary">
                                            <i class="fas fa-users"></i> View Students
                                        </a>
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

<?php echo view('teacher/layout/footer'); ?>
