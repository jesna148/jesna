<?php
session_start();

// Simple check to ensure only a valid admin can access this page
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header('Location: login.php');
    exit();
}

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "project";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Handle approval/denial actions
if (isset($_GET['action']) && isset($_GET['id'])) {
    $user_id = $_GET['id'];
    $action = $_GET['action'];
    
    if ($action === 'approve') {
       // Step 1: Update user status
       $stmt_user = $conn->prepare("UPDATE users SET status = 'approved', is_approved = 1 WHERE id = ?");
       $stmt_user->bind_param("i", $user_id);
       $stmt_user->execute();
       $stmt_user->close();

       // Step 2: Get the club_id from the user's profile
       $stmt_get_club_id = $conn->prepare("SELECT club_id FROM users WHERE id = ?");
       $stmt_get_club_id->bind_param("i", $user_id);
       $stmt_get_club_id->execute();
       $result_club = $stmt_get_club_id->get_result();
       $user_data = $result_club->fetch_assoc();
       $club_id = $user_data['club_id'];
       $stmt_get_club_id->close();

       // Step 3: Update the clubs_details table to assign the leader
       if ($club_id) { // Only run if a club was selected during signup
           $stmt_update_club = $conn->prepare("UPDATE clubs_details SET leader_id = ? WHERE id = ?");
           $stmt_update_club->bind_param("ii", $user_id, $club_id);
           $stmt_update_club->execute();
           $stmt_update_club->close();
       }
       
       echo "<script>alert('User approved and club assigned successfully!'); window.location='admin-panel-demo.php';</script>";
       exit();
    } elseif ($action === 'deny') {
        $stmt = $conn->prepare("UPDATE users SET status = 'rejected',is_approved = 0 WHERE id = ?");
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        echo "<script>alert('User denied!'); window.location='admin-panel-demo.php';</script>";
        exit();
    }
}

// Fetch a list of all unapproved club leaders
$stmt_pending_leaders = $conn->prepare("SELECT id, fullname, email ,role,status FROM users WHERE  status='pending' AND role = 'club_leader'");
$stmt_pending_leaders->execute();
$result_pending_leaders = $stmt_pending_leaders->get_result();
$pending_leaders = $result_pending_leaders->fetch_all(MYSQLI_ASSOC);
$stmt_pending_leaders->close();

// Fetch a list of all unassigned club leaders
$stmt_unassigned_leaders = $conn->prepare("SELECT id, fullname FROM users WHERE role = 'club_leader' AND club_id IS NULL AND status = 'approved'");
$stmt_unassigned_leaders->execute();
$result_unassigned_leaders = $stmt_unassigned_leaders->get_result();
$unassigned_leaders = $result_unassigned_leaders->fetch_all(MYSQLI_ASSOC);
$stmt_unassigned_leaders->close();

// Fetch a list of all unassigned clubs
$stmt_unassigned_clubs = $conn->prepare("SELECT id, club_name FROM clubs_details WHERE leader_id IS NULL");
$stmt_unassigned_clubs->execute();
$result_unassigned_clubs = $stmt_unassigned_clubs->get_result();
$unassigned_clubs = $result_unassigned_clubs->fetch_all(MYSQLI_ASSOC);
$stmt_unassigned_clubs->close();

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel - Pending Approvals</title>
    <style>
        body { font-family: Arial, sans-serif; padding: 20px; }
        .container { max-width: 900px; margin: auto; }
        h2 { border-bottom: 2px solid #ccc; padding-bottom: 10px; margin-top: 40px; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { padding: 10px; border: 1px solid #ccc; text-align: left; }
        th { background-color: #f2f2f2; }
        .approve-btn, .deny-btn, .assign-btn {
            background-color: #4CAF50;
            color: white;
            border: none;
            padding: 8px 12px;
            cursor: pointer;
        }
        .deny-btn { background-color: #f44336; }
        .assignment-form {
            background: #f9f9f9;
            padding: 20px;
            border: 1px solid #ddd;
            border-radius: 8px;
            margin-top: 20px;
        }
        .form-group { margin-bottom: 15px; }
        label { font-weight: bold; }
        select { width: 100%; padding: 8px; border: 1px solid #ccc; border-radius: 4px; }
        .assign-btn { background-color: #007bff; }
    </style>
</head>
<body>

    <div class="container">
        <h2>Admin Panel: Pending Club Leader Approvals</h2>

        <?php if (empty($pending_leaders)): ?>
            <p>No new club leaders pending approval. 🎉</p>
        <?php else: ?>
            <table>
                <thead>
                    <tr>
                        <th>Full Name</th>
                        <th>Email</th>
                        <th>Role</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($pending_leaders as $user): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($user['fullname']); ?></td>
                            <td><?php echo htmlspecialchars($user['email']); ?></td>
                            <td>Club Leader</td>
                            <td>
                                <a href="admin-panel-demo.php?action=approve&id=<?php echo $user['id']; ?>">
                                    <button class="approve-btn">Approve</button>
                                </a>
                                <a href="admin-panel-demo.php?action=deny&id=<?php echo $user['id']; ?>">
                                    <button class="deny-btn">Deny</button>
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
        <div style="text-align: center; margin-top: 20px;">
    <a href="create_club.php">
        <button style="padding: 10px 20px; font-size: 16px; background-color: #007bff; color: white; border: none; border-radius: 5px; cursor: pointer;">
            Create New Club
        </button>
    </a>
</div>

        <h2>Assign Club Leader to a Club</h2>
        <div class="assignment-form">
            <form action="updated_assign_leader.php" method="POST">
                <div class="form-group">
                    <label for="leader_id">Select Club Leader:</label>
                    <select name="leader_id" id="leader_id" required>
                        <option value="">-- Select a Leader --</option>
                        <?php foreach ($unassigned_leaders as $leader): ?>
                            <option value="<?php echo htmlspecialchars($leader['id']); ?>">
                                <?php echo htmlspecialchars($leader['fullname']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label for="club_id">Select Club:</label>
                    <select name="club_id" id="club_id" required>
                        <option value="">-- Select a Club --</option>
                        <?php foreach ($unassigned_clubs as $club): ?>
                            <option value="<?php echo htmlspecialchars($club['id']); ?>">
                                <?php echo htmlspecialchars($club['club_name']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <button type="submit" class="assign-btn">Assign Club</button>
            </form>
        </div>
    </div>
</body>
</html>
 