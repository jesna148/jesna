<?php 
session_start();
if (!isset($_SESSION['email']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php"); 
    exit();
}

$conn = new mysqli('localhost', 'root', '', 'project');

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    $conn->query("DELETE FROM users WHERE id=$id");
    echo "<script>window.location='manage_users.php';</script>";
}

if (isset($_GET['role']) && isset($_GET['id'])) {
    $id = intval($_GET['id']);
    $newRole = $_GET['role'];
    $conn->query("UPDATE users SET role='$newRole' WHERE id=$id");
    echo "<script>window.location='manage_users.php';</script>";
}

if (isset($_GET['approve'])) {
    $id = intval($_GET['approve']);
    $conn->query("UPDATE users SET is_approved=1 WHERE id=$id");
    echo "<script>window.location='manage_users.php';</script>";
}

if (isset($_GET['reject'])) {
    $id = intval($_GET['reject']);
    $conn->query("UPDATE users SET is_approved=0 WHERE id=$id");
    echo "<script>window.location='manage_users.php';</script>";
}

$result = $conn->query("SELECT * FROM users ORDER BY id ASC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Users - Admin Panel</title>
    <style>
        body {
            margin: 0;
            padding: 0;
            font-family: Arial, sans-serif;
            background: #f0f2f5;
            display: flex;
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

        .content {
            margin-left: 270px;
            padding: 20px;
            width: 100%;
        }

        h2 {
            text-align: center;
        }

        table {
            width: 95%;
            margin: 20px auto;
            border-collapse: collapse;
            background: #fff;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }

        th, td {
            padding: 12px;
            border: 1px solid #ccc;
            text-align: center;
        }

        th {
            background-color: #4CAF50;
            color: white;
        }

        .action-btn {
            padding: 6px 12px;
            margin: 2px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            color: white;
        }

        .delete-btn { background-color: #e74c3c; }
        .role-btn { background-color: #3498db; }
        .approve-btn { background-color: #2ecc71; }
        .reject-btn { background-color: #e67e22; }
        .pending-status { color: orange; font-weight: bold; }
        .approved-status { color: green; font-weight: bold; }
    </style>
</head>
<body>
    <div class="sidebar">
        <h2>Admin Panel</h2>
        <a href="manage_users.php" class="active">Manage Users</a>
        <a href="manage_clubs.php">Manage Clubs</a>
        <a href="admin_reg.php">Manage Events</a>
        <a href="report.php">Reports</a>
        <a href="settings.php">Settings</a>
        <a href="more.php">More</a>
        <div class="logout">
            <a href="logout.php">Logout</a>
        </div>
    </div>

    <div class="content">
        <h2>Manage Users</h2>

        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Full Name</th>
                    <th>Email</th>
                    <th>Role</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
            <?php while ($row = $result->fetch_assoc()) { ?>
                <tr>
                    <td><?= $row['id'] ?></td>
                    <td><?= htmlspecialchars($row['fullname']) ?></td>
                    <td><?= htmlspecialchars($row['email']) ?></td>
                    <td><?= ucfirst($row['role']) ?></td>
                    <td>
                        <?php if ($row['is_approved'] == 1) { ?>
                            <span class="approved-status">Approved</span>
                        <?php } else { ?>
                            <span class="pending-status">Pending</span>
                        <?php } ?>
                    </td>
                    <td>
    <?php if ($row['role'] !== 'admin') { ?>
        <a href="?delete=<?= $row['id'] ?>" onclick="return confirm('Are you sure?')">
            <button class="action-btn delete-btn">Delete</button>
        </a>

        <?php if ($row['role'] != 'club_leader') { ?>
            <a href="?role=club_leader&id=<?= $row['id'] ?>">
                <button class="action-btn role-btn">Make Club Leader</button>
            </a>
        <?php } else { ?>
            <a href="?role=student&id=<?= $row['id'] ?>">
                <button class="action-btn role-btn">Demote to Student</button>
            </a>
        <?php } ?>

        <?php if ($row['is_approved'] == 0) { ?>
            <a href="?approve=<?= $row['id'] ?>">
                <button class="action-btn approve-btn">Approve</button>
            </a>
        <?php } else { ?>
            <a href="?reject=<?= $row['id'] ?>">
                <button class="action-btn reject-btn">Reject</button>
            </a>
        <?php } ?>
    <?php } else { ?>
        <span class="approved-status">Admin</span>
    <?php } ?>
</td>

                </tr>
            <?php } ?>
            </tbody>
        </table>
    </div>
</body>
</html>

<?php $conn->close(); ?>