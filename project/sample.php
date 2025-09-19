<?php
session_start();
include("conn.php");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];
    $password = $_POST['password'];
    $role = $_POST['role'];

    // Check user exists with matching role
    $stmt = $conn->prepare("SELECT * FROM users WHERE email = ? AND role = ?");
    $stmt->bind_param("ss", $email, $role);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $user = $result->fetch_assoc();

        if (password_verify($password, $user['password'])) {
            $_SESSION['email'] = $user['email'];
            $_SESSION['role'] = $user['role'];

            // Redirect based on role
            switch ($user['role']) {
                case 'student':
                    header("Location: student_home.html");
                    break;
                case 'club_leader':
                    header("Location: leader_home.html");
                    break;
                case 'admin':
                    header("Location: admin_home.html");
                    break;
                default:
                    echo "Invalid role!";
            }
            exit;
        } else {
            echo "<script>alert('Incorrect password'); window.history.back();</script>";
        }
    } else {
        echo "<script>alert('User not found'); window.history.back();</script>";
    }

    $stmt->close();
    $conn->close();
}
?>
