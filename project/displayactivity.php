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

// Check if an activity ID was passed in the URL
if (!isset($_GET['id'])) {
    die("Activity not found.");
}

$activityId = $_GET['id'];

// Fetch activity details from the database
$stmt = $conn->prepare("SELECT * FROM club_activities WHERE id = ?");
$stmt->bind_param("i", $activityId);
$stmt->execute();
$result = $stmt->get_result();
$activity = $result->fetch_assoc();
$stmt->close();

if (!$activity) {
    die("Activity details not found.");
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title><?php echo htmlspecialchars($activity['caption']); ?></title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background-color: #f4f6f8;
            padding: 20px;
            margin: 0;
        }
        .container {
            max-width: 800px;
            margin: 50px auto;
            background-color: #fff;
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.1);
            text-align: center;
        }
        h1 {
            color: #2575fc;
            margin-bottom: 20px;
        }
        img {
            max-width: 100%;
            height: auto;
            border-radius: 8px;
            margin-bottom: 20px;
        }
        p {
            color: #555;
            line-height: 1.6;
        }
    </style>
</head>
<body>

<div class="container">
    <h1><?php echo htmlspecialchars($activity['caption']); ?></h1>
    <img src="<?php echo htmlspecialchars($activity['image_path']); ?>" alt="Club Activity Image">
    <p><?php echo htmlspecialchars($activity['description']); ?></p>
</div>

</body>
</html>