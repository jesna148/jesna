<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Settings</title>
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
      color: #FF9800;
    }

    label {
      display: block;
      margin: 10px 0 5px;
      font-weight: bold;
    }

    input {
      padding: 8px;
      width: 300px;
      border: 1px solid #ccc;
      border-radius: 5px;
    }

    button {
      margin-top: 15px;
      padding: 8px 15px;
      background: #FF9800;
      border: none;
      color: #fff;
      cursor: pointer;
      border-radius: 5px;
      font-size: 16px;
    }

    button:hover {
      background: #E68900;
    }

    form {
      background: #fff;
      padding: 20px;
      border-radius: 8px;
      box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
      width: 400px;
    }
  </style>
</head>
<body>

  <div class="sidebar">
    <h2>Admin Panel</h2>
    <a href="manage_users.php">Manage Users</a>
    <a href="manage_clubs.php">Manage Clubs</a>
    <a href="admin_reg.php">Manage Events</a>
    <a href="report.php">Reports</a>
    <a href="settings.php" class="active">Settings</a>
    <a href="more.php">More</a>
    <div class="logout">
      <a href="logout.php">Logout</a>
    </div>
  </div>

  <div class="main-content">
    <h2>Settings</h2>
    <form method="post">
      <label for="site_name">Website Name</label>
      <input type="text" id="site_name" value="Campus Clubs Admin Panel">

      <label for="admin_email">Admin Email</label>
      <input type="email" id="admin_email" value="admin@campus.com">

      <button type="submit">Save Changes</button>
    </form>
  </div>

</body>
</html>
