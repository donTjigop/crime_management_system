<?php
include 'config_db.php'; // Include database connection

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Retrieve form input
    $name = $_POST['name'];
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT); // Hash the password

    // Insert user data into the database
    try {
        $stmt = $conn->prepare("INSERT INTO users (name, email, password) VALUES (?, ?, ?)");
        $stmt->execute([$name, $email, $password]);

        // Redirect to the login page after successful registration
        header('Location: login.php');
        exit();
    } catch (PDOException $e) {
        // Handle errors, such as duplicate email or database issues
        $error = "Error registering user: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register</title>
    <link rel="stylesheet" href="../assets/css/bootstrap.min.css">
    <style>
        body {
            margin: 0;
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background: url('assets/crimesss.jpg') no-repeat center center fixed; /* Replace with your image path */
            background-size: cover; /* Ensures the image covers the whole viewport */
            font-family: 'Arial', sans-serif;
            color: #fff; /* Make the text color white to stand out */
        }
        .register-container {
            background: rgba(255, 255, 255, 0.8); /* Semi-transparent background for better contrast */
            border-radius: 20px;
            box-shadow: 0px 10px 20px rgba(0, 0, 0, 0.2);
            padding: 40px;
            width: 100%;
            max-width: 450px;
            text-align: center;
        }
        .register-container h2 {
            margin-bottom: 30px;
            font-size: 26px;
            color: #333;
        }
        .form-label {
            font-weight: bold;
            display: block;
            text-align: left;
            margin-bottom: 10px;
            color: #555;
        }
        .form-control {
            padding: 10px 15px;
            border: 1px solid #ddd;
            border-radius: 30px;
            margin-bottom: 20px;
            width: 100%;
            font-size: 14px;
            outline: none;
            transition: border 0.3s ease;
        }
        .form-control:focus {
            border: 1px solid #6e8efb;
        }
        .btn-primary {
            width: 100%;
            padding: 12px;
            font-size: 16px;
            border: none;
            border-radius: 30px;
            background: linear-gradient(90deg, #6e8efb, #a777e3);
            color: #fff;
            transition: background 0.3s ease;
        }
        .btn-primary:hover {
            background: linear-gradient(90deg, #a777e3, #6e8efb);
        }
        .text-center a {
            color: #6e8efb;
            text-decoration: none;
            font-size: 14px;
        }
        .text-center a:hover {
            text-decoration: underline;
        }
        .alert {
            font-size: 14px;
            color: #d9534f;
            background: #f8d7da;
            padding: 10px;
            border-radius: 10px;
            margin-bottom: 20px;
            border: 1px solid #f5c6cb;
        }
    </style>
</head>
<body>
    <div class="register-container">
        <h2>Create Account</h2>
        <?php if (isset($error)): ?>
            <div class="alert"><?= htmlspecialchars($error); ?></div>
        <?php endif; ?>
        <form method="POST">
            <div>
                <label for="name" class="form-label">Name</label>
                <input type="text" name="name" id="name" class="form-control" placeholder="Enter your full name" required>
            </div>
            <div>
                <label for="email" class="form-label">Email</label>
                <input type="email" name="email" id="email" class="form-control" placeholder="Enter your email" required>
            </div>
            <div>
                <label for="password" class="form-label">Password</label>
                <input type="password" name="password" id="password" class="form-control" placeholder="Create a password" required>
            </div>
            <button type="submit" class="btn-primary">Register</button>
            <div class="text-center mt-3">
                <small>Already have an account? <a href="login.php">Login here</a></small>
            </div>
        </form>
    </div>
</body>
</html>
