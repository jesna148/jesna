<?php
session_start();

// Check for admin role
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header('Location: login.php');
    exit();
}

$servername = "localhost";
$username = "root";
$password = ""; 
$dbname = "project";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $club_name = trim($_POST['club_name']);
    $club_description = trim($_POST['club_description']);
    $image_path = NULL;

    // Check if a club with the same name already exists
    $stmt = $conn->prepare("SELECT id FROM clubs_details WHERE club_name = ?");
    $stmt->bind_param("s", $club_name);
    $stmt->execute();
    $result = $stmt->get_result();
    $stmt->close();
    
    if ($result->num_rows > 0) {
        $message = "Error: A club with this name already exists.";
    } else {
        // Handle file upload
        if (isset($_FILES['club_image']) && $_FILES['club_image']['error'] == 0) {
            $target_dir = "uploads/club_images/"; 
            if (!is_dir($target_dir)) {
                mkdir($target_dir, 0755, true);
            }
            
            $file_extension = pathinfo($_FILES['club_image']['name'], PATHINFO_EXTENSION);
            $new_filename = uniqid() . '.' . $file_extension;
            $target_file = $target_dir . $new_filename;

            $check = getimagesize($_FILES['club_image']['tmp_name']);
            if ($check !== false) {
                if (move_uploaded_file($_FILES['club_image']['tmp_name'], $target_file)) {
                    $image_path = $target_file;
                } else {
                    $message = "Error uploading file. Please try again.";
                }
            } else {
                $message = "File is not an image.";
            }
        }

        if ($message === "") {
            // Insert the new club into the database
            $stmt_insert = $conn->prepare("INSERT INTO clubs_details (club_name, club_description,club_image) VALUES (?, ?, ?)");
            $stmt_insert->bind_param("sss", $club_name, $club_description, $image_path);
            
            if ($stmt_insert->execute()) {
                $message = "Club created successfully!";
            } else {
                $message = "Error: Unable to create club. Please try again.";
            }
            $stmt_insert->close();
        }
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create a New Club</title>
    <style>
        body { font-family: Arial, sans-serif; background-color: #f4f4f4; padding: 20px; }
        .container { max-width: 600px; margin: auto; background: white; padding: 30px; border-radius: 8px; box-shadow: 0 0 10px rgba(0,0,0,0.1); }
        h2 { text-align: center; color: #333; }
        .form-group { margin-bottom: 15px; }
        label { display: block; margin-bottom: 5px; font-weight: bold; }
        input[type="text"], textarea, input[type="file"] { width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 4px; }
        button { background-color: #007bff; color: white; padding: 10px 15px; border: none; border-radius: 4px; cursor: pointer; }
        button:hover { background-color: #0056b3; }
        .message { margin-top: 20px; text-align: center; font-weight: bold; }
        .success { color: #4CAF50; }
        .error { color: #f44336; }
        .back-link { display: block; text-align: center; margin-top: 20px; }
    </style>
</head>
<body>

<div class="container">
    <h2>Create a New Club</h2>
    
    <?php if (!empty($message)): ?>
        <p class="message <?php echo (strpos($message, 'Error') !== false) ? 'error' : 'success'; ?>">
            <?php echo htmlspecialchars($message); ?>
        </p>
    <?php endif; ?>

    <form action="" method="post" enctype="multipart/form-data">
        <div class="form-group">
            <label for="club_name">Club Name:</label>
            <input type="text" id="club_name" name="club_name" required>
        </div>
        <div class="form-group">
            <label for="club_description">Club Description:</label>
            <textarea id="club_description" name="club_description" rows="5" required></textarea>
        </div>
        <div class="form-group">
            <label for="club_image">Club Image:</label>
            <input type="file" id="club_image" name="club_image" accept="image/*">
        </div>
        <button type="submit">Create Club</button>
    </form>

    <div class="back-link">
        <a href="admin-panel-demo.php">Back to Admin Panel</a>
    </div>
</div>

</body>
</html>