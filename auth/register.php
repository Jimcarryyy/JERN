<?php 
session_start();
include '../config/db_connection.php';

$error_message = '';
$success_message = '';
$name_class = $email_class = $password_class = $confirm_password_class = '';
$name_error = $email_error = $password_error = $confirm_password_error = '';
$name = $email = ''; // Initialize the variables

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);
    $confirm_password = trim($_POST['confirm_password']);
    
    if (!empty($name) && !empty($email) && !empty($password) && !empty($confirm_password)) {
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $email_error = "Invalid email format.";
            $email_class = "is-invalid";
        } elseif ($password !== $confirm_password) {
            $confirm_password_error = "Passwords do not match.";
            $confirm_password_class = "is-invalid";
        } else {
            // Determine the role based on the email domain
            $role = '';
            if (str_ends_with($email, '@gmail.com')) {
                $role = 'student';
            } elseif (str_ends_with($email, '@ctu.edu.ph')) {
                $role = 'teacher';
            } elseif (str_ends_with($email, '@admin.ctu.edu.ph')) {
                $role = 'admin';
            } else {
                $error_message = "Email domain not allowed.";
            }

            if (empty($error_message)) {
                // Check if email already exists
                $stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
                $stmt->bind_param("s", $email);
                $stmt->execute();
                $stmt->store_result();

                if ($stmt->num_rows > 0) {
                    $error_message = "Email already exists.";
                } else {
                    // Hash the password
                    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

                    // Insert the new user with the determined role
                    $stmt = $conn->prepare("INSERT INTO users (name, email, password, role) VALUES (?, ?, ?, ?)");
                    $stmt->bind_param("ssss", $name, $email, $hashed_password, $role);

                    if ($stmt->execute()) {
                        $success_message = "Registration successful! Please <a href='../auth/login.php'>login</a>.";
                    } else {
                        $error_message = "Error occurred. Please try again.";
                    }
                }
                $stmt->close();
            }
        }
    } else {
        $error_message = "All fields are required.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>JERN - Sign up</title>
    <link rel="icon" href="../Images/jern-logo.png" type="image/png">
    <link href="../public/dist/css/tabler.min.css" rel="stylesheet">
    <link href="../public/dist/css/tabler-flags.min.css" rel="stylesheet">
    <link href="../public/dist/css/tabler-payments.min.css" rel="stylesheet">
    <link href="../public/dist/css/tabler-vendors.min.css" rel="stylesheet">
    <link href="../public/dist/css/demo.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../css/global.css">
    <link rel="stylesheet" href="../css/register.css">
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

            <?php if (!empty($error_message)) : ?>
                <div class="alert alert-danger alert-dismissible">
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    <?= $error_message; ?>
                </div>
            <?php endif; ?>
            <?php if (!empty($success_message)) : ?>
                <div class="alert alert-success alert-dismissible">
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    <?= $success_message; ?>
                </div>
            <?php endif; ?>
            <form class="card card-md" action="register.php" method="post">
                <div class="card-body">
                    <h2 id="register-text" class="card-title text-center mb-4">Create a new account</h2>
                    <div class="mb-3">
                        <label class="form-label">Full Name</label>
                        <input type="text" name="name" id="name" class="form-control <?= $name_class; ?>" placeholder="Enter your full name" value="<?= htmlspecialchars($name); ?>" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Email address</label>
                        <input type="email" name="email" id="email" class="form-control <?= $email_class; ?>" placeholder="Enter your email" value="<?= htmlspecialchars($email); ?>" required>
                        <?php if (!empty($email_error)): ?>
                            <div class="invalid-feedback"><?= $email_error; ?></div>
                        <?php endif; ?>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Password</label>
                        <input type="password" name="password" id="password" class="form-control <?= $password_class; ?>" placeholder="Enter your password" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Confirm Password</label>
                        <input type="password" name="confirm_password" id="confirm_password" class="form-control <?= $confirm_password_class; ?>" placeholder="Confirm your password" required>
                        <?php if (!empty($confirm_password_error)): ?>
                            <div class="invalid-feedback"><?= $confirm_password_error; ?></div>
                        <?php endif; ?>
                    </div>
                    <div class="form-footer">
                        <button type="submit" class="btn btn-primary w-100" id="submitButton">
                            <span id="loader" class="spinner-border spinner-border-sm" role="status" aria-hidden="true" style="display: none;"></span>
                            <span id="buttonText">Sign up</span>
                        </button>
                    </div>
                </div>
            </form>
            <div class="text-center text-muted mt-3">
                Already have an account? <a href="../auth/login.php">Sign in</a>
            </div>
        </div>
    </div>

    <?php include '../util/footer.php'; ?>

    <script src="../public/dist/js/tabler.min.js"></script>
    <script src="../js/register.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const nameField = document.getElementById('name');
            const emailField = document.getElementById('email');
            const passwordField = document.getElementById('password');
            const confirmPasswordField = document.getElementById('confirm_password');

            nameField.addEventListener('input', function() {
                if (nameField.value.length < 8) {
                    nameField.classList.add('is-invalid');
                    nameField.classList.remove('is-valid');
                } else {
                    nameField.classList.add('is-valid');
                    nameField.classList.remove('is-invalid');
                }
            });

            emailField.addEventListener('input', function() {
                const emailPattern = /^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/;
                if (!emailPattern.test(emailField.value)) {
                    emailField.classList.add('is-invalid');
                    emailField.classList.remove('is-valid');
                } else {
                    emailField.classList.add('is-valid');
                    emailField.classList.remove('is-invalid');
                }
            });

            passwordField.addEventListener('input', function() {
                if (passwordField.value.length < 6) {
                    passwordField.classList.add('is-invalid');
                    passwordField.classList.remove('is-valid');
                } else {
                    passwordField.classList.add('is-valid');
                    passwordField.classList.remove('is-invalid');
                }
            });

            confirmPasswordField.addEventListener('input', function() {
                if (confirmPasswordField.value !== passwordField.value) {
                    confirmPasswordField.classList.add('is-invalid');
                    confirmPasswordField.classList.remove('is-valid');
                } else {
                    confirmPasswordField.classList.add('is-valid');
                    confirmPasswordField.classList.remove('is-invalid');
                }
            });
        });
    </script>
</body>
</html>
