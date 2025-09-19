<?php
session_start();
$conn = new mysqli("localhost", "root", "", "project");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if (isset($_POST['user_action']) && isset($_POST['user_id'])) {
    $user_id = $_POST['user_id'];
    $action = $_POST['user_action'];
    $status = ($action === 'approve') ? 1 : 0;
    $conn->query("UPDATE users SET is_approved=$status WHERE id=$user_id");
}

if (isset($_POST['event_action']) && isset($_POST['event_id'])) {
    $event_id = $_POST['event_id'];
    $action = $_POST['event_action'];
    $status = ($action === 'accept') ? 'accepted' : 'rejected';
    $conn->query("UPDATE event_registrations SET status='$status' WHERE id=$event_id");
}

$event_sql = "SELECT er.id, er.student_email, er.event_id, e.title AS event_name, er.registered_on, er.status
             FROM event_registrations er
             JOIN events e ON er.event_id = e.id
             ORDER BY er.registered_on DESC";
$event_result = $conn->query($event_sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Panel - Approvals</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: Arial, sans-serif;
        }

        body {
            display: flex;
            background-color: #f4f6f9;
        }

.sidebar {
    width: 250px;
    height: 100vh;
    background-color: #2c3e50;
    color: #fff;
    position: fixed;
    top: 0;
    left: 0;
    padding-top: 20px;
    box-shadow: 2px 0 8px rgba(0, 0, 0, 0.2);
}

.sidebar h2 {
    text-align: center;
    margin-bottom: 20px;
    color: #fff;
    font-size: 22px;
    border-bottom: 2px solid #34495e;
    padding-bottom: 10px;
}

.sidebar a {
    display: block;
    padding: 12px 20px;
    color: #ecf0f1;
    text-decoration: none;
    font-size: 16px;
    transition: 0.3s;
}

.sidebar a:hover {
    background-color: #34495e;
    padding-left: 25px;
}

.sidebar a.active {
    background-color: #1abc9c;
    color: white;
    font-weight: bold;
    border-left: 5px solid #2ecc71;
}

.sidebar .logout {
    margin-top: 30px;
    text-align: center;
}

.sidebar .logout a {
    background-color: #e74c3c;
    color: white;
    padding: 10px 15px;
    display: inline-block;
    border-radius: 5px;
    text-decoration: none;
    transition: 0.3s;
}

.sidebar .logout a:hover {
    background-color: #c0392b;
}


        .main-content {
            margin-left: 250px;
            padding: 20px;
            width: calc(100% - 250px);
        }

        h1, h2 {
            color: #2c3e50;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
            background: #fff;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }

        th, td {
            border: 1px solid #ccc;
            padding: 10px;
            text-align: center;
        }

        th {
            background-color: #2c3e50;
            color: white;
        }

        .btn {
            padding: 5px 10px;
            border: none;
            cursor: pointer;
            color: white;
            border-radius: 4px;
        }

        .approve { background-color: #28a745; }
        .reject { background-color: #dc3545; }
        .accept { background-color: #007bff; }

        .pending { color: orange; font-weight: bold; }
        .approved { color: green; font-weight: bold; }
        .rejected { color: red; font-weight: bold; }
        .accepted { color: green; font-weight: bold; }
    </style>
</head>
<body>

    <div class="sidebar">
        <h2>Admin Panel</h2>
        <a href="manage_users.php">Manage Users</a>
        <a href="manage_clubs.php">Manage Clubs</a>
        <a href="admin_reg.php" class="active">Manage Events</a>
        <a href="report.php">Reports</a>
        <a href="settings.php">Settings</a>
        <a href="more.php">More</a>
        <div class="logout">
            <a href="logout.php">Logout</a>
        </div>
    </div>

    <div class="main-content">
        <h1>Event Registration Approvals</h1>

        <h2>Event Registrations</h2>
        <table>
            <tr>
                <th>ID</th>
                <th>Student Email</th>
                <th>Event</th>
                <th>Registered On</th>
                <th>Status</th>
                <th>Action</th>
            </tr>
            <?php while ($row = $event_result->fetch_assoc()) { ?>
                <tr>
                    <td><?= $row['id'] ?></td>
                    <td><?= $row['student_email'] ?></td>
                    <td><?= $row['event_name'] ?></td>
                    <td><?= $row['registered_on'] ?></td>
                    <td class="<?= $row['status'] ?>"><?= ucfirst($row['status']) ?></td>
                    <td>
                        <?php if ($row['status'] === 'pending') { ?>
                            <form method="POST" style="display:inline;">
                                <input type="hidden" name="event_id" value="<?= $row['id'] ?>">
                                <button type="submit" name="event_action" value="accept" class="btn accept">Accept</button>
                            </form>
                            <form method="POST" style="display:inline;">
                                <input type="hidden" name="event_id" value="<?= $row['id'] ?>">
                                <button type="submit" name="event_action" value="reject" class="btn reject">Reject</button>
                            </form>
                        <?php } else { echo "—"; } ?>
                    </td>
                </tr>
            <?php } ?>
        </table>
    </div>
</body>
</html>
