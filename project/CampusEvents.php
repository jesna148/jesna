<?php session_start(); ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Campus Events</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f2f2f2;
            margin: 0;
            padding: 0;
        }
        .header {
            background: linear-gradient(to right, #4facfe, #00f2fe);
            padding: 20px;
            text-align: center;
            color: white;
        }
        .section {
            padding: 30px;
        }
        .section h2 {
            color: #333;
            margin-bottom: 20px;
        }
        .card {
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
            margin-bottom: 20px;
            position: relative;
        }
        .card h3 {
            margin-top: 0;
        }
        .register-button {
            display: inline-block;
            padding: 10px 15px;
            background-color: #28a745;
            color: white;
            border: none;
            border-radius: 5px;
            text-decoration: none;
            cursor: pointer;
        }
        .register-button:hover {
            background-color: #218838;
        }

        /* Modal */
        .modal {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0; top: 0;
            width: 100%; height: 100%;
            overflow: auto;
            background-color: rgba(0,0,0,0.4);
        }
        .modal-content {
            background-color: #fff;
            margin: 10% auto;
            padding: 20px;
            border-radius: 8px;
            width: 90%;
            max-width: 400px;
        }
        .close {
            color: #aaa;
            float: right;
            font-size: 22px;
            font-weight: bold;
            cursor: pointer;
        }
        .close:hover {
            color: black;
        }
        input[type="text"], input[type="email"] {
            width: 100%;
            padding: 10px;
            margin-top: 10px;
            margin-bottom: 20px;
            border: 1px solid #ccc;
            border-radius: 4px;
        }
        .submit-btn {
            background-color: #007bff;
            color: white;
            padding: 10px 15px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }
        .submit-btn:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>

<div class="header">
    <h1>Campus Events</h1>
    <p>Explore upcoming, ongoing, and past campus events</p>
</div>


<div class="section" id="ongoing-events">
    <h2>Ongoing Events</h2>
    <div class="card">
        <h3>Art Exhibition 2025</h3>
        <p>Explore student artworks in the main auditorium. Running from July 28 to August 3.</p>
        <p><strong>Status:</strong> Ongoing</p>
    </div>
</div>

<div class="section" id="upcoming-events">
    <h2>Upcoming Events</h2>

    <!-- Tech Fest -->
    <div class="card">
        <h3>Tech Fest 2025</h3>
        <p>Join the campus tech carnival from August 10–12 with coding, robotics, and startup expo!</p>
        <p><strong>Status:</strong> Starts on August 10</p>
        <button class="register-button" onclick="openModal('Tech Fest 2025')">Register</button>
    </div>

    <!-- Music Night -->
    <div class="card">
        <h3>Music Night</h3>
        <p>Live band performances, solo singers and DJ night on August 18 at the Open Grounds.</p>
        <p><strong>Status:</strong> Starts on August 18</p>
        <button class="register-button" onclick="openModal('Music Night')">Register</button>
    </div>
</div>

<div class="section" id="finished-events">
    <h2>Finished Events</h2>
    <div class="card">
        <h3>Literary Fest</h3>
        <p>Held on June 15. Included debates, poetry and writing competitions across departments.</p>
        <p><strong>Status:</strong> Completed</p>
    </div>
</div>

<!-- Modal Registration Form -->
<div id="registrationModal" class="modal">
    <div class="modal-content">
        <span class="close" onclick="closeModal()">&times;</span>
        <h3>Register for <span id="eventNameLabel"></span></h3>
        <form action="Event_register.php" method="POST">
            <input type="hidden" name="event" id="eventInput">
            <input type="text" name="name" placeholder="Your Name" required>
            <input type="email" name="email" placeholder="Your Email" required>
            <button type="submit" class="submit-btn">Submit</button>
        </form>
    </div>
</div>

<script>
    function openModal(eventName) {
        document.getElementById('eventNameLabel').innerText = eventName;
        document.getElementById('eventInput').value = eventName;
        document.getElementById('registrationModal').style.display = 'block';
    }

    function closeModal() {
        document.getElementById('registrationModal').style.display = 'none';
    }

    window.onclick = function(event) {
        const modal = document.getElementById('registrationModal');
        if (event.target === modal) {
            closeModal();
        }
    };
</script>

</body>
</html>
