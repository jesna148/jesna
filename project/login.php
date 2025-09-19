<?php
session_start();

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "project";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Use prepared statements to prevent SQL injection
    $stmt = $conn->prepare("SELECT id, fullname, role, status, club_id FROM users WHERE email = ? AND password = ?");
    $stmt->bind_param("ss", $email, $password);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['fullname'] = $user['fullname'];
        $_SESSION['role'] = $user['role'];

        if ($user['status'] == 'rejected') {
            // Account is not approved
            session_destroy();
            echo "<script>alert('Your account is pending admin approval.'); window.location.href = ' login.php';</script>";
            exit();
        }

        // Account is approved, now check role and redirect
        if ($user['role'] === 'admin') {
            header("Location: admin_home.html");
            exit();
        } else if ($user['role'] === 'club_leader') {
            if ($user['club_id'] !== NULL) {
                // Leader is approved and has a club, redirect
                $_SESSION['club_id'] = $user['club_id'];
                header("Location: club_leader_dashboard.php"); // Assuming you have a leader dashboard page
                exit();
            } else {
                // Leader is approved but has no club assigned yet
                session_destroy();
                echo "<script>alert('Your account is approved, but you have not been assigned to a club yet. Please contact the admin.'); window.location.href = 'login.php';</script>";
                exit();
            }
        } else if ($user['role'] === 'student') {
            header("Location: student_dashboard.php"); // Assuming you have a student dashboard page
            exit();
        }
    } else {
        echo "<script>alert('Invalid email or password.'); window.location.href = ' login.php';</script>";
        exit();
    }
}
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Campus Login</title>
    <style>
        /* === RESET === */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Poppins', sans-serif;
        }

        body {
            background: linear-gradient(135deg, #6e8efb, #a777e3);
            height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        /* === LOGIN CONTAINER === */
        .login-container {
            background-color: white;
            padding: 30px 40px;
            border-radius: 15px;
            box-shadow: 0 10px 25px rgba(0,0,0,0.3);
            width: 350px;
            text-align: center;
            animation: fadeIn 0.8s ease-in-out;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(-20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        h2 {
            margin-bottom: 15px;
            color: #333;
        }

        .input-group {
            margin-bottom: 15px;
            text-align: left;
        }

        label {
            display: block;
            font-weight: bold;
            margin-bottom: 5px;
            color: #555;
        }

        input, select {
            width: 100%;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 8px;
            outline: none;
            transition: border-color 0.3s;
        }

        input:focus, select:focus {
            border-color: #6e8efb;
        }

        button {
            width: 100%;
            padding: 10px;
            background: #6e8efb;
            border: none;
            color: white;
            font-size: 16px;
            font-weight: bold;
            border-radius: 8px;
            cursor: pointer;
            transition: 0.3s;
        }

        button:hover {
            background: #5a75e6;
        }

        .error {
            color: red;
            font-size: 14px;
            margin-bottom: 10px;
        }
    </style>
</head>
<body>

<div class="login-container">
    <h2>Campus Login</h2>
    <?php if (!empty($error)): ?>
        <p class="error"><?php echo htmlspecialchars($error); ?></p>
    <?php endif; ?>
    <form method="POST" action="">
        <div class="input-group">
            <label>Email</label>
            <input type="email" name="email" required>
        </div>

        <div class="input-group">
            <label>Password</label>
            <input type="password" name="password" required>
        </div>

        <div class="input-group">
            <label>Role</label>
            <select name="role" required>
                <option value="student">Student</option>
                <option value="club_leader">Club Leader</option>
                <option value="admin">Admin</option>
            </select>
        </div>

        <button type="submit">Login</button>
    </form>
</div>
</body>
</html>