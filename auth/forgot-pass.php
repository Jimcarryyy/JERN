<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forgot Password - Tabler</title>

    <!-- Set the favicon -->
    <link rel="icon" href="../Images/learning.png" type="image/png">
    <link rel="stylesheet" href="../css/global.css">
    <link rel="stylesheet" href="../css/forgot-pass.css">

    <!-- Tabler Core CSS -->
    <link href="../public/dist/css/tabler.min.css" rel="stylesheet">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@100;300;400;600&display=swap" rel="stylesheet">
</head>

<body class="border-top-wide border-primary d-flex flex-column">
    <div class="page page-center">
        <div class="container-tight py-4">
            <div class="logo-container text-center mb-4">
                <a href="#" class="navbar-brand">
                    <img src="../Images/learning.png" height="36" alt="Tabler logo" id="lms-logo">
                </a>
                <h2 id="lms-text-logo">LMS</h2>
            </div>
            <form class="card card-md" action="#" method="post">
                <div class="card-body">
                    <h2 id="forgot-text" class="card-title text-center mb-4 fw-bold">Forgot your password?</h2>
                    <p class="text-muted text-center mb-4">Enter your email address below and we'll send you instructions on how to reset your password.</p>

                    <div class="mb-3">
                        <label class="form-label">Email address</label>
                        <input type="email" class="form-control" placeholder="Enter your email" required>
                    </div>

                    <div class="form-footer">
                        <button type="submit" class="btn btn-primary w-100">Send Reset Link</button>
                    </div>
                </div>
            </form>
            <div class="text-center text-muted mt-3">
                Remembered your password? <a href="../auth/login.php">Log in</a>
            </div>
        </div>
    </div>

    <?php include '../util/footer.php'; ?>

    <!-- Tabler Core JS -->
    <script src="../public/dist/js/tabler.min.js"></script>
</body>

</html>
