<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Reports</title>
  <style>
    body {
      font-family: Arial, sans-serif;
      margin: 0;
      background-color: #f4f6f8;
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

    .main-content {
      margin-left: 250px;
      padding: 20px;
      width: calc(100% - 250px);
    }

    h2 {
      color: #9C27B0;
    }

    .report-box {
      background: #fff;
      padding: 20px;
      border-radius: 8px;
      margin: 15px 0;
      box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
    }

    .report-box h3 {
      margin: 0 0 10px;
      color: #2c3e50;
    }

    .report-box p {
      margin: 5px 0;
      font-size: 16px;
      color: #333;
    }
  </style>
</head>
<body>

    <div class="sidebar">
        <h2>Admin Panel</h2>
        <a href="manage_users.php">Manage Users</a>
        <a href="manage_clubs.php">Manage Clubs</a>
        <a href="admin_reg.php" >Manage Events</a>
        <a href="report.php" class="active">Reports</a>
        <a href="settings.php">Settings</a>
        <a href="more.php">More</a>
        <div class="logout">
            <a href="logout.php">Logout</a>
        </div>
    </div>

  <div class="main-content">
    <h2>Reports</h2>

    <div class="report-box">
      <h3>📊 User Report</h3>
      <p>Total Users: 150</p>
    </div>

    <div class="report-box">
      <h3>📊 Clubs Report</h3>
      <p>Total Clubs: 12</p>
    </div>

    <div class="report-box">
      <h3>📊 Events Report</h3>
      <p>Upcoming Events: 5</p>
    </div>
  </div>

</body>
</html>
