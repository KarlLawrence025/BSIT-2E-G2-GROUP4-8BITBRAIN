<?php
session_start();
if (!isset($_SESSION['logged_in']) || $_SESSION['account_type'] !== 'user') {
    header("Location: index.php");
    exit();
}
$user_id = $_SESSION['user_id'];
?>
<!doctype html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>8BitBrain — My Profile</title>
    <link rel="stylesheet" href="style.css" />
    <link rel="icon" href="imgs/Sans_Favi.png" type="image/png" />
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=Bitcount+Single:wght@100..900&display=swap" rel="stylesheet" />
    <style>
    /* ══════════════════════════════════════════
       DASHBOARD LAYOUT
    ══════════════════════════════════════════ */
    * { box-sizing: border-box; }

    .dash-page {
      min-height: 100vh;
      padding: 88px 24px 60px;
      display: grid;
      grid-template-columns: 290px 1fr;
      gap: 24px;
      max-width: 1280px;
      margin: 0 auto;
    }

    /* ── Shared card style ── */
    .dash-card {
      background: rgba(10,4,28,.82);
      backdrop-filter: blur(14px);
      border: 1px solid rgba(255,47,179,.2);
      border-radius: 18px;
      padding: 28px 24px;
      box-shadow: 0 8px 40px rgba(0,0,0,.35);
    }

    .dash-card-title {
      font-size: 13px;
      font-weight: 800;
      letter-spacing: 2px;
      text-transform: uppercase;
      color: #ff2fb3;
      margin-bottom: 20px;
      padding-bottom: 12px;
      border-bottom: 1px solid rgba(255,47,179,.18);
    }

    /* ══════════════════════════════════════════
       LEFT SIDEBAR — PROFILE
    ══════════════════════════════════════════ */
    .sidebar {
      display: flex;
      flex-direction: column;
      gap: 20px;
    }

    /* ── Profile card ── */
    .profile-card {
      text-align: center;
    }

    .profile-avatar {
      width: 90px;
      height: 90px;
      border-radius: 50%;
      background: linear-gradient(135deg, #ff2fb3, #8b5cf6);
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 38px;
      font-weight: 800;
      color: #fff;
      margin: 0 auto 16px;
      border: 3px solid rgba(255,47,179,.5);
      box-shadow: 0 0 20px rgba(255,47,179,.3);
    }

    .profile-name {
      font-size: 20px;
      font-weight: 700;
      color: #fff;
      margin-bottom: 4px;
    }

    .profile-username {
      font-size: 13px;
      color: rgba(255,255,255,.45);
      margin-bottom: 16px;
    }

    .profile-rank-badge {
      display: inline-flex;
      align-items: center;
      gap: 6px;
      background: rgba(139,92,246,.18);
      border: 1px solid rgba(139,92,246,.4);
      border-radius: 20px;
      padding: 5px 14px;
      font-size: 12px;
      font-weight: 700;
      color: #a78bfa;
      margin-bottom: 20px;
    }

    /* Level + XP bar */
    .profile-level-row {
      display: flex;
      justify-content: space-between;
      align-items: center;
      margin-bottom: 8px;
    }

    .profile-level-label {
      font-size: 12px;
      color: rgba(255,255,255,.5);
      font-weight: 600;
    }

    .profile-level-val {
      font-size: 14px;
      font-weight: 800;
      color: #ff2fb3;
    }

    .xp-bar-wrap {
      height: 8px;
      background: rgba(255,255,255,.08);
      border-radius: 10px;
      overflow: hidden;
      margin-bottom: 6px;
    }

    .xp-bar-fill {
      height: 100%;
      border-radius: 10px;
      background: linear-gradient(90deg, #ff2fb3, #a855f7);
      transition: width .6s ease;
      box-shadow: 0 0 8px rgba(255,47,179,.4);
    }

    .xp-bar-sub {
      font-size: 11px;
      color: rgba(255,255,255,.3);
      text-align: right;
    }

    /* Info rows */
    .profile-info-list {
      margin-top: 20px;
      display: flex;
      flex-direction: column;
      gap: 10px;
    }

    .profile-info-row {
      display: flex;
      justify-content: space-between;
      align-items: center;
      padding: 9px 12px;
      background: rgba(255,255,255,.04);
      border-radius: 8px;
      border: 1px solid rgba(255,255,255,.06);
    }

    .pir-label {
      font-size: 12px;
      color: rgba(255,255,255,.45);
      display: flex;
      align-items: center;
      gap: 6px;
    }

    .pir-val {
      font-size: 13px;
      font-weight: 700;
      color: #fff;
    }

    /* Quick actions */
    .quick-actions {
      display: flex;
      flex-direction: column;
      gap: 10px;
    }

    .qa-btn {
      width: 100%;
      padding: 13px 16px;
      border-radius: 10px;
      border: none;
      font-family: inherit;
      font-size: 14px;
      font-weight: 700;
      cursor: pointer;
      transition: all .2s;
      display: flex;
      align-items: center;
      gap: 10px;
    }

    .qa-btn.primary {
      background: linear-gradient(135deg, #ff2fb3, #a855f7);
      color: #fff;
      box-shadow: 0 4px 16px rgba(255,47,179,.3);
    }
    .qa-btn.primary:hover { transform: translateY(-2px); box-shadow: 0 8px 24px rgba(255,47,179,.4); }

    .qa-btn.secondary {
      background: rgba(255,255,255,.06);
      color: rgba(255,255,255,.75);
      border: 1px solid rgba(255,255,255,.12);
    }
    .qa-btn.secondary:hover { background: rgba(255,255,255,.1); color: #fff; border-color: rgba(255,255,255,.25); }

    /* ══════════════════════════════════════════
       MAIN CONTENT
    ══════════════════════════════════════════ */
    .main-content {
      display: flex;
      flex-direction: column;
      gap: 20px;
    }

    /* ── Stats grid ── */
    .stats-grid {
      display: grid;
      grid-template-columns: repeat(4, 1fr);
      gap: 14px;
    }

    .stat-box {
      background: rgba(10,4,28,.82);
      backdrop-filter: blur(14px);
      border: 1px solid rgba(255,255,255,.08);
      border-radius: 14px;
      padding: 18px 14px;
      text-align: center;
      transition: border-color .2s, box-shadow .2s;
    }

    .stat-box:hover {
      border-color: rgba(255,47,179,.35);
      box-shadow: 0 0 20px rgba(255,47,179,.08);
    }

    .stat-box-val {
      font-size: 28px;
      font-weight: 800;
      color: #ff2fb3;
      text-shadow: 0 0 10px rgba(255,47,179,.4);
      line-height: 1;
      margin-bottom: 6px;
    }

    .stat-box-lbl {
      font-size: 11px;
      color: rgba(255,255,255,.45);
      text-transform: uppercase;
      letter-spacing: 1px;
    }

    .stat-box.cyan  .stat-box-val { color: #38bdf8; text-shadow: 0 0 10px rgba(56,189,248,.4); }
    .stat-box.green .stat-box-val { color: #4ade80; text-shadow: 0 0 10px rgba(74,222,128,.4); }
    .stat-box.gold  .stat-box-val { color: #fbbf24; text-shadow: 0 0 10px rgba(251,191,36,.4); }

    /* ── Leaderboard card ── */
    .lb-row {
      display: flex;
      align-items: center;
      gap: 14px;
      padding: 13px 14px;
      border-radius: 10px;
      background: rgba(255,255,255,.04);
      border: 1px solid rgba(255,255,255,.06);
      margin-bottom: 10px;
      transition: background .18s, border-color .18s;
    }

    .lb-row:last-child { margin-bottom: 0; }

    .lb-row:hover { background: rgba(255,47,179,.06); border-color: rgba(255,47,179,.2); }

    .lb-row.you {
      background: rgba(255,47,179,.1);
      border-color: rgba(255,47,179,.35);
      box-shadow: 0 0 16px rgba(255,47,179,.12);
    }

    .lb-medal {
      width: 36px;
      height: 36px;
      border-radius: 50%;
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 18px;
      font-weight: 800;
      flex-shrink: 0;
    }

    .lb-medal.m1 { background: linear-gradient(135deg,#ffd700,#f59e0b); color:#000; box-shadow:0 0 12px rgba(255,215,0,.4); }
    .lb-medal.m2 { background: linear-gradient(135deg,#e2e8f0,#94a3b8); color:#000; }
    .lb-medal.m3 { background: linear-gradient(135deg,#cd7f32,#92400e); color:#fff; }
    .lb-medal.mx { background: rgba(255,47,179,.2); color:#ff2fb3; font-size:14px; }

    .lb-info { flex: 1; min-width: 0; }

    .lb-name {
      font-size: 15px;
      font-weight: 700;
      color: #fff;
      white-space: nowrap;
      overflow: hidden;
      text-overflow: ellipsis;
    }

    .lb-you-tag {
      display: inline-block;
      font-size: 10px;
      font-weight: 800;
      background: #ff2fb3;
      color: #fff;
      padding: 1px 7px;
      border-radius: 4px;
      margin-left: 8px;
      letter-spacing: .5px;
    }

    .lb-username {
      font-size: 12px;
      color: rgba(255,255,255,.4);
      margin-top: 2px;
    }

    .lb-pts {
      font-size: 18px;
      font-weight: 800;
      color: #ff2fb3;
      text-shadow: 0 0 8px rgba(255,47,179,.4);
      white-space: nowrap;
    }

    .lb-pts-lbl {
      font-size: 10px;
      color: rgba(255,255,255,.35);
      text-align: right;
      margin-top: 2px;
    }

    .lb-view-btn {
      display: block;
      width: 100%;
      margin-top: 16px;
      padding: 12px;
      background: transparent;
      border: 1.5px solid rgba(255,47,179,.4);
      color: #ff2fb3;
      border-radius: 10px;
      font-family: inherit;
      font-size: 14px;
      font-weight: 700;
      cursor: pointer;
      transition: all .2s;
      text-decoration: none;
      text-align: center;
    }

    .lb-view-btn:hover { background: rgba(255,47,179,.1); border-color: #ff2fb3; }

    /* ── Recent attempts ── */
    .recent-row {
      display: flex;
      align-items: center;
      gap: 12px;
      padding: 11px 14px;
      border-radius: 10px;
      background: rgba(255,255,255,.04);
      border: 1px solid rgba(255,255,255,.06);
      margin-bottom: 8px;
      transition: background .15s;
    }

    .recent-row:last-child { margin-bottom: 0; }
    .recent-row:hover { background: rgba(255,255,255,.07); }

    .recent-mode-badge {
      font-size: 10px;
      font-weight: 800;
      padding: 3px 9px;
      border-radius: 5px;
      text-transform: uppercase;
      letter-spacing: .5px;
      white-space: nowrap;
      flex-shrink: 0;
    }

    .recent-mode-badge.single_player { background:rgba(255,47,179,.2); color:#ff2fb3; border:1px solid rgba(255,47,179,.4); }
    .recent-mode-badge.timed_quiz    { background:rgba(245,158,11,.2);  color:#f59e0b; border:1px solid rgba(245,158,11,.4); }
    .recent-mode-badge.ranked_quiz   { background:rgba(239,68,68,.2);   color:#ef4444; border:1px solid rgba(239,68,68,.4);  }
    .recent-mode-badge.memory_match  { background:rgba(16,185,129,.2);  color:#10b981; border:1px solid rgba(16,185,129,.4); }
    .recent-mode-badge.endless_quiz  { background:rgba(139,92,246,.2);  color:#8b5cf6; border:1px solid rgba(139,92,246,.4); }

    .recent-title {
      flex: 1;
      font-size: 13px;
      color: rgba(255,255,255,.8);
      white-space: nowrap;
      overflow: hidden;
      text-overflow: ellipsis;
    }

    .recent-score {
      font-size: 13px;
      font-weight: 700;
      color: #4ade80;
      white-space: nowrap;
    }

    .recent-ratio {
      font-size: 12px;
      color: rgba(255,255,255,.4);
      white-space: nowrap;
    }

    .recent-date {
      font-size: 11px;
      color: rgba(255,255,255,.3);
      white-space: nowrap;
    }

    /* Empty state */
    .empty-state-dash {
      text-align: center;
      padding: 32px 16px;
      color: rgba(255,255,255,.35);
      font-size: 14px;
    }

    .empty-state-dash .esi { font-size: 32px; margin-bottom: 8px; }

    /* Loading skeleton pulse */
    .skel {
      background: linear-gradient(90deg, rgba(255,255,255,.05) 25%, rgba(255,255,255,.1) 50%, rgba(255,255,255,.05) 75%);
      background-size: 200% 100%;
      animation: skelPulse 1.4s ease infinite;
      border-radius: 6px;
      height: 18px;
    }

    @keyframes skelPulse { 0%{background-position:200% 0} 100%{background-position:-200% 0} }

    /* ── Responsive ── */
    @media (max-width: 1024px) {
      .stats-grid { grid-template-columns: repeat(2,1fr); }
    }

    @media (max-width: 860px) {
      .dash-page { grid-template-columns: 1fr; }
      .sidebar { max-width: 480px; margin: 0 auto; width: 100%; }
    }

    @media (max-width: 500px) {
      .stats-grid { grid-template-columns: repeat(2,1fr); }
      .dash-page  { padding: 80px 14px 40px; gap: 14px; }
      .dash-card  { padding: 20px 16px; }
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

    <script>
      const SESSION_USER_ID = <?php echo json_encode($user_id); ?>;
    </script>

    <main>
      <div class="dash-page" id="dashPage">

        <!-- ── LEFT SIDEBAR ── -->
        <aside class="sidebar">

          <!-- Profile card -->
          <div class="dash-card profile-card" id="profileCard">
            <div class="profile-avatar" id="profileAvatar">?</div>
            <div class="profile-name" id="profileName">Loading...</div>
            <div class="profile-username" id="profileUsername">@...</div>
            <div class="profile-rank-badge" id="profileRankBadge">
              🏅 Rank <span id="profileRankNum">—</span>
            </div>

            <!-- Level + XP -->
            <div class="profile-level-row">
              <span class="profile-level-label">Level</span>
              <span class="profile-level-val" id="profileLevel">1</span>
            </div>
            <div class="xp-bar-wrap">
              <div class="xp-bar-fill" id="xpBarFill" style="width:0%"></div>
            </div>
            <div class="xp-bar-sub" id="xpBarSub">0 / 500 XP</div>

            <!-- Info rows -->
            <div class="profile-info-list" id="profileInfoList">
              <div class="profile-info-row">
                <span class="pir-label">📅 Member Since</span>
                <span class="pir-val skel" style="width:70px">&nbsp;</span>
              </div>
            </div>
          </div>

          <!-- Quick actions -->
          <div class="dash-card" style="padding:20px 20px;">
            <div class="dash-card-title">Quick Actions</div>
            <div class="quick-actions">
              <button class="qa-btn primary" onclick="window.location.href='modes.php'">
                🎮 Play Now
              </button>
              <button class="qa-btn secondary" onclick="window.location.href='leaderboards.php'">
                🏆 Leaderboards
              </button>
            </div>
          </div>

        </aside>

        <!-- ── MAIN CONTENT ── -->
        <div class="main-content">

          <!-- Stats row -->
          <div class="stats-grid" id="statsGrid">
            <div class="stat-box">       <div class="stat-box-val skel">&nbsp;</div><div class="stat-box-lbl">Total Points</div></div>
            <div class="stat-box cyan">  <div class="stat-box-val skel">&nbsp;</div><div class="stat-box-lbl">Quizzes Taken</div></div>
            <div class="stat-box green"> <div class="stat-box-val skel">&nbsp;</div><div class="stat-box-lbl">Accuracy</div></div>
            <div class="stat-box gold">  <div class="stat-box-val skel">&nbsp;</div><div class="stat-box-lbl">Best Score</div></div>
          </div>

          <!-- Leaderboard preview -->
          <div class="dash-card">
            <div class="dash-card-title">🏆 Top Rankings — All Modes</div>
            <div id="lbPreview">
              <div class="skel" style="height:52px;margin-bottom:10px;border-radius:10px;"></div>
              <div class="skel" style="height:52px;margin-bottom:10px;border-radius:10px;"></div>
              <div class="skel" style="height:52px;border-radius:10px;"></div>
            </div>
            <a href="leaderboards.php" class="lb-view-btn">View Full Leaderboards →</a>
          </div>

          <!-- Recent activity -->
          <div class="dash-card">
            <div class="dash-card-title">📋 Recent Quiz Attempts</div>
            <div id="recentAttempts">
              <div class="skel" style="height:44px;margin-bottom:8px;border-radius:10px;"></div>
              <div class="skel" style="height:44px;margin-bottom:8px;border-radius:10px;"></div>
              <div class="skel" style="height:44px;border-radius:10px;"></div>
            </div>
          </div>

        </div>
      </div>
    </main>

    <script>
    const MODE_LABELS = {
        single_player: 'Single Player',
        timed_quiz:    'Timed Quiz',
        ranked_quiz:   'Ranked Quiz',
        memory_match:  'Memory Match',
        endless_quiz:  'Endless Quiz'
    };

    function esc(s) {
        const d = document.createElement('div');
        d.appendChild(document.createTextNode(s || ''));
        return d.innerHTML;
    }

    function setText(id, val) {
        const el = document.getElementById(id);
        if (el) el.textContent = val;
    }

    async function loadDashboard() {
        try {
            const res  = await fetch('api/get_user_stats.php');
            const data = await res.json();

            if (!data.success) {
                console.error('Dashboard error:', data.message);
                return;
            }

            renderProfile(data.user, data.stats);
            renderStats(data.stats);
            renderLeaderboard(data.top3, data.user);
            renderRecent(data.recent);

        } catch (e) {
            console.error('Dashboard fetch error:', e);
        }
    }

    function renderProfile(user, stats) {
        // Avatar initial
        const avatarEl = document.getElementById('profileAvatar');
        if (avatarEl) avatarEl.textContent = (user.fullname || user.username || '?').charAt(0).toUpperCase();

        setText('profileName',     user.fullname || user.username);
        setText('profileUsername', '@' + user.username);

        // Rank badge
        const rankNum = document.getElementById('profileRankNum');
        if (rankNum) rankNum.textContent = stats.global_rank ? `#${stats.global_rank}` : '—';

        // Level + XP
        setText('profileLevel', stats.level);
        const xpFill = document.getElementById('xpBarFill');
        if (xpFill) xpFill.style.width = stats.xp_pct + '%';
        setText('xpBarSub', `${stats.xp_in_level} / 500 XP to next level`);

        // Info rows
        const infoList = document.getElementById('profileInfoList');
        const favModeLabel = stats.fav_mode ? (MODE_LABELS[stats.fav_mode] || stats.fav_mode) : '—';
        const accColor = stats.accuracy >= 80 ? '#4ade80' : stats.accuracy >= 50 ? '#fbbf24' : '#f87171';

        infoList.innerHTML = `
            <div class="profile-info-row">
                <span class="pir-label">📅 Member Since</span>
                <span class="pir-val">${esc(user.member_since)}</span>
            </div>
            <div class="profile-info-row">
                <span class="pir-label">🎮 Fav. Mode</span>
                <span class="pir-val">${esc(favModeLabel)}</span>
            </div>
            <div class="profile-info-row">
                <span class="pir-label">✅ Correct Answers</span>
                <span class="pir-val">${stats.total_correct} / ${stats.total_questions}</span>
            </div>
            <div class="profile-info-row">
                <span class="pir-label">🎯 Accuracy</span>
                <span class="pir-val" style="color:${accColor}">${stats.accuracy}%</span>
            </div>`;
    }

    function renderStats(stats) {
        const grid = document.getElementById('statsGrid');
        grid.innerHTML = `
            <div class="stat-box">
                <div class="stat-box-val">${Number(stats.total_points).toLocaleString()}</div>
                <div class="stat-box-lbl">Total Points</div>
            </div>
            <div class="stat-box cyan">
                <div class="stat-box-val">${stats.total_attempts}</div>
                <div class="stat-box-lbl">Quizzes Taken</div>
            </div>
            <div class="stat-box green">
                <div class="stat-box-val">${stats.accuracy}%</div>
                <div class="stat-box-lbl">Accuracy</div>
            </div>
            <div class="stat-box gold">
                <div class="stat-box-val">${Number(stats.best_score).toLocaleString()}</div>
                <div class="stat-box-lbl">Best Score</div>
            </div>`;
    }

    function renderLeaderboard(top3, currentUser) {
        const el = document.getElementById('lbPreview');

        if (!top3 || top3.length === 0) {
            el.innerHTML = `<div class="empty-state-dash"><div class="esi">🏆</div>No scores yet — be the first to play!</div>`;
            return;
        }

        const medalClass = ['m1','m2','m3'];
        const medalIcon  = ['🥇','🥈','🥉'];

        el.innerHTML = top3.map((entry, i) => {
            const isYou = entry.username === currentUser.username;
            return `
                <div class="lb-row ${isYou ? 'you' : ''}">
                    <div class="lb-medal ${medalClass[i]}">${medalIcon[i]}</div>
                    <div class="lb-info">
                        <div class="lb-name">
                            ${esc(entry.fullname || entry.username)}
                            ${isYou ? '<span class="lb-you-tag">YOU</span>' : ''}
                        </div>
                        <div class="lb-username">@${esc(entry.username)}</div>
                    </div>
                    <div style="text-align:right">
                        <div class="lb-pts">${Number(entry.pts).toLocaleString()}</div>
                        <div class="lb-pts-lbl">points</div>
                    </div>
                </div>`;
        }).join('');
    }

    function renderRecent(recent) {
        const el = document.getElementById('recentAttempts');

        if (!recent || recent.length === 0) {
            el.innerHTML = `<div class="empty-state-dash"><div class="esi">📋</div>No quiz attempts yet. <a href="modes.php" style="color:#ff2fb3">Play now!</a></div>`;
            return;
        }

        el.innerHTML = recent.map(r => {
            const date = new Date(r.created_at).toLocaleDateString('en-US', {
                month: 'short', day: 'numeric', year: 'numeric'
            });
            const title     = r.quiz_title || 'Endless Quiz';
            const modeLabel = MODE_LABELS[r.mode] || r.mode;

            return `
                <div class="recent-row">
                    <span class="recent-mode-badge ${r.mode}">${modeLabel}</span>
                    <span class="recent-title" title="${esc(title)}">${esc(title)}</span>
                    <span class="recent-score">+${Number(r.points_earned).toLocaleString()} pts</span>
                    <span class="recent-ratio">${r.correct}/${r.total}</span>
                    <span class="recent-date">${date}</span>
                </div>`;
        }).join('');
    }

    // Boot
    loadDashboard();
    </script>
  </body>
</html>
