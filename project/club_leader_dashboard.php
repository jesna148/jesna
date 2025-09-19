<?php
session_start();

// Redirect to login if the user is not a logged-in club leader
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'club_leader') {
    header('Location:  login.php');
    exit();
}

// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "project";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch club details for the logged-in leader
$club_name = "Your Club"; // Default value
$club_id = $_SESSION['club_id'];

if ($club_id) {
    $stmt = $conn->prepare("SELECT club_name FROM clubs2 WHERE id = ? AND leader_id = ?");
    $stmt->bind_param("ii", $club_id, $_SESSION['user_id']);
    $stmt->execute();
    $result = $stmt->get_result();
    $club = $result->fetch_assoc();
    if ($club) {
        $club_name = htmlspecialchars($club['club_name']);
    }
    $stmt->close();
}

$conn->close();

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Club Leader Dashboard</title>
    <style>
        * {
            box-sizing: border-box;
        }

        body {
            font-family: 'Poppins', sans-serif;
            background: linear-gradient(to right, #f8f9fa, #e0eafc);
            margin: 0;
            padding: 0;
        }

        .header {
            background: linear-gradient(135deg, #fff, #cdd1d8);
            color: white;
            text-align: center;
            padding: 40px 20px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.2);
        }

        .hero {
            background-image: url('uploads/music.jpg');
            background-size: cover;
            background-position: center;
            color: white;
            padding: 100px 20px;
            text-align: center;
        }

        .hero h2 {
            font-size: 3rem;
            margin-bottom: 10px;
            font-weight: 700;
        }

        .hero p {
            font-size: 1.2rem;
            margin-top: 0;
        }

        #accessBtn {
            padding: 10px 20px;
            font-size: 1.2em;
            color: white;
            background: linear-gradient(45deg, #308bdb, #9d39d6);
            border: none;
            border-radius: 8px;
            cursor: pointer;
            box-shadow: 0 4px 15px rgba(0,0,0,0.2);
            transition: all 0.3s ease;
            text-decoration: none;
            display: inline-block;
        }

        #accessBtn:hover {
            background: linear-gradient(45deg, #00f2fe, #4facfe);
            box-shadow: 0 6px 20px rgba(0,0,0,0.3);
            transform: scale(1.05);
        }

        /* Corrected CSS for the scrolling container */
        .clubs-container {
            display: flex; /* Use flexbox to align cards */
            justify-content: flex-start;
            padding: 50px 20px;
            gap: 30px;
            white-space: nowrap; /* Prevent cards from wrapping to the next line */
            animation: scroll 30s linear infinite;
            will-change: transform;
            width: fit-content;
        }
        
        @keyframes scroll {
            0% {
                transform: translateX(0);
            }
            100% {
                transform: translateX(-50%);
            }
        }
        
        .clubs-container:hover {
            animation-play-state: paused;
        }

        .club-card {
            background: white;
            border-radius: 18px;
            width: 250px;
            overflow: hidden;
            box-shadow: 0 8px 20px rgba(0,0,0,0.1);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            flex-shrink: 0; /* Prevents cards from shrinking */
        }
        
        .club-card:hover {
            transform: translateY(-8px);
            box-shadow: 0 12px 25px rgba(0,0,0,0.2);
        }
        
        .club-card img {
            width: 100%;
            height: 200px;
            object-fit: cover;
        }
        
        .club-card h3 {
            margin: 15px 0 10px;
            font-size: 1.3em;
            color: #34495e;
            text-align: center;
        }
        
        .club-card p {
            color: #666;
            padding: 0 20px 20px;
            font-size: 0.95em;
            text-align: center;
        }
    </style>
</head>
<body>

<div class="header">
    <div class="hero">
        <h2>Welcome, <?php echo htmlspecialchars($_SESSION['fullname']); ?>!</h2>
        <p>You can manage your club, add events, and monitor participation from this dashboard.</p>
        <a href="club_dashboard.php?club_id=<?php echo htmlspecialchars($club_id); ?>" id="accessBtn">
            Access My Club: <?php echo $club_name; ?>
        </a>
    </div>
</div>

<div style="overflow: hidden; width: 100%;">
    <div class="clubs-container" id="clubs-container">
        <div class="club-card">
            <img src="uploads/workshop.jpg" alt="Literary Club">
            <h3>Literary Club</h3>
            <p>A space for poetry, books, and creative writing.</p>
        </div>
        <div class="club-card">
            <img src="uploads/music.jpg" alt="Music Club">
            <h3>Music Club</h3>
            <p>Explore instruments, vocals, and stage performances.</p>
        </div>
        <div class="club-card">
            <img src="uploads/tech.jpg" alt="Tech Club">
            <h3>Tech Club</h3>
            <p>For coders, inventors, and technology lovers.</p>
        </div>
        <div class="club-card">
            <img src="uploads/arts.jpg" alt="Art Club">
            <h3>Art Club</h3>
            <p>Unleash your creativity through painting, sketching, and crafts.</p>
        </div>
        <div class="club-card">
            <img src="uploads/football.jpg" alt="Sports Club">
            <h3>Sports Club</h3>
            <p>Promoting fitness, sportsmanship, and healthy competition.</p>
        </div>
        <div class="club-card">
            <img src="uploads/trekking.jpg" alt="Photography Club">
            <h3>Photography Club</h3>
            <p>Capture moments, learn editing, and explore visual storytelling.</p>
        </div>
        <div class="club-card">
            <img src="uploads/plant.jpg" alt="Environment Club">
            <h3>Environment Club</h3>
            <p>Raise awareness, go green, and lead eco-friendly initiatives.</p>
        </div>
    </div>
</div>
<script>
    const container = document.getElementById('clubs-container');
    const originalContent = container.innerHTML;
    container.innerHTML += originalContent;

    let scrollSpeed = 0.1; 
    let currentScroll = 0;

    function scroll() {
        currentScroll += scrollSpeed;
        if (currentScroll >= container.scrollWidth / 2) {
            currentScroll = 0; 
        }
        container.scrollLeft = currentScroll;
        requestAnimationFrame(scroll);
    }
    scroll();

</script>
</body>
</html>