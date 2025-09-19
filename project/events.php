<?php
session_start();
$conn = new mysqli("localhost", "root", "", "project");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$student_email = $_SESSION['email'];

$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$category = isset($_GET['category']) ? $_GET['category'] : '';

$query = "SELECT * FROM events WHERE title LIKE '%$search%'";
if (!empty($category)) {
    $query .= " AND category='$category'";
}
$query .= " ORDER BY start_date ASC";
$events = $conn->query($query);

$ongoing = [];
$upcoming = [];
$past = [];

while ($row = $events->fetch_assoc()) {
    $today = date("Y-m-d");
    if ($today >= $row['start_date'] && $today <= $row['end_date']) {
        $ongoing[] = $row;
    } elseif ($today < $row['start_date']) {
        $upcoming[] = $row;
    } else {
        $past[] = $row;
    }
}

$my_registrations = [];
$reg_query = $conn->query("SELECT event_id FROM event_registrations WHERE student_email='$student_email'");
while ($r = $reg_query->fetch_assoc()) {
    $my_registrations[] = $r['event_id'];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Campus Events</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Poppins', sans-serif;
        }

        body {
            background-color: #f8f9fc;
            color: #333;
            display: flex;
        }

 
        .sidebar {
            width: 250px;
            background: #1e293b;
            color: white;
            height: 100vh;
            padding: 20px 0;
            position: fixed;
            top: 0;
            left: 0;
            box-shadow: 2px 0 10px rgba(0, 0, 0, 0.2);
        }

        .sidebar h2 {
            text-align: center;
            font-size: 22px;
            margin-bottom: 25px;
            color: #fff;
        }

        .sidebar a {
            display: flex;
            align-items: center;
            padding: 12px 20px;
            color: #cbd5e1;
            text-decoration: none;
            font-size: 16px;
            transition: 0.3s;
        }

        .sidebar a:hover,
        .sidebar a.active {
            background: #2563eb;
            color: #fff;
        }

        .sidebar a i {
            margin-right: 12px;
            font-size: 18px;
        }

        .main-content {
            margin-left: 230px;
            padding: 20px;
            width: calc(100% - 230px);
        }

        .header {
            text-align: center;
            padding: 25px;
            background: #4e73df;
            color: white;
            border-radius: 10px;
            margin-bottom: 20px;
        }

        .header h1 {
            font-size: 28px;
            margin-bottom: 5px;
        }

        .header p {
            font-size: 15px;
            opacity: 0.9;
        }

        .search-bar {
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 12px;
            margin: 20px auto;
            padding: 15px;
            background: #fff;
            width: 80%;
            max-width: 800px;
            border-radius: 10px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        }

        .search-bar input,
        .search-bar select {
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 6px;
            outline: none;
            font-size: 14px;
        }

        .search-bar input {
            flex: 1;
        }

        .search-bar button {
            background: #4e73df;
            color: white;
            padding: 10px 18px;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            transition: 0.3s;
            font-size: 14px;
        }

        .search-bar button:hover {
            background: #2e59d9;
        }

        h2 {
            margin: 25px 0 15px 5%;
            font-size: 22px;
            color: #4e73df;
        }

        .event-container {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 18px;
            padding: 0 5%;
            margin-bottom: 30px;
        }

        .event-card {
            background: #fff;
            border-radius: 12px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
            padding: 15px;
            text-align: center;
            transition: 0.3s;
        }

        .event-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 6px 16px rgba(0,0,0,0.15);
        }

        .event-card img {
            width: 100%;
            height: 150px;
            object-fit: cover;
            border-radius: 10px;
            margin-bottom: 12px;
        }

        .event-card h3 {
            font-size: 18px;
            color: #333;
            margin-bottom: 8px;
        }

        .event-card p {
            font-size: 14px;
            color: #555;
            margin-bottom: 8px;
        }

        .btn, button {
            display: inline-block;
            background: #1cc88a;
            color: white;
            padding: 8px 14px;
            border-radius: 6px;
            text-decoration: none;
            transition: 0.3s;
            font-size: 14px;
        }

        .btn:hover {
            background: #17a673;
        }

        button[disabled] {
            background: #ccc;
            cursor: not-allowed;
        }

        .event-container p {
            grid-column: 1/-1;
            text-align: center;
            color: #888;
            font-size: 15px;
        }

       
        @media (max-width: 768px) {
            .sidebar {
                width: 180px;
            }
            .main-content {
                margin-left: 180px;
                width: calc(100% - 180px);
            }
        }

        @media (max-width: 600px) {
            .sidebar {
                display: none;
            }
            .main-content {
                margin-left: 0;
                width: 100%;
            }
            .search-bar {
                flex-direction: column;
                gap: 10px;
                width: 90%;
            }
        }
    </style>
