<?php session_start(); ?>
<!doctype html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>8BitBrain</title>

    <link rel="stylesheet" href="style.css" />
    <link rel="icon" href="imgs/Sans_Favi.png" type="image/png" />
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link
      href="https://fonts.googleapis.com/css2?family=Bitcount+Single:wght@100..900&display=swap"
      rel="stylesheet"
    />
  </head>

  <body>
    <div class="bg"></div>

    <header class="header">
      <a href="index.php" class="logo">
        8BitBrain
        <img src="imgs/Sans_Favi.png" alt="logo" class="logoimg" />
      </a>
      <?php include("navbar.php"); ?>
    </header>

    <main>
      <section class="modes-section">
        <div class="herostart">
          <h1>Select Mode</h1>
          <button id="timed-btn" class="btn">Timed Quiz</button>
          <button id="ranked-btn" class="btn">Ranked Quiz</button>
          <button id="memory-btn" class="btn">Memory Match</button>
          <button id="endless-btn" class="btn">Endless Quiz</button>
          <button id="single-btn" class="btn">Single Player</button>
        </div>
      </section>
    </main>
    <script src="script.js"></script>
  </body>
</html>
