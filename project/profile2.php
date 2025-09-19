<?php
// save_profile.php
$conn = new mysqli('localhost', 'root', '', 'project');

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Retrieve form data
if ($_SERVER["REQUEST_METHOD"] == "POST") {
$name = $_POST['name'];
$email = $_POST['email'];
$date_of_birth = $_POST['date_of_birth'];
$gender = $_POST['gender'];
$class = $_POST['class'];
}

// Set date_of_join to current date
$date_of_join = date('Y-m-d');

// Prepare and execute insert statement
$stmt = $conn->prepare("INSERT INTO club_members (name, email, date_of_birth, date_of_join, gender, class) VALUES (?, ?, ?, ?, ?, ?)");
$stmt->bind_param("ssssss", $name, $email, $date_of_birth, $date_of_join, $gender, $class);

if ($stmt->execute()) {
    echo "Profile saved successfully!";
    // Redirect to profile page or dashboard
    header("Location:  club_display.php");
} else {
    echo "Error: " . $stmt->error;
}

$stmt->close();
$conn->close();
?>