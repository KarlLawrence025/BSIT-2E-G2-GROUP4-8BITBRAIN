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
      /* ── Page ── */
      .lb-page {
        min-height: 100vh;
        padding: 100px 24px 60px;
        display: flex;
        flex-direction: column;
        align-items: center;
        color: #fff;
      }

      .lb-page h1 {
        font-size: 46px;
        text-shadow: #f70606 3px 3px;
        margin-bottom: 8px;
        text-align: center;
      }

      .lb-sub {
        font-size: 15px;
        color: rgba(255,255,255,.5);
        margin-bottom: 32px;
        text-align: center;
      }

      /* ── Filter row ── */
      .lb-filter {
        display: flex;
        align-items: center;
        gap: 12px;
        margin-bottom: 28px;
        flex-wrap: wrap;
        justify-content: center;
      }

      .lb-filter label {
        font-size: 15px;
        color: rgba(255,255,255,.65);
      }

      .lb-filter select {
        background: rgba(0,0,0,.5);
        border: 1.5px solid rgba(255,47,179,.4);
        color: #fff;
        padding: 9px 16px;
        border-radius: 8px;
        font-family: inherit;
        font-size: 15px;
        cursor: pointer;
        outline: none;
        transition: border-color .2s, box-shadow .2s;
        -webkit-appearance: none;
      }

      .lb-filter select:focus {
        border-color: #ff2fb3;
        box-shadow: 0 0 0 3px rgba(255,47,179,.18);
      }

      .lb-filter select option { background: #1a0b2e; }

      /* ── Table wrapper ── */
      .lb-table-wrap {
        width: 100%;
        max-width: 1000px;
        background: rgba(10,4,28,.8);
        backdrop-filter: blur(14px);
        border-radius: 16px;
        border: 1px solid rgba(255,47,179,.2);
        overflow: hidden;
        box-shadow: 0 0 0 1px rgba(255,255,255,.04), 0 20px 60px rgba(0,0,0,.5);
      }

      .lb-table {
        width: 100%;
        border-collapse: collapse;
      }

      /* ── Header ── */
      .lb-table thead tr {
        background: linear-gradient(90deg, rgba(255,47,179,.18), rgba(168,85,247,.12));
        border-bottom: 1px solid rgba(255,47,179,.25);
      }

      .lb-table th {
        padding: 16px 18px;
        font-size: 11px;
        font-weight: 800;
        text-transform: uppercase;
        letter-spacing: 1.5px;
        color: #ff2fb3;
        text-align: left;
        white-space: nowrap;
      }

      .lb-table th.center { text-align: center; }

      /* ── Rows ── */
      .lb-table tbody tr {
        border-bottom: 1px solid rgba(255,255,255,.05);
        transition: background .18s;
      }

      .lb-table tbody tr:last-child { border-bottom: none; }

      .lb-table tbody tr:hover {
        background: rgba(255,47,179,.06);
      }

      /* Highlight top 3 */
      .lb-table tbody tr.rank-1 { background: rgba(255,215,0,.06); }
      .lb-table tbody tr.rank-2 { background: rgba(192,192,192,.05); }
      .lb-table tbody tr.rank-3 { background: rgba(205,127,50,.05); }
      .lb-table tbody tr.rank-1:hover { background: rgba(255,215,0,.1); }
      .lb-table tbody tr.rank-2:hover { background: rgba(192,192,192,.09); }
      .lb-table tbody tr.rank-3:hover { background: rgba(205,127,50,.09); }

      .lb-table td {
        padding: 16px 18px;
        font-size: 14px;
        vertical-align: middle;
      }

      .lb-table td.center { text-align: center; }

      /* ── Rank cell ── */
      .lb-rank {
        width: 44px;
        height: 44px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 20px;
        font-weight: 800;
        margin: 0 auto;
      }

      .lb-rank.r1 { background: linear-gradient(135deg,#ffd700,#f59e0b); color:#000; box-shadow:0 0 16px rgba(255,215,0,.4); }
      .lb-rank.r2 { background: linear-gradient(135deg,#e2e8f0,#94a3b8); color:#000; box-shadow:0 0 12px rgba(148,163,184,.3); }
      .lb-rank.r3 { background: linear-gradient(135deg,#cd7f32,#92400e); color:#fff; box-shadow:0 0 12px rgba(205,127,50,.3); }
      .lb-rank.rn { background: rgba(255,255,255,.08); color:rgba(255,255,255,.6); font-size:15px; }

      /* ── Player cell ── */
      .lb-player {
        display: flex;
        align-items: center;
        gap: 14px;
      }

      .lb-avatar {
        width: 42px;
        height: 42px;
        border-radius: 50%;
        background: linear-gradient(135deg, #ff2fb3, #8b5cf6);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 18px;
        font-weight: 800;
        color: #fff;
        flex-shrink: 0;
        border: 2px solid rgba(255,255,255,.12);
      }

      .lb-player-name {
        font-size: 15px;
        font-weight: 700;
        color: #fff;
        line-height: 1.2;
      }

      .lb-player-username {
        font-size: 12px;
        color: rgba(255,255,255,.4);
        margin-top: 2px;
      }

      /* ── Points cell ── */
      .lb-points {
        font-size: 22px;
        font-weight: 800;
        color: #ff2fb3;
        text-shadow: 0 0 10px rgba(255,47,179,.45);
      }

      /* ── Correct / Total ── */
      .lb-ratio {
        font-size: 14px;
        color: rgba(255,255,255,.8);
      }

      .lb-ratio .lb-correct { color: #4ade80; font-weight: 700; }
      .lb-ratio .lb-sep     { color: rgba(255,255,255,.3); margin: 0 3px; }
      .lb-ratio .lb-total   { color: rgba(255,255,255,.55); }

      /* ── Accuracy ── */
      .lb-accuracy {
        font-weight: 700;
        font-size: 14px;
      }
      .lb-accuracy.high   { color: #4ade80; }
      .lb-accuracy.mid    { color: #fbbf24; }
      .lb-accuracy.low    { color: #f87171; }

      /* ── Attempts ── */
      .lb-attempts {
        font-size: 14px;
        color: rgba(255,255,255,.65);
        font-weight: 600;
      }

      /* ── Empty / loading states ── */
      .lb-state {
        text-align: center;
        padding: 60px 20px;
        color: rgba(255,255,255,.4);
      }

      .lb-state-icon { font-size: 40px; margin-bottom: 12px; }
      .lb-state p    { font-size: 15px; }

      /* ── Responsive ── */
      @media (max-width: 700px) {
        .lb-table th:nth-child(5),
        .lb-table td:nth-child(5) { display: none; } /* hide attempts */
        .lb-page h1 { font-size: 32px; }
        .lb-points  { font-size: 18px; }
      }

      @media (max-width: 500px) {
        .lb-table th:nth-child(4),
        .lb-table td:nth-child(4) { display: none; } /* hide accuracy */
        .lb-player-username { display: none; }
      }
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
      <div class="lb-page">
        <h1>🏆 Leaderboards</h1>
        <p class="lb-sub">Top players ranked by total accumulated points across all attempts</p>

        <!-- Filter -->
        <div class="lb-filter">
          <label for="modeSelect">Filter by Mode:</label>
          <select id="modeSelect" onchange="loadLeaderboard(this.value)">
            <option value="">All Modes</option>
            <option value="single_player">Single Player</option>
            <option value="timed_quiz">Timed Quiz</option>
            <option value="ranked_quiz">Ranked Quiz</option>
            <option value="memory_match">Memory Match</option>
            <option value="endless_quiz">Endless Quiz</option>
          </select>
        </div>

        <!-- Table -->
        <div class="lb-table-wrap">
          <table class="lb-table">
            <thead>
              <tr>
                <th class="center">Rank</th>
                <th>Player</th>
                <th class="center">Total Points</th>
                <th class="center">Correct / Total</th>
                <th class="center">Accuracy</th>
                <th class="center">Attempts</th>
              </tr>
            </thead>
            <tbody id="lbBody">
              <tr>
                <td colspan="6">
                  <div class="lb-state">
                    <div class="lb-state-icon">⏳</div>
                    <p>Loading...</p>
                  </div>
                </td>
              </tr>
            </tbody>
          </table>
        </div>

      </div>
    </main>

    <script>
      async function loadLeaderboard(mode = '') {
        const body = document.getElementById('lbBody');

        body.innerHTML = `
          <tr><td colspan="6">
            <div class="lb-state">
              <div class="lb-state-icon">⏳</div>
              <p>Loading...</p>
            </div>
          </td></tr>`;

        const url = mode
          ? `api/get_leaderboard.php?mode=${encodeURIComponent(mode)}`
          : 'api/get_leaderboard.php';

        try {
          const res    = await fetch(url);
          const result = await res.json();

          if (!result.success || !result.data.length) {
            body.innerHTML = `
              <tr><td colspan="6">
                <div class="lb-state">
                  <div class="lb-state-icon">🎮</div>
                  <p>No scores yet${mode ? ' for this mode' : ''} — be the first to play!</p>
                </div>
              </td></tr>`;
            return;
          }

          body.innerHTML = result.data.map(entry => buildRow(entry)).join('');

        } catch (err) {
          body.innerHTML = `
            <tr><td colspan="6">
              <div class="lb-state">
                <div class="lb-state-icon">⚠️</div>
                <p>Failed to load. Is XAMPP running?</p>
              </div>
            </td></tr>`;
        }
      }

      function buildRow(e) {
        const rank       = parseInt(e.rank);
        const rankClass  = rank <= 3 ? `rank-${rank}` : '';
        const rankIcon   = rank === 1 ? '🥇' : rank === 2 ? '🥈' : rank === 3 ? '🥉' : rank;
        const rankBadge  = rank <= 3
          ? `<div class="lb-rank r${rank}">${rankIcon}</div>`
          : `<div class="lb-rank rn">${rank}</div>`;

        // Avatar initial
        const name    = e.fullname || e.username || '?';
        const initial = name.charAt(0).toUpperCase();

        // Correct / Total — always sum of ALL attempts
        const totalCorrect   = parseInt(e.total_correct)   || 0;
        const totalQuestions = parseInt(e.total_questions) || 0;
        const ratio = `
          <span class="lb-ratio">
            <span class="lb-correct">${totalCorrect}</span>
            <span class="lb-sep">/</span>
            <span class="lb-total">${totalQuestions}</span>
          </span>`;

        // Accuracy colour
        const acc    = parseFloat(e.accuracy) || 0;
        const accCls = acc >= 80 ? 'high' : acc >= 50 ? 'mid' : 'low';

        return `
          <tr class="${rankClass}">
            <td class="center">${rankBadge}</td>
            <td>
              <div class="lb-player">
                <div class="lb-avatar">${initial}</div>
                <div>
                  <div class="lb-player-name">${esc(e.fullname || e.username)}</div>
                  <div class="lb-player-username">@${esc(e.username)}</div>
                </div>
              </div>
            </td>
            <td class="center"><span class="lb-points">${Number(e.total_points).toLocaleString()}</span></td>
            <td class="center">${ratio}</td>
            <td class="center"><span class="lb-accuracy ${accCls}">${acc}%</span></td>
            <td class="center"><span class="lb-attempts">${e.attempts}</span></td>
          </tr>`;
      }

      function esc(s) {
        const d = document.createElement('div');
        d.appendChild(document.createTextNode(s || ''));
        return d.innerHTML;
      }

      // Load on page ready
      loadLeaderboard('');
    </script>
  </body>
</html>
