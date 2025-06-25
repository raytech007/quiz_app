<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Quiz Application</title>
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="<?= base_url('assets/lib/bootstrap/css/bootstrap.min.css') ?>">
    <!-- Custom styles -->
    <link rel="stylesheet" href="<?= base_url('assets/css/auth.css') ?>">
</head>
<body class="bg-light">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-6 col-lg-5">
                <div class="card shadow-lg border-0 rounded-lg mt-5">
                    <div class="card-header bg-primary text-white text-center py-4">
                        <h3 class="font-weight-light my-2">Quiz Application</h3>
                        <h4 class="font-weight-light">Login</h4>
                    </div>
                    <div class="card-body">
                        <?php if (session()->has('error')) : ?>
                            <div class="alert alert-danger">
                                <?= session('error') ?>
                            </div>
                        <?php endif; ?>

                        <?php if (session()->has('message')) : ?>
                            <div class="alert alert-success">
                                <?= session('message') ?>
                            </div>
                        <?php endif; ?>

                        <form action="<?= base_url('login') ?>" method="post">
                            <?= csrf_field() ?>

                            <div class="form-floating mb-3">
                                <input class="form-control" id="email" name="email" type="email" placeholder="name@example.com" value="<?= old('email') ?>" required />
                                <label for="email">Email address</label>
                                <?php if (isset($errors) && isset($errors['email'])) : ?>
                                    <div class="text-danger"><?= $errors['email'] ?></div>
                                <?php endif; ?>
                            </div>

                            <div class="form-floating mb-3">
                                <input class="form-control" id="password" name="password" type="password" placeholder="Password" required />
                                <label for="password">Password</label>
                                <?php if (isset($errors) && isset($errors['password'])) : ?>
                                    <div class="text-danger"><?= $errors['password'] ?></div>
                                <?php endif; ?>
                            </div>

                            <div class="d-flex align-items-center justify-content-between mt-4 mb-0">
                                <button type="submit" class="btn btn-primary w-100">Login</button>
                            </div>
                        </form>
                    </div>
                    <div class="card-footer text-center py-3">
                        <div class="small text-muted">
                            If you don't have an account or forgot your password, please contact the administrator.
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS Bundle with Popper -->
    <script src="<?= base_url('assets/lib/bootstrap/js/bootstrap.bundle.min.js') ?>"></script>
</body>
</html>
