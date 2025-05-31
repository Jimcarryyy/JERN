<?php
session_start();
include '../config/db_connection.php';

$saved_email = isset($_COOKIE['email']) ? $_COOKIE['email'] : '';
$saved_password = isset($_COOKIE['password']) ? $_COOKIE['password'] : '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);

    if (!empty($email) && !empty($password)) {
        // Prepare SQL to prevent SQL injection
        $stmt = $conn->prepare("SELECT id, name, password, role FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 1) {
            $user = $result->fetch_assoc();
            // Verify the password
            if (password_verify($password, $user['password'])) {
                // Set session variables
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['user_name'] = $user['name'];
                $_SESSION['user_role'] = $user['role'];

                $_SESSION['login_time'] = time(); 
                $_SESSION['session_expiry'] = 30 * 86400;

                if (isset($_POST['remember_me'])) {
                    setcookie('email', $email, time() + (86400 * 30), "/"); // 30 days
                    setcookie('password', $password, time() + (86400 * 30), "/"); // 30 days
                }                

                // Redirect based on role (optional)
                if ($user['role'] === 'admin') {
                    header("Location: ../admin_dashboard.php");
                } elseif ($user['role'] === 'teacher') {
                    header("Location: ../teacher_dashboard.php");
                } else {
                    header('Location: ../util/loading-page.html?redirect=../user/admin.php');
                }
                exit();
            } else {
                $error_message = "Incorrect Email or Password.";
            }
        } else {
            $error_message = "Incorrect Email or Password.";
        }

        $stmt->close();
    } else {
        $error_message = "Please fill in all fields.";
    }
}
?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>JERN - Login</title>

    <!-- Set the favicon -->
    <link rel="icon" href="../Images/jern-logo.png" type="image/png">

    <!-- Tabler Core CSS -->
    <link href="../public/dist/css/tabler.min.css" rel="stylesheet">

    <link rel="stylesheet" href="../css/global.css">
    <link rel="stylesheet" href="../css/login.css">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=DynaPuff:wght@400..700&family=Inter:ital,opsz,wght@0,14..32,100..900;1,14..32,100..900&display=swap" rel="stylesheet">
</head>

<body class="border-top-wide border-primary d-flex flex-column">
    <div class="page page-center">
        <div class="container-tight py-4">
            <div class="logo-container text-center mb-4">
                <h2 id="lms-text-logo">JERN</h2>
            </div>
            <form class="card card-md" action="" method="POST">
                <div class="card-body">
                    <h2 id="login-text" class="card-title text-center mb-4">Login to your account</h2>
                    <div class="mb-3">
                        <label class="form-label">Email address</label>
                        <input type="email" name="email" class="form-control" placeholder="Enter your email" 
                        value="<?php echo htmlspecialchars($saved_email); ?>" required>
                    </div>
                    <div class="mb-2">
                        <label class="form-label">
                            Password
                            <span class="form-label-description">
                                <a href="../auth/forgot-pass.php">I forgot my password</a>
                            </span>
                        </label>
                        <input type="password" name="password" class="form-control" placeholder="Enter your password" 
                        value="<?php echo htmlspecialchars($saved_password); ?>" required>
                    </div>
                    <div class="mb-2">
                        <label class="form-check">
                            <input type="checkbox" name="remember_me" class="form-check-input">
                            <span class="form-check-label">Remember me</span>
                        </label>
                    </div>
                    <div class="form-footer">
                        
                        <button type="submit" id="submitButton" class="btn btn-primary w-100">
                            <span id="loader" class="spinner-border spinner-border-sm" role="status" aria-hidden="true" style="display: none;"></span>
                            <span id="buttonText">Sign in</span>
                        </button>
                    </div>

                    <div class="alert-dismissible p-0 pt-3 <?php echo isset($error_message) ? '' : 'd-none'; ?>">
                        <div class="alert alert-danger alert-dismissible fade show mb-0" role="alert">
                            <strong><?php echo $error_message ?? ''; ?></strong>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    </div>
                </div>
            </form>
            <div class="text-center text-muted mt-3">
                Don't have an account? <a href="../auth/register.php">Sign up</a>
            </div>
        </div>
    </div>

    <?php include '../util/footer.php'; ?>

    <!-- Tabler Core JS -->
    <script src="public/dist/js/tabler.min.js"></script>
    <script src="../js/login.js"></script>
</body>

</html>