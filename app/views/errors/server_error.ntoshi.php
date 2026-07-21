<?php /** No view variables needed - uses only constants/Session */ ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Server Error - <?= APP_NAME ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { 
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
        }
        .error-container {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-radius: 20px;
            box-shadow: 0 20px 40px rgba(0,0,0,0.1);
        }
        .error-icon {
            font-size: 5rem;
            color: #dc3545;
        }
        .btn-home {
            background: linear-gradient(45deg, #667eea, #764ba2);
            border: none;
            border-radius: 50px;
            padding: 12px 30px;
            font-weight: 600;
            transition: all 0.3s ease;
        }
        .btn-home:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(0,0,0,0.2);
        }
        .btn-back {
            border-radius: 50px;
            padding: 12px 30px;
            font-weight: 600;
            transition: all 0.3s ease;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="row justify-content-center align-items-center min-vh-100">
            <div class="col-md-8">
                <div class="error-container p-5 text-center">
                    <div class="mb-4">
                        <i class="bi bi-exclamation-triangle-fill error-icon"></i>
                    </div>
                    <h1 class="display-4 fw-bold text-dark mb-3">Oops! Something went wrong</h1>
                    <h2 class="h4 text-muted mb-4">Server Error (500)</h2>
                    
                    <div class="alert alert-light border-start border-4 border-danger mb-4">
                        <p class="mb-2">
                            <strong>We're sorry!</strong> An unexpected error occurred while processing your request.
                        </p>
                        <p class="mb-0 text-muted">
                            Our technical team has been notified and is working to resolve this issue.
                        </p>
                    </div>
                    
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <div class="card border-0 bg-light mb-3">
                                <div class="card-body">
                                    <i class="bi bi-clock-history text-primary fs-4 mb-2 d-block"></i>
                                    <small class="text-muted">Error Time</small>
                                    <div class="fw-bold"><?= date('Y-m-d H:i:s') ?></div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card border-0 bg-light mb-3">
                                <div class="card-body">
                                    <i class="bi bi-ticket-perforated text-success fs-4 mb-2 d-block"></i>
                                    <small class="text-muted">Reference ID</small>
                                    <div class="fw-bold"><?= uniqid('ERR_') ?></div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="d-flex justify-content-center gap-3">
                        <a href="javascript:history.back()" class="btn btn-secondary btn-back">
                            <i class="bi bi-arrow-left me-2"></i>Go Back
                        </a>
                        <a href="<?= ROOT ?>" class="btn btn-primary btn-home">
                            <i class="bi bi-house me-2"></i>Go Home
                        </a>
                    </div>
                    
                    <div class="mt-4">
                        <small class="text-muted">
                            If this problem persists, please contact our support team at 
                            <a href="mailto:support@<?= APP_DOMAIN ?>">support@<?= APP_DOMAIN ?></a>
                        </small>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>