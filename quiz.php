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
    <title>8BitBrain - Quiz</title>
    <link rel="stylesheet" href="style.css" />
    <link rel="stylesheet" href="quiz.css" />
    <link rel="icon" href="imgs/Sans_Favi.png" type="image/png" />
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=Bitcount+Single:wght@100..900&display=swap" rel="stylesheet" />

    <style>
    /* ══════════════════════════════════════════════
       QUIZ RESULTS — centered, spacious, readable
    ══════════════════════════════════════════════ */

    #quizResults {
      min-height: 100vh;
      display: flex;
      align-items: center;
      justify-content: center;
      padding: 90px 20px 60px;
    }

    .results-card {
      width: 100%;
      max-width: 680px;
      background: rgba(10, 4, 28, 0.85);
      backdrop-filter: blur(18px);
      border-radius: 22px;
      padding: 48px 44px 40px;
      border: 1px solid rgba(255, 47, 179, 0.25);
      box-shadow:
        0 0 0 1px rgba(255,255,255,.04),
        0 24px 80px rgba(0,0,0,.6),
        0 0 60px rgba(255,47,179,.08);
      animation: cardIn .5s cubic-bezier(.22,1,.36,1) both;
    }

    @keyframes cardIn {
      from { opacity:0; transform:translateY(24px) scale(.97); }
      to   { opacity:1; transform:translateY(0)    scale(1);   }
    }

    /* ── Title ── */
    #resultsModeTitle {
      font-size: 32px;
      color: #fff;
      text-align: center;
      margin-bottom: 36px;
      text-shadow: #f70606 2px 2px;
      letter-spacing: .5px;
    }

    /* ── Stats row ── */
    .results-stats {
      display: grid;
      grid-template-columns: repeat(4, 1fr);
      gap: 14px;
      margin-bottom: 36px;
    }

    .stat {
      background: rgba(255,255,255,.05);
      border: 1px solid rgba(255,255,255,.1);
      border-radius: 14px;
      padding: 20px 12px;
      text-align: center;
      transition: border-color .2s, box-shadow .2s;
    }

    .stat:hover {
      border-color: rgba(255,47,179,.4);
      box-shadow: 0 0 20px rgba(255,47,179,.1);
    }

    .stat h3 {
      font-size: 30px;
      font-weight: 700;
      color: #ff2fb3;
      text-shadow: 0 0 12px rgba(255,47,179,.5);
      margin-bottom: 6px;
      line-height: 1;
    }

    .stat p {
      font-size: 12px;
      color: rgba(255,255,255,.5);
      text-transform: uppercase;
      letter-spacing: 1px;
      margin: 0;
    }

    /* ══════════════════════════════════════════════
       FEEDBACK PANEL
    ══════════════════════════════════════════════ */

    .feedback-prompt {
      margin: 28px 0 24px;
      border-radius: 16px;
      overflow: hidden;
      border: 1.5px solid transparent;
      background:
        linear-gradient(rgba(14,6,34,.97), rgba(14,6,34,.97)) padding-box,
        linear-gradient(135deg, #ff2fb3 0%, #a855f7 50%, #38bdf8 100%) border-box;
      box-shadow:
        0 0 30px rgba(255,47,179,.1),
        inset 0 0 40px rgba(168,85,247,.04);
      animation: fbIn .55s .1s cubic-bezier(.22,1,.36,1) both;
    }

    @keyframes fbIn {
      from { opacity:0; transform:translateY(12px); }
      to   { opacity:1; transform:translateY(0);    }
    }

    /* ── Toggle header ── */
    .fb-header {
      display: flex;
      align-items: center;
      justify-content: space-between;
      padding: 20px 24px;
      cursor: pointer;
      user-select: none;
      background: linear-gradient(90deg,
        rgba(255,47,179,.1) 0%,
        rgba(168,85,247,.07) 60%,
        rgba(56,189,248,.05) 100%);
      transition: background .25s;
    }

    .fb-header:hover {
      background: linear-gradient(90deg,
        rgba(255,47,179,.18) 0%,
        rgba(168,85,247,.13) 60%,
        rgba(56,189,248,.09) 100%);
    }

    .fb-header-left { display:flex; align-items:center; gap:14px; }

    .fb-icon {
      font-size: 26px;
      filter: drop-shadow(0 0 8px rgba(255,47,179,.8));
    }

    .fb-header-title {
      font-size: 17px;
      font-weight: 700;
      color: #fff;
      letter-spacing: .3px;
    }

    .fb-header-sub {
      font-size: 12px;
      color: rgba(255,255,255,.4);
      margin-top: 3px;
    }

    .fb-chevron {
      font-size: 20px;
      color: rgba(255,255,255,.35);
      transition: transform .35s ease, color .2s;
    }

    .feedback-prompt.open .fb-chevron {
      transform: rotate(180deg);
      color: #ff2fb3;
    }

    /* ── Collapsible body ── */
    .fb-body {
      max-height: 0;
      overflow: hidden;
      transition: max-height .45s cubic-bezier(.4,0,.2,1);
    }

    .feedback-prompt.open .fb-body { max-height: 700px; }

    .fb-inner {
      padding: 28px 28px 24px;
      display: flex;
      flex-direction: column;
      gap: 22px;
      border-top: 1px solid rgba(255,255,255,.06);
    }

    /* ── Section label ── */
    .fb-label {
      font-size: 11px;
      font-weight: 700;
      color: rgba(255,255,255,.45);
      text-transform: uppercase;
      letter-spacing: 1.8px;
      margin-bottom: 10px;
      display: block;
    }

    /* ── Stars ── */
    .fb-stars { display:flex; gap:8px; }

    .star {
      font-size: 34px;
      color: rgba(255,255,255,.18);
      cursor: pointer;
      transition: color .15s, transform .15s, filter .15s;
      line-height: 1;
      user-select: none;
    }

    .star:hover,
    .star.hovered,
    .star.active {
      color: #fbbf24;
      transform: scale(1.25);
      filter: drop-shadow(0 0 10px rgba(251,191,36,.65));
    }

    /* ── Select ── */
    .fb-select {
      width: 100%;
      background: rgba(255,255,255,.05);
      border: 1.5px solid rgba(255,255,255,.12);
      border-radius: 10px;
      color: #fff;
      font-family: inherit;
      font-size: 15px;
      padding: 13px 16px;
      cursor: pointer;
      transition: border-color .2s, box-shadow .2s, background .2s;
      outline: none;
      -webkit-appearance: none;
      box-sizing: border-box;
    }

    .fb-select option { background: #1a0b2e; color: #fff; }

    .fb-select:focus {
      border-color: #ff2fb3;
      background: rgba(255,47,179,.07);
      box-shadow: 0 0 0 3px rgba(255,47,179,.18);
    }

    /* ── Textarea ── */
    .fb-textarea {
      width: 100%;
      background: rgba(255,255,255,.05);
      border: 1.5px solid rgba(255,255,255,.12);
      border-radius: 10px;
      color: #fff;
      font-family: inherit;
      font-size: 15px;
      padding: 14px 16px;
      min-height: 110px;
      resize: vertical;
      transition: border-color .2s, box-shadow .2s, background .2s;
      outline: none;
      line-height: 1.65;
      box-sizing: border-box;
    }

    .fb-textarea::placeholder { color: rgba(255,255,255,.25); }

    .fb-textarea:focus {
      border-color: #ff2fb3;
      background: rgba(255,47,179,.06);
      box-shadow: 0 0 0 3px rgba(255,47,179,.18);
    }

    /* char counter */
    .fb-char-count {
      text-align: right;
      font-size: 11px;
      color: rgba(255,255,255,.28);
      margin-top: -14px;
      transition: color .2s;
    }
    .fb-char-count.warn { color: #f59e0b; }
    .fb-char-count.over { color: #f87171; }

    /* error hint */
    .fb-error {
      font-size: 12px;
      color: #f87171;
      margin-top: -14px;
      display: none;
    }
    .fb-error.show { display: block; }

    /* ── Actions ── */
    .fb-actions {
      display: flex;
      gap: 14px;
      align-items: stretch;
    }

    /* Submit */
    .fb-btn-submit {
      position: relative;
      flex: 1;
      padding: 14px 20px;
      font-size: 15px;
      font-weight: 700;
      font-family: inherit;
      color: #fff;
      background: #120b24;
      border: none;
      border-radius: 50px;
      cursor: pointer;
      letter-spacing: .4px;
      transition: background .3s;
      overflow: visible;
    }

    @property --fb-fill {
      syntax: '<percentage>';
      inherits: true;
      initial-value: 0%;
    }

    .fb-btn-submit::after {
      content: '';
      position: absolute;
      inset: -2px;
      border-radius: inherit;
      z-index: -1;
      background: conic-gradient(
        from 0deg,
        #ff2fb3, #a855f7, #38bdf8,
        #ff2fb3 var(--fb-fill), transparent var(--fb-fill)
      );
      transition: --fb-fill .5s ease;
    }

    .fb-btn-submit:hover:not(:disabled) { background:#1a1033; --fb-fill:100%; }
    .fb-btn-submit:active:not(:disabled) { transform:scale(.98); }
    .fb-btn-submit:disabled { opacity:.5; cursor:not-allowed; }

    /* Skip */
    .fb-btn-skip {
      padding: 14px 20px;
      font-size: 14px;
      font-family: inherit;
      color: rgba(255,255,255,.4);
      background: transparent;
      border: 1.5px solid rgba(255,255,255,.12);
      border-radius: 50px;
      cursor: pointer;
      transition: color .2s, border-color .2s, background .2s;
      white-space: nowrap;
    }

    .fb-btn-skip:hover {
      color: #fff;
      border-color: rgba(255,255,255,.3);
      background: rgba(255,255,255,.05);
    }

    /* ── Success ── */
    .fb-success {
      display: none;
      flex-direction: column;
      align-items: center;
      gap: 12px;
      padding: 40px 28px;
      text-align: center;
      border-top: 1px solid rgba(255,255,255,.06);
      animation: fbSuccIn .4s cubic-bezier(.22,1,.36,1) both;
    }

    @keyframes fbSuccIn {
      from { opacity:0; transform:scale(.92); }
      to   { opacity:1; transform:scale(1);   }
    }

    .fb-success-icon {
      font-size: 54px;
      filter: drop-shadow(0 0 18px rgba(74,222,128,.65));
      animation: fbPop .4s ease;
    }

    @keyframes fbPop {
      0%  { transform:scale(0); }
      70% { transform:scale(1.2); }
      100%{ transform:scale(1); }
    }

    .fb-success-title {
      font-size: 22px;
      font-weight: 700;
      color: #4ade80;
      text-shadow: 0 0 14px rgba(74,222,128,.55);
    }

    .fb-success-sub {
      font-size: 14px;
      color: rgba(255,255,255,.45);
      max-width: 320px;
      line-height: 1.6;
    }

    /* ── Divider ── */
    .fb-divider {
      height: 1px;
      background: linear-gradient(90deg, transparent, rgba(255,255,255,.08), transparent);
      margin: 4px 0 24px;
    }

    /* ── Results action buttons ── */
    .results-actions {
      display: flex;
      gap: 16px;
      justify-content: center;
      flex-wrap: wrap;
    }

    .results-actions .btn,
    .results-actions .btn-primary {
      min-width: 180px;
      justify-content: center;
    }

    /* ── Responsive ── */
    @media (max-width: 600px) {
      .results-card      { padding: 32px 20px 28px; }
      .results-stats     { grid-template-columns: repeat(2, 1fr); }
      #resultsModeTitle  { font-size: 24px; }
      .stat h3           { font-size: 24px; }
      .fb-inner          { padding: 20px 18px; gap: 18px; }
      .fb-actions        { flex-direction: column; }
      .fb-btn-submit,
      .fb-btn-skip       { width: 100%; text-align: center; }
      .star              { font-size: 28px; }
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

    <main>

      <!-- ── Loading ── -->
      <section id="quizLoading" style="
        min-height:100vh;display:flex;flex-direction:column;
        justify-content:center;align-items:center;color:#fff;gap:16px;padding-top:60px;">
        <div style="font-size:52px">⏳</div>
        <p style="font-size:20px;opacity:.7">Loading your quiz...</p>
      </section>

      <!-- ── Quiz Game ── -->
      <section class="quiz-game" id="quizGame" style="display:none">
        <div class="container">
          <div class="quiz-header">
            <div class="quiz-info">
              <h2 id="quizTitle">Quiz</h2>
              <div class="quiz-stats">
                <span id="questionCounter">Question 1 of 10</span>
                <span id="timer" style="display:none">⏱ 0:00</span>
                <span id="score">Score: 0</span>
              </div>
            </div>
            <button class="btn-secondary" id="quitBtn">← Back to Modes</button>
          </div>
          <div class="question-container">
            <div class="question-card">
              <h3 id="questionText">Loading...</h3>
              <div class="options" id="options"></div>
            </div>
          </div>
          <div class="quiz-controls">
            <button class="btn-primary" id="submitBtn" style="display:none">Submit Quiz</button>
          </div>
        </div>
      </section>

      <!-- ── Results ── -->
      <section id="quizResults" style="display:none">
        <div class="results-card">

          <h1 id="resultsModeTitle">Quiz Complete! 🎉</h1>

          <!-- Stats -->
          <div class="results-stats">
            <div class="stat">
              <h3 id="pointsEarned">+0</h3>
              <p>Points</p>
            </div>
            <div class="stat">
              <h3 id="correctAnswers">0</h3>
              <p>Correct</p>
            </div>
            <div class="stat">
              <h3 id="totalQuestions">0</h3>
              <p>Total</p>
            </div>
            <div class="stat">
              <h3 id="timeTaken">0:00</h3>
              <p>Time</p>
            </div>
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

              <!-- Form -->
              <div class="fb-inner" id="fbFormWrap">

                <div>
                  <span class="fb-label">Overall Rating</span>
                  <div class="fb-stars" id="starRow">
                    <span class="star" data-val="1">★</span>
                    <span class="star" data-val="2">★</span>
                    <span class="star" data-val="3">★</span>
                    <span class="star" data-val="4">★</span>
                    <span class="star" data-val="5">★</span>
                  </div>
                  <input type="hidden" id="feedbackRating" value="">
                </div>

                <div>
                  <span class="fb-label">Feedback Type</span>
                  <select class="fb-select" id="feedbackType">
                    <option value="general">💬 General</option>
                    <option value="suggestion">💡 Suggestion</option>
                    <option value="bug">🐛 Bug Report</option>
                    <option value="complaint">⚠️ Complaint</option>
                  </select>
                </div>

                <div>
                  <span class="fb-label">Your Message <span style="color:#ff2fb3">*</span></span>
                  <textarea class="fb-textarea" id="feedbackText"
                    placeholder="Tell us what you think about this quiz..."
                    maxlength="500"
                    oninput="updateCharCount(this)"></textarea>
                  <div class="fb-char-count" id="charCount">0 / 500</div>
                  <div class="fb-error" id="fbTextError">Please write something before submitting.</div>
                </div>

                <div class="fb-actions">
                  <button class="fb-btn-submit" id="feedbackSubmitBtn" onclick="submitFeedback()">
                    Send Feedback
                  </button>
                  <button class="fb-btn-skip" onclick="toggleFeedback()">Maybe Later</button>
                </div>

              </div>

              <!-- Success -->
              <div class="fb-success" id="fbSuccess">
                <div class="fb-success-icon">✅</div>
                <div class="fb-success-title">Thanks for your feedback!</div>
                <div class="fb-success-sub">We read every submission and use it to make 8BitBrain better.</div>
              </div>

            </div>
          </div>
          <!-- end feedback-prompt -->

          <div class="fb-divider"></div>

          <div class="results-actions">
            <button class="btn" onclick="window.location.href='modes.php'">← Back to Modes</button>
            <button class="btn-primary" onclick="window.location.href='leaderboards.php'">View Leaderboards</button>
          </div>

        </div>
      </section>

    </main>

    <script src="quiz.js"></script>
  </body>
</html>
