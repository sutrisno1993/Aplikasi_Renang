<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? 'Aplikasi Renang' ?></title>
    <link rel="icon" type="image/png" href="<?= base_url('favicon.png?v=' . time()) ?>">
    <link rel="shortcut icon" type="image/png" href="<?= base_url('favicon.png?v=' . time()) ?>">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/css/bootstrap.min.css">
    <link rel="stylesheet" href="<?= r2_url('css/style.css') ?>">
</head>
<body>
    <!-- Tambahkan navbar di sini jika diperlukan -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container">
            <a class="navbar-brand" href="#">Aplikasi Renang</a>
            <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ml-auto">
                    <?php if(session()->get('isLoggedIn')): ?>
                    <li class="nav-item">
                        <a class="nav-link" href="#">Selamat datang, <?= session()->get('nama') ?></a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="<?= base_url('auth/logout') ?>">Logout</a>
                    </li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container mt-5">
        <?= $this->renderSection('content') ?>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/js/bootstrap.min.js"></script>
    
    <?php if(file_exists(APPPATH . 'Views/templates/footer.php')): ?>
        <?= $this->include('templates/footer') ?>
    <?php endif; ?>
</body>
</html>