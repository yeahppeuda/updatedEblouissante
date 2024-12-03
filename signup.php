<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign Up</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f0f8ff;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }
        .signup-container {
            background: #fff;
            padding: 25px 30px;
            border-radius: 8px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            width: 350px;
        }
        .signup-container h2 {
            text-align: center;
            margin-bottom: 15px;
        }
        .signup-container input {
            width: 100%;
            padding: 10px;
            margin: 8px 0;
            border: 1px solid #ddd;
            border-radius: 4px;
        }
        .signup-container button {
            width: 100%;
            padding: 10px;
            background: #28a745;
            color: #fff;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }
        .signup-container button:hover {
            background: #218838;
        }
        .error-message {
            text-align: center;
            color: red;
            font-size: 0.9em;
            margin: 10px 0;
        }
    </style>
</head>
<body>
    <div class="signup-container">
        <h2>Sign Up</h2>
        <?php
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            include 'db.php';

            $username = $_POST['username'];
            $email = $_POST['email'];
            $password = $_POST['password'];
            $confirm_password = $_POST['confirm_password'];

            // Validate passwords match
            if ($password !== $confirm_password) {
                echo "<p class='error-message'>Passwords do not match!</p>";
            } else {
                // Hash the password for security
                $hashed_password = password_hash($password, PASSWORD_BCRYPT);

                // Insert user into the database
                $stmt = $conn->prepare("INSERT INTO users (username, email, password) VALUES (?, ?, ?)");
                $stmt->bind_param("sss", $username, $email, $hashed_password);

                if ($stmt->execute()) {
                    // Redirect to login page
                    header("Location: login.php");
                    exit(); // Stop further script execution
                } else {
                    echo "<p class='error-message'>Error: " . $stmt->error . "</p>";
                }

                $stmt->close();
            }
        }
        ?>
        <form method="POST">
            <input type="text" name="username" placeholder="Username" required>
            <input type="email" name="email" placeholder="Email Address" required>
            <input type="password" name="password" placeholder="Password" required>
            <input type="password" name="confirm_password" placeholder="Confirm Password" required>
            <button type="submit">Sign Up</button>
        </form>
        <p style="text-align: center; margin-top: 10px;">Already have an account? <a href="login.php">Log in</a></p>
    </div>
</body>
</html>
