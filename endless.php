<?php
session_start();
if (!isset($_SESSION['logged_in']) || $_SESSION['account_type'] !== 'user') {
    header("Location: login.html");
    exit();
}
?>
<!doctype html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>8BitBrain — Endless Quiz</title>
    <link rel="stylesheet" href="style.css" />
    <link rel="icon" href="imgs/Sans_Favi.png" type="image/png" />
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=Bitcount+Single:wght@100..900&display=swap" rel="stylesheet" />
    <style>
    /* ══════════════════════════════════════════
       ENDLESS QUIZ — full page layout
    ══════════════════════════════════════════ */

    * { box-sizing: border-box; }

    /* ── Loading ── */
    #elLoading {
        min-height: 100vh;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        gap: 18px;
        color: #fff;
        padding-top: 70px;
    }

    .el-loading-icon { font-size: 56px; animation: spin 1.4s linear infinite; }
    @keyframes spin { to { transform: rotate(360deg); } }

    .el-loading-text { font-size: 20px; opacity: .7; }

    /* ── Game wrapper ── */
    #elGame {
        display: none;
        min-height: 100vh;
        padding: 80px 24px 60px;
        flex-direction: column;
        align-items: center;
    }

    .el-container {
        width: 100%;
        max-width: 760px;
        display: flex;
        flex-direction: column;
        gap: 0;
    }

    /* ── Top HUD ── */
    .el-hud {
        display: flex;
        align-items: center;
        justify-content: space-between;
        flex-wrap: wrap;
        gap: 14px;
        margin-bottom: 28px;
    }

    .el-hud-left { display: flex; flex-direction: column; gap: 4px; }

    .el-mode-label {
        font-size: 11px;
        font-weight: 700;
        letter-spacing: 2px;
        text-transform: uppercase;
        color: #8b5cf6;
    }

    .el-title {
        font-size: 22px;
        font-weight: 700;
        color: #fff;
        text-shadow: #f70606 2px 2px;
    }

    /* Lives display */
    .el-lives {
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .el-lives-label {
        font-size: 13px;
        color: rgba(255,255,255,.5);
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 1px;
    }

    .el-hearts {
        display: flex;
        gap: 6px;
    }

    .el-heart {
        font-size: 28px;
        line-height: 1;
        transition: all .3s cubic-bezier(.22,1,.36,1);
        filter: drop-shadow(0 0 6px rgba(239,68,68,.6));
    }

    .el-heart.lost {
        opacity: .18;
        filter: grayscale(1);
        transform: scale(.8);
    }

    /* Score + streak */
    .el-stats-row {
        display: flex;
        gap: 20px;
        align-items: center;
        flex-wrap: wrap;
    }

    .el-stat {
        display: flex;
        flex-direction: column;
        align-items: center;
        background: rgba(255,255,255,.05);
        border: 1px solid rgba(255,255,255,.1);
        border-radius: 12px;
        padding: 10px 18px;
        min-width: 80px;
        transition: border-color .2s;
    }

    .el-stat-val {
        font-size: 26px;
        font-weight: 700;
        color: #ff2fb3;
        text-shadow: 0 0 10px rgba(255,47,179,.5);
        line-height: 1;
    }

    .el-stat-lbl {
        font-size: 10px;
        color: rgba(255,255,255,.45);
        text-transform: uppercase;
        letter-spacing: 1px;
        margin-top: 4px;
    }

    .el-stat.streak-stat .el-stat-val { color: #fbbf24; text-shadow: 0 0 10px rgba(251,191,36,.5); }
    .el-stat.streak-stat { border-color: rgba(251,191,36,.3); }

    /* ── Progress bar (questions answered) ── */
    .el-progress {
        margin-bottom: 24px;
        display: flex;
        flex-direction: column;
        gap: 6px;
    }

    .el-progress-top {
        display: flex;
        justify-content: space-between;
        font-size: 12px;
        color: rgba(255,255,255,.4);
    }

    .el-progress-bar {
        height: 5px;
        background: rgba(255,255,255,.08);
        border-radius: 10px;
        overflow: hidden;
    }

    .el-progress-fill {
        height: 100%;
        border-radius: 10px;
        background: linear-gradient(90deg, #8b5cf6, #ff2fb3);
        transition: width .4s ease;
        box-shadow: 0 0 8px rgba(139,92,246,.5);
    }

    /* ── Question card ── */
    .el-question-card {
        background: rgba(10,4,28,.8);
        backdrop-filter: blur(16px);
        border: 1.5px solid rgba(139,92,246,.3);
        border-radius: 18px;
        padding: 32px 30px 26px;
        margin-bottom: 20px;
        box-shadow: 0 0 40px rgba(139,92,246,.08), 0 8px 40px rgba(0,0,0,.4);
        animation: qCardIn .35s cubic-bezier(.22,1,.36,1);
    }

    @keyframes qCardIn {
        from { opacity:0; transform:translateY(14px) scale(.98); }
        to   { opacity:1; transform:translateY(0)    scale(1);   }
    }

    /* Source tag (which quiz this came from) */
    .el-source {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        background: rgba(139,92,246,.15);
        border: 1px solid rgba(139,92,246,.35);
        border-radius: 6px;
        padding: 4px 10px;
        font-size: 11px;
        color: #a78bfa;
        margin-bottom: 16px;
        font-weight: 600;
        letter-spacing: .3px;
    }

    .el-source-diff {
        font-size: 10px;
        font-weight: 700;
        padding: 2px 7px;
        border-radius: 4px;
        color: #000;
    }
    .el-source-diff.easy   { background: #4ade80; }
    .el-source-diff.medium { background: #fbbf24; }
    .el-source-diff.hard   { background: #f87171; }

    .el-question-number {
        font-size: 12px;
        color: rgba(255,255,255,.35);
        margin-bottom: 10px;
        letter-spacing: .5px;
    }

    .el-question-text {
        font-size: 20px;
        font-weight: 600;
        color: #fff;
        line-height: 1.55;
        margin: 0;
    }

    /* ── Answer options ── */
    .el-options {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 12px;
        margin-bottom: 20px;
    }

    .el-option {
        background: rgba(255,255,255,.05);
        border: 2px solid rgba(255,255,255,.12);
        border-radius: 12px;
        padding: 16px 18px;
        font-size: 15px;
        color: #fff;
        cursor: pointer;
        transition: border-color .18s, background .18s, transform .18s, box-shadow .18s;
        line-height: 1.45;
        text-align: left;
        font-family: inherit;
        background: none;
        width: 100%;
    }

    .el-option:hover:not(:disabled) {
        border-color: #8b5cf6;
        background: rgba(139,92,246,.12);
        transform: translateY(-2px);
        box-shadow: 0 4px 16px rgba(139,92,246,.2);
    }

    .el-option:disabled { cursor: not-allowed; }

    .el-option.correct {
        border-color: #4ade80 !important;
        background: rgba(74,222,128,.18) !important;
        box-shadow: 0 0 20px rgba(74,222,128,.25) !important;
    }

    .el-option.wrong {
        border-color: #f87171 !important;
        background: rgba(248,113,113,.18) !important;
        box-shadow: 0 0 20px rgba(248,113,113,.2) !important;
        animation: wrongShake .4s ease;
    }

    .el-option.reveal-correct {
        border-color: rgba(74,222,128,.5) !important;
        background: rgba(74,222,128,.08) !important;
    }

    @keyframes wrongShake {
        0%,100% { transform: translateX(0); }
        20%     { transform: translateX(-6px); }
        40%     { transform: translateX(6px); }
        60%     { transform: translateX(-4px); }
        80%     { transform: translateX(4px); }
    }

    /* ── Streak bonus toast ── */
    .el-streak-toast {
        position: fixed;
        top: 90px;
        left: 50%;
        transform: translateX(-50%) translateY(-20px);
        background: linear-gradient(135deg, #fbbf24, #f59e0b);
        color: #000;
        font-size: 14px;
        font-weight: 800;
        padding: 10px 22px;
        border-radius: 50px;
        box-shadow: 0 4px 20px rgba(251,191,36,.5);
        opacity: 0;
        pointer-events: none;
        transition: opacity .25s, transform .25s;
        z-index: 400;
        letter-spacing: .5px;
    }

    .el-streak-toast.show {
        opacity: 1;
        transform: translateX(-50%) translateY(0);
    }

    /* ── Quit button ── */
    .el-quit-btn {
        align-self: flex-end;
        padding: 9px 18px;
        background: transparent;
        border: 1.5px solid rgba(255,255,255,.2);
        color: rgba(255,255,255,.55);
        border-radius: 8px;
        font-family: inherit;
        font-size: 13px;
        cursor: pointer;
        transition: all .2s;
        margin-bottom: 20px;
    }

    .el-quit-btn:hover { border-color: #f87171; color: #f87171; background: rgba(248,113,113,.08); }

    /* ══════════════════════════════════════════
       GAME OVER / RESULTS
    ══════════════════════════════════════════ */

    #elResults {
        display: none;
        min-height: 100vh;
        align-items: center;
        justify-content: center;
        padding: 90px 20px 60px;
    }

    .el-results-card {
        width: 100%;
        max-width: 620px;
        background: rgba(10,4,28,.9);
        backdrop-filter: blur(18px);
        border-radius: 22px;
        padding: 48px 40px 40px;
        text-align: center;
        border: 1.5px solid rgba(139,92,246,.3);
        box-shadow: 0 0 0 1px rgba(255,255,255,.04),
                    0 24px 80px rgba(0,0,0,.65),
                    0 0 60px rgba(139,92,246,.1);
        animation: cardIn .5s cubic-bezier(.22,1,.36,1) both;
    }

    @keyframes cardIn {
        from { opacity:0; transform:translateY(24px) scale(.97); }
        to   { opacity:1; transform:translateY(0)    scale(1);   }
    }

    .el-result-icon  { font-size: 64px; margin-bottom: 16px; line-height: 1; }
    .el-result-title { font-size: 34px; font-weight: 700; color: #fff; text-shadow: #f70606 2px 2px; margin-bottom: 6px; }
    .el-result-sub   { font-size: 14px; color: rgba(255,255,255,.45); margin-bottom: 36px; }

    .el-result-stats {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 14px;
        margin-bottom: 36px;
    }

    .el-result-stat {
        background: rgba(255,255,255,.05);
        border: 1px solid rgba(255,255,255,.1);
        border-radius: 14px;
        padding: 20px 12px;
        transition: border-color .2s;
    }

    .el-result-stat:hover { border-color: rgba(139,92,246,.4); }

    .el-result-stat-val {
        font-size: 32px;
        font-weight: 700;
        color: #8b5cf6;
        text-shadow: 0 0 12px rgba(139,92,246,.5);
        margin-bottom: 6px;
        line-height: 1;
    }

    .el-result-stat-lbl {
        font-size: 11px;
        color: rgba(255,255,255,.45);
        text-transform: uppercase;
        letter-spacing: 1px;
    }

    .el-result-stat.highlight .el-result-stat-val {
        color: #ff2fb3;
        text-shadow: 0 0 14px rgba(255,47,179,.55);
    }

    /* Best streak badge */
    .el-best-streak {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        background: rgba(251,191,36,.12);
        border: 1px solid rgba(251,191,36,.35);
        border-radius: 50px;
        padding: 8px 20px;
        font-size: 14px;
        font-weight: 700;
        color: #fbbf24;
        margin-bottom: 32px;
    }

    /* Divider */
    .el-divider { height: 1px; background: linear-gradient(90deg, transparent, rgba(255,255,255,.08), transparent); margin: 0 0 28px; }

    /* Feedback panel (same style as quiz.php) */
    .feedback-prompt {
        margin-bottom: 24px;
        border-radius: 16px;
        overflow: hidden;
        border: 1.5px solid transparent;
        background:
            linear-gradient(rgba(14,6,34,.97), rgba(14,6,34,.97)) padding-box,
            linear-gradient(135deg, #8b5cf6 0%, #ff2fb3 100%) border-box;
        box-shadow: 0 0 20px rgba(139,92,246,.1);
        text-align: left;
    }

    .fb-header {
        display: flex; align-items: center; justify-content: space-between;
        padding: 18px 22px; cursor: pointer; user-select: none;
        background: linear-gradient(90deg, rgba(139,92,246,.1), rgba(255,47,179,.06));
        transition: background .25s;
    }
    .fb-header:hover { background: linear-gradient(90deg, rgba(139,92,246,.18), rgba(255,47,179,.1)); }
    .fb-header-left { display:flex; align-items:center; gap:12px; }
    .fb-icon { font-size:24px; filter:drop-shadow(0 0 8px rgba(139,92,246,.8)); }
    .fb-header-title { font-size:16px; font-weight:700; color:#fff; }
    .fb-header-sub { font-size:12px; color:rgba(255,255,255,.4); margin-top:3px; }
    .fb-chevron { font-size:20px; color:rgba(255,255,255,.35); transition:transform .35s ease,color .2s; }
    .feedback-prompt.open .fb-chevron { transform:rotate(180deg); color:#8b5cf6; }
    .fb-body { max-height:0; overflow:hidden; transition:max-height .45s cubic-bezier(.4,0,.2,1); }
    .feedback-prompt.open .fb-body { max-height:700px; }
    .fb-inner { padding:24px 22px 20px; display:flex; flex-direction:column; gap:18px; border-top:1px solid rgba(255,255,255,.06); }
    .fb-label { font-size:11px; font-weight:700; color:rgba(255,255,255,.45); text-transform:uppercase; letter-spacing:1.8px; margin-bottom:8px; display:block; }
    .fb-stars { display:flex; gap:8px; }
    .star { font-size:32px; color:rgba(255,255,255,.18); cursor:pointer; transition:color .15s,transform .15s; line-height:1; user-select:none; }
    .star:hover,.star.hovered,.star.active { color:#fbbf24; transform:scale(1.25); filter:drop-shadow(0 0 10px rgba(251,191,36,.65)); }
    .fb-select,.fb-textarea { width:100%; background:rgba(255,255,255,.05); border:1.5px solid rgba(255,255,255,.12); border-radius:10px; color:#fff; font-family:inherit; font-size:15px; padding:12px 15px; transition:border-color .2s,box-shadow .2s; outline:none; box-sizing:border-box; }
    .fb-select { cursor:pointer; -webkit-appearance:none; }
    .fb-select option { background:#1a0b2e; color:#fff; }
    .fb-textarea { min-height:100px; resize:vertical; line-height:1.6; }
    .fb-textarea::placeholder { color:rgba(255,255,255,.25); }
    .fb-select:focus,.fb-textarea:focus { border-color:#8b5cf6; background:rgba(139,92,246,.07); box-shadow:0 0 0 3px rgba(139,92,246,.18); }
    .fb-char-count { text-align:right; font-size:11px; color:rgba(255,255,255,.28); margin-top:-12px; }
    .fb-char-count.warn { color:#f59e0b; } .fb-char-count.over { color:#f87171; }
    .fb-error { font-size:12px; color:#f87171; margin-top:-12px; display:none; }
    .fb-error.show { display:block; }
    .fb-actions { display:flex; gap:12px; }
    @property --fb-fill { syntax:'<percentage>'; inherits:true; initial-value:0%; }
    .fb-btn-submit { position:relative; flex:1; padding:13px 20px; font-size:15px; font-weight:700; font-family:inherit; color:#fff; background:#120b24; border:none; border-radius:50px; cursor:pointer; overflow:visible; transition:background .3s; }
    .fb-btn-submit::after { content:''; position:absolute; inset:-2px; border-radius:inherit; z-index:-1; background:conic-gradient(from 0deg,#8b5cf6,#ff2fb3,#8b5cf6 var(--fb-fill),transparent var(--fb-fill)); transition:--fb-fill .5s ease; }
    .fb-btn-submit:hover:not(:disabled) { background:#1a1033; --fb-fill:100%; }
    .fb-btn-submit:disabled { opacity:.5; cursor:not-allowed; }
    .fb-btn-skip { padding:13px 18px; font-size:13px; font-family:inherit; color:rgba(255,255,255,.4); background:transparent; border:1.5px solid rgba(255,255,255,.12); border-radius:50px; cursor:pointer; transition:color .2s,border-color .2s; white-space:nowrap; }
    .fb-btn-skip:hover { color:#fff; border-color:rgba(255,255,255,.3); }
    .fb-success { display:none; flex-direction:column; align-items:center; gap:10px; padding:36px 22px; border-top:1px solid rgba(255,255,255,.06); }
    .fb-success-icon { font-size:48px; filter:drop-shadow(0 0 16px rgba(74,222,128,.65)); }
    .fb-success-title { font-size:20px; font-weight:700; color:#4ade80; }
    .fb-success-sub { font-size:13px; color:rgba(255,255,255,.45); max-width:300px; line-height:1.6; text-align:center; }

    .el-result-actions { display:flex; gap:14px; justify-content:center; flex-wrap:wrap; }
    .el-result-actions .btn,.el-result-actions .btn-primary { min-width:170px; justify-content:center; }

    /* ── Responsive ── */
    @media (max-width: 560px) {
        .el-options { grid-template-columns: 1fr; }
        .el-hud { flex-direction: column; align-items: flex-start; }
        .el-result-stats { grid-template-columns: repeat(2,1fr); }
        .el-results-card { padding: 28px 18px 24px; }
        .el-result-title { font-size: 26px; }
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
      const SESSION_USER_ID  = <?php echo json_encode($_SESSION['user_id']  ?? null); ?>;
      const SESSION_USERNAME = <?php echo json_encode($_SESSION['username'] ?? ''); ?>;
      const SESSION_FULLNAME = <?php echo json_encode($_SESSION['fullname'] ?? ''); ?>;
    </script>

    <!-- Streak toast -->
    <div class="el-streak-toast" id="streakToast"></div>

    <main>

      <!-- Loading -->
      <div id="elLoading">
        <div class="el-loading-icon">♾️</div>
        <div class="el-loading-text">Loading Endless Quiz...</div>
      </div>

      <!-- Game -->
      <div id="elGame">
        <div class="el-container">

          <!-- HUD -->
          <div class="el-hud">
            <div class="el-hud-left">
              <div class="el-mode-label">♾️ Endless Quiz</div>
              <div class="el-title">How far can you go?</div>
            </div>
            <button class="el-quit-btn" id="elQuitBtn">✕ Quit</button>
          </div>

          <!-- Lives + stats -->
          <div style="display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:16px; margin-bottom:22px;">
            <div class="el-lives">
              <span class="el-lives-label">Lives</span>
              <div class="el-hearts" id="elHearts">
                <span class="el-heart" id="heart1">❤️</span>
                <span class="el-heart" id="heart2">❤️</span>
                <span class="el-heart" id="heart3">❤️</span>
              </div>
            </div>
            <div class="el-stats-row">
              <div class="el-stat">
                <div class="el-stat-val" id="elScore">0</div>
                <div class="el-stat-lbl">Score</div>
              </div>
              <div class="el-stat streak-stat">
                <div class="el-stat-val" id="elStreak">0</div>
                <div class="el-stat-lbl">Streak</div>
              </div>
              <div class="el-stat">
                <div class="el-stat-val" id="elAnswered">0</div>
                <div class="el-stat-lbl">Answered</div>
              </div>
            </div>
          </div>

          <!-- Progress -->
          <div class="el-progress">
            <div class="el-progress-top">
              <span id="elProgressLabel">Question 1</span>
              <span id="elProgressRight">0 correct</span>
            </div>
            <div class="el-progress-bar">
              <div class="el-progress-fill" id="elProgressFill" style="width:0%"></div>
            </div>
          </div>

          <!-- Question card -->
          <div class="el-question-card" id="elQuestionCard">
            <div class="el-source" id="elSource">
              <span>📚</span>
              <span id="elSourceTitle">—</span>
              <span class="el-source-diff" id="elSourceDiff"></span>
            </div>
            <div class="el-question-number" id="elQuestionNumber">Question 1</div>
            <p class="el-question-text" id="elQuestionText">Loading...</p>
          </div>

          <!-- Options -->
          <div class="el-options" id="elOptions"></div>

        </div>
      </div>

      <!-- Results -->
      <div id="elResults">
        <div class="el-results-card">

          <div class="el-result-icon" id="elResultIcon">💀</div>
          <div class="el-result-title" id="elResultTitle">Game Over!</div>
          <div class="el-result-sub" id="elResultSub">You ran out of lives</div>

          <div class="el-result-stats">
            <div class="el-result-stat highlight">
              <div class="el-result-stat-val" id="elFinalScore">0</div>
              <div class="el-result-stat-lbl">Final Score</div>
            </div>
            <div class="el-result-stat">
              <div class="el-result-stat-val" id="elFinalCorrect">0</div>
              <div class="el-result-stat-lbl">Correct</div>
            </div>
            <div class="el-result-stat">
              <div class="el-result-stat-val" id="elFinalAnswered">0</div>
              <div class="el-result-stat-lbl">Answered</div>
            </div>
          </div>

          <div class="el-best-streak">
            🔥 Best Streak: <span id="elFinalStreak">0</span>
          </div>

          <!-- Feedback -->
          <div class="feedback-prompt" id="feedbackPrompt">
            <div class="fb-header" onclick="toggleFeedback()">
              <div class="fb-header-left">
                <span class="fb-icon">💬</span>
                <div>
                  <div class="fb-header-title">Leave Feedback</div>
                  <div class="fb-header-sub">Optional — helps us improve 8BitBrain</div>
                </div>
              </div>
              <span class="fb-chevron">▾</span>
            </div>
            <div class="fb-body">
              <div class="fb-inner" id="fbFormWrap">
                <div>
                  <span class="fb-label">Rating</span>
                  <div class="fb-stars">
                    <span class="star" data-val="1">★</span>
                    <span class="star" data-val="2">★</span>
                    <span class="star" data-val="3">★</span>
                    <span class="star" data-val="4">★</span>
                    <span class="star" data-val="5">★</span>
                  </div>
                  <input type="hidden" id="feedbackRating" value="">
                </div>
                <div>
                  <span class="fb-label">Type</span>
                  <select class="fb-select" id="feedbackType">
                    <option value="general">💬 General</option>
                    <option value="suggestion">💡 Suggestion</option>
                    <option value="bug">🐛 Bug Report</option>
                    <option value="complaint">⚠️ Complaint</option>
                  </select>
                </div>
                <div>
                  <span class="fb-label">Your Message <span style="color:#8b5cf6">*</span></span>
                  <textarea class="fb-textarea" id="feedbackText"
                    placeholder="What did you think of Endless Quiz mode?"
                    maxlength="500" oninput="updateCharCount(this)"></textarea>
                  <div class="fb-char-count" id="charCount">0 / 500</div>
                  <div class="fb-error" id="fbTextError">Please write something first.</div>
                </div>
                <div class="fb-actions">
                  <button class="fb-btn-submit" id="feedbackSubmitBtn" onclick="submitFeedback()">Send Feedback</button>
                  <button class="fb-btn-skip" onclick="toggleFeedback()">Maybe Later</button>
                </div>
              </div>
              <div class="fb-success" id="fbSuccess">
                <div class="fb-success-icon">✅</div>
                <div class="fb-success-title">Thanks!</div>
                <div class="fb-success-sub">Your feedback helps make 8BitBrain better.</div>
              </div>
            </div>
          </div>

          <div class="el-divider"></div>

          <div class="el-result-actions">
            <button class="btn" onclick="restartEndless()">♾️ Play Again</button>
            <button class="btn-primary" onclick="window.location.href='leaderboards.php'">🏆 Leaderboards</button>
            <button class="btn" style="min-width:auto" onclick="window.location.href='modes.php'">← Modes</button>
          </div>

        </div>
      </div>

    </main>
    <script src="endless.js"></script>
  </body>
</html>
