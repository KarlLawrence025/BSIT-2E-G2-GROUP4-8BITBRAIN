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
    <link rel="stylesheet" href="feedback_styles.css" />
    <link rel="icon" href="imgs/Sans_Favi.png" type="image/png" />
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=Bitcount+Single:wght@100..900&display=swap" rel="stylesheet" />
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
        <div style="font-size:52px;">⏳</div>
        <p style="font-size:20px;opacity:.7;">Loading your quiz...</p>
      </section>

      <!-- ── Quiz Game ── -->
      <section class="quiz-game" id="quizGame" style="display:none">
        <div class="container">
          <div class="quiz-header">
            <div class="quiz-info">
              <h2 id="quizTitle">Quiz</h2>
              <div class="quiz-stats">
                <span id="questionCounter">Question 1 of 10</span>
                <span id="timer" style="display:none;">⏱ 0:00</span>
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
      <section class="quiz-results" id="quizResults" style="display:none">
        <div class="container">
          <div class="results-card">
            <h1 id="resultsModeTitle">Quiz Complete!</h1>

            <div class="results-stats">
              <div class="stat">
                <h3 id="pointsEarned">+0</h3>
                <p>Points Earned</p>
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
                <p>Time Taken</p>
              </div>
            </div>

            <!-- ── Feedback Panel ── -->
            <div class="feedback-prompt" id="feedbackPrompt">

              <!-- Header / toggle bar -->
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

              <!-- Collapsible body -->
              <div class="fb-body">
                <div class="fb-inner" id="fbFormWrap">

                  <!-- Star rating -->
                  <div class="fb-rating-row">
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

                  <!-- Type -->
                  <div class="fb-field">
                    <span class="fb-label">Feedback Type</span>
                    <select class="fb-select" id="feedbackType">
                      <option value="general">💬 General</option>
                      <option value="suggestion">💡 Suggestion</option>
                      <option value="bug">🐛 Bug Report</option>
                      <option value="complaint">⚠️ Complaint</option>
                    </select>
                  </div>

                  <!-- Message -->
                  <div class="fb-field">
                    <span class="fb-label">Your Message <span style="color:#ff2fb3;">*</span></span>
                    <textarea
                      class="fb-textarea"
                      id="feedbackText"
                      placeholder="Tell us what you think about this quiz..."
                      maxlength="500"
                      oninput="updateCharCount(this)"></textarea>
                    <div class="fb-char-count" id="charCount">0 / 500</div>
                    <div class="fb-error" id="fbTextError">Please write something before submitting.</div>
                  </div>

                  <!-- Actions -->
                  <div class="fb-actions">
                    <button class="fb-btn-submit" id="feedbackSubmitBtn" onclick="submitFeedback()">
                      Send Feedback
                    </button>
                    <button class="fb-btn-skip" onclick="toggleFeedback()">
                      Maybe Later
                    </button>
                  </div>

                </div>

                <!-- Success state (hidden until submitted) -->
                <div class="fb-success" id="fbSuccess">
                  <div class="fb-success-icon">✅</div>
                  <div class="fb-success-title">Thanks for your feedback!</div>
                  <div class="fb-success-sub">We read every submission and use it to make 8BitBrain better.</div>
                </div>
              </div>

            </div>
            <!-- end .feedback-prompt -->

            <div class="fb-divider"></div>

            <div class="results-actions">
              <button class="btn" onclick="window.location.href='modes.php'">← Back to Modes</button>
              <button class="btn-primary" onclick="window.location.href='leaderboards.php'">View Leaderboards</button>
            </div>
          </div>
        </div>
      </section>
    </main>

    <script src="quiz.js"></script>
  </body>
</html>
