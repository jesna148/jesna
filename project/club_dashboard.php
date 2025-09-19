<?php
session_start();

// Ensure user is logged in as a club leader
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'club_leader') {
    header('Location: login.php');
    exit();
}

// Ensure the club_id is available in the session
if (!isset($_SESSION['club_id'])) {
    die("Error: No club ID found for this user.");
}

// Get the club ID and user ID from the session
$club_id = $_SESSION['club_id'];
$user_id = $_SESSION['user_id'];
$leader_fullname = $_SESSION['fullname']; // Get the full name from the session

// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "project";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch club info for the logged-in leader's club
$stmt = $conn->prepare("SELECT * FROM clubs_details WHERE id = ? AND leader_id = ?");
$stmt->bind_param("ii", $club_id, $user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows != 1) {
    die("Invalid club or unauthorized access.");
}
$club = $result->fetch_assoc();
$stmt->close();

// New: Fetch total number of members in the club
// This assumes your users table has a 'club_id' column
$stmt_members = $conn->prepare("SELECT COUNT(*) AS total_members FROM users WHERE club_id = ?");
$stmt_members->bind_param("i", $club_id);
$stmt_members->execute();
$result_members = $stmt_members->get_result();
$members_count = $result_members->fetch_assoc()['total_members'];
$stmt_members->close();

// New: Fetch total number of events for the club
// This assumes you have an 'events' table with a 'club_id' column
$stmt_events = $conn->prepare("SELECT COUNT(*) AS total_events FROM club_events WHERE club_event_id = ?");
$stmt_events->bind_param("i", $club_id);
$stmt_events->execute();
$result_events = $stmt_events->get_result();
$events_count = $result_events->fetch_assoc()['total_events'];
$stmt_events->close();

$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title><?php echo htmlspecialchars($club['club_name']); ?> Dashboard</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(to right, #f8f9fa, #e0eafc);
            font-family: 'Poppins', sans-serif;
            margin: 0;
            padding: 0;
            background: #f4f7f9;
            display: flex;
        }

        .sidebar {
            width: 250px;
            background: #fff;
            color: #333;
            padding: 20px;
            box-shadow: 2px 0 5px rgba(0,0,0,0.1);
            position: fixed;
            height: 100%;
            overflow-y: auto;
        }
        .sidebar h2 {
            text-align: center;
            color: #4a5568;
            border-bottom: 2px solid #e2e8f0;
            padding-bottom: 10px;
            margin-bottom: 20px;
        }
        .sidebar a {
            display: block;
            color: #4a5568;
            padding: 15px;
            text-decoration: none;
            border-radius: 8px;
            margin-bottom: 10px;
            transition: background-color 0.3s, color 0.3s;
        }
        .sidebar a:hover, .sidebar a.active {
            background-color: #e2e8f0;
            color: #2b6cb0;
        }
        .content {
            margin-left: 250px;
            padding: 40px;
            width: 100%;
        }
        .club-header {
            background: linear-gradient(to right, #6366f1, #a855f7);
            color: white;
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.2);
            text-align: center;
            margin-bottom: 30px;
        }
        .club-header h1 {
            margin: 0;
            font-size: 2.8em;
            font-weight: 700;
        }
        .club-header p {
            margin: 5px 0 0;
            font-size: 1.2em;
        }
        .dashboard-content {
            background: white;
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 4px 10px rgba(0,0,0,0.1);
            min-height: 500px;
        }
        .card-container {
            display: flex;
            gap: 20px;
            margin-top: 30px;
        }
        .card {
            flex: 1;
            background-color: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            padding: 20px;
            text-align: center;
            box-shadow: 0 2px 5px rgba(0,0,0,0.05);
        }
        .card h4 {
            margin-top: 0;
            color: #4a5568;
        }
        .card .count {
            font-size: 2.5em;
            font-weight: 700;
            color: #2b6cb0;
        }
    </style>
</head>
<body>

    <div class="sidebar">
        <h2><?php echo htmlspecialchars($club['club_name']); ?></h2>
        <a href=" club_leader_dashboard.php">Back to Main Dashboard</a>
        <a href="#" class="active">Dashboard</a>
        <a href="display_membership.php">View Members</a>
        <a href="event_add.php">Add Event</a>
        <a href="view_event.php">Edit Events</a>
        <a href="#">Edit Club Details</a>
        <a href="club-activity-form.html">Post Activity</a>
        <a href="logout.php">Logout</a>
    </div>

    <div class="content">
        <div class="club-header">
            <p>Club Leader Dashboard</p>
            <h1><?php echo htmlspecialchars($club['club_name']); ?></h1>
            <h3>Club leader: <?php echo htmlspecialchars($leader_fullname); ?></h3>
        </div>

        <div class="dashboard-content">
            <h3>Welcome, <?php echo htmlspecialchars($leader_fullname); ?>!</h3>
            <p>This is your central hub for managing your club. Use the menu on the left to navigate between different options.</p>

            <div class="card-container">
                <div class="card">
                    <h4>Total Members</h4>
                    <div class="count"><?php echo htmlspecialchars($members_count); ?></div>
                </div>
                <div class="card">
                    <h4>Total Events</h4>
                    <div class="count"><?php echo htmlspecialchars($events_count); ?></div>
                </div>
            </div>
        </div>
    </div>

</body>
</html>