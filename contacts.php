<?php session_start(); ?>
<!doctype html>
<html lang="en">
  <head>
    <meta charset="UTF-8" /><meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>8BitBrain - Contact</title>
    <link rel="stylesheet" href="style.css" />
    <link rel="icon" href="imgs/Sans_Favi.png" type="image/png" />
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=Bitcount+Single:wght@100..900&display=swap" rel="stylesheet" />
  </head>
  <body>
    <div class="bg"></div>
    <header class="header">
      <a href="index.php" class="logo">8BitBrain<img src="imgs/Sans_Favi.png" alt="logo" class="logoimg" /></a>
      <?php include("navbar.php"); ?>
    </header>
    <main>
      <section class="contacts-section">
        <div class="member-left">
          <img src="imgs/placeholder.jpg" alt="Hanz Christian Galgana" />
          <div class="member-info"><h3>Hanz Christian Galgana</h3><p>Front-end Developer</p></div>
        </div>
        <div class="member-right">
          <img src="imgs/placeholder.jpg" alt="Karl Lawrence Pacia" />
          <div class="member-info"><h3>Karl Lawrence Pacia</h3><p>UI/UX Designer</p></div>
        </div>
        <div class="member-left">
          <img src="imgs/placeholder.jpg" alt="Luis Magluyan Garcia" />
          <div class="member-info"><h3>Luis Magluyan Garcia</h3><p>Back-end Developer</p></div>
        </div>
        <div class="member-right">
          <img src="imgs/placeholder.jpg" alt="Lance Jasper Porciuncula" />
          <div class="member-info"><h3>Lance Jasper Porciuncula</h3><p>QA Engineer</p></div>
        </div>
        <div class="member-left">
          <img src="imgs/placeholder.jpg" alt="Angela Nichole Bairan" />
          <div class="member-info"><h3>Angela Nichole Bairan</h3><p>Database Administrator</p></div>
        </div>
      </section>
    </main>
    <script src="script.js"></script>
  </body>
</html>
