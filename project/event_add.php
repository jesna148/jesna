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

// Initialize editEvent variable
$editEvent = null;

// Handle form submissions (Add or Edit event)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $eventName = $_POST['eventName'];
    $eventDate = $_POST['date'];
    $eventTime = $_POST['time'];
    $venue = $_POST['venue'];
    $description = $_POST['description'];
    $eventId = isset($_POST['eventId']) ? $_POST['eventId'] : null;

    if ($eventId) {
        // Update an existing event
        $stmt = $conn->prepare("UPDATE club_events SET event_name = ?, event_date = ?, event_time = ?, venue = ?, description = ? WHERE id = ? AND club_event_id = ?");
        $stmt->bind_param("sssssii", $eventName, $eventDate, $eventTime, $venue, $description, $eventId, $club_id);
        $message = "Event updated successfully!";
    } else {
        // Add a new event
        $stmt = $conn->prepare("INSERT INTO club_events (club_event_id, event_name, event_date, event_time, venue, description) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("isssss", $club_id, $eventName, $eventDate, $eventTime, $venue, $description);
        $message = "Event added successfully!";
    }

    if ($stmt->execute()) {
        echo "<script>alert('$message'); window.location.href='event_add.php';</script>";
        exit();
    } else {
        echo "<script>alert('Error: " . $stmt->error . "');</script>";
    }
    $stmt->close();
}

// Fetch event details for editing if an ID is present in the URL
// THIS BLOCK IS CRITICAL and should be outside the POST check
if (isset($_GET['action']) && $_GET['action'] === 'edit' && isset($_GET['id'])) {
    $eventId = $_GET['id'];
    $stmt = $conn->prepare("SELECT * FROM club_events WHERE id = ? AND club_event_id = ?");
    $stmt->bind_param("ii", $eventId, $club_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $editEvent = $result->fetch_assoc();
    $stmt->close();
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Manage Club Events</title>
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

  .form-container, .events-table-container {
      margin-bottom: 40px;
  }

  h2 {
    text-align: center;
    color: #333;
    margin-bottom: 20px;
  }

  label {
    display: block;
    margin-bottom: 6px;
    font-weight: 600;
    color: #555;
  }

  input[type="text"],
  input[type="date"],
  input[type="time"],
  textarea {
    width: 100%;
    padding: 10px 12px;
    margin-bottom: 15px;
    border: 1px solid #ccc;
    border-radius: 4px;
    box-sizing: border-box;
    font-size: 14px;
    transition: border-color 0.3s;
  }

  input[type="text"]:focus,
  input[type="date"]:focus,
  input[type="time"]:focus,
  textarea:focus {
    border-color: #007BFF;
    outline: none;
  }

  textarea {
    resize: vertical;
    min-height: 80px;
  }

  button {
    width: 100%;
    padding: 12px;
    background-color: #9abcd3;
    border: none;
    border-radius: 4px;
    color: #141416;
    font-size: 16px;
    cursor: pointer;
    transition: background-color 0.3s;
  }

  button:hover {
    background-color: #53a4d3;
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
    </style>
</head>
<body>

<div class="container">
    <h2><?php echo $editEvent ? "Edit Event" : "Add New Event"; ?></h2>
    <form action="event_add.php" method="post">
        <?php if ($editEvent): ?>
            <input type="hidden" name="eventId" value="<?php echo htmlspecialchars($editEvent['id']); ?>" />
        <?php endif; ?>

        <label for="eventName">Event Name</label>
        <input type="text" id="eventName" name="eventName" placeholder="Enter event name" value="<?php echo htmlspecialchars($editEvent['event_name'] ?? ''); ?>" required />

        <label for="date">Date</label>
        <input type="date" id="date" name="date" value="<?php echo htmlspecialchars($editEvent['event_date'] ?? ''); ?>" required />

        <label for="time">Time</label>
        <input type="time" id="time" name="time" value="<?php echo htmlspecialchars($editEvent['event_time'] ?? ''); ?>" required />

        <label for="venue">Venue</label>
        <input type="text" id="venue" name="venue" placeholder="Enter venue" value="<?php echo htmlspecialchars($editEvent['venue'] ?? ''); ?>" required />

        <label for="description">Description</label>
        <textarea id="description" name="description" placeholder="Enter a short description" required><?php echo htmlspecialchars($editEvent['description'] ?? ''); ?></textarea>

        <button type="submit"><?php echo $editEvent ? "Update Event" : "Add Event"; ?></button>
    </form>
</div>

</body>
</html>