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
      <!-- Quiz Selection -->
      <section class="quiz-selection" id="quizSelection">
        <div class="container">
          <h1 id="modeTitle" style="color:white;text-align:center;text-shadow:2px 2px 1px red;">
            Select Quiz
          </h1>
          <div class="quiz-grid" id="quizGrid"></div>
        </div>
      </section>

      <!-- Quiz Game -->
      <section class="quiz-game" id="quizGame" style="display:none">
        <div class="container">
          <div class="quiz-header">
            <div class="quiz-info">
              <h2 id="quizTitle">Quiz Title</h2>
              <div class="quiz-stats">
                <span id="questionCounter">Question 1 of 10</span>
                <span id="timer">Time: --</span>
                <span id="score">Score: 0</span>
              </div>
            </div>
            <button class="btn-secondary" id="quitBtn">Quit Quiz</button>
          </div>
          <div class="question-container">
            <div class="question-card">
              <h3 id="questionText">Question text here</h3>
              <div class="options" id="options"></div>
            </div>
          </div>
          <div class="quiz-controls">
            <button class="btn" id="prevBtn" disabled>Previous</button>
            <button class="btn" id="nextBtn" disabled>Next</button>
            <button class="btn-primary" id="submitBtn" style="display:none">Submit Quiz</button>
          </div>
        </div>
      </section>

      <!-- Quiz Results -->
      <section class="quiz-results" id="quizResults" style="display:none">
        <div class="container">
          <div class="results-card">
            <h1 id="resultsModeTitle">Quiz Complete!</h1>

            <div class="results-stats">
              <div class="stat points-stat">
                <h3 id="pointsEarned">+0</h3>
                <p>Points Earned</p>
              </div>
              <div class="stat">
                <h3 id="correctAnswers">0</h3>
                <p>Correct</p>
              </div>
              <div class="stat">
                <h3 id="totalQuestions">0</h3>
                <p>Total Questions</p>
              </div>
              <div class="stat">
                <h3 id="timeTaken">0:00</h3>
                <p>Time Taken</p>
              </div>
            </div>

            <!-- Optional Feedback -->
            <div class="feedback-prompt" id="feedbackPrompt">
              <h3 class="feedback-toggle-label">💬 Leave Feedback <span style="font-size:14px;opacity:.7;">(optional)</span></h3>
              <button class="btn-feedback-toggle" id="feedbackToggleBtn" onclick="toggleFeedback()">Give Feedback</button>

              <div class="feedback-form-wrapper" id="feedbackFormWrapper" style="display:none">
                <form id="feedbackForm" onsubmit="submitFeedback(event)">
                  <div class="feedback-row">
                    <label>Rating</label>
                    <div class="star-row" id="starRow">
                      <span class="star" data-val="1">★</span>
                      <span class="star" data-val="2">★</span>
                      <span class="star" data-val="3">★</span>
                      <span class="star" data-val="4">★</span>
                      <span class="star" data-val="5">★</span>
                    </div>
                    <input type="hidden" id="feedbackRating" value="">
                  </div>
                  <div class="feedback-row">
                    <label for="feedbackType">Type</label>
                    <select id="feedbackType">
                      <option value="general">General</option>
                      <option value="suggestion">Suggestion</option>
                      <option value="bug">Bug Report</option>
                      <option value="complaint">Complaint</option>
                    </select>
                  </div>
                  <div class="feedback-row">
                    <label for="feedbackText">Your Feedback</label>
                    <textarea id="feedbackText" rows="3" placeholder="Tell us what you think..." required></textarea>
                  </div>
                  <div class="feedback-actions">
                    <button type="submit" class="btn-feedback-submit" id="feedbackSubmitBtn">Submit Feedback</button>
                    <button type="button" class="btn-feedback-skip" onclick="toggleFeedback()">Cancel</button>
                  </div>
                </form>
                <p class="feedback-success" id="feedbackSuccess" style="display:none">✅ Thanks for your feedback!</p>
              </div>
            </div>

            <div class="results-actions">
              <button class="btn" onclick="window.location.href='modes.php'">Try Another Mode</button>
              <button class="btn-primary" onclick="window.location.href='leaderboards.php'">View Leaderboards</button>
            </div>
          </div>
        </div>
      </section>
    </main>
    <script src="quiz.js"></script>
  </body>
</html>
