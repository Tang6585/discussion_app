<?php
// PHP logic to handle form submission will go here.
$registration_message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // 1. Collect and sanitize input data
    $full_name = htmlspecialchars($_POST['full_name'] ?? '');
    $email = htmlspecialchars($_POST['email'] ?? '');
    $role = htmlspecialchars($_POST['role'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';

    // Simple validation (More robust validation will be added later with database setup)
    if (empty($full_name) || empty($email) || empty($role) || empty($password) || empty($confirm_password)) {
        $registration_message = '<div class="alert alert-danger" role="alert">Please fill in all required fields.</div>';
    } elseif ($password !== $confirm_password) {
        $registration_message = '<div class="alert alert-danger" role="alert">Passwords do not match.</div>';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $registration_message = '<div class="alert alert-danger" role="alert">Invalid email format.</div>';
    } else {
        // --- Placeholder for Database Logic ---
        // In a real application, you would:
        // 1. Connect to the MySQL database.
        // 2. Hash the password: $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        // 3. Check if the email already exists in the 'users' table.
        // 4. Insert the new user data (full_name, email, role, hashed_password) into the database.
        
        $registration_message = '<div class="alert alert-success" role="alert">
                                    Registration simulated successfully! <br> 
                                    Name: ' . $full_name . ', Role: ' . $role . ', Email: ' . $email . '. 
                                    We will connect to the database in the next step.
                                 </div>';
        
        // Clear variables after simulation (or successful insertion)
        // $full_name = $email = $role = '';
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register | GSU CS Forum</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome for Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    
    <style>
        :root {
            --gsu-blue: #003366;
            --gsu-yellow: #ffc107;
        }
        body {
            background-color: #f8f9fa;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        .register-container {
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 40px 15px;
        }
        .registration-card {
            max-width: 550px;
            width: 100%;
            border: none;
            border-radius: 12px;
            box-shadow: 0 10px 30px rgba(0, 51, 102, 0.1);
        }
        .form-header {
            background-color: var(--gsu-blue);
            color: #ffffff;
            border-top-left-radius: 12px;
            border-top-right-radius: 12px;
            padding: 25px;
            text-align: center;
        }
        .form-header h2 {
            margin-bottom: 0;
            font-weight: 700;
        }
        .btn-primary {
            background-color: var(--gsu-blue);
            border-color: var(--gsu-blue);
            transition: background-color 0.3s;
            font-weight: 600;
        }
        .btn-primary:hover {
            background-color: #002244;
            border-color: #002244;
        }
        .form-control:focus {
            border-color: var(--gsu-blue);
            box-shadow: 0 0 0 0.25rem rgba(0, 51, 102, 0.25);
        }
        .form-select, .form-control {
            border-radius: 8px;
            padding: 10px 15px;
        }
    </style>
</head>
<body>

    <div class="register-container">
        <div class="registration-card card">
            <div class="form-header">
                <h2><i class="fas fa-user-plus me-2"></i>Create Your Forum Account</h2>
                <p class="mb-0 mt-2 small">Join the GSU Computer Science academic community.</p>
            </div>
            <div class="card-body p-4 p-md-5">
                
                <?php echo $registration_message; // Display registration messages ?>

                <form method="POST" action="register.php">
                    <div class="mb-3">
                        <label for="full_name" class="form-label fw-bold">Full Name</label>
                        <input type="text" class="form-control" id="full_name" name="full_name" required 
                               value="<?php echo htmlspecialchars($full_name ?? ''); ?>" placeholder="e.g., John E. Doe">
                    </div>

                    <div class="mb-3">
                        <label for="email" class="form-label fw-bold">GSU Email Address</label>
                        <input type="email" class="form-control" id="email" name="email" required 
                               value="<?php echo htmlspecialchars($email ?? ''); ?>" placeholder="e.g., doe.j@gsu.edu.ng">
                        <div class="form-text">Use your official GSU email for verification.</div>
                    </div>

                    <div class="mb-3">
                        <label for="role" class="form-label fw-bold">Select Your Role</label>
                        <select class="form-select" id="role" name="role" required>
                            <option value="" disabled selected>--- Choose Your Account Type ---</option>
                            <option value="student" <?php if (($role ?? '') === 'student') echo 'selected'; ?>>Student</option>
                            <option value="lecturer" <?php if (($role ?? '') === 'lecturer') echo 'selected'; ?>>Lecturer</option>
                            <option value="mentor" <?php if (($role ?? '') === 'mentor') echo 'selected'; ?>>Mentor/Alumnus</option>
                        </select>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="password" class="form-label fw-bold">Password</label>
                            <input type="password" class="form-control" id="password" name="password" required placeholder="Minimum 8 characters">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="confirm_password" class="form-label fw-bold">Confirm Password</label>
                            <input type="password" class="form-control" id="confirm_password" name="confirm_password" required placeholder="Re-enter password">
                        </div>
                    </div>

                    <button type="submit" class="btn btn-primary w-100 mt-3 py-2">
                        <i class="fas fa-check-circle me-1"></i> Register Account
                    </button>
                </form>

                <div class="text-center mt-4">
                    <p class="text-muted mb-0">Already have an account? <a href="login.php" class="text-decoration-none fw-bold" style="color: var(--gsu-blue);">Login Here</a></p>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
