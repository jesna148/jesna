<?php 
session_start();

$servername = "localhost";
$username = "root";
$password = "";  
$dbname = "project";
$conn = new mysqli('localhost', 'root', '', 'project');

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if (isset($_GET['email'])) {
    $email = $_GET['email'];
    $stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    echo json_encode(['exists' => $result->num_rows > 0]);
    exit();
}


if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $fullname = trim($_POST['fullname']);
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);
    $role = $_POST['role'];

    
    $stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        echo "<script>alert('Email already registered! Please login.'); window.location='login.php';</script>";
        exit();
    }

    
    $hashed_password = $password;

    
    $stmt = $conn->prepare("INSERT INTO users (fullname, email, password, role, is_approved) VALUES (?, ?, ?, ?, 0)");
    $stmt->bind_param("ssss", $fullname, $email, $hashed_password, $role);

    if ($stmt->execute()) {
    
        echo "<script>
            alert('Signup successful! Please wait for admin approval before logging in.');
            window.location='login.php';
        </script>";
        exit();
    } else {
        echo "<script>alert('Error: Unable to register. Please try again later.');</script>";
    }
}

$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0"/>
<title>Campus Signup</title>
<style>
    body {
        margin: 0;
        padding: 0;
        font-family: Arial, sans-serif;
        background: url('https://images.unsplash.com/photo-1551836022-d5d88e9218df') no-repeat center center/cover;
        display: flex;
        justify-content: center;
        align-items: center;
        height: 100vh;
    }
    .signup-container {
        background: rgba(255, 255, 255, 0.95);
        padding: 30px;
        border-radius: 10px;
        width: 350px;
        box-shadow: 0 0 10px rgba(0,0,0,0.3);
        text-align: center;
    }
    .signup-container h2 {
        text-align: center;
        margin-bottom: 20px;
    }
    input, select, button {
        width: 100%;
        padding: 10px;
        margin: 10px 0;
        border-radius: 5px;
        border: 1px solid #ccc;
    }
    button {
        background-color: #4CAF50;
        color: white;
        border: none;
        cursor: pointer;
    }
    button:hover {
        background-color: #45a049;
    }
    .login-link {
        text-align: center;
    }
    
    .pending-msg {
        display: none;
        color: #ff9800;
        font-weight: bold;
        margin-top: 10px;
    }
</style>
</head>
<body>

<div class="signup-container">
    <h2>Sign Up</h2>
    <form action="" method="post" autocomplete="off">
        <label for="fullname">Full Name</label>
        <input type="text" id="fullname" name="fullname" placeholder="Full Name" required>

        <label for="email">Email</label>
        <input type="email" id="email" name="email" placeholder="Email" required>
        <span id="email-message" style="color: red; text-align: center; font-size: 14px; display: block; margin-top: 5px;"></span>

        <label for="password">Password</label>
        <input type="password" id="password" name="password" placeholder="Password" required>

        <select name="role" id="role" required>
            <option value="" disabled selected>Select Role</option> 
            <option value="student">Student</option>
            <option value="club_leader">Club Leader</option>
        </select>

        <button type="submit">Sign Up</button>
    </form>

    
    <p id="pendingMsg" class="pending-msg">Your account is pending admin approval!</p>

    <div class="login-link">
        Already have an account? <a href="login.php">Login</a>
    </div>
</div>

<script>
document.addEventListener("DOMContentLoaded", () => {
    const emailInput = document.querySelector('input[name="email"]');
    const emailMessage = document.getElementById('email-message');

    emailInput.addEventListener("input", () => {
        const email = emailInput.value.trim();
        emailMessage.textContent = '';

        if  (email !== '') {
            fetch('check_email.php?email=${encodeURIComponent(email)}')
                .then(response => response.json())
                .then(data => {
                    if (data.exists) {
                        emailMessage.textContent = "This email is already registered!";
                        emailMessage.style.color = "red";
                    } else {
                        emailMessage.textContent = "Email is available.";
                        emailMessage.style.color = "green";
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                });
        }
    });
});
</script>

</body>
</html>