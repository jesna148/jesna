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

// Handle AJAX request for email checking
if (isset($_GET['check_email'])) {
    $email = filter_var(trim($_GET['check_email']), FILTER_SANITIZE_EMAIL);
    $stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    echo json_encode(['exists' => $result->num_rows > 0]);
    $stmt->close();
    $conn->close();
    exit();
}

$message = '';
// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $fullname = trim($_POST['fullname']);
    $email = filter_var(trim($_POST['email']), FILTER_SANITIZE_EMAIL);
    $password = $_POST['password'];
    $role = $_POST['role'];
    $club_id = isset($_POST['club_id']) && !empty($_POST['club_id']) ? intval($_POST['club_id']) : NULL;

    // Check if the email is already registered
    $stmt_check = $conn->prepare("SELECT id FROM users WHERE email = ?");
    $stmt_check->bind_param("s", $email);
    $stmt_check->execute();
    $result_check = $stmt_check->get_result();
    
    if ($result_check->num_rows > 0) {
        $message = "This email is already registered.";
    } else {
        $is_approved = ($role === 'admin') ? 1 : 0; 
        $status = 'pending';
        
        $stmt_user = $conn->prepare("INSERT INTO users (fullname, email, password, role, is_approved, club_id, status) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt_user->bind_param("sssssis", $fullname, $email, $password, $role, $is_approved, $club_id, $status);
        
        if ($stmt_user->execute()) {
            $_SESSION['message'] = "Signup successful! Your account is pending admin approval.";
            header("Location: login.php");
            exit();
        } else {
            $message = "Error: Unable to register. Please try again later.";
        }
        $stmt_user->close();
    }
    $stmt_check->close();
}

// Fetch a list of all unassigned clubs for the dropdown
$stmt_clubs = $conn->prepare("SELECT id, club_name FROM clubs_details WHERE leader_id IS NULL");
$stmt_clubs->execute();
$result_clubs = $stmt_clubs->get_result();
$clubs = $result_clubs->fetch_all(MYSQLI_ASSOC);
$stmt_clubs->close();

$conn->close();

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <title>Campus Signup</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #6e8efb, #a777e3);
            font-family: 'Inter', sans-serif;
        }
    </style>
</head>
<body class="bg-gray-100 flex items-center justify-center min-h-screen">

<div class="bg-white p-8 rounded-2xl shadow-xl w-full max-w-md">
    <h1 class="text-3xl font-bold text-gray-800 mb-6 text-center">Sign Up</h1>
    
    <?php if ($message): ?>
        <div class="bg-red-100 text-red-700 p-4 mb-6 rounded-lg border border-red-200 text-center">
            <?php echo htmlspecialchars($message); ?>
        </div>
    <?php endif; ?>

    <form action="" method="post" autocomplete="off">
        <div class="mb-4">
            <label for="fullname" class="block text-sm font-medium text-gray-700 mb-1">Full Name</label>
            <input type="text" id="fullname" name="fullname" required class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 p-2">
        </div>
        <div class="mb-4">
            <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email</label>
            <input type="email" id="email" name="email" required class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 p-2">
            <div id="email-message" class="text-sm mt-1"></div>
        </div>
        <div class="mb-4">
            <label for="password" class="block text-sm font-medium text-gray-700 mb-1">Password</label>
            <input type="password" id="password" name="password" required class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 p-2">
        </div>
        <div class="mb-4">
            <label for="role" class="block text-sm font-medium text-gray-700 mb-1">Select Role</label>
            <select name="role" id="role" required class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 p-2">
                <option value="" disabled selected>Select Role</option>
                <option value="student">Student</option>
                <option value="club_leader">Club Leader</option>
            </select>
        </div>
        
        <!-- Club Selection for Club Leaders -->
        <div class="mb-6 hidden" id="club-select-container">
            <label for="club_id" class="block text-sm font-medium text-gray-700 mb-1">Select Your Club</label>
            <select name="club_id" id="club_id" class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 p-2">
                <option value="" disabled selected>-- Select a Club --</option>
                <?php foreach ($clubs as $club): ?>
                    <option value="<?php echo htmlspecialchars($club['id']); ?>">
                        <?php echo htmlspecialchars($club['club_name']); ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <button type="submit" class="w-full py-2 px-4 border border-transparent rounded-lg shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-colors">
            Sign Up
        </button>
    </form>

    <div class="login-link mt-6 text-center text-sm text-gray-600">
        Already have an account? <a href=" login.php" class="font-medium text-indigo-600 hover:text-indigo-500">Login here</a>
    </div>
</div>

<script>
document.addEventListener("DOMContentLoaded", () => {
    const roleSelect = document.getElementById('role');
    const clubSelectContainer = document.getElementById('club-select-container');
    const clubSelect = document.getElementById('club_id');
    const emailInput = document.getElementById('email');
    const emailMessage = document.getElementById('email-message');

    // Show/hide the club dropdown based on role selection
    roleSelect.addEventListener("change", () => {
        if (roleSelect.value === 'club_leader') {
            clubSelectContainer.classList.remove('hidden');
            clubSelect.required = true;
        } else {
            clubSelectContainer.classList.add('hidden');
            clubSelect.required = false;
        }
    });

    // Handle real-time email checking
    let emailCheckTimeout;
    emailInput.addEventListener("input", () => {
        clearTimeout(emailCheckTimeout);
        emailMessage.textContent = '';

        if (emailInput.value.trim() === '') {
            return;
        }

        emailCheckTimeout = setTimeout(() => {
            fetch(`check_email.php?email=${encodeURIComponent(emailInput.value)}`)
                .then(response => response.json())
                .then(data => {
                    if (data.exists) {
                        emailMessage.textContent = "This email is already registered.";
                        emailMessage.classList.add('text-red-600');
                        emailMessage.classList.remove('text-green-600');
                    } else {
                        emailMessage.textContent = "Email is available!";
                        emailMessage.classList.add('text-green-600');
                        emailMessage.classList.remove('text-red-600');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    emailMessage.textContent = "Error checking email.";
                    emailMessage.classList.add('text-red-600');
                    emailMessage.classList.remove('text-green-600');
                });
        }, 500); // Wait for 500ms before sending the request
    });
});
</script>
</body>
</html>
