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

// Get the club ID for the logged-in club leader
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
    echo "You are not assigned to a club. Please contact the admin.";
    exit();
}

// Handle event deletion via POST
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'delete' && isset($_POST['id'])) {
    $eventId = $_POST['id'];
    $stmt = $conn->prepare("DELETE FROM club_events WHERE id = ? AND club_event_id = ?");
    $stmt->bind_param("ii", $eventId, $club_id);
    if ($stmt->execute()) {
        echo "<script>alert('Event deleted successfully!'); window.location.href='view_event.php';</script>";
    } else {
        echo "<script>alert('Error deleting event: " . $stmt->error . "');</script>";
    }
    $stmt->close();
}

// Fetch all events for the current club to display
$events = [];
$stmt = $conn->prepare("SELECT * FROM club_events WHERE club_event_id = ? ORDER BY event_date ASC, event_time ASC");
$stmt->bind_param("i", $club_id);
$stmt->execute();
$result = $stmt->get_result();
while ($row = $result->fetch_assoc()) {
    $events[] = $row;
}
$stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Your Club Events</title>
    <style>
          body {
            font-family: Arial, sans-serif;
            background-color: #f4f6f8;
            margin: 0;
            padding: 20px;
        }

        .container {
            max-width: 900px;
            margin: 50px auto;
            background-color: #fff;
            padding: 30px 40px;
            border-radius: 8px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        }

        h2 {
            text-align: center;
            color: #333;
            margin-bottom: 20px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        th, td {
            padding: 12px;
            border: 1px solid #ddd;
            text-align: left;
        }

        th {
            background-color: #f2f2f2;
            color: #333;
        }

        .action-buttons a {
            text-decoration: none;
            color: #fff;
            padding: 6px 10px;
            border-radius: 4px;
            font-size: 14px;
            margin-right: 5px;
            display: inline-block;
        }
        .action-buttons .edit-btn {
            background-color: #007bff;
        }
        .action-buttons .delete-btn {
            background-color: #dc3545;
        }

        .no-events {
            text-align: center;
            color: #777;
            font-style: italic;
        }

        .add-event-button {
            display: block;
            text-align: center;
            margin-bottom: 20px;
        }
        .add-event-button a {
            padding: 10px 20px;
            background-color: #28a745;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            transition: background-color 0.3s;
        }
        .add-event-button a:hover {
            background-color: #218838;
        }
    </style>
</head>
<body>

<div class="container">
    <h2>Your Club Events</h2>
    <div class="add-event-button">
        <a href="event_add.php">Add New Event</a>
    </div>

    <div class="events-table-container">
        <?php if (empty($events)): ?>
            <p class="no-events">You have not created any events yet.</p>
        <?php else: ?>
            <table>
                <thead>
                    <tr>
                        <th>Event Name</th>
                        <th>Date</th>
                        <th>Time</th>
                        <th>Venue</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($events as $event): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($event['event_name']); ?></td>
                            <td><?php echo htmlspecialchars($event['event_date']); ?></td>
                            <td><?php echo htmlspecialchars($event['event_time']); ?></td>
                            <td><?php echo htmlspecialchars($event['venue']); ?></td>
                            <td class="action-buttons">
                            <a href="event_add.php?action=edit&id=<?php echo $event['id']; ?>" class="edit-btn">Edit</a>
                                <form action="view_event.php" method="post" style="display:inline;">
                                    <input type="hidden" name="action" value="delete">
                                    <input type="hidden" name="id" value="<?php echo $event['id']; ?>">
                                    <button type="submit" class="delete-btn" onclick="return confirm('Are you sure you want to delete this event?');">Delete</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>
</div>

</body>
</html>