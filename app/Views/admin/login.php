<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login - Renang</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        :root {
            --primary-color: #00a8ff;
            --secondary-color: #0097e6;
            --dark-color: #2f3640;
            --light-color: #f5f6fa;
        }

        body {
            font-family: 'Poppins', 'Segoe UI', sans-serif;
            background: #eef2f7 url('https://images.unsplash.com/photo-1519791883288-dc8bd696e667?ixlib=rb-4.0.3&auto=format&fit=crop&w=1920&q=80') no-repeat center center fixed;
            background-size: cover;
            height: 100vh;
            margin: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
        }

        body::before {
            content: '';
            position: absolute;
            top: 0; left: 0; right: 0; bottom: 0;
            background: linear-gradient(135deg, rgba(0, 168, 255, 0.8), rgba(47, 54, 64, 0.85));
            z-index: 1;
        }

        .container {
            position: relative;
            z-index: 2;
        }
        
        .login-card {
            width: 100%;
            max-width: 420px;
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-radius: 24px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
            overflow: hidden;
            border: 1px solid rgba(255, 255, 255, 0.2);
            animation: fadeInDown 0.8s ease-out;
        }

        @keyframes fadeInDown {
            from { opacity: 0; transform: translateY(-30px); }
            to { opacity: 1; transform: translateY(0); }
        }
        
        .login-header {
            background: var(--dark-color);
            padding: 40px 20px;
            text-align: center;
            color: white;
            position: relative;
        }

        .login-header::after {
            content: '';
            position: absolute;
            bottom: 0; left: 0; right: 0;
            height: 4px;
            background: linear-gradient(to right, var(--primary-color), var(--secondary-color));
        }

        .login-header h3 {
            font-weight: 700;
            letter-spacing: 1px;
            margin-bottom: 8px;
            text-transform: uppercase;
        }

        .login-header p {
            font-size: 0.9rem;
            opacity: 0.8;
            font-weight: 300;
        }
        
        .login-body {
            padding: 40px;
        }
        
        .form-control {
            border-radius: 12px;
            padding: 12px 16px;
            border: 1px solid #dcdde1;
            background: #f8f9fa;
            transition: all 0.3s;
        }

        .form-control:focus {
            box-shadow: 0 0 0 4px rgba(0, 168, 255, 0.15);
            border-color: var(--primary-color);
            background: white;
        }
        
        .btn-login {
            background: linear-gradient(to right, var(--primary-color), var(--secondary-color));
            border: none;
            border-radius: 12px;
            padding: 14px;
            font-weight: 600;
            color: white;
            text-transform: uppercase;
            letter-spacing: 1px;
            transition: all 0.4s;
            margin-top: 10px;
        }
        
        .btn-login:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 20px rgba(0, 168, 255, 0.4);
            filter: brightness(1.1);
        }

        .input-group-text {
            background: transparent;
            border: none;
            color: #7f8c8d;
        }

        .login-footer {
            text-align: center;
            margin-top: 25px;
            font-size: 0.85rem;
            color: #7f8c8d;
        }

        .alert {
            border-radius: 12px;
            font-size: 0.9rem;
            border: none;
            box-shadow: 0 4px 12px rgba(231, 76, 60, 0.1);
        }
    </style>
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap" rel="stylesheet">
</head>
<body>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-12 d-flex justify-content-center">
                <div class="login-card">
                    <div class="login-header">
                        <div class="mb-3">
                            <i class="fas fa-water fa-3x text-info"></i>
                        </div>
                        <h3>Aplikasi Renang</h3>
                        <p class="mb-0">Portal Administrasi & Keuangan</p>
                    </div>
                    <div class="login-body">
                        <?php if (session()->getFlashdata('error')) : ?>
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                <i class="fas fa-exclamation-circle me-2"></i>
                                <?= session()->getFlashdata('error') ?>
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                        <?php endif; ?>
                        
                        <form action="<?= base_url('auth/login') ?>" method="post">
                            <?= csrf_field() ?>
                            <div class="mb-3">
                                <label class="form-label small fw-bold text-muted text-uppercase">Email Address</label>
                                <div class="input-group">
                                    <input type="email" class="form-control" name="email" placeholder="nama@email.com" required autofocus>
                                </div>
                            </div>
                            <div class="mb-4">
                                <label class="form-label small fw-bold text-muted text-uppercase">Password</label>
                                <div class="input-group">
                                    <input type="password" class="form-control" name="password" placeholder="••••••••" required>
                                </div>
                            </div>
                            <div class="d-grid">
                                <button type="submit" class="btn btn-login shadow-sm">
                                    Masuk Sekarang <i class="fas fa-arrow-right ms-2"></i>
                                </button>
                            </div>
                        </form>
                        
                        <div class="login-footer">
                            &copy; <?= date('Y') ?> Aplikasi Renang Management System
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>