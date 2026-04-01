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
    <link
      href="https://fonts.googleapis.com/css2?family=Bitcount+Single:wght@100..900&display=swap"
      rel="stylesheet"
    />
  </head>

  <body>
    <div class="bg"></div>

    <header class="header">
      <a href="index.php" class="logo">
        8BitBrain
        <img src="imgs/Sans_Favi.png" alt="logo" class="logoimg" />
      </a>
      <?php include("navbar.php"); ?>
    </header>

    <main>
      <!-- Quiz Selection Screen -->
      <section class="quiz-selection" id="quizSelection">
        <div class="container">
          <h1
            id="modeTitle"
            style="
              color: white;
              text-align: center;
              text-shadow: 2px 2px 1px red;
            "
          >
            Select Quiz
          </h1>
          <div class="quiz-grid" id="quizGrid">
            <!-- Quizzes will be loaded here -->
          </div>
        </div>
      </section>

      <!-- Quiz Game Screen -->
      <section class="quiz-game" id="quizGame" style="display: none">
        <div class="container">
          <div class="quiz-header">
            <div class="quiz-info">
              <h2 id="quizTitle">Quiz Title</h2>
              <div class="quiz-stats">
                <span id="questionCounter">Question 1 of 15</span>
                <span id="timer">Time: 10:00</span>
                <span id="score">Score: 0</span>
              </div>
            </div>
            <button class="btn-secondary" id="quitBtn">Quit Quiz</button>
          </div>

          <div class="question-container">
            <div class="question-card">
              <h3 id="questionText">Question text here</h3>
              <div class="options" id="options">
                <!-- Options will be loaded here -->
              </div>
            </div>
          </div>

          <div class="quiz-controls">
            <button class="btn" id="prevBtn" disabled>Previous</button>
            <button class="btn" id="nextBtn" disabled>Next</button>
            <button class="btn-primary" id="submitBtn" style="display: none">
              Submit Quiz
            </button>
          </div>
        </div>
      </section>

      <!-- Quiz Results Screen -->
      <section class="quiz-results" id="quizResults" style="display: none">
        <div class="container">
          <div class="results-card">
            <h1 id="modeTitle">Quiz Complete!</h1>
            <div class="results-stats">
              <div class="stat">
                <h3 id="finalScore">0</h3>
                <p>Final Score</p>
              </div>
              <div class="stat">
                <h3 id="correctAnswers">0</h3>
                <p>Correct Answers</p>
              </div>
              <div class="stat">
                <h3 id="totalQuestions">15</h3>
                <p>Total Questions</p>
              </div>
              <div class="stat">
                <h3 id="timeTaken">0:00</h3>
                <p>Time Taken</p>
              </div>
            </div>
            <div class="results-actions">
              <button class="btn" onclick="window.location.href = 'modes.php'">
                Try Another Mode
              </button>
              <button
                class="btn-primary"
                onclick="window.location.href = 'leaderboards.php'"
              >
                View Leaderboards
              </button>
            </div>
          </div>
        </div>
      </section>
    </main>

    <script src="quiz.js"></script>
  </body>
</html>
