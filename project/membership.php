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

// Fetch both the club ID and name from the clubs_details table
$sql = "SELECT id, club_name FROM clubs_details ORDER BY club_name ASC";
$result = $conn->query($sql);

$clubs = [];
if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $clubs[] = $row; // Store both ID and name
    }
}
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <title>Registration Form</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: linear-gradient(135deg, #589fbb, #8b52d6);
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }

        .form-container {
            background-color: #fff;
            padding: 50px;
            border-radius: 20px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.2);
            max-width: 600px;
            width: 100%;
            height:90%;
            animation: fadeIn 1s ease-in-out;
        }

        @keyframes fadeIn {
            from {opacity: 0; transform: translateY(-20px);}
            to {opacity: 1; transform: translateY(0);}
        }

        h2 {
            text-align: center;
            margin-bottom: 15px;
            color: #333;
        }

        p {
            text-align: center;
            margin-bottom: 25px;
            color: #555;
        }

        .form-group {
            display: flex;
            align-items: center;
            margin-top: 25px;
        }

        .form-group label {
            width: 150px;
            font-weight: bold;
            margin-right: 10px;
        }

        .form-group input[type=text],
        .form-group input[type=email],
        .form-group input[type=number],
        .form-group input[type=date],
        .form-group select,
        .form-group textarea {
            flex: 1;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 6px;
            transition: border-color 0.3s, box-shadow 0.3s;
        }

        input[type=text]:focus,
        input[type=email]:focus,
        input[type=number]:focus,
        input[type=date]:focus,
        select:focus,
        textarea:focus {
            border-color: #007bff;
            box-shadow: 0 0 8px rgba(0,123,255,0.2);
            outline: none;
        }
        .gender-label {
            width: 150px;
            font-weight: bold;
            margin-right: 10px;
            margin-top: 15px;
        }

        .gender-options {
            display: inline;
            gap: 10px;
            margin-top: 5px;
        }

        .button-container {
            display: flex;
            justify-content: center;
            margin-top: 25px;
        }

        button {
            width: 150px;
            padding: 12px;
            background-color: #9abcd3;
            color: #fff;
            border: none;
            border-radius: 8px;
            font-size: 16px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        button:hover {
            background-color: #53a4d3;
        }

        #clubType {
            background-color: #f2f3b8;
        }
    </style>
</head>
<body>

<div class="form-container">
    <h2>Registration Form</h2>
    <p>Fill out the form carefully for registration</p>
    <form action="membership_submit.php" method="POST">
        <div class="form-group">
            <label for="fname">First Name</label>
            <input type="text" id="fname" name="fname" placeholder="Enter your first name" required />
        </div>
        <div class="form-group">
            <label for="lname">Last Name</label>
            <input type="text" id="lname" name="lname" placeholder="Enter your last name" required />
        </div>
        <div class="form-group">
            <div class="gender-label">Gender</div>
            <div class="gender-options">
                <label><input type="radio" name="gender" value="Male" /> Male</label>
                <label><input type="radio" name="gender" value="Female" /> Female</label>
                <label><input type="radio" name="gender" value="Other" /> Other</label>
            </div>
        </div>

        <div class="form-group">
            <label for="contact">Phn no or Email ID</label>
            <input type="text" id="contact" name="contact" placeholder="Enter phone or email" required />
        </div>
        <div class="form-group">
            <label for="dob">Date of Birth</label>
            <input type="date" id="dob" name="dob" required />
        </div>
        <div class="form-group">
            <label for="studentID">Student ID</label>
            <input type="text" id="studentID" name="studentID" placeholder="Enter your student ID" required />
        </div>
        <div class="form-group">
            <label for="department">Department / Class</label>
            <select id="department" name="department" required>
                <option value="">Please Select</option>
                <option value="BCA">BCA</option>
                <option value="BBA">BBA</option>
                <option value="BSW">BSW</option>
                <option value="BCOM">BCOM</option>
                <option value="BACE">BACE</option>
            </select>
        </div>
        <div class="form-group">
            <label for="clubType">Type of Club Membership</label>
            <select id="clubType" name="clubType" required>
                <option value="">Please Select</option>
                <?php foreach ($clubs as $club): ?>
                    <option value="<?php echo htmlspecialchars($club['id']); ?>">
                        <?php echo htmlspecialchars($club['club_name']); ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="button-container">
            <button type="submit">Submit</button>
        </div>
    </form>
</div>
</body>
</html>