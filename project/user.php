<?php
include("conn.php");

// Array of students
 $students = [
   ["student2@example.com", "pass123"],
   ["student3@example.com", "pass456"]
];

// Check connection again just in case
if (!$conn) {
    die("Connection failed!");
}

foreach ($students as $student) {
    $email = $student[0];
    $plain_password = $student[1];
    $hashed_password = password_hash($plain_password, PASSWORD_DEFAULT);
    $role = "student";

    // Prepare insert query
    $stmt = $conn->prepare("INSERT INTO users (email, password, role) VALUES (?, ?, ?)");
    
    if ($stmt) {
        $stmt->bind_param("sss", $email, $hashed_password, $role);
        $stmt->execute();
        $stmt->close();
    } else {
        echo "Failed to prepare statement for $email<br>";
    }
}

$conn->close();
echo "Students inserted successfully!";
?>
