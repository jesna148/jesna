<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Campus Clubs</title>
   <style>
    * {
      box-sizing: border-box;
    }

    body {
      font-family: 'Poppins', sans-serif;
      background: linear-gradient(to right, #f8f9fa, #e0eafc);
      margin: 0;
      padding: 0;
    }
    h2{
      text-align: center;
      color:#2575fc;
    }

    .header {
      background: linear-gradient(135deg, #fff, #2575fc);
      color: white;
      text-align: center;
      padding: 40px 20px;
      box-shadow: 0 4px 12px rgba(0,0,0,0.2);
    }

    .header h1 {
      margin: 0;
      font-size: 2.8em;
      letter-spacing: 1px;
    }

    @keyframes scroll {
      0% {
      transform: translateX(0);
    }
      100% {
      transform: translateX(-50%);
    }
  }

    .clubs-container {
     display: flex;
     flex-wrap: nowrap;  
     justify-content: flex-start;
     padding: 50px 20px;
     gap: 30px;
     animation: scroll 30s linear infinite;
     will-change: transform;
     width: fit-content;
    }
    .clubs-container:hover {
     animation-play-state: paused;
  }

    .club-card {
      background: white;
      border-radius: 18px;
      width: 250px;
      overflow: hidden;
      box-shadow: 0 8px 20px rgba(0,0,0,0.1);
      transition: transform 0.3s ease, box-shadow 0.3s ease;
    }

    .club-card:hover {
      transform: translateY(-8px);
      box-shadow: 0 12px 25px rgba(0,0,0,0.2);
    }

    .club-card img {
      width: 100%;
      height: 200px;
      object-fit:cover;
    }

    .club-card h3 {
      margin: 15px 0 10px;
      font-size: 1.3em;
      color: #34495e;
      text-align: center;
    }

    .club-card p {
      color: #666;
      padding: 0 20px 20px;
      font-size: 0.95em;
      text-align: center;
    }

    /*.join-button {
      text-align: center;
      background: linear-gradient(135deg, #00c9ff, #92fe9d);
      color: #fff;
      font-weight: 600;
      padding: 10px 24px;
      margin-bottom: 20px;
      border-radius: 30px;
      text-decoration: none;
      transition: background 0.3s;
       
    }*/

   /* .join-button:hover {
      background: linear-gradient(135deg, #00b4db, #39e29d);
    }*/
  </style>
  </head>
<body>
<div class="header">
  <h1>Campus Clubs</h1>
</div>
<div>
<h2>"Join Our Dynamic Campus Clubs and Make the Most of Your College Life"</h2>
</div>
<div style="overflow: hidden;width: 100%;">
<div class="clubs-container" id="clubs-container" style="display: flex; gap: 30px; white-space: nowrap;" >
  <div class="club-card">
    <img src="workshop.jpg" alt="Literary Club">
    <h3>Literary Club</h3>
    <p>A space for poetry, books, and creative writing.</p>
  </div>

  <div class="club-card">
    <img src="music.jpg" alt="Music Club">
    <h3>Music Club</h3>
    <p>Explore instruments, vocals, and stage performances.</p>
  </div>

  <div class="club-card">
    <img src="tech.jpg" alt="Tech Club">
    <h3>Tech Club</h3>
    <p>For coders, inventors, and technology lovers.</p>
  </div>

  <div class="club-card">
    <img src="uploads/arts.jpg" alt="Art Club">
    <h3>Art Club</h3>
    <p>Unleash your creativity through painting, sketching, and crafts.</p>
  </div>

  <div class="club-card">
    <img src="uploads/arts.jpg" alt="Drama Club">
    <h3>Drama Club</h3>
    <p>Acting, theatre, and stage presence — perfect for performers.</p>
  </div>

  <div class="club-card">
    <img src="uploads/football.jpg" alt="Sports Club">
    <h3>Sports Club</h3>
    <p>Promoting fitness, sportsmanship, and healthy competition.</p>
  </div>

  <div class="club-card">
    <img src="trekking.jpg" alt="Photography Club">
    <h3>Photography Club</h3>
    <p>Capture moments, learn editing, and explore visual storytelling.</p>
  </div>

  <div class="club-card">
    <img src="plant.jpg" alt="Environment Club">
    <h3>Environment Club</h3>
    <p>Raise awareness, go green, and lead eco-friendly initiatives.</p>
  </div>
</div>

<script>
const container = document.getElementById('clubs-container');
const originalContent = container.innerHTML;
container.innerHTML += originalContent;

let scrollSpeed = 0.5;  
let currentScroll = 0;

function scroll() {
  currentScroll += scrollSpeed;
  if (currentScroll >= container.scrollWidth / 2) {
    currentScroll = 0;  
  }
  container.scrollLeft = currentScroll;
  requestAnimationFrame(scroll);
}
scroll();
</script>
</script>
</body>
</html>
    

  
 
