<?php
// Start the session to access the user's login information
session_start();

include 'conn.php';

// Check if the user is logged in and their ID is in the session
if (!isset($_SESSION['user_id'])) {
    // If not logged in, redirect to the login page
    header("Location: login.php");
    exit();
}

$leaderUserId = $_SESSION['user_id'];

// Step 1: Get the club_id for the logged-in club leader from the users table
$stmt = $conn->prepare("SELECT club_id FROM users WHERE id = ?");
$stmt->bind_param("i", $leaderUserId);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $leaderClubId = $row['club_id'];
} else {
    die("Error: Club leader not assigned to a club.");
}
$stmt->close();

// Step 2: Modify the SQL query to filter by the leader's club_id
$sql = "SELECT m.id, m.first_name, m.last_name, m.gender, m.contact, m.dob, m.student_id, m.department, c.club_name
        FROM membership m
        INNER JOIN clubs_details c ON m.club_id = c.id
        WHERE m.club_id = ?
        ORDER BY m.id ASC";

// Step 3: Use a prepared statement to securely fetch the filtered data
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $leaderClubId);
$stmt->execute();
$result = $stmt->get_result();

if (!$result) {
    die("Query failed: " . $conn->error);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8" />
<title>My Club Members</title>
<style>
body {
    font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
    background-color: #f8f9fa;
    margin: 20px;
    padding: 0;
    color: #333;
}

h2 {
    font-size: 24px;
    font-weight: 600;
    margin-bottom: 20px;
    text-align: center;
    color: #222;
}

.table-container {
    width: 100%;
    overflow-x: auto;
    margin-bottom: 30px;
}

table {
    width: 100%;
    border-collapse: collapse;
    min-width: 800px;
    background-color: #fff;
    box-shadow: 0 2px 8px rgba(0,0,0,0.05);
}

thead {
    background-color: #e9ecef;
}

th {
    padding: 12px 15px;
    text-align: left;
    font-weight: 600;
    font-size: 14px;
    border-bottom: 2px solid #dee2e6;
    background-color: bisque;
}

td {
    padding: 12px 15px;
    border-bottom: 1px solid #dee2e6;
    font-size: 14px;
}

tr:hover {
    background-color: #f1f3f5;
}

@media(max-width: 768px) {
    table {
        min-width: 100%;
    }
}
</style>
</head>
<body>
<h2>Members of My Club</h2>
<div class="table-container">
<table border="1" cellpadding="10" cellspacing="0">
<tr>
    <th>ID</th>
    <th>First Name</th>
    <th>Last Name</th>
    <th>Gender</th>
    <th>Contact</th>
    <th>Date of Birth</th>
    <th>Student ID</th>
    <th>Department</th>
</tr>
<?php
if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        echo "<tr>
                <td>" . htmlspecialchars($row['id']) . "</td>
                <td>" . htmlspecialchars($row['first_name']) . "</td>
                <td>" . htmlspecialchars($row['last_name']) . "</td>
                <td>" . htmlspecialchars($row['gender']) . "</td>
                <td>" . htmlspecialchars($row['contact']) . "</td>
                <td>" . htmlspecialchars($row['dob']) . "</td>
                <td>" . htmlspecialchars($row['student_id']) . "</td>
                <td>" . htmlspecialchars($row['department']) . "</td>
              </tr>";
    }
} else {
    echo "<tr><td colspan='8' style='text-align:center;'>No members found for this club yet.</td></tr>";
}
?>
</table>
</div>
</body>
</html>

<?php
$conn->close();
?>