<?php session_start(); ?>
<!doctype html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>8BitBrain - Game Modes</title>
    <link rel="stylesheet" href="style.css" />
    <link rel="icon" href="imgs/Sans_Favi.png" type="image/png" />
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=Bitcount+Single:wght@100..900&display=swap" rel="stylesheet" />
    <style>
      /* ── Page ── */
      .modes-page {
        min-height: 100vh;
        padding: 100px 24px 60px;
        display: flex;
        flex-direction: column;
        align-items: center;
      }

      .modes-page h1 {
        color: #fff;
        font-size: 40px;
        text-shadow: #f70606 3px 3px;
        margin-bottom: 8px;
        text-align: center;
      }

      .modes-subtitle {
        color: rgba(255,255,255,.55);
        font-size: 15px;
        margin-bottom: 48px;
        text-align: center;
      }

      /* ── Mode cards ── */
      .modes-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(260px, 1fr));
        gap: 22px;
        width: 100%;
        max-width: 1080px;
      }

      .mode-card {
        background: rgba(255,255,255,.05);
        backdrop-filter: blur(12px);
        border: 1.5px solid rgba(255,255,255,.13);
        border-radius: 18px;
        padding: 30px 24px;
        cursor: pointer;
        transition: transform .25s, border-color .25s, box-shadow .25s;
        text-align: center;
        user-select: none;
      }

      .mode-card:hover {
        transform: translateY(-6px);
        border-color: var(--mc, #ff2fb3);
        box-shadow: 0 16px 40px rgba(0,0,0,.4), 0 0 30px var(--mg, rgba(255,47,179,.25));
      }

      .mode-card:active { transform: translateY(-2px); }

      .mode-card[data-mode="single_player"] { --mc:#ff2fb3; --mg:rgba(255,47,179,.3); }
      .mode-card[data-mode="timed_quiz"]    { --mc:#f59e0b; --mg:rgba(245,158,11,.3); }
      .mode-card[data-mode="ranked_quiz"]   { --mc:#ef4444; --mg:rgba(239,68,68,.3);  }
      .mode-card[data-mode="memory_match"]  { --mc:#10b981; --mg:rgba(16,185,129,.3); }
      .mode-card[data-mode="endless_quiz"]  { --mc:#8b5cf6; --mg:rgba(139,92,246,.3); }

      .mode-icon {
        font-size: 50px;
        margin-bottom: 14px;
        display: block;
        transition: transform .25s;
      }
      .mode-card:hover .mode-icon { transform: scale(1.15); }

      .mode-name {
        font-size: 20px;
        font-weight: 700;
        color: #fff;
        margin-bottom: 10px;
      }

      .mode-desc {
        font-size: 13px;
        color: rgba(255,255,255,.55);
        line-height: 1.6;
        margin-bottom: 16px;
      }

      .mode-badge {
        display: inline-block;
        padding: 4px 14px;
        border-radius: 20px;
        font-size: 11px;
        font-weight: 700;
        border: 1px solid var(--mc, #ff2fb3);
        color: var(--mc, #ff2fb3);
        text-transform: uppercase;
        letter-spacing: .5px;
      }

      /* ── Endless card special START button ── */
      .endless-start-btn {
        display: inline-block;
        margin-top: 14px;
        padding: 10px 28px;
        background: linear-gradient(135deg, #8b5cf6, #6d28d9);
        color: #fff;
        border: none;
        border-radius: 50px;
        font-size: 15px;
        font-weight: 800;
        font-family: inherit;
        cursor: pointer;
        transition: transform .2s, box-shadow .2s;
        box-shadow: 0 4px 18px rgba(139,92,246,.45);
        letter-spacing: .5px;
      }

      .endless-start-btn:hover {
        transform: translateY(-3px);
        box-shadow: 0 8px 28px rgba(139,92,246,.65);
      }

      .endless-start-btn:active { transform: translateY(-1px); }

      /* ── Login prompt overlay for endless ── */
      .login-prompt-overlay {
        display: none;
        position: fixed;
        inset: 0;
        z-index: 400;
        background: rgba(0,0,0,.82);
        backdrop-filter: blur(8px);
        align-items: center;
        justify-content: center;
        padding: 20px;
      }

      .login-prompt-overlay.open { display: flex; }

      .login-prompt-modal {
        background: linear-gradient(135deg, rgba(14,6,34,.98), rgba(8,3,20,.98));
        border: 2px solid rgba(139,92,246,.5);
        border-radius: 20px;
        padding: 40px 36px;
        width: 100%;
        max-width: 420px;
        text-align: center;
        box-shadow: 0 24px 80px rgba(0,0,0,.7), 0 0 50px rgba(139,92,246,.15);
        animation: popIn .3s cubic-bezier(.22,1,.36,1);
      }

      @keyframes popIn {
        from { opacity:0; transform:scale(.94) translateY(20px); }
        to   { opacity:1; transform:scale(1) translateY(0); }
      }

      .lp-icon  { font-size:52px; margin-bottom:14px; }
      .lp-title { font-size:22px; font-weight:800; color:#fff; margin-bottom:8px; text-shadow:#f70606 2px 2px; }
      .lp-sub   { font-size:14px; color:rgba(255,255,255,.5); margin-bottom:28px; line-height:1.6; }

      .lp-actions { display:flex; gap:12px; justify-content:center; }

      .lp-btn-login {
        position: relative;
        padding: 13px 28px;
        font-size: 15px;
        font-weight: 700;
        font-family: inherit;
        color: #fff;
        background: #120b24;
        border: none;
        border-radius: 50px;
        cursor: pointer;
        overflow: visible;
        transition: background .3s;
      }

      @property --lpfill { syntax:'<percentage>'; inherits:true; initial-value:0%; }

      .lp-btn-login::after {
        content:''; position:absolute; inset:-2px; border-radius:inherit; z-index:-1;
        background: conic-gradient(from 0deg,#8b5cf6,#ff2fb3,#38bdf8,#8b5cf6 var(--lpfill),transparent var(--lpfill));
        transition: --lpfill .5s ease;
      }
      .lp-btn-login:hover { background:#1a1033; --lpfill:100%; }

      .lp-btn-cancel {
        padding: 13px 22px;
        font-size: 14px;
        font-family: inherit;
        color: rgba(255,255,255,.5);
        background: transparent;
        border: 1.5px solid rgba(255,255,255,.15);
        border-radius: 50px;
        cursor: pointer;
        transition: color .2s, border-color .2s;
      }
      .lp-btn-cancel:hover { color:#fff; border-color:rgba(255,255,255,.35); }

      /* ── Quiz selection overlay (for non-endless modes) ── */
      .quiz-overlay {
        display: none;
        position: fixed;
        inset: 0;
        z-index: 300;
        background: rgba(0,0,0,.78);
        backdrop-filter: blur(6px);
        align-items: flex-start;
        justify-content: center;
        padding: 80px 20px 40px;
        overflow-y: auto;
      }

      .quiz-overlay.open { display: flex; }

      .quiz-panel {
        background: linear-gradient(135deg, rgba(20,8,44,.98), rgba(10,4,24,.98));
        border: 1.5px solid rgba(255,47,179,.35);
        border-radius: 20px;
        width: 100%;
        max-width: 880px;
        padding: 36px;
        box-shadow: 0 24px 80px rgba(0,0,0,.65), 0 0 50px rgba(255,47,179,.1);
        animation: panelSlide .3s cubic-bezier(.22,1,.36,1);
      }

      @keyframes panelSlide {
        from { opacity:0; transform:translateY(28px) scale(.97); }
        to   { opacity:1; transform:translateY(0) scale(1); }
      }

      .panel-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 6px;
      }

      .panel-title { font-size:20px; font-weight:700; color:#fff; }
      .panel-title span { color:#ff2fb3; }
      .panel-sub { font-size:13px; color:rgba(255,255,255,.4); margin-bottom:24px; }

      .btn-panel-close {
        width:36px; height:36px; border-radius:50%;
        border:1px solid rgba(255,255,255,.18);
        background:rgba(255,255,255,.06);
        color:#fff; font-size:18px; cursor:pointer;
        display:flex; align-items:center; justify-content:center;
        transition:background .2s, border-color .2s; flex-shrink:0;
      }
      .btn-panel-close:hover { background:rgba(255,47,179,.25); border-color:#ff2fb3; }

      .quiz-list {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(220px, 1fr));
        gap: 14px;
      }

      .quiz-pick-card {
        background: rgba(255,255,255,.04);
        border: 1.5px solid rgba(255,255,255,.1);
        border-radius: 14px;
        padding: 20px 18px;
        cursor: pointer;
        transition: border-color .22s, background .22s, transform .22s, box-shadow .22s;
        display: flex;
        flex-direction: column;
        gap: 8px;
      }

      .quiz-pick-card:hover {
        border-color: #ff2fb3;
        background: rgba(255,47,179,.07);
        transform: translateY(-3px);
        box-shadow: 0 8px 24px rgba(255,47,179,.18);
      }

      .quiz-pick-card:active { transform: translateY(-1px); }

      .qpc-title  { font-size:15px; font-weight:700; color:#fff; line-height:1.35; }
      .qpc-meta   { display:flex; align-items:center; gap:8px; flex-wrap:wrap; }
      .qpc-category { font-size:12px; color:#ff2fb3; font-weight:600; }
      .qpc-diff   { font-size:11px; font-weight:700; padding:2px 8px; border-radius:6px; color:#000; }
      .qpc-diff.easy   { background:#4ade80; }
      .qpc-diff.medium { background:#fbbf24; }
      .qpc-diff.hard   { background:#f87171; }
      .qpc-count  { font-size:12px; color:rgba(255,255,255,.4); }
      .qpc-arrow  { margin-top:4px; text-align:right; font-size:16px; color:rgba(255,47,179,.45); transition:transform .2s, color .2s; }
      .quiz-pick-card:hover .qpc-arrow { transform:translateX(5px); color:#ff2fb3; }

      .panel-state { grid-column:1 / -1; text-align:center; padding:56px 20px; color:rgba(255,255,255,.45); }
      .panel-state-icon { font-size:40px; margin-bottom:12px; }
      .panel-state p { font-size:15px; line-height:1.6; }

      @media (max-width: 680px) {
        .modes-grid { grid-template-columns:1fr; }
        .quiz-panel { padding:22px 16px; }
        .quiz-list  { grid-template-columns:1fr; }
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
      <div class="modes-page">
        <h1>Choose a Game Mode</h1>
        <p class="modes-subtitle">Pick a mode and start playing!</p>

        <div class="modes-grid">

          <!-- Single Player -->
          <div class="mode-card" data-mode="single_player" onclick="openPanel('single_player')">
            <span class="mode-icon">🎮</span>
            <div class="mode-name">Single Player</div>
            <div class="mode-desc">Classic quiz at your own pace. No pressure — just you and the questions.</div>
            <span class="mode-badge">Classic</span>
          </div>

          <!-- Timed Quiz -->
          <div class="mode-card" data-mode="timed_quiz" onclick="openPanel('timed_quiz')">
            <span class="mode-icon">⏱️</span>
            <div class="mode-name">Timed Quiz</div>
            <div class="mode-desc">Answer all questions before 60 seconds runs out for bonus points.</div>
            <span class="mode-badge">60 sec</span>
          </div>

          <!-- Ranked Quiz -->
          <div class="mode-card" data-mode="ranked_quiz" onclick="openPanel('ranked_quiz')">
            <span class="mode-icon">⚔️</span>
            <div class="mode-name">Ranked Quiz</div>
            <div class="mode-desc">Compete for leaderboard glory. Score weighted by speed and accuracy.</div>
            <span class="mode-badge">Competitive</span>
          </div>

          <!-- Memory Match -->
          <div class="mode-card" data-mode="memory_match" onclick="openPanel('memory_match')">
            <span class="mode-icon">🧠</span>
            <div class="mode-name">Memory Match</div>
            <div class="mode-desc">Flip cards to match terms with definitions before the timer runs out.</div>
            <span class="mode-badge">3 min</span>
          </div>

          <!-- Endless Quiz — NO panel, direct launch -->
          <div class="mode-card" data-mode="endless_quiz" onclick="launchEndless()">
            <span class="mode-icon">♾️</span>
            <div class="mode-name">Endless Quiz</div>
            <div class="mode-desc">Answer questions from the entire question bank until you run out of 3 lives. How far can your streak reach?</div>
            <span class="mode-badge">3 lives</span>
            <br>
            <button class="endless-start-btn" onclick="event.stopPropagation(); launchEndless();">
              ♾️ Start Endless
            </button>
          </div>

        </div>
      </div>
    </main>

    <!-- Quiz Selection Overlay (for non-endless modes only) -->
    <div class="quiz-overlay" id="quizOverlay">
      <div class="quiz-panel" id="quizPanel">
        <div class="panel-header">
          <div class="panel-title">Select a Quiz — <span id="panelModeName">Mode</span></div>
          <button class="btn-panel-close" onclick="closePanel()" title="Close">✕</button>
        </div>
        <div class="panel-sub" id="panelSub">Loading quizzes...</div>
        <div class="quiz-list" id="quizList">
          <div class="panel-state">
            <div class="panel-state-icon">⏳</div>
            <p>Loading...</p>
          </div>
        </div>
      </div>
    </div>

    <!-- Login prompt for guests trying Endless -->
    <div class="login-prompt-overlay" id="loginPromptOverlay">
      <div class="login-prompt-modal">
        <div class="lp-icon">🔐</div>
        <div class="lp-title">Login Required</div>
        <div class="lp-sub">You need to be logged in to play Endless Quiz and save your score to the leaderboard.</div>
        <div class="lp-actions">
          <button class="lp-btn-login" onclick="window.location.href='login.html'">Login / Sign Up</button>
          <button class="lp-btn-cancel" onclick="closeLoginPrompt()">Cancel</button>
        </div>
      </div>
    </div>

    <script>
      const MODE_LABELS = {
        single_player: 'Single Player',
        timed_quiz:    'Timed Quiz',
        ranked_quiz:   'Ranked Quiz',
        memory_match:  'Memory Match',
        endless_quiz:  'Endless Quiz'
      };

      // ── Endless Quiz — direct launch ───────────────────────────────────────
      function launchEndless() {
        <?php if (isset($_SESSION['logged_in']) && $_SESSION['logged_in']): ?>
          // Logged in — go straight to endless.php
          localStorage.setItem('selectedMode', 'endless_quiz');
          window.location.href = 'endless.php';
        <?php else: ?>
          // Guest — show login prompt
          document.getElementById('loginPromptOverlay').classList.add('open');
          document.body.style.overflow = 'hidden';
        <?php endif; ?>
      }

      function closeLoginPrompt() {
        document.getElementById('loginPromptOverlay').classList.remove('open');
        document.body.style.overflow = '';
      }

      // ── Quiz panel (for non-endless modes) ────────────────────────────────
      var activeMode = null;

      async function openPanel(mode) {
        activeMode = mode;
        localStorage.removeItem('selectedQuizId');
        localStorage.setItem('selectedMode', mode);

        document.getElementById('panelModeName').textContent = MODE_LABELS[mode] || mode;
        document.getElementById('panelSub').textContent      = 'Loading quizzes...';
        document.getElementById('quizList').innerHTML = `
          <div class="panel-state">
            <div class="panel-state-icon">⏳</div>
            <p>Loading <strong>${MODE_LABELS[mode]}</strong> quizzes...</p>
          </div>`;

        document.getElementById('quizOverlay').classList.add('open');
        document.body.style.overflow = 'hidden';

        try {
          const res  = await fetch('api/get_quizzes.php?mode=' + encodeURIComponent(mode));
          const data = await res.json();

          if (!data.success) { showPanelError('Could not load quizzes. Is XAMPP running?'); return; }
          renderQuizList(data.data, mode);
        } catch (e) {
          showPanelError('Network error. Make sure XAMPP Apache is running.');
        }
      }

      function renderQuizList(quizzes, mode) {
        const list = document.getElementById('quizList');
        const sub  = document.getElementById('panelSub');

        if (!quizzes || quizzes.length === 0) {
          sub.textContent = 'No quizzes available for this mode yet.';
          list.innerHTML  = `
            <div class="panel-state">
              <div class="panel-state-icon">🎲</div>
              <p>No quizzes for <strong>${MODE_LABELS[mode]}</strong> yet.<br>
                 <span style="opacity:.6;font-size:13px;">Ask your admin to add some!</span></p>
            </div>`;
          return;
        }

        sub.textContent = `${quizzes.length} quiz${quizzes.length !== 1 ? 'zes' : ''} available — pick one to start`;

        list.innerHTML = quizzes.map(q => {
          const count = parseInt(q.question_count) || 0;
          return `
            <div class="quiz-pick-card" onclick="launchQuiz(${q.id}, '${mode}')">
              <div class="qpc-title">${esc(q.title)}</div>
              <div class="qpc-meta">
                <span class="qpc-category">${esc(q.category)}</span>
                <span class="qpc-diff ${q.difficulty}">${q.difficulty}</span>
              </div>
              <div class="qpc-count">${count} question${count !== 1 ? 's' : ''}</div>
              <div class="qpc-arrow">→</div>
            </div>`;
        }).join('');
      }

      function launchQuiz(quizId, mode) {
        localStorage.setItem('selectedMode',   mode);
        localStorage.setItem('selectedQuizId', quizId);
        window.location.href = 'quiz.php';
      }

      function closePanel() {
        document.getElementById('quizOverlay').classList.remove('open');
        document.body.style.overflow = '';
        activeMode = null;
        localStorage.removeItem('selectedQuizId');
      }

      // Close panel on backdrop click
      document.getElementById('quizOverlay').addEventListener('click', function(e) {
        if (e.target === this) closePanel();
      });

      // Close login prompt on backdrop click
      document.getElementById('loginPromptOverlay').addEventListener('click', function(e) {
        if (e.target === this) closeLoginPrompt();
      });

      // ESC key
      document.addEventListener('keydown', e => {
        if (e.key === 'Escape') { closePanel(); closeLoginPrompt(); }
      });

      function showPanelError(msg) {
        document.getElementById('panelSub').textContent = '';
        document.getElementById('quizList').innerHTML = `
          <div class="panel-state">
            <div class="panel-state-icon">⚠️</div>
            <p>${msg}</p>
          </div>`;
      }

      function esc(s) {
        const d = document.createElement('div');
        d.appendChild(document.createTextNode(s || ''));
        return d.innerHTML;
      }
    </script>
  </body>
</html>
