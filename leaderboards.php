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
      <section class="leaderboards-section">
        <h1>Leaderboards</h1>
        <p>Welcome to the 8BitBrain Leaderboards! Top 50 scores are shown below.</p>

        <div class="leaderboards-filter">
          <label for="modeSelect">Mode:</label>
          <select id="modeSelect">
            <option value="">All</option>
            <option value="single">Single Player</option>
            <option value="timed">Timed Quiz</option>
            <option value="endless">Endless Quiz</option>
            <option value="multiplayer">Multiplayer</option>
            <option value="memory">Memory Match</option>
            <option value="ranked">Ranked Quiz</option>
          </select>
        </div>

        <table id="leaderboardTable" class="leaderboard-table">
          <thead>
            <tr>
              <th>#</th>
              <th>Player</th>
              <th>Mode</th>
              <th>Score</th>
              <th>Correct/Total</th>
              <th>Date</th>
            </tr>
          </thead>
          <tbody></tbody>
        </table>

        <div id="leaderboardEmpty" class="message" style="display:none;">No leaderboard entries yet.</div>
      </section>
    </main>
    <script src="script.js"></script>
  </body>
</html>
