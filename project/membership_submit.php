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
    
    $fname = $_POST['fname'];
    $lname = $_POST['lname'];
    $gender = $_POST['gender'];
    $contact = $_POST['contact'];
    $dob = $_POST['dob'];
    $studentID = $_POST['studentID'];
    $department = $_POST['department'];
    $clubId = $_POST['clubType']; // This is now the club's ID, not the name

    // Insert all data into the memberships table, using club_id
    $stmt = $conn->prepare("INSERT INTO membership (first_name, last_name, gender, contact, dob, student_id, department, club_id) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("sssssssi", $fname, $lname, $gender, $contact, $dob, $studentID, $department, $clubId);
    
    if ($stmt->execute()) {
        echo "<script>alert('Registration successful! You have joined the club.'); window.location.href='index.html';</script>";
    } else {
        echo "<script>alert('Error: " . $stmt->error . "'); window.location.href='membership.php';</script>";
    }
    
    $stmt->close();
}

$conn->close();
?>