<?php
session_start();
$conn = new mysqli("localhost", "root", "", "project");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if (!isset($_SESSION['email']) || $_SESSION['role'] !== 'clubleader') {
    header("Location: login.php");
    exit();
}

$email = $_SESSION['email'];

$sql = "SELECT * FROM club_leaders WHERE email='$email'";
$result = $conn->query($sql);
$clubleader = $result->fetch_assoc();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Club Leader Dashboard</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Poppins', sans-serif;
            background-color: #f5f7fa;
            display: flex;
        }

        .sidebar {
            width: 250px;
            background: #6a11cb;
            color: #fff;
            height: 100vh;
            padding-top: 30px;
            position: fixed;
        }

        .sidebar h2 {
            text-align: center;
            margin-bottom: 20px;
            font-size: 22px;
        }

        .sidebar a {
            display: block;
            color: white;
            padding: 12px 20px;
            text-decoration: none;
            font-weight: 500;
            transition: background 0.3s;
        }

        .sidebar a:hover {
            background: #4b0ca3;
            border-left: 4px solid #fff;
        }

        .main {
            margin-left: 250px;
            padding: 20px;
            width: 100%;
        }

        .header {
            background: white;
            padding: 15px;
            border-radius: 10px;
            box-shadow: 0px 3px 6px rgba(0,0,0,0.1);
            margin-bottom: 20px;
            display: flex;
            justify-content: space-between;
        }

        .cards {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 15px;
        }

        .card {
            background: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0px 3px 6px rgba(0,0,0,0.1);
            text-align: center;
            transition: transform 0.3s;
            cursor: pointer;
        }

        .card:hover {
            transform: scale(1.05);
        }

        .card h3 {
            color: #333;
            margin-bottom: 10px;
        }

        .card p {
            color: #666;
        }

        .logout {
            background: #ff4757;
            padding: 10px 20px;
            border-radius: 6px;
            text-decoration: none;
            color: white;
            font-weight: bold;
        }

        .logout:hover {
            background: #e84118;
        }
    </style>
</head>
<body>

<div class="sidebar">
    <h2>Club Leader</h2>
    <a href="clubleader_home.php">🏠 Dashboard</a>
    <a href="manage_club.php">📌 Club Details</a>
    <a href="manage_events.php">📅 Manage Events</a>
    <a href="registrations.php">✅ Approve Registrations</a>
    <a href="view_events.php">🎉 View Upcoming Events</a>
    <a href="profile.php">👤 Profile</a>
    <a href="logout.php" class="logout">🚪 Logout</a>
</div>

<div class="main">
    <div class="header">
        <h2>Welcome, <?php echo $clubleader['name']; ?> 👋</h2>
        <a href="logout.php" class="logout">Logout</a>
    </div>

    <div class="cards">
        <div class="card" onclick="window.location='manage_club.php'">
            <h3>📌 Club Details</h3>
            <p>View and update your club information</p>
        </div>

        <div class="card" onclick="window.location='manage_events.php'">
            <h3>📅 Manage Events</h3>
            <p>Create, edit, or delete your club events</p>
        </div>

        <div class="card" onclick="window.location='registrations.php'">
            <h3>✅ Approve Registrations</h3>
            <p>Approve or reject student participation</p>
        </div>

        <div class="card" onclick="window.location='view_events.php'">
            <h3>🎉 Upcoming Events</h3>
            <p>Check all upcoming events from your club</p>
        </div>

        <div class="card" onclick="window.location='profile.php'">
            <h3>👤 My Profile</h3>
            <p>Update your personal details and club info</p>
        </div>
    </div>
</div>

</body>
</html>
