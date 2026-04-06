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

    .lb-filter {
      display: flex;
      align-items: center;
      gap: 12px;
      margin-bottom: 28px;
      flex-wrap: wrap;
      justify-content: center;
    }

    .lb-filter label { font-size: 15px; color: rgba(255,255,255,.65); }

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
      -webkit-appearance: none;
    }

    .lb-filter select option { background: #1a0b2e; }

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

    .lb-table { width: 100%; border-collapse: collapse; }

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

    .lb-table tbody tr {
      border-bottom: 1px solid rgba(255,255,255,.05);
      transition: background .18s;
    }

    .lb-table tbody tr:last-child { border-bottom: none; }
    .lb-table tbody tr:hover { background: rgba(255,47,179,.06); }
    .lb-table tbody tr.rank-1 { background: rgba(255,215,0,.05); }
    .lb-table tbody tr.rank-2 { background: rgba(192,192,192,.04); }
    .lb-table tbody tr.rank-3 { background: rgba(205,127,50,.04); }

    .lb-table td {
      padding: 14px 18px;
      font-size: 14px;
      vertical-align: middle;
    }

    .lb-table td.center { text-align: center; }

    /* Rank badge */
    .lb-rank {
      width: 40px; height: 40px;
      border-radius: 50%;
      display: flex; align-items: center; justify-content: center;
      font-size: 18px; font-weight: 800;
      margin: 0 auto;
    }
    .lb-rank.r1 { background:linear-gradient(135deg,#ffd700,#f59e0b);color:#000;box-shadow:0 0 14px rgba(255,215,0,.4); }
    .lb-rank.r2 { background:linear-gradient(135deg,#e2e8f0,#94a3b8);color:#000; }
    .lb-rank.r3 { background:linear-gradient(135deg,#cd7f32,#92400e);color:#fff; }
    .lb-rank.rn { background:rgba(255,255,255,.08);color:rgba(255,255,255,.6);font-size:14px; }

    /* Player cell — clickable */
    .lb-player {
      display: flex;
      align-items: center;
      gap: 12px;
      cursor: pointer;
    }

    .lb-player:hover .lb-player-name { color: #ff2fb3; text-decoration: underline; }

    .lb-avatar-img {
      width: 40px; height: 40px;
      border-radius: 50%; object-fit: cover;
      border: 2px solid rgba(255,255,255,.12); flex-shrink: 0;
    }

    .lb-avatar-init {
      width: 40px; height: 40px;
      border-radius: 50%;
      background: linear-gradient(135deg,#ff2fb3,#8b5cf6);
      display: flex; align-items: center; justify-content: center;
      font-size: 16px; font-weight: 800; color: #fff;
      flex-shrink: 0; border: 2px solid rgba(255,255,255,.12);
    }

    .lb-player-name     { font-size: 15px; font-weight: 700; color: #fff; transition: color .15s; }
    .lb-player-username { font-size: 12px; color: rgba(255,255,255,.4); margin-top: 2px; }

    .lb-points   { font-size: 20px; font-weight: 800; color: #ff2fb3; text-shadow: 0 0 10px rgba(255,47,179,.45); }

    .lb-ratio .lb-correct { color: #4ade80; font-weight: 700; }
    .lb-ratio .lb-sep     { color: rgba(255,255,255,.3); margin: 0 3px; }
    .lb-ratio .lb-total   { color: rgba(255,255,255,.55); }

    .lb-acc.high { color: #4ade80; font-weight: 700; }
    .lb-acc.mid  { color: #fbbf24; font-weight: 700; }
    .lb-acc.low  { color: #f87171; font-weight: 700; }

    .lb-attempts { color: rgba(255,255,255,.65); font-weight: 600; }

    .lb-state {
      text-align: center; padding: 60px 20px; color: rgba(255,255,255,.4);
    }
    .lb-state-icon { font-size: 40px; margin-bottom: 12px; }
    .lb-state p    { font-size: 15px; }

    /* ══════════════════════════════════════
       PROFILE MODAL
    ══════════════════════════════════════ */
    .profile-overlay {
      display: none;
      position: fixed;
      inset: 0;
      z-index: 400;
      background: rgba(0,0,0,.8);
      backdrop-filter: blur(8px);
      align-items: flex-start;
      justify-content: center;
      padding: 70px 20px 40px;
      overflow-y: auto;
    }

    .profile-overlay.open { display: flex; }

    .profile-modal {
      width: 100%;
      max-width: 700px;
      background: linear-gradient(135deg, rgba(14,6,34,.98), rgba(8,3,20,.98));
      border: 1.5px solid rgba(255,47,179,.35);
      border-radius: 22px;
      overflow: hidden;
      box-shadow: 0 24px 80px rgba(0,0,0,.7), 0 0 50px rgba(255,47,179,.1);
      animation: modalIn .3s cubic-bezier(.22,1,.36,1);
    }

    @keyframes modalIn {
      from { opacity:0; transform:translateY(28px) scale(.97); }
      to   { opacity:1; transform:translateY(0)    scale(1);   }
    }

    /* Modal header */
    .pm-header {
      background: linear-gradient(135deg, rgba(255,47,179,.15), rgba(139,92,246,.1));
      padding: 28px 28px 24px;
      display: flex;
      align-items: center;
      gap: 20px;
      border-bottom: 1px solid rgba(255,255,255,.07);
      position: relative;
    }

    .pm-close {
      position: absolute;
      top: 18px; right: 18px;
      width: 34px; height: 34px;
      border-radius: 50%;
      border: 1px solid rgba(255,255,255,.2);
      background: rgba(255,255,255,.06);
      color: #fff;
      font-size: 18px;
      cursor: pointer;
      display: flex; align-items: center; justify-content: center;
      transition: all .2s;
    }
    .pm-close:hover { background: rgba(255,47,179,.25); border-color: #ff2fb3; }

    .pm-avatar-img {
      width: 80px; height: 80px;
      border-radius: 50%; object-fit: cover;
      border: 3px solid rgba(255,47,179,.5);
      box-shadow: 0 0 20px rgba(255,47,179,.3);
      flex-shrink: 0;
    }

    .pm-avatar-init {
      width: 80px; height: 80px;
      border-radius: 50%;
      background: linear-gradient(135deg,#ff2fb3,#8b5cf6);
      display: flex; align-items: center; justify-content: center;
      font-size: 34px; font-weight: 800; color: #fff;
      border: 3px solid rgba(255,47,179,.5);
      box-shadow: 0 0 20px rgba(255,47,179,.3);
      flex-shrink: 0;
    }

    .pm-header-info { flex: 1; min-width: 0; }

    .pm-name {
      font-size: 24px; font-weight: 700; color: #fff;
      text-shadow: 0 2px 8px rgba(0,0,0,.5);
      margin-bottom: 2px;
    }

    .pm-username { font-size: 14px; color: rgba(255,255,255,.45); margin-bottom: 10px; }

    .pm-badges {
      display: flex; flex-wrap: wrap; gap: 8px;
    }

    .pm-badge {
      display: inline-flex; align-items: center; gap: 5px;
      padding: 4px 12px; border-radius: 20px;
      font-size: 12px; font-weight: 700;
    }

    .pm-badge.rank  { background:rgba(139,92,246,.2); border:1px solid rgba(139,92,246,.45); color:#a78bfa; }
    .pm-badge.level { background:rgba(255,47,179,.15); border:1px solid rgba(255,47,179,.4);  color:#ff2fb3; }
    .pm-badge.since { background:rgba(56,189,248,.12); border:1px solid rgba(56,189,248,.35); color:#38bdf8; }

    /* Modal body */
    .pm-body { padding: 24px 28px 28px; display: flex; flex-direction: column; gap: 24px; }

    /* Stats grid */
    .pm-stats-grid {
      display: grid;
      grid-template-columns: repeat(4,1fr);
      gap: 12px;
    }

    .pm-stat {
      background: rgba(255,255,255,.05);
      border: 1px solid rgba(255,255,255,.08);
      border-radius: 12px;
      padding: 16px 10px;
      text-align: center;
      transition: border-color .2s;
    }

    .pm-stat:hover { border-color: rgba(255,47,179,.3); }

    .pm-stat-val {
      font-size: 24px; font-weight: 800; color: #ff2fb3;
      text-shadow: 0 0 10px rgba(255,47,179,.4);
      line-height: 1; margin-bottom: 5px;
    }

    .pm-stat-lbl {
      font-size: 10px; color: rgba(255,255,255,.4);
      text-transform: uppercase; letter-spacing: 1px;
    }

    .pm-stat.cyan  .pm-stat-val { color:#38bdf8; text-shadow:0 0 10px rgba(56,189,248,.4); }
    .pm-stat.green .pm-stat-val { color:#4ade80; text-shadow:0 0 10px rgba(74,222,128,.4); }
    .pm-stat.gold  .pm-stat-val { color:#fbbf24; text-shadow:0 0 10px rgba(251,191,36,.4); }

    /* XP bar */
    .pm-xp-wrap { margin-top: -8px; }
    .pm-xp-label { display:flex; justify-content:space-between; font-size:11px; color:rgba(255,255,255,.4); margin-bottom:6px; }
    .pm-xp-bar   { height:6px; background:rgba(255,255,255,.08); border-radius:10px; overflow:hidden; }
    .pm-xp-fill  { height:100%; border-radius:10px; background:linear-gradient(90deg,#ff2fb3,#a855f7); box-shadow:0 0 8px rgba(255,47,179,.4); transition:width .6s ease; }

    /* Per-mode table */
    .pm-section-title {
      font-size: 11px; font-weight: 800; letter-spacing: 2px;
      text-transform: uppercase; color: #ff2fb3;
      margin-bottom: 12px;
      padding-bottom: 8px;
      border-bottom: 1px solid rgba(255,47,179,.15);
    }

    .pm-mode-row {
      display: flex;
      align-items: center;
      gap: 12px;
      padding: 10px 12px;
      border-radius: 9px;
      background: rgba(255,255,255,.04);
      border: 1px solid rgba(255,255,255,.06);
      margin-bottom: 8px;
      transition: background .15s;
    }

    .pm-mode-row:last-child { margin-bottom: 0; }
    .pm-mode-row:hover { background: rgba(255,255,255,.07); }

    .pm-mode-badge {
      font-size: 10px; font-weight: 800;
      padding: 3px 9px; border-radius: 5px;
      text-transform: uppercase; letter-spacing: .5px;
      white-space: nowrap; flex-shrink: 0;
    }
    .pm-mode-badge.single_player { background:rgba(255,47,179,.2);  color:#ff2fb3; border:1px solid rgba(255,47,179,.4); }
    .pm-mode-badge.timed_quiz    { background:rgba(245,158,11,.2);  color:#f59e0b; border:1px solid rgba(245,158,11,.4); }
    .pm-mode-badge.ranked_quiz   { background:rgba(239,68,68,.2);   color:#ef4444; border:1px solid rgba(239,68,68,.4);  }
    .pm-mode-badge.memory_match  { background:rgba(16,185,129,.2);  color:#10b981; border:1px solid rgba(16,185,129,.4); }
    .pm-mode-badge.endless_quiz  { background:rgba(139,92,246,.2);  color:#8b5cf6; border:1px solid rgba(139,92,246,.4); }

    .pm-mode-pts  { margin-left:auto; font-size:14px; font-weight:800; color:#ff2fb3; white-space:nowrap; }
    .pm-mode-acc  { font-size:12px; color:rgba(255,255,255,.45); white-space:nowrap; }
    .pm-mode-att  { font-size:12px; color:rgba(255,255,255,.35); white-space:nowrap; }

    /* Recent attempts */
    .pm-recent-row {
      display: flex; align-items: center; gap: 10px;
      padding: 9px 12px; border-radius: 9px;
      background: rgba(255,255,255,.04);
      border: 1px solid rgba(255,255,255,.06);
      margin-bottom: 7px; transition: background .15s;
    }
    .pm-recent-row:last-child { margin-bottom: 0; }
    .pm-recent-row:hover { background: rgba(255,255,255,.07); }
    .pm-recent-title  { flex:1; font-size:12px; color:rgba(255,255,255,.75); overflow:hidden; text-overflow:ellipsis; white-space:nowrap; }
    .pm-recent-score  { font-size:13px; font-weight:700; color:#4ade80; white-space:nowrap; }
    .pm-recent-date   { font-size:11px; color:rgba(255,255,255,.3); white-space:nowrap; }

    .pm-empty { text-align:center; padding:20px; color:rgba(255,255,255,.3); font-size:14px; }

    /* Loading spinner */
    .pm-loading {
      display: flex; flex-direction: column;
      align-items: center; justify-content: center;
      padding: 60px 20px; gap: 14px; color: rgba(255,255,255,.5);
    }
    .pm-loading-icon { font-size: 40px; animation: spin 1.2s linear infinite; }
    @keyframes spin { to { transform: rotate(360deg); } }

    /* Responsive */
    @media (max-width:700px) {
      .lb-table th:nth-child(5), .lb-table td:nth-child(5) { display:none; }
      .lb-table th:nth-child(6), .lb-table td:nth-child(6) { display:none; }
      .pm-stats-grid { grid-template-columns: repeat(2,1fr); }
      .pm-header { flex-direction:column; text-align:center; padding:24px 20px 20px; }
      .pm-close  { top:14px; right:14px; }
      .pm-body   { padding:20px; }
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
        <p class="lb-sub">Click any player's name to view their full stats</p>

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
              <tr><td colspan="6">
                <div class="lb-state"><div class="lb-state-icon">⏳</div><p>Loading...</p></div>
              </td></tr>
            </tbody>
          </table>
        </div>
      </div>
    </main>

    <!-- ── Profile Modal ── -->
    <div class="profile-overlay" id="profileOverlay">
      <div class="profile-modal" id="profileModal">
        <div id="profileModalContent">
          <div class="pm-loading">
            <div class="pm-loading-icon">⏳</div>
            <p>Loading profile...</p>
          </div>
        </div>
      </div>
    </div>

    <script>
    const MODE_LABELS = {
      single_player:'Single Player', timed_quiz:'Timed Quiz',
      ranked_quiz:'Ranked Quiz', memory_match:'Memory Match', endless_quiz:'Endless Quiz'
    };

    function esc(s) {
      const d = document.createElement('div');
      d.appendChild(document.createTextNode(s||''));
      return d.innerHTML;
    }

    // ══════════════════════════════════════
    // LEADERBOARD TABLE
    // ══════════════════════════════════════
    async function loadLeaderboard(mode = '') {
      const body = document.getElementById('lbBody');
      body.innerHTML = `<tr><td colspan="6"><div class="lb-state"><div class="lb-state-icon">⏳</div><p>Loading...</p></div></td></tr>`;

      const url = mode
        ? `api/get_leaderboard.php?mode=${encodeURIComponent(mode)}`
        : 'api/get_leaderboard.php';

      try {
        const res    = await fetch(url);
        const result = await res.json();

        if (!result.success || !result.data.length) {
          body.innerHTML = `<tr><td colspan="6"><div class="lb-state"><div class="lb-state-icon">🎮</div><p>No scores yet — be the first to play!</p></div></td></tr>`;
          return;
        }

        body.innerHTML = result.data.map(e => buildRow(e)).join('');

      } catch (err) {
        body.innerHTML = `<tr><td colspan="6"><div class="lb-state"><div class="lb-state-icon">⚠️</div><p>Failed to load. Is XAMPP running?</p></div></td></tr>`;
      }
    }

    function buildRow(e) {
      const rank      = parseInt(e.rank);
      const rankClass = rank <= 3 ? `rank-${rank}` : '';
      const rankIcon  = rank === 1 ? '🥇' : rank === 2 ? '🥈' : rank === 3 ? '🥉' : rank;
      const rankBadge = rank <= 3
        ? `<div class="lb-rank r${rank}">${rankIcon}</div>`
        : `<div class="lb-rank rn">${rank}</div>`;

      const initial   = (e.fullname || e.username || '?').charAt(0).toUpperCase();
      const avatarHtml = e.avatar
        ? `<img src="${esc(e.avatar)}" class="lb-avatar-img" alt=""
               onerror="this.outerHTML='<div class=\\'lb-avatar-init\\'>${esc(initial)}</div>'">`
        : `<div class="lb-avatar-init">${esc(initial)}</div>`;

      const totalC = parseInt(e.total_correct)   || 0;
      const totalQ = parseInt(e.total_questions) || 0;
      const acc    = parseFloat(e.accuracy) || 0;
      const accCls = acc >= 80 ? 'high' : acc >= 50 ? 'mid' : 'low';

      return `
        <tr class="${rankClass}">
          <td class="center">${rankBadge}</td>
          <td>
            <div class="lb-player" onclick="openProfile(${e.user_id})" title="View ${esc(e.fullname||e.username)}'s stats">
              ${avatarHtml}
              <div>
                <div class="lb-player-name">${esc(e.fullname || e.username)}</div>
                <div class="lb-player-username">@${esc(e.username)}</div>
              </div>
            </div>
          </td>
          <td class="center"><span class="lb-points">${Number(e.total_points).toLocaleString()}</span></td>
          <td class="center">
            <span class="lb-ratio">
              <span class="lb-correct">${totalC}</span>
              <span class="lb-sep">/</span>
              <span class="lb-total">${totalQ}</span>
            </span>
          </td>
          <td class="center"><span class="lb-acc ${accCls}">${acc}%</span></td>
          <td class="center"><span class="lb-attempts">${e.attempts}</span></td>
        </tr>`;
    }

    // ══════════════════════════════════════
    // PROFILE MODAL
    // ══════════════════════════════════════
    async function openProfile(userId) {
      // Show overlay with spinner
      const overlay = document.getElementById('profileOverlay');
      const content = document.getElementById('profileModalContent');

      content.innerHTML = `
        <button class="pm-close" onclick="closeProfile()">✕</button>
        <div class="pm-loading">
          <div class="pm-loading-icon">⏳</div>
          <p>Loading profile...</p>
        </div>`;

      overlay.classList.add('open');
      document.body.style.overflow = 'hidden';

      try {
        const res    = await fetch(`api/get_public_profile.php?id=${userId}`);
        const data   = await res.json();

        if (!data.success) {
          content.innerHTML = `
            <button class="pm-close" onclick="closeProfile()">✕</button>
            <div class="pm-loading">
              <div class="pm-loading-icon">⚠️</div>
              <p>${esc(data.message)}</p>
            </div>`;
          return;
        }

        content.innerHTML = buildProfileModal(data);

      } catch(e) {
        content.innerHTML = `
          <button class="pm-close" onclick="closeProfile()">✕</button>
          <div class="pm-loading"><div class="pm-loading-icon">⚠️</div><p>Network error.</p></div>`;
      }
    }

    function buildProfileModal(data) {
      const u     = data.user;
      const s     = data.stats;
      const initial = (u.fullname || u.username || '?').charAt(0).toUpperCase();

      const avatarHtml = u.avatar
        ? `<img src="${esc(u.avatar)}" class="pm-avatar-img" alt="Avatar"
               onerror="this.outerHTML='<div class=\\'pm-avatar-init\\'>${esc(initial)}</div>'">`
        : `<div class="pm-avatar-init">${esc(initial)}</div>`;

      const accColor = s.accuracy >= 80 ? '#4ade80' : s.accuracy >= 50 ? '#fbbf24' : '#f87171';

      // Per-mode breakdown
      const modeRows = data.by_mode.length > 0
        ? data.by_mode.map(m => {
            const mAcc = m.total > 0 ? Math.round((m.correct / m.total) * 100) : 0;
            return `
              <div class="pm-mode-row">
                <span class="pm-mode-badge ${m.mode}">${MODE_LABELS[m.mode]||m.mode}</span>
                <span class="pm-mode-att">${m.attempts} attempt${m.attempts!=1?'s':''}</span>
                <span class="pm-mode-acc">${mAcc}% acc</span>
                <span class="pm-mode-pts">${Number(m.pts).toLocaleString()} pts</span>
              </div>`;
          }).join('')
        : `<div class="pm-empty">No mode data yet</div>`;

      // Recent attempts
      const recentRows = data.recent.length > 0
        ? data.recent.map(r => {
            const date  = new Date(r.created_at).toLocaleDateString('en-US',{month:'short',day:'numeric',year:'numeric'});
            const title = r.quiz_title || 'Endless Quiz';
            return `
              <div class="pm-recent-row">
                <span class="pm-mode-badge ${r.mode}" style="font-size:9px;padding:2px 7px;">${MODE_LABELS[r.mode]||r.mode}</span>
                <span class="pm-recent-title">${esc(title)}</span>
                <span class="pm-recent-score">+${Number(r.points_earned).toLocaleString()}</span>
                <span class="pm-recent-date">${date}</span>
              </div>`;
          }).join('')
        : `<div class="pm-empty">No recent attempts</div>`;

      return `
        <!-- Header -->
        <div class="pm-header">
          <button class="pm-close" onclick="closeProfile()">✕</button>
          ${avatarHtml}
          <div class="pm-header-info">
            <div class="pm-name">${esc(u.fullname || u.username)}</div>
            <div class="pm-username">@${esc(u.username)}</div>
            <div class="pm-badges">
              <span class="pm-badge rank">🏅 Rank #${s.global_rank || '—'}</span>
              <span class="pm-badge level">⚡ Level ${s.level}</span>
              <span class="pm-badge since">📅 ${esc(u.member_since)}</span>
            </div>
          </div>
        </div>

        <!-- Body -->
        <div class="pm-body">

          <!-- Stat boxes -->
          <div class="pm-stats-grid">
            <div class="pm-stat">
              <div class="pm-stat-val">${Number(s.total_points).toLocaleString()}</div>
              <div class="pm-stat-lbl">Total Points</div>
            </div>
            <div class="pm-stat cyan">
              <div class="pm-stat-val">${s.total_attempts}</div>
              <div class="pm-stat-lbl">Quizzes Taken</div>
            </div>
            <div class="pm-stat green">
              <div class="pm-stat-val" style="color:${accColor}">${s.accuracy}%</div>
              <div class="pm-stat-lbl">Accuracy</div>
            </div>
            <div class="pm-stat gold">
              <div class="pm-stat-val">${Number(s.best_score).toLocaleString()}</div>
              <div class="pm-stat-lbl">Best Score</div>
            </div>
          </div>

          <!-- XP bar -->
          <div class="pm-xp-wrap">
            <div class="pm-xp-label">
              <span>Level ${s.level}</span>
              <span>${s.xp_in_level} / 500 XP</span>
            </div>
            <div class="pm-xp-bar">
              <div class="pm-xp-fill" style="width:${s.xp_pct}%"></div>
            </div>
          </div>

          <!-- Correct / Total summary -->
          <div style="background:rgba(255,255,255,.04);border:1px solid rgba(255,255,255,.07);border-radius:10px;padding:14px 16px;display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:10px;">
            <span style="font-size:13px;color:rgba(255,255,255,.5);">Total Correct Answers</span>
            <span style="font-size:18px;font-weight:800;">
              <span style="color:#4ade80;">${s.total_correct}</span>
              <span style="color:rgba(255,255,255,.25);margin:0 4px;">/</span>
              <span style="color:rgba(255,255,255,.55);">${s.total_questions}</span>
            </span>
          </div>

          <!-- Per-mode breakdown -->
          <div>
            <div class="pm-section-title">Performance by Mode</div>
            ${modeRows}
          </div>

          <!-- Recent attempts -->
          <div>
            <div class="pm-section-title">Recent Attempts</div>
            ${recentRows}
          </div>

        </div>`;
    }

    function closeProfile() {
      document.getElementById('profileOverlay').classList.remove('open');
      document.body.style.overflow = '';
    }

    // Close on backdrop click
    document.getElementById('profileOverlay').addEventListener('click', function(e) {
      if (e.target === this) closeProfile();
    });

    // Close on ESC
    document.addEventListener('keydown', e => { if (e.key === 'Escape') closeProfile(); });

    // ── Boot ──
    loadLeaderboard('');
    </script>
  </body>
</html>
