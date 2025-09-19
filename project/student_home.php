<?php
session_start();
$conn = new mysqli("localhost", "root", "", "project");

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// ✅ Check if the student is logged in
if (!isset($_SESSION['email']) || $_SESSION['role'] !== 'student') {
    header("Location: login.php");
    exit();
}

$email = $_SESSION['email'];

// ✅ Fetch student details
$studentQuery = $conn->prepare("SELECT fullname, email FROM users WHERE email=?");
$studentQuery->bind_param("s", $email);
$studentQuery->execute();
$student = $studentQuery->get_result()->fetch_assoc();
$studentName = $student['fullname'];

// ✅ Fetch stats
$totalEvents = $conn->query("SELECT COUNT(*) AS total FROM events")->fetch_assoc()['total'];
$registeredEvents = $conn->query("SELECT COUNT(*) AS reg FROM event_registrations WHERE student_email='$email'")->fetch_assoc()['reg'];
$activeClubs = $conn->query("SELECT COUNT(*) AS clubs FROM clubs")->fetch_assoc()['clubs'];

// ✅ Fetch upcoming events (limit 5) → assuming your events table exists
$events = $conn->query("SELECT id, title, banner, start_date FROM events ORDER BY start_date ASC LIMIT 5");

// ✅ Fetch featured clubs (limit 4)
//$clubs = $conn->query("SELECT id, club_name FROM clubs ORDER BY created_at DESC LIMIT 4");

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Dashboard</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="student_home.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>
<body>
    <!-- Header -->
    <header class="header">
        <h1>Welcome, <?php echo htmlspecialchars($studentName); ?> 🎓</h1>
        <div class="profile">
            <img src="assets/default-avatar.png" alt="Profile">
        </div>
    </header>

    <!-- Quick Stats Section -->
    <section class="stats">
        <div class="card">
            <i class="fas fa-calendar-alt"></i>
            <h3><?php echo $totalEvents; ?></h3>
            <a href="events.php">Total Events</a>
            
        </div>
        <div class="card">
            <i class="fas fa-ticket-alt"></i>
            <h3><?php echo $registeredEvents; ?></h3>
            <a href="events.php">Registered Events</a>
        </div>
        <div class="card">
            <i class="fas fa-users"></i>
            <h3><?php echo $activeClubs; ?></h3>
            <a href="clubs.php">Active Clubs</a>
        </div>
    </section>

    <!-- Upcoming Events -->
    <section class="events">
        <h2>🎉 Upcoming Events</h2>
        <div class="event-slider">
            <?php if ($events && $events->num_rows > 0) {
                while ($event = $events->fetch_assoc()) { ?>
                    <div class="event-card">
                        <img src="<?php echo $event['banner']; ?>" alt="Event">
                        <h3><?php echo htmlspecialchars($event['title']); ?></h3>
                        <p><?php echo date("d M, Y", strtotime($event['start_date'])); ?></p>
                        <a href="events.php?id=<?php echo $event['id']; ?>" class="btn">View Details</a>
                    </div>
                <?php }
            } else {
                echo "<p>No upcoming events.</p>";
            } ?>
        </div>
    </section>

    <!-- Featured Clubs -->
    <section class="clubs">
        <h2>🔥 Featured Clubs</h2>
        <div class="club-container">
            <?php if ($clubs && $clubs->num_rows > 0) {
                while ($club = $clubs->fetch_assoc()) { ?>
                    <div class="club-card">
                        <img src="assets/default-club.png" alt="Club">
                        <h3><?php echo htmlspecialchars($club['club_name']); ?></h3>
                        <a href="club_details.php?id=<?php echo $club['id']; ?>" class="btn">Join Now</a>
                    </div>
                <?php }
            } else {
                echo "<p>No clubs available.</p>";
            } ?>
        </div>
    </section>

    <!-- Notifications -->
    <section class="notifications">
        <h2>🔔 Notifications</h2>
        <div class="notif-card">
            <p>⚡ Event registration deadline is approaching!</p>
        </div>
        <div class="notif-card">
            <p>🎯 Join clubs to connect with your peers!</p>
        </div>
    </section>
</body>
</html>