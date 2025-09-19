<?php
session_start();

// Database credentials
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "project";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check if the form was submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['image'])) {

    // Get the club ID from the session
    $user_id = $_SESSION['user_id'];
    $club_id = null;
    $stmt = $conn->prepare("SELECT club_id FROM users WHERE id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($row = $result->fetch_assoc()) {
        $club_id = $row['club_id'];
    }
    $stmt->close();

    if (!$club_id) {
        die("You are not assigned to a club. Please contact the admin.");
    }

    // Sanitize and retrieve form data
    $title = $conn->real_escape_string($_POST['title']);
    $description = $conn->real_escape_string($_POST['description']);

    // Image upload handling
    $target_dir = "uploads/club_activities/";
    
    // Create the directory if it doesn't exist
    if (!is_dir($target_dir)) {
        mkdir($target_dir, 0777, true);
    }

    $image_name = basename($_FILES["image"]["name"]);
    $target_file = $target_dir . uniqid() . '-' . $image_name;
    $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

    $uploadOk = 1;

    // Check if image file is a real image
    $check = getimagesize($_FILES["image"]["tmp_name"]);
    if ($check === false) {
        echo "<script>alert('File is not an image.'); window.location.href='club-activity-form.php';</script>";
        $uploadOk = 0;
    }

    // Check file size (e.g., 5MB limit)
    if ($_FILES["image"]["size"] > 5000000) {
        echo "<script>alert('Sorry, your file is too large. Max 5MB.'); window.location.href='club-activity-form.php';</script>";
        $uploadOk = 0;
    }

    // Allow only specific file formats
    if($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg" && $imageFileType != "gif" ) {
        echo "<script>alert('Sorry, only JPG, JPEG, PNG & GIF files are allowed.'); window.location.href='club-activity-form.php';</script>";
        $uploadOk = 0;
    }

    // Check if $uploadOk is set to 0 by an error
    if ($uploadOk == 0) {
        die(); // Stop script execution
    } else {
        // Attempt to upload file
        if (move_uploaded_file($_FILES["image"]["tmp_name"], $target_file)) {
            // File uploaded successfully, now insert data into database
            $stmt = $conn->prepare("INSERT INTO club_activities (club_id, image_path, caption) VALUES (?, ?, ?)");
            $stmt->bind_param("iss", $club_id, $target_file, $description);

            if ($stmt->execute()) {
                echo "<script>alert('Event submitted successfully!'); window.location.href='club_dashboard.php';</script>";
            } else {
                echo "<script>alert('Error: " . $stmt->error . "'); window.location.href='club-activity-form.php';</script>";
            }
            $stmt->close();
        } else {
            echo "<script>alert('Sorry, there was an error uploading your file.'); window.location.href='club-activity-form.php';</script>";
        }
    }
}
$conn->close();
?>