<?php
// Database connection
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

// Fetch all clubs from the database
$stmt = $conn->prepare("SELECT club_name, club_description, club_image FROM clubs_details ORDER BY club_name");
$stmt->execute();
$result = $stmt->get_result();
$clubs = $result->fetch_all(MYSQLI_ASSOC);
$stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Campus Clubs</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
    <style>
        * {
            box-sizing: border-box;
        }

        body {
            font-family: 'Poppins', sans-serif;
            background: linear-gradient(to right, #f8f9fa, #e0eafc);
            margin: 0;
            padding: 0;
            overflow-x: hidden;
        }

        .top-container {
            position: relative;
            background: linear-gradient(135deg, #2575fc, #6a11cb);
            color: white;
            padding: 40px 20px;
            text-align: center;
            box-shadow: 0 4px 12px rgba(0,0,0,0.2);
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        .top-container h1 {
            margin: 0;
            font-size: 2.8em;
            letter-spacing: 1px;
        }

        .join-button-container {
            position: absolute;
            top: 20px;
            right: 20px;
        }

        .join-button {
            padding: 10px 20px;
            background-color: #f7f7f7;
            color: #2575fc;
            text-decoration: none;
            border-radius: 5px;
            font-weight: 600;
            transition: transform 0.2s, background-color 0.2s;
            box-shadow: 0 2px 5px rgba(0,0,0,0.2);
            white-space: nowrap;
        }

        .join-button:hover {
            transform: scale(1.05);
            background-color: #e0e0e0;
        }

        h2 {
            text-align: center;
            color: #2575fc;
            padding: 20px 0;
            font-weight: 600;
        }

        @keyframes scroll {
            0% { transform: translateX(0); }
            100% { transform: translateX(-50%); }
        }

        .clubs-container {
            display: flex;
            flex-wrap: nowrap;
            justify-content: flex-start;
            padding: 50px 20px;
            gap: 30px;
            animation: scroll 30s linear infinite;
            will-change: transform;
            width: fit-content;
            margin: 0 auto;
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
            flex: 0 0 auto;
            display: flex; /* Added for button alignment */
            flex-direction: column; /* Added for button alignment */
            justify-content: space-between; /* Added for button alignment */
        }

        .club-card:hover {
            transform: translateY(-8px);
            box-shadow: 0 12px 25px rgba(0,0,0,0.2);
        }
        
        .club-info {
            padding: 0 20px 10px;
            text-align: center;
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
            font-size: 0.95em;
            text-align: center;
            margin: 0;
        }

        .club-card .card-footer {
            padding: 10px 20px 20px;
        }

        .details-button {
            display: block;
            width: 100%;
            padding: 10px;
            background-color: #2575fc;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            font-weight: 600;
            transition: background-color 0.3s;
        }

        .details-button:hover {
            background-color: #1a5acb;
        }

        .no-clubs {
            text-align: center;
            color: #6c757d;
            font-size: 1.2em;
            margin-top: 50px;
        }
    </style>
</head>
<body>
<div class="top-container">
    <div class="join-button-container">
        <a href="membership.php" class="join-button">Join a Club</a>
    </div>
    <h1>Campus Clubs</h1>
</div>

<div>
    <h2>"Join Our Dynamic Campus Clubs and Make the Most of Your College Life"</h2>
</div>

<div style="overflow: hidden; width: 100%;">
    <?php if (empty($clubs)): ?>
        <p class="no-clubs">There are no clubs available yet. Check back soon! 😔</p>
    <?php else: ?>
        <div class="clubs-container" id="clubs-container">
            <?php foreach ($clubs as $club): ?>
                <div class="club-card">
                    <div>
                        <img src="<?php echo htmlspecialchars($club['club_image']); ?>" alt="<?php echo htmlspecialchars($club['club_name']); ?>">
                        <div class="club-info">
                            <h3><?php echo htmlspecialchars($club['club_name']); ?></h3>
                            <p><?php echo htmlspecialchars($club['club_description']); ?></p>
                        </div>
                    </div>
                    <div class="card-footer">
                        <a href="club_details.php?club_name=<?php echo urlencode($club['club_name']); ?>" class="details-button">Learn More</a>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<script>
    const container = document.getElementById('clubs-container');
    if (container) {
        const originalContent = container.innerHTML;
        if (originalContent.trim() !== '') {
            container.innerHTML += originalContent;
        }
    }
</script>
</body>
</html>