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
        position: relative;
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

      /* Special glow ring for endless */
      .mode-card[data-mode="endless_quiz"] {
        border-color: rgba(139,92,246,.3);
      }
      .mode-card[data-mode="endless_quiz"]:hover {
        border-color: #8b5cf6;
        box-shadow: 0 16px 40px rgba(0,0,0,.4), 0 0 40px rgba(139,92,246,.35);
      }

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

      /* "No quiz selection needed" tag for endless */
      .mode-special-tag {
        position: absolute;
        top: -10px;
        right: 16px;
        background: linear-gradient(135deg, #8b5cf6, #6d28d9);
        color: #fff;
        font-size: 10px;
        font-weight: 800;
        padding: 3px 10px;
        border-radius: 20px;
        letter-spacing: .5px;
        box-shadow: 0 2px 10px rgba(139,92,246,.5);
      }

      /* ── Quiz overlay ── */
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
        to   { opacity:1; transform:translateY(0)    scale(1);   }
      }

      .panel-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 6px;
      }

      .panel-title { font-size: 20px; font-weight: 700; color: #fff; }
      .panel-title span { color: #ff2fb3; }
      .panel-sub { font-size: 13px; color: rgba(255,255,255,.4); margin-bottom: 24px; }

      .btn-panel-close {
        width: 36px; height: 36px;
        border-radius: 50%;
        border: 1px solid rgba(255,255,255,.18);
        background: rgba(255,255,255,.06);
        color: #fff; font-size: 18px; cursor: pointer;
        display: flex; align-items: center; justify-content: center;
        transition: background .2s, border-color .2s;
      }
      .btn-panel-close:hover { background: rgba(255,47,179,.25); border-color: #ff2fb3; }

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
        display: flex; flex-direction: column; gap: 8px;
      }
      .quiz-pick-card:hover {
        border-color: #ff2fb3;
        background: rgba(255,47,179,.07);
        transform: translateY(-3px);
        box-shadow: 0 8px 24px rgba(255,47,179,.18);
      }

      .qpc-title { font-size: 15px; font-weight: 700; color: #fff; line-height: 1.35; }
      .qpc-meta  { display: flex; align-items: center; gap: 8px; flex-wrap: wrap; }
      .qpc-category { font-size: 12px; color: #ff2fb3; font-weight: 600; }
      .qpc-diff { font-size: 11px; font-weight: 700; padding: 2px 8px; border-radius: 6px; color: #000; }
      .qpc-diff.easy { background:#4ade80; } .qpc-diff.medium { background:#fbbf24; } .qpc-diff.hard { background:#f87171; }
      .qpc-count { font-size: 12px; color: rgba(255,255,255,.4); }
      .qpc-arrow { margin-top: 4px; text-align: right; font-size: 16px; color: rgba(255,47,179,.45); transition: transform .2s, color .2s; }
      .quiz-pick-card:hover .qpc-arrow { transform: translateX(5px); color: #ff2fb3; }

      .panel-state {
        grid-column: 1/-1; text-align: center; padding: 56px 20px; color: rgba(255,255,255,.45);
      }
      .panel-state-icon { font-size: 40px; margin-bottom: 12px; }
      .panel-state p    { font-size: 15px; line-height: 1.6; }

      @media (max-width: 680px) {
        .modes-grid { grid-template-columns: 1fr; }
        .quiz-panel { padding: 22px 16px; }
        .quiz-list  { grid-template-columns: 1fr; }
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
        <p class="modes-subtitle">Pick a mode, select a quiz, then start playing!</p>

        <div class="modes-grid">

          <div class="mode-card" data-mode="single_player" onclick="openPanel('single_player')">
            <span class="mode-icon">🎮</span>
            <div class="mode-name">Single Player</div>
            <div class="mode-desc">Classic quiz at your own pace. No pressure — just you and the questions.</div>
            <span class="mode-badge">Classic</span>
          </div>

          <div class="mode-card" data-mode="timed_quiz" onclick="openPanel('timed_quiz')">
            <span class="mode-icon">⏱️</span>
            <div class="mode-name">Timed Quiz</div>
            <div class="mode-desc">Answer all questions before 60 seconds runs out for bonus points.</div>
            <span class="mode-badge">60 sec</span>
          </div>

          <div class="mode-card" data-mode="ranked_quiz" onclick="openPanel('ranked_quiz')">
            <span class="mode-icon">⚔️</span>
            <div class="mode-name">Ranked Quiz</div>
            <div class="mode-desc">Compete for leaderboard glory. Score weighted by speed and accuracy.</div>
            <span class="mode-badge">Competitive</span>
          </div>

          <div class="mode-card" data-mode="memory_match" onclick="openPanel('memory_match')">
            <span class="mode-icon">🧠</span>
            <div class="mode-name">Memory Match</div>
            <div class="mode-desc">Flip cards to match terms with definitions before the timer runs out.</div>
            <span class="mode-badge">3 min</span>
          </div>

          <!-- Endless — goes directly, no quiz picker needed -->
          <div class="mode-card" data-mode="endless_quiz" onclick="launchEndless()">
            <span class="mode-special-tag">✨ All questions</span>
            <span class="mode-icon">♾️</span>
            <div class="mode-name">Endless Quiz</div>
            <div class="mode-desc">Every question in the database, shuffled and non-stop. 3 lives — how far can you go?</div>
            <span class="mode-badge">3 lives</span>
          </div>

        </div>
      </div>
    </main>

    <!-- Quiz Selection Overlay (for non-endless modes) -->
    <div class="quiz-overlay" id="quizOverlay">
      <div class="quiz-panel">
        <div class="panel-header">
          <div class="panel-title">Select a Quiz — <span id="panelModeName">Mode</span></div>
          <button class="btn-panel-close" onclick="closePanel()">✕</button>
        </div>
        <div class="panel-sub" id="panelSub">Loading quizzes...</div>
        <div class="quiz-list" id="quizList">
          <div class="panel-state"><div class="panel-state-icon">⏳</div><p>Loading...</p></div>
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

      /* ── Endless goes straight to its own page ── */
      function launchEndless() {
        localStorage.setItem('selectedMode', 'endless_quiz');
        localStorage.removeItem('selectedQuizId');
        window.location.href = 'endless.php';
      }

      /* ── Other modes open the quiz picker ── */
      async function openPanel(mode) {
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
        localStorage.removeItem('selectedQuizId');
      }

      document.getElementById('quizOverlay').addEventListener('click', function(e) {
        if (e.target === this) closePanel();
      });

      document.addEventListener('keydown', e => { if (e.key === 'Escape') closePanel(); });

      function showPanelError(msg) {
        document.getElementById('panelSub').textContent = '';
        document.getElementById('quizList').innerHTML   = `
          <div class="panel-state"><div class="panel-state-icon">⚠️</div><p>${msg}</p></div>`;
      }

      function esc(s) {
        const d = document.createElement('div');
        d.appendChild(document.createTextNode(s || ''));
        return d.innerHTML;
      }
    </script>
  </body>
</html>
