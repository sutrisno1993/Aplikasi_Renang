<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? 'Aplikasi Renang1' ?></title>
    <link rel="icon" type="image/png" href="<?= base_url('favicon.png?v=' . time()) ?>">
    <link rel="shortcut icon" type="image/png" href="<?= base_url('favicon.png?v=' . time()) ?>">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <link rel="stylesheet" href="<?= r2_url('css/style.css') ?>">
    <style>
        body {
            padding-top: 70px; /* Memberikan ruang untuk fixed navbar */
        }
    </style>
    <?= $this->renderSection('styles') ?>
</head>
<body>
    
    <!-- Test Base Template Loaded -->
    <?php if(ENVIRONMENT !== 'production'): ?>
    <?php endif; ?>
    <?= $this->renderSection('navbar') ?>

    <div class="container mt-4">
        <?= $this->renderSection('content') ?>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/js/bootstrap.min.js"></script>
    <?= $this->renderSection('scripts') ?>
    
    <?php if(file_exists(APPPATH . 'Views/templates/footer.php')): ?>
        <?= $this->include('templates/footer') ?>
    <?php endif; ?>
    

</body>
</html>