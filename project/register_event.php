<?php
session_start();
$conn = new mysqli("localhost", "root", "", "project");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if (!isset($_SESSION['email'])) {
    echo "<script>alert('Please login first!'); window.location.href='login.php';</script>";
    exit();
}

$student_email = $_SESSION['email'];

if (!isset($_GET['id']) || empty($_GET['id'])) {
    echo "<script>alert('Invalid event ID!'); window.location.href='events.php';</script>";
    exit();
}

$event_id = intval($_GET['id']);

$check = $conn->query("SELECT * FROM event_registrations WHERE student_email='$student_email' AND event_id='$event_id'");

if ($check->num_rows > 0) {
    echo "<script>alert('You are already registered for this event!'); window.location.href='events.php';</script>";
} else {
 
    $conn->query("INSERT INTO event_registrations(event_id, student_email) VALUES('$event_id', '$student_email')");
    echo "<script>alert('Registration successful!'); window.location.href='events.php';</script>";
}
?>
