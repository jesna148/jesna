<?php
 $servername = "localhost";
 $username = "root";
 $password = "";  
 $dbname = "project";
$conn = new mysqli('localhost','root','','project');

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if (isset($_GET['email'])) {
    $email = $_GET['email'];

    // Use prepared statement for security
    $stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    // Return JSON response
    echo json_encode(['exists' => ($result->num_rows > 0)]);
    exit();
}
?>