</head>
<body>

<div class="sidebar">
        <h2>📚 Student Panel</h2>
        <a href="student_home.php" ><i class="fas fa-home"></i> Dashboard</a>
        <a href="events.php" class="active"><i class="fas fa-calendar-alt"></i> Events</a>
        <a href="clubs.php"><i class="fas fa-users"></i> Clubs</a>
        <a href="profile.php"><i class="fas fa-user"></i> Profile</a>
        <a href="settings.php"><i class="fas fa-cog"></i> Settings</a>
        <a href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
    </div>

<div class="main-content">
    <div class="header">
        <h1>Campus Events</h1>
        <p>Explore upcoming, ongoing, and past events</p>
    </div>

    <form method="get" class="search-bar">
        <input type="text" name="search" placeholder="Search events..." value="<?php echo $search; ?>">
        <select name="category">
            <option value="">All Categories</option>
            <option value="Technical" <?php if($category=="Technical") echo "selected"; ?>>Technical</option>
            <option value="Cultural" <?php if($category=="Cultural") echo "selected"; ?>>Cultural</option>
            <option value="Sports" <?php if($category=="Sports") echo "selected"; ?>>Sports</option>
        </select>
        <button type="submit">Search</button>
    </form>

    <h2>Ongoing Events</h2>
    <div class="event-container">
        <?php if(count($ongoing)>0){ foreach($ongoing as $event){ ?>
            <div class="event-card">
                <img src="uploads/<?php echo $event['banner']; ?>" alt="Event Banner">
                <h3><?php echo $event['title']; ?></h3>
                <p><?php echo substr($event['description'], 0, 100); ?>...</p>
                <p><b>Ends:</b> <?php echo $event['end_date']; ?></p>
                <?php if(in_array($event['id'], $my_registrations)){ ?>
                    <button disabled>Registered ✅</button>
                <?php } else { ?>
                    <a href="register_event.php?id=<?php echo $event['id']; ?>" class="btn">Register</a>
                <?php } ?>
            </div>
        <?php }} else { echo "<p>No ongoing events</p>"; } ?>
    </div>

    <h2>Upcoming Events</h2>
    <div class="event-container">
        <?php if(count($upcoming)>0){ foreach($upcoming as $event){ ?>
            <div class="event-card">
                <img src="uploads/<?php echo $event['banner']; ?>" alt="Event Banner">
                <h3><?php echo $event['title']; ?></h3>
                <p><?php echo substr($event['description'], 0, 100); ?>...</p>
                <p><b>Starts:</b> <?php echo $event['start_date']; ?></p>
                <?php if(in_array($event['id'], $my_registrations)){ ?>
                    <button disabled>Registered ✅</button>
                <?php } else { ?>
                    <a href="register_event.php?id=<?php echo $event['id']; ?>" class="btn">Register</a>
                <?php } ?>
            </div>
        <?php }} else { echo "<p>No upcoming events</p>"; } ?>
    </div>

    <h2>Past Events</h2>
    <div class="event-container">
        <?php if(count($past)>0){ foreach($past as $event){ ?>
            <div class="event-card">
                <img src="uploads/<?php echo $event['banner']; ?>" alt="Event Banner">
                <h3><?php echo $event['title']; ?></h3>
                <p><?php echo substr($event['description'], 0, 100); ?>...</p>
                <p><b>Ended:</b> <?php echo $event['end_date']; ?></p>
            </div>
        <?php }} else { echo "<p>No past events</p>"; } ?>
    </div>
</div>

</body>
</html>