<?php
session_start();

// Check for admin role
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header('Location: login.php');
    exit();
}

// Check for POST request and required data
if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_POST['leader_id']) || !isset($_POST['club_id'])) {
    header('Location: admin-panel-demo.php');
    exit();
}

$leader_id = intval($_POST['leader_id']);
$club_id = intval($_POST['club_id']);

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "project";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Start a transaction to ensure both updates succeed or fail together
$conn->begin_transaction();

try {
    // 1. Update the user's club_id
    $stmt_user = $conn->prepare("UPDATE users SET club_id = ? WHERE id = ? AND role = 'club_leader' AND club_id IS NULL");
    $stmt_user->bind_param("ii", $club_id, $leader_id);
    $stmt_user->execute();

    if ($stmt_user->affected_rows === 0) {
        throw new Exception("User update failed. Leader may already be assigned or not valid.");
    }
    $stmt_user->close();

    // 2. Update the club's leader_id
    $stmt_club = $conn->prepare("UPDATE clubs_details SET leader_id = ? WHERE id = ? AND leader_id IS NULL");
    $stmt_club->bind_param("ii", $leader_id, $club_id);
    $stmt_club->execute();

    if ($stmt_club->affected_rows === 0) {
        throw new Exception("Club update failed. Club may already have a leader.");
    }
    $stmt_club->close();

    $conn->commit();
    $_SESSION['message'] = "Club leader assigned successfully!";

} catch (Exception $e) {
    $conn->rollback();
    $_SESSION['message'] = "Assignment failed: " . $e->getMessage();
}

$conn->close();
header('Location: admin-panel-demo.php');
exit();
