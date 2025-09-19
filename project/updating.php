<?php
 include 'conn.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Retrieve and trim inputs
    $first_name = trim($_POST['fname']);
    $last_name = trim($_POST['lname']);
    $contact = trim($_POST['contact']);
    $dob = trim($_POST['dob']);
    $student_id = trim($_POST['studentID']);
    $department = trim($_POST['department']);
    $club_type = trim($_POST['clubType']);
    $gender=trim($_POST['gender']);

    // Prepare statement
    $stmt = $conn->prepare("INSERT INTO membership (first_name, last_name,contact, dob, student_id, department, club_type,gender) VALUES (?, ?, ?, ?, ?, ?, ?,?)");
    if ($stmt === false) {
        die('Prepare failed: ' . htmlspecialchars($conn->error));
    }

    // Bind parameters
    $stmt->bind_param("ssssssss", $first_name, $last_name,$contact, $dob, $student_id, $department, $club_type,$gender);

    // Execute
    if ($stmt->execute()) {
        echo "<p>Registration successful!</p>";
    } else {
        echo "<p>Error: " . htmlspecialchars($stmt->error) . "</p>";
    }

    $stmt->close();
}

$conn->close();
?>
 