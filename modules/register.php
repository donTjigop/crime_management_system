<?php
include '../config_db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    $stmt = $conn->prepare("INSERT INTO users (name, email, password) VALUES (?, ?, ?)");
    $stmt->execute([$name, $email, $password]);

    header('Location: login.php');
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register</title>
    <link rel="stylesheet" href="assets/css/bootstrap.min.css">
    <style>
        /* Full-Screen Background Image */
        body, html {
            height: 100%;
            margin: 0;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            overflow: hidden;
            background: url('path/to/your/crime-scene-image.jpg') center center/cover no-repeat;
            color: white; /* text color for contrast */
        }

        /* Glowing effect for Inputs */
        .form-control {
            background: rgba(255, 255, 255, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.3);
            color: white;
            border-radius: 25px;
            padding: 15px;
            font-size: 1rem;
            transition: all 0.3s ease-in-out;
        }

        .form-control:focus {
            border: 2px solid #ff6f61;
            box-shadow: 0 0 10px rgba(255, 111, 97, 0.8);
            background: rgba(255, 255, 255, 0.3);
        }

        /* Registration Container */
        .register-container {
            width: 100%;
            max-width: 500px;
            background-color: rgba(0, 0, 0, 0.8);
            padding: 40px;
            border-radius: 30px;
            box-shadow: 0 4px 30px rgba(0, 0, 0, 0.6);
            color: #fff;
            text-align: center;
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
        }

        .register-container h2 {
            font-size: 3rem;
            margin-bottom: 30px;
            text-transform: uppercase;
            letter-spacing: 3px;
            color: #ff6f61;
            animation: fadeInText 1.5s ease-out;
        }

        /* Button with 3D Hover effect */
        .btn-primary {
            background-color: #ff6f61;
            border-radius: 30px;
            padding: 12px 40px;
            border: none;
            color: white;
            font-size: 1.2rem;
            width: 100%;
            transition: all 0.3s ease;
        }

        .btn-primary:hover {
            transform: translateY(-5px);
            box-shadow: 0px 15px 30px rgba(255, 111, 97, 0.6);
        }

        .btn-primary:active {
            transform: translateY(2px);
            box-shadow: 0px 10px 20px rgba(255, 111, 97, 0.6);
        }

        /* Floating Labels */
        .floating-label {
            position: relative;
            margin-bottom: 20px;
        }

        .floating-label input {
            border: none;
            border-bottom: 2px solid #ddd;
            background-color: transparent;
            color: white;
            font-size: 1.2rem;
            padding: 10px 0;
            width: 100%;
            transition: 0.3s ease;
        }

        .floating-label input:focus {
            border-color: #ff6f61;
            outline: none;
        }

        .floating-label label {
            position: absolute;
            top: 50%;
            left: 0;
            color: white;
            font-size: 1.2rem;
            pointer-events: none;
            transform: translateY(-50%);
            transition: 0.3s ease;
        }

        .floating-label input:focus + label,
        .floating-label input:not(:placeholder-shown) + label {
            top: -10px;
            font-size: 1rem;
            color: #ff6f61;
        }

        /* Floating error messages */
        .alert {
            padding: 15px;
            background-color: #ff3d3d;
            border-radius: 8px;
            color: #fff;
            font-size: 1rem;
            margin-top: 15px;
            display: none;
        }

        /* Animated Particle Background */
        .particle-bg {
            position: absolute;
            top: 0;
            left: 0;
            height: 100%;
            width: 100%;
            background: url('particles.png') repeat;
            pointer-events: none;
            opacity: 0.5;
            z-index: -1;
            animation: moveParticles 5s infinite linear;
        }

        @keyframes moveParticles {
            0% { transform: translateX(0); }
            100% { transform: translateX(1000px); }
        }

        /* Animation for fading in text */
        @keyframes fadeInText {
            0% {
                opacity: 0;
                transform: translateY(-30px);
            }
            100% {
                opacity: 1;
                transform: translateY(0);
            }
        }
    </style>
</head>
<body>

<!-- Background Image -->
<div class="bg-image" style="background: url('path/to/your/crime-scene-image.jpg') center center/cover no-repeat; position: fixed; top: 0; left: 0; width: 100%; height: 100%; z-index: -2; opacity: 0.6;"></div>

<!-- Registration Form -->
<div class="register-container">
    <h2>Register an Account</h2>
    
    <form method="POST">
        <div class="floating-label">
            <input type="text" name="name" required placeholder=" ">
            <label for="name">name</label>
        </div>
        
        <div class="floating-label">
            <input type="email" name="email" required placeholder=" ">
            <label for="email">Email</label>
        </div>
        
        <div class="floating-label">
            <input type="password" name="password" required placeholder=" ">
            <label for="password">Password</label>
        </div>
        
        <button type="submit" class="btn btn-primary">Register</button>
    </form>

    <div class="footer mt-4">
        <p>Already have an account? <a href="login.php" style="color: #ff6f61;">Login here</a></p>
    </div>
</div>

<script src="assets/js/bootstrap.bundle.min.js"></script>
</body>
</html>
