<?php session_start(); ?>
<!doctype html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>8BitBrain - Leaderboards</title>
    <link rel="stylesheet" href="style.css" />
    <link rel="icon" href="imgs/Sans_Favi.png" type="image/png" />
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=Bitcount+Single:wght@100..900&display=swap" rel="stylesheet" />
    <style>
      .leaderboards-section {
        padding: 100px 30px 60px;
        color: #fff;
        max-width: 1000px;
        margin: 0 auto;
      }

      .leaderboards-section h1 {
        text-align: center;
        font-size: 48px;
        text-shadow: #f70606 3px 3px;
        margin-bottom: 10px;
      }

      .leaderboards-section > p {
        text-align: center;
        color: rgba(255,255,255,.7);
        margin-bottom: 30px;
      }

      .leaderboards-filter {
        display: flex;
        justify-content: center;
        align-items: center;
        gap: 12px;
        margin-bottom: 30px;
        flex-wrap: wrap;
      }

      .leaderboards-filter label { font-size: 16px; }

      .leaderboards-filter select {
        background: rgba(0,0,0,.5);
        border: 1px solid rgba(255,47,179,.4);
        color: #fff;
        padding: 8px 14px;
        border-radius: 8px;
        font-family: inherit;
        font-size: 15px;
        cursor: pointer;
      }

      .leaderboard-table {
        width: 100%;
        border-collapse: collapse;
        background: rgba(0,0,0,.3);
        border-radius: 12px;
        overflow: hidden;
        backdrop-filter: blur(10px);
        border: 1px solid rgba(255,47,179,.2);
      }

      .leaderboard-table thead {
        background: linear-gradient(135deg, rgba(255,47,179,.3), rgba(168,85,247,.3));
      }

      .leaderboard-table th {
        padding: 14px 16px;
        text-align: left;
        font-size: 13px;
        text-transform: uppercase;
        letter-spacing: 1px;
        color: #ff2fb3;
        border-bottom: 1px solid rgba(255,47,179,.3);
      }

      .leaderboard-table td {
        padding: 13px 16px;
        font-size: 15px;
        border-bottom: 1px solid rgba(255,255,255,.05);
        vertical-align: middle;
      }

      .leaderboard-table tbody tr:hover {
        background: rgba(255,47,179,.07);
      }

      /* Rank badges */
      .rank-badge {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 32px;
        height: 32px;
        border-radius: 50%;
        font-weight: bold;
        font-size: 14px;
      }

      .rank-1 { background: #ffd700; color: #000; }
      .rank-2 { background: #c0c0c0; color: #000; }
      .rank-3 { background: #cd7f32; color: #000; }
      .rank-other { background: rgba(255,255,255,.1); color: #fff; }

      /* Points highlight */
      .points-cell {
        font-size: 18px;
        font-weight: bold;
        color: #ff2fb3;
        text-shadow: 0 0 8px rgba(255,47,179,.5);
      }

      .accuracy-cell { color: #4ade80; }

      .message {
        text-align: center;
        padding: 60px 20px;
        color: rgba(255,255,255,.5);
        font-size: 18px;
      }

      .loading { text-align: center; padding: 40px; color: rgba(255,255,255,.6); }
    </style>
  </head>

  <body>
    <div class="bg"></div>
    <header class="header">
      <a href="index.php" class="logo">
        8BitBrain <img src="imgs/Sans_Favi.png" alt="logo" class="logoimg" />
      </a>
      <?php include("navbar.php"); ?>
    </header>

    <main>
      <section class="leaderboards-section">
        <h1>🏆 Leaderboards</h1>
        <p>Top players ranked by total accumulated points</p>

        <div class="leaderboards-filter">
          <label for="modeSelect">Filter by Mode:</label>
          <select id="modeSelect">
            <option value="">All Modes</option>
            <option value="single_player">Single Player</option>
            <option value="timed_quiz">Timed Quiz</option>
            <option value="ranked_quiz">Ranked Quiz</option>
            <option value="memory_match">Memory Match</option>
            <option value="endless_quiz">Endless Quiz</option>
          </select>
        </div>

        <table id="leaderboardTable" class="leaderboard-table">
          <thead>
            <tr>
              <th>Rank</th>
              <th>Player</th>
              <th>Total Points</th>
              <th>Correct / Total</th>
              <th>Accuracy</th>
              <th>Attempts</th>
            </tr>
          </thead>
          <tbody id="leaderboardBody">
            <tr><td colspan="6" class="loading">Loading...</td></tr>
          </tbody>
        </table>

        <div id="leaderboardEmpty" class="message" style="display:none">
          No scores yet — be the first to play!
        </div>
      </section>
    </main>

    <script src="script.js"></script>
    <script>
      async function loadLeaderboard(mode = '') {
        const body  = document.getElementById('leaderboardBody');
        const empty = document.getElementById('leaderboardEmpty');
        body.innerHTML = '<tr><td colspan="6" class="loading">Loading...</td></tr>';
        empty.style.display = 'none';

        const url = mode
          ? `api/get_leaderboard.php?mode=${encodeURIComponent(mode)}`
          : 'api/get_leaderboard.php';

        try {
          const response = await fetch(url);
          const result   = await response.json();

          if (!result.success || !result.data.length) {
            body.innerHTML = '';
            empty.style.display = 'block';
            return;
          }

          const modeLabels = {
            single_player: 'Single Player',
            timed_quiz:    'Timed Quiz',
            ranked_quiz:   'Ranked Quiz',
            memory_match:  'Memory Match',
            endless_quiz:  'Endless Quiz'
          };

          body.innerHTML = result.data.map(entry => {
            const rankClass = entry.rank <= 3
              ? `rank-${entry.rank}`
              : 'rank-other';
            const rankIcon = entry.rank === 1 ? '🥇'
                           : entry.rank === 2 ? '🥈'
                           : entry.rank === 3 ? '🥉'
                           : entry.rank;

            return `
              <tr>
                <td>
                  <span class="rank-badge ${rankClass}">${rankIcon}</span>
                </td>
                <td><strong>${entry.fullname || entry.username}</strong>
                    <br><small style="opacity:.5">@${entry.username}</small>
                </td>
                <td class="points-cell">${Number(entry.total_points).toLocaleString()}</td>
                <td>${entry.total_correct} / ${entry.total_questions}</td>
                <td class="accuracy-cell">${entry.accuracy}%</td>
                <td>${entry.attempts}</td>
              </tr>`;
          }).join('');

        } catch (err) {
          body.innerHTML = '<tr><td colspan="6" class="loading">Failed to load leaderboard.</td></tr>';
        }
      }

      document.getElementById('modeSelect').addEventListener('change', e => {
        loadLeaderboard(e.target.value);
      });

      loadLeaderboard();
    </script>
  </body>
</html>
