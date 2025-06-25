<?php echo view('admin/layout/header'); ?>

<div class="row my-4">
    <!-- Total Students Card -->
    <div class="col-lg-3 col-md-6 mb-4">
        <div class="card bg-primary text-white h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div class="me-3">
                        <div class="text-white-75 small">Total Students</div>
                        <div class="text-lg fw-bold"><?= $totalStudents ?></div>
                    </div>
                    <i class="fas fa-user-graduate fa-2x text-white-50"></i>
                </div>
            </div>
            <div class="card-footer d-flex align-items-center justify-content-between">
                <a class="small text-white stretched-link" href="<?= base_url('admin/users') ?>">View Details</a>
                <div class="small text-white"><i class="fas fa-angle-right"></i></div>
            </div>
        </div>
    </div>

    <!-- Total Teachers Card -->
    <div class="col-lg-3 col-md-6 mb-4">
        <div class="card bg-success text-white h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div class="me-3">
                        <div class="text-white-75 small">Total Teachers</div>
                        <div class="text-lg fw-bold"><?= $totalTeachers ?></div>
                    </div>
                    <i class="fas fa-chalkboard-teacher fa-2x text-white-50"></i>
                </div>
            </div>
            <div class="card-footer d-flex align-items-center justify-content-between">
                <a class="small text-white stretched-link" href="<?= base_url('admin/users') ?>">View Details</a>
                <div class="small text-white"><i class="fas fa-angle-right"></i></div>
            </div>
        </div>
    </div>

    <!-- Total Quizzes Card -->
    <div class="col-lg-3 col-md-6 mb-4">
        <div class="card bg-danger text-white h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div class="me-3">
                        <div class="text-white-75 small">Total Quizzes</div>
                        <div class="text-lg fw-bold"><?= $totalQuizzes ?></div>
                    </div>
                    <i class="fas fa-clipboard-list fa-2x text-white-50"></i>
                </div>
            </div>
            <div class="card-footer d-flex align-items-center justify-content-between">
                <a class="small text-white stretched-link" href="<?= base_url('admin/quizzes') ?>">View Details</a>
                <div class="small text-white"><i class="fas fa-angle-right"></i></div>
            </div>
        </div>
    </div>

    <!-- Total Questions Card -->
    <div class="col-lg-3 col-md-6 mb-4">
        <div class="card bg-info text-white h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div class="me-3">
                        <div class="text-white-75 small">Total Questions</div>
                        <div class="text-lg fw-bold"><?= $totalQuestions ?></div>
                    </div>
                    <i class="fas fa-question-circle fa-2x text-white-50"></i>
                </div>
            </div>
            <div class="card-footer d-flex align-items-center justify-content-between">
                <a class="small text-white stretched-link" href="<?= base_url('admin/questions') ?>">View Details</a>
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
                                            <a href="<?= base_url('admin/results/view/' . $attempt['id']) ?>" class="btn btn-sm btn-primary">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                    <div class="text-end mt-2">
                        <a href="<?= base_url('admin/results') ?>" class="btn btn-sm btn-primary">View All</a>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Recent User Activities -->
    <div class="col-md-6 mb-4">
        <div class="card">
            <div class="card-header">
                <i class="fas fa-chart-line me-1"></i>
                Recent User Activities
            </div>
            <div class="card-body">
                <?php if (empty($recentActivities)): ?>
                    <p class="text-center">No recent activities found.</p>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover">
                            <thead>
                                <tr>
                                    <th>User</th>
                                    <th>Activity</th>
                                    <th>Date</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($recentActivities as $activity): ?>
                                    <tr>
                                        <td>
                                            <?php if (!empty($activity['first_name'])): ?>
                                                <?= esc($activity['first_name']) ?> <?= esc($activity['last_name']) ?>
                                            <?php else: ?>
                                                <em>System</em>
                                            <?php endif; ?>
                                        </td>
                                        <td><?= esc($activity['description']) ?></td>
                                        <td><?= date('M d, Y H:i', strtotime($activity['created_at'])) ?></td>
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
    <!-- Classes and Students -->
    <div class="col-md-12 mb-4">
        <div class="card">
            <div class="card-header">
                <i class="fas fa-user-graduate me-1"></i>
                Classes and Students
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <div class="card bg-light mb-3">
                            <div class="card-body">
                                <h5 class="card-title">Total Classes</h5>
                                <h2 class="display-4"><?= $totalClasses ?></h2>
                                <a href="<?= base_url('admin/classes') ?>" class="btn btn-primary btn-sm">Manage Classes</a>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card bg-light mb-3">
                            <div class="card-body">
                                <h5 class="card-title">Total Admins</h5>
                                <h2 class="display-4"><?= $totalAdmins ?></h2>
                                <a href="<?= base_url('admin/users') ?>" class="btn btn-primary btn-sm">Manage Users</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php echo view('admin/layout/footer'); ?>
