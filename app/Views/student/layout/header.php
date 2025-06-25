<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? 'Student Dashboard' ?> - Quiz Application</title>

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="<?= base_url('assets/lib/bootstrap/css/bootstrap.min.css') ?>">

    <!-- Font Awesome -->
    <link rel="stylesheet" href="<?= base_url('assets/lib/fontawesome/css/all.min.css') ?>">

    <!-- Custom Student CSS -->
    <link rel="stylesheet" href="<?= base_url('assets/css/student.css') ?>">

    <!-- MathJax -->
    <script src="<?= base_url('assets/lib/mathjax/tex-mml-chtml.js') ?>"></script>

    <!-- Common CSS -->
    <link rel="stylesheet" href="<?= base_url('assets/css/common.css') ?>">

    <!-- Favicon -->
    <link rel="icon" href="<?= base_url('assets/images/favicon.png') ?>">
</head>
<body>
    <div class="d-flex" id="wrapper">
        <!-- Sidebar -->
        <div class="bg-dark" id="sidebar-wrapper">
            <div class="sidebar-heading text-center py-4 text-white fs-4 fw-bold text-uppercase border-bottom">
                <i class="fas fa-graduation-cap me-2"></i>Quiz App
            </div>
            <div class="list-group list-group-flush my-3">
                <a href="<?= base_url('student') ?>" class="list-group-item list-group-item-action bg-transparent second-text <?= ($active ?? '') === 'dashboard' ? 'active' : '' ?>">
                    <i class="fas fa-tachometer-alt me-2"></i>Dashboard
                </a>
                <a href="<?= base_url('student/quizzes') ?>" class="list-group-item list-group-item-action bg-transparent second-text <?= ($active ?? '') === 'quizzes' ? 'active' : '' ?>">
                    <i class="fas fa-clipboard-list me-2"></i>My Quizzes
                </a>
                <a href="<?= base_url('student/results') ?>" class="list-group-item list-group-item-action bg-transparent second-text <?= ($active ?? '') === 'results' ? 'active' : '' ?>">
                    <i class="fas fa-chart-bar me-2"></i>My Results
                </a>
                <a href="<?= base_url('logout') ?>" class="list-group-item list-group-item-action bg-transparent text-danger">
                    <i class="fas fa-sign-out-alt me-2"></i>Logout
                </a>
            </div>
        </div>
        <!-- /#sidebar-wrapper -->

        <!-- Page Content -->
        <div id="page-content-wrapper">
            <nav class="navbar navbar-expand-lg navbar-light bg-transparent py-4 px-4">
                <div class="d-flex align-items-center">
                    <i class="fas fa-align-left primary-text fs-4 me-3" id="menu-toggle"></i>
                    <h2 class="fs-2 m-0"><?= $title ?? 'Student Dashboard' ?></h2>
                </div>

                <button class="navbar-toggler" type="button" data-bs-toggle="collapse"
                    data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent"
                    aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>

                <div class="collapse navbar-collapse" id="navbarSupportedContent">
                    <ul class="navbar-nav ms-auto mb-2 mb-lg-0">
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle second-text fw-bold" href="#" id="navbarDropdown"
                                role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="fas fa-user me-2"></i><?= session()->get('firstName') . ' ' . session()->get('lastName') ?>
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
                                <li><a class="dropdown-item" href="#"><i class="fas fa-user-circle me-2"></i>Profile</a></li>
                                <li><hr class="dropdown-divider"></li>
                                <li><a class="dropdown-item" href="<?= base_url('logout') ?>"><i class="fas fa-sign-out-alt me-2"></i>Logout</a></li>
                            </ul>
                        </li>
                    </ul>
                </div>
            </nav>

            <div class="container-fluid px-4">
                <?php if (session()->has('message')): ?>
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <?= session('message') ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                <?php endif; ?>

                <?php if (session()->has('error')): ?>
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <?= session('error') ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                <?php endif; ?>
