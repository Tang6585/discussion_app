<?php
// PHP structure to handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // 1. Database Connection (Will be added in the next step)
    // require_once 'db_connection.php'; 

    // 2. Collect and Sanitize Form Data
    $full_name = htmlspecialchars($_POST['full_name']);
    $email = htmlspecialchars($_POST['email']);
    $role = htmlspecialchars($_POST['role']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    // 3. Simple Validation (More rigorous validation and email verification will be needed later)
    if ($password !== $confirm_password) {
        $error = "Passwords do not match.";
    } elseif (strlen($password) < 8) {
        $error = "Password must be at least 8 characters long.";
    } elseif (empty($full_name) || empty($email) || empty($role)) {
        $error = "All fields are required.";
    } else {
        // 4. Secure Password Hashing (Mandatory Security Practice)
        // $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        
        // 5. Database Insertion Logic (To be completed with the database step)
        // $sql = "INSERT INTO users (full_name, email, role, password) VALUES (?, ?, ?, ?)";
        // ... execute query ...
        
        // Placeholder success message for now:
        $success = "Registration successful! You can now log in.";
        // header("Location: login.php"); // Redirect user to login page after success
        // exit;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register | GSU CS Discussion Forum</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    <link rel="stylesheet" href="style.css"> 

    <style>
        /* Styles to center the form and apply GSU theme colors */
        .register-body {
            /* 100vh minus navbar height (approx 56px) to center content */
            min-height: calc(100vh - 56px); 
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px 0;
            background-color: #f8f9fa; /* Light gray background */
        }
        .register-card {
            max-width: 500px;
            width: 100%;
            border: none;
            border-top: 5px solid #003366; /* GSU Blue Stripe */
        }
        .btn-primary-gsu {
            background-color: #003366; 
            border-color: #003366;
            transition: background-color 0.3s;
        }
        .btn-primary-gsu:hover {
            background-color: #002244; 
            border-color: #002244;
        }
    </style>
</head>
<body>
    
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark sticky-top">
        <div class="container">
            <a class="navbar-brand fw-bold" href="index2.html"><i class="fas fa-graduation-cap"></i> GSU CS Forum</a>
            <div class="d-flex">
                <a href="login.php" class="btn btn-outline-light me-2">Login</a>
            </div>
        </div>
    </nav>

    <div class="register-body">
        <div class="register-card card shadow-lg p-4">
            <h2 class="card-title text-center text-dark fw-bold mb-4">
                <i class="fas fa-user-plus me-2"></i> Register Your Account
            </h2>

            <?php if (isset($error)): ?>
                <div class="alert alert-danger" role="alert"><?php echo $error; ?></div>
            <?php endif; ?>
            <?php if (isset($success)): ?>
                <div class="alert alert-success" role="alert"><?php echo $success; ?></div>
            <?php endif; ?>

            <form method="POST" action="register.php">
                
                <div class="mb-3">
                    <label for="full_name" class="form-label">Full Name</label>
                    <input type="text" class="form-control" id="full_name" name="full_name" required>
                </div>

                <div class="mb-3">
                    <label for="email" class="form-label">GSU Email Address</label>
                    <input type="email" class="form-control" id="email" name="email" placeholder="e.g., username@gsu.edu.ng" required>
                </div>
                
                <div class="mb-3">
                    <label for="role" class="form-label">Select Your Role</label>
                    <select class="form-select" id="role" name="role" required>
                        <option value="" disabled selected>Choose your primary role...</option>
                        <option value="Student">Student</option>
                        <option value="Lecturer">Lecturer</option>
                        <option value="Mentor">Mentor</option>
                    </select>
                </div>

                <div class="mb-3">
                    <label for="password" class="form-label">Password</label>
                    <input type="password" class="form-control" id="password" name="password" required minlength="8">
                </div>

                <div class="mb-4">
                    <label for="confirm_password" class="form-label">Confirm Password</label>
                    <input type="password" class="form-control" id="confirm_password" name="confirm_password" required minlength="8">
                </div>

                <button type="submit" class="btn btn-primary-gsu w-100 py-2 fw-bold">Register</button>
            </form>

            <p class="text-center mt-3 mb-0 small text-muted">
                Already have an account? <a href="login.php" class="text-decoration-none">Log in here.</a>
            </p>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>