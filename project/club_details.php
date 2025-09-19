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

// Check if a club name was passed in the URL
if (!isset($_GET['club_name'])) {
    die("Club not found.");
}

$clubName = urldecode($_GET['club_name']);

// Fetch club details from the clubs_details table
$stmt = $conn->prepare("SELECT id, club_name, club_description FROM clubs_details WHERE club_name = ?");
$stmt->bind_param("s", $clubName);
$stmt->execute();
$clubDetails = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$clubDetails) {
    die("Club details not found for " . htmlspecialchars($clubName));
}

$clubId = $clubDetails['id'];

// Fetch images for this club from the club_activities table
// This assumes you have a table named club_activities with columns like club_id and image_path
$stmt = $conn->prepare("SELECT image_path, caption FROM club_activities WHERE club_id = ? ORDER BY created_at DESC");
$stmt->bind_param("i", $clubId);
$stmt->execute();
$activities = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt->close();

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title><?php echo htmlspecialchars($clubDetails['club_name']); ?> Details</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background: #f4f6f8;
            color: #333;
            margin: 0;
            padding: 20px;
        }

        .container {
            max-width: 900px;
            margin: auto;
            background: #fff;
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.1);
        }

        h1 {
            color: #2575fc;
            text-align: center;
            margin-bottom: 10px;
        }

        h2 {
            border-bottom: 2px solid #eee;
            padding-bottom: 10px;
            margin-top: 30px;
            color: #34495e;
        }

        .club-info p {
            line-height: 1.6;
        }

        .activity-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin-top: 20px;
        }

        .activity-item {
            background: #f9f9f9;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
            transition: transform 0.2s;
        }

        .activity-item:hover {
            transform: translateY(-5px);
        }

        .activity-item img {
            width: 100%;
            height: 200px;
            object-fit: cover;
        }

        .activity-item .caption {
            padding: 15px;
            text-align: center;
            font-size: 0.9em;
            color: #555;
        }
        .activity-link {
    text-decoration: none;
    color: inherit;
}

        .back-button {
            display: inline-block;
            margin-top: 20px;
            padding: 10px 20px;
            background-color: #6a11cb;
            color: #fff;
            text-decoration: none;
            border-radius: 5px;
            transition: background-color 0.3s;
        }

        .back-button:hover {
            background-color: #5a0ebc;
        }

        .no-activities {
            text-align: center;
            color: #888;
            font-style: italic;
        }
    </style>
</head>
<body>

<div class="container">
    <a href="clubs.php" class="back-button">← Back to Clubs</a>

    <h1><?php echo htmlspecialchars($clubDetails['club_name']); ?></h1>

    <div class="club-info">
        <h2>About Us</h2>
        <p><?php echo htmlspecialchars($clubDetails['club_description']); ?></p>
    </div>

    <div class="club-activities">
    <h2>Recent Activities</h2>
    <?php if (empty($activities)): ?>
        <p class="no-activities">No activities have been posted for this club yet.</p>
    <?php else: ?>
        <div class="activity-grid">
        <?php foreach ($activities as $activity): ?>
            <a href="displayactivity.php?id=<?php echo $activity['id']; ?>" class="activity-link">
                <div class="activity-item">
                    <img src="<?php echo htmlspecialchars($activity['image_path']); ?>" alt="Club Activity Image">
                    <?php if ($activity['caption']): ?>
                        <div class="caption">
                            <p><?php echo htmlspecialchars($activity['caption']); ?></p>
                        </div>
                    <?php endif; ?>
                </div>
            </a>
        <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

</body>
</html>