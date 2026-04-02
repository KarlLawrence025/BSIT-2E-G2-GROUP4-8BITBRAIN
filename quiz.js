// ========================================
// QUIZ SYSTEM - MAIN QUIZ LOGIC
// ========================================

console.log("Quiz system loaded 🎯");

// ── Global state ──────────────────────────────────────────────────────────────
let currentMode          = "";
let currentQuiz          = null;
let currentQuestions     = [];
let currentQuestionIndex = 0;
let userAnswers          = [];
let quizStartTime        = null;
let quizTimer            = null;
let score                = 0;
let timeLimit            = 0;
let isQuizActive         = false;
let correctAnswers       = 0;

let endlessMode      = false;
let memoryMatchMode  = false;
let rankedMode       = false;
let timedMode        = false;
let singlePlayerMode = false;

let memoryCards   = [];
let flippedCards  = [];
let matchedPairs  = 0;
let endlessLives  = 3;
let endlessStreak = 0;

// ── Init ──────────────────────────────────────────────────────────────────────
document.addEventListener("DOMContentLoaded", () => { initializeQuiz(); });

function initializeQuiz() {
  currentMode = localStorage.getItem("selectedMode") || "single_player";
  setModeSettings();
  loadQuizSelection();
  setupEventListeners();
}

function setModeSettings() {
  endlessMode = memoryMatchMode = rankedMode = timedMode = singlePlayerMode = false;
  switch (currentMode) {
    case "timed_quiz":   timedMode        = true; timeLimit = 60;  break;
    case "ranked_quiz":  rankedMode       = true; timeLimit = 300; break;
    case "memory_match": memoryMatchMode  = true; timeLimit = 180; break;
    case "endless_quiz": endlessMode      = true; timeLimit = 0;   break;
    default:             singlePlayerMode = true; timeLimit = 0;   break;
  }
}

// ── Quiz Selection ────────────────────────────────────────────────────────────
async function loadQuizSelection() {
  const container = document.getElementById("quizGrid");
  if (!container) return;

  container.innerHTML = `<p style="color:#fff;text-align:center;grid-column:1/-1;">Loading quizzes...</p>`;

  try {
    const response = await fetch(`api/get_quizzes.php?mode=${currentMode}`);
    const data     = await response.json();

    if (!data.success) {
      container.innerHTML = `<p style="color:#f87171;text-align:center;grid-column:1/-1;">Failed to load quizzes.</p>`;
      return;
    }

    displayQuizSelection(data.data);
  } catch (e) {
    container.innerHTML = `<p style="color:#f87171;text-align:center;grid-column:1/-1;">Network error. Is XAMPP running?</p>`;
  }
}

function displayQuizSelection(quizzes) {
  const container = document.getElementById("quizGrid");
  if (!container) return;
  container.innerHTML = "";

  if (!quizzes || quizzes.length === 0) {
    container.innerHTML = `
      <div style="color:#fff;text-align:center;grid-column:1/-1;padding:40px;">
        <h3 style="margin-bottom:10px;">No quizzes available for this mode yet.</h3>
        <p style="opacity:.6;">Ask your admin to create some quizzes!</p>
      </div>`;
    return;
  }

  quizzes.forEach(quiz => {
    const card     = document.createElement("div");
    card.className = "quiz-card";
    card.onclick   = () => selectQuiz(quiz.id);
    card.innerHTML = `
      <h3>${quiz.title}</h3>
      <p class="category">${quiz.category}</p>
      <p class="difficulty ${quiz.difficulty}">${quiz.difficulty}</p>
      <p class="question-count">${quiz.question_count || 0} questions</p>`;
    container.appendChild(card);
  });
}

function selectQuiz(quizId) {
  localStorage.setItem("selectedQuizId", quizId);
  startQuiz();
}

// ── Quiz Gameplay ─────────────────────────────────────────────────────────────
async function startQuiz() {
  const quizId = parseInt(localStorage.getItem("selectedQuizId"));
  if (!quizId || quizId <= 0) {
    showError("No quiz selected. Please go back and pick a quiz.");
    return;
  }

  try {
    const response = await fetch(`api/get_quiz_questions.php?quiz_id=${quizId}`);
    const data     = await response.json();

    if (!data.success) {
      // Show the error message from the server clearly
      showError(data.message || "Failed to load quiz questions.");
      return;
    }

    if (!data.questions || data.questions.length === 0) {
      showError("This quiz has no questions yet. Please ask your admin to add some.");
      return;
    }

    currentQuiz      = data.quiz;
    currentQuestions = data.questions;

    prepareQuestionsForMode();
    resetQuizState();
    showQuizGame();
    loadQuestion();
    if (timedMode || rankedMode) startTimer();

  } catch (e) {
    console.error("startQuiz error:", e);
    showError("Network error loading quiz. Please check your connection.");
  }
}

function prepareQuestionsForMode() {
  if (memoryMatchMode) {
    currentQuestions = createMemoryMatchPairs(currentQuestions);
  } else {
    currentQuestions = shuffleArray(currentQuestions);
  }
}

function createMemoryMatchPairs(questions) {
  const pairs = [];
  questions.forEach(q => {
    const correct = q.answers.find(a => a.is_correct);
    if (correct) {
      pairs.push({ type: "term",       content: q.question, pairId: q.id });
      pairs.push({ type: "definition", content: correct.text, pairId: q.id });
    }
  });
  return shuffleArray(pairs);
}

function resetQuizState() {
  currentQuestionIndex = 0;
  userAnswers          = [];
  score                = 0;
  correctAnswers       = 0;
  quizStartTime        = Date.now();
  isQuizActive         = true;
  endlessLives         = endlessMode ? 3 : 0;
  endlessStreak        = 0;
  matchedPairs         = 0;
  flippedCards         = [];
}

function showQuizGame() {
  document.getElementById("quizSelection").style.display = "none";
  document.getElementById("quizGame").style.display      = "block";
  updateQuizHeader();
}

function updateQuizHeader() {
  const titleEl   = document.getElementById("quizTitle");
  const counterEl = document.getElementById("questionCounter");
  const scoreEl   = document.getElementById("score");

  if (titleEl)   titleEl.textContent = currentQuiz?.title || "Quiz";
  if (scoreEl)   scoreEl.textContent = `Score: ${score}`;

  if (memoryMatchMode) {
    if (counterEl) counterEl.textContent = `Pairs: ${matchedPairs}/${currentQuestions.length / 2}`;
  } else if (endlessMode) {
    if (counterEl) counterEl.textContent = `Lives: ${endlessLives} | Streak: ${endlessStreak}`;
  } else {
    if (counterEl) counterEl.textContent = `Question ${currentQuestionIndex + 1} of ${currentQuestions.length}`;
  }
}

function loadQuestion() {
  if (!isQuizActive) return;
  memoryMatchMode ? loadMemoryMatchQuestion() : loadStandardQuestion();
}

function loadStandardQuestion() {
  const question = currentQuestions[currentQuestionIndex];
  if (!question) { endQuiz(); return; }

  const questionEl = document.getElementById("questionText");
  const optionsEl  = document.getElementById("options");

  if (questionEl) questionEl.textContent = question.question;

  if (optionsEl) {
    optionsEl.innerHTML = "";

    if (!question.answers || question.answers.length === 0) {
      optionsEl.innerHTML = `<p style="color:#f87171;">This question has no answer options.</p>`;
      return;
    }

    question.answers.forEach((answer, index) => {
      const div       = document.createElement("div");
      div.className   = "option";
      div.textContent = answer.text;
      div.onclick     = () => selectAnswer(index);
      optionsEl.appendChild(div);
    });
  }
  updateQuizHeader();
}

function loadMemoryMatchQuestion() {
  const container     = document.getElementById("questionText").parentElement;
  container.innerHTML = "<h3>Memory Match — pair the terms with their definitions</h3>";

  const grid     = document.createElement("div");
  grid.className = "memory-grid";
  grid.id        = "memoryGrid";

  currentQuestions.forEach((item, index) => {
    const card          = document.createElement("div");
    card.className      = "memory-card";
    card.dataset.index  = index;
    card.dataset.pairId = item.pairId;
    card.dataset.type   = item.type;
    card.onclick        = () => flipCard(card);

    const content       = document.createElement("div");
    content.className   = "card-content";
    content.textContent = item.content;
    card.appendChild(content);
    grid.appendChild(card);
  });
  container.appendChild(grid);
  memoryCards = document.querySelectorAll(".memory-card");
}

function flipCard(card) {
  if (card.classList.contains("flipped") ||
      card.classList.contains("matched") ||
      flippedCards.length >= 2) return;

  card.classList.add("flipped");
  flippedCards.push(card);
  if (flippedCards.length === 2) setTimeout(checkMatch, 1000);
}

function checkMatch() {
  const [c1, c2] = flippedCards;
  if (c1.dataset.pairId === c2.dataset.pairId && c1.dataset.type !== c2.dataset.type) {
    c1.classList.add("matched");
    c2.classList.add("matched");
    matchedPairs++;
    score += 10;
    if (matchedPairs === currentQuestions.length / 2) endQuiz();
  } else {
    c1.classList.remove("flipped");
    c2.classList.remove("flipped");
    if (endlessMode) { endlessLives--; if (endlessLives <= 0) endQuiz(); }
  }
  flippedCards = [];
  updateQuizHeader();
}

function selectAnswer(answerIndex) {
  const question  = currentQuestions[currentQuestionIndex];
  const answer    = question.answers[answerIndex];
  const isCorrect = Number(answer.is_correct) === 1;

  userAnswers.push({ questionId: question.id, selectedAnswer: answerIndex, isCorrect });

  if (isCorrect) {
    score += 10;
    correctAnswers++;
    endlessStreak++;
  } else {
    if (endlessMode) {
      endlessLives--;
      endlessStreak = 0;
      if (endlessLives <= 0) { endQuiz(); return; }
    }
  }

  currentQuestionIndex++;
  currentQuestionIndex >= currentQuestions.length ? endQuiz() : loadQuestion();
}

// ── Timer ─────────────────────────────────────────────────────────────────────
function startTimer() {
  let timeLeft = timeLimit;
  updateTimerDisplay(timeLeft);
  quizTimer = setInterval(() => {
    timeLeft--;
    updateTimerDisplay(timeLeft);
    if (timeLeft <= 0) { clearInterval(quizTimer); endQuiz(); }
  }, 1000);
}

function updateTimerDisplay(seconds) {
  const el = document.getElementById("timer");
  if (el) {
    const m = Math.floor(seconds / 60);
    const s = seconds % 60;
    el.textContent = `Time: ${m}:${s.toString().padStart(2, "0")}`;
  }
}

// ── End Quiz ──────────────────────────────────────────────────────────────────
function endQuiz() {
  isQuizActive = false;
  clearInterval(quizTimer);

  const timeTaken      = Math.floor((Date.now() - quizStartTime) / 1000);
  const totalQuestions = endlessMode ? userAnswers.length : currentQuestions.length;

  saveQuizResult(correctAnswers, totalQuestions, timeTaken).then(pointsEarned => {
    showQuizResults(correctAnswers, totalQuestions, timeTaken, pointsEarned);
  });
}

async function saveQuizResult(correct, total, timeTaken) {
  try {
    const response = await fetch("api/save_quiz_result.php", {
      method:  "POST",
      headers: { "Content-Type": "application/json" },
      body: JSON.stringify({
        quiz_id:         currentQuiz?.id || 0,
        mode:            currentMode,
        correct_answers: correct,
        total_questions: total,
        time_taken:      timeTaken,
        time_limit:      timeLimit,
        user_id:         SESSION_USER_ID
      })
    });
    const data = await response.json();
    console.log("Save result:", data);
    return data.points_earned || 0;
  } catch (e) {
    console.error("Error saving result:", e);
    return 0;
  }
}

function showQuizResults(correct, total, timeTaken, pointsEarned) {
  document.getElementById("quizGame").style.display    = "none";
  document.getElementById("quizResults").style.display = "block";

  let title          = "Quiz Complete!";
  let summaryCorrect = correct;
  let summaryTotal   = total;

  if (endlessMode)     { title = endlessLives > 0 ? "Endless — Survived!" : "Game Over!"; }
  if (memoryMatchMode) { title = "Memory Match Complete!"; summaryCorrect = matchedPairs; summaryTotal = currentQuestions.length / 2; }
  if (rankedMode)      { title = "Ranked Quiz Complete!"; }

  document.getElementById("resultsModeTitle").textContent = title;
  document.getElementById("correctAnswers").textContent   = summaryCorrect;
  document.getElementById("totalQuestions").textContent   = summaryTotal;
  document.getElementById("timeTaken").textContent        = formatTime(timeTaken);
  document.getElementById("pointsEarned").textContent     = `+${pointsEarned}`;

  resetFeedbackForm();
}

// ── Utilities ─────────────────────────────────────────────────────────────────
function formatTime(seconds) {
  const m = Math.floor(seconds / 60);
  const s = seconds % 60;
  return `${m}:${s.toString().padStart(2, "0")}`;
}

function shuffleArray(array) {
  for (let i = array.length - 1; i > 0; i--) {
    const j = Math.floor(Math.random() * (i + 1));
    [array[i], array[j]] = [array[j], array[i]];
  }
  return array;
}

function showError(message) {
  alert(message);
}

function setupEventListeners() {
  document.getElementById("quitBtn")?.addEventListener("click", () => {
    if (confirm("Are you sure you want to quit?")) endQuiz();
  });
  document.getElementById("submitBtn")?.addEventListener("click", () => endQuiz());
}

// ── Feedback ──────────────────────────────────────────────────────────────────
let selectedRating = 0;

function resetFeedbackForm() {
  selectedRating = 0;
  const form = document.getElementById("feedbackForm");
  if (form) form.reset();
  document.querySelectorAll(".star").forEach(s => s.classList.remove("active"));
  const wrapper = document.getElementById("feedbackFormWrapper");
  const success = document.getElementById("feedbackSuccess");
  const btn     = document.getElementById("feedbackToggleBtn");
  if (wrapper) wrapper.style.display = "none";
  if (success) success.style.display = "none";
  if (btn)     { btn.style.display = "inline-block"; btn.textContent = "Give Feedback"; }
}

function toggleFeedback() {
  const wrapper = document.getElementById("feedbackFormWrapper");
  const btn     = document.getElementById("feedbackToggleBtn");
  if (!wrapper) return;
  const isHidden        = wrapper.style.display === "none";
  wrapper.style.display = isHidden ? "block" : "none";
  if (btn) btn.style.display = isHidden ? "none" : "inline-block";
}

document.addEventListener("DOMContentLoaded", () => {
  document.querySelectorAll(".star").forEach(star => {
    star.addEventListener("click", () => {
      selectedRating = parseInt(star.dataset.val);
      document.getElementById("feedbackRating").value = selectedRating;
      document.querySelectorAll(".star").forEach(s => {
        s.classList.toggle("active", parseInt(s.dataset.val) <= selectedRating);
      });
    });
    star.addEventListener("mouseenter", () => {
      const val = parseInt(star.dataset.val);
      document.querySelectorAll(".star").forEach(s => {
        s.classList.toggle("hovered", parseInt(s.dataset.val) <= val);
      });
    });
    star.addEventListener("mouseleave", () => {
      document.querySelectorAll(".star").forEach(s => s.classList.remove("hovered"));
    });
  });
});

async function submitFeedback(event) {
  event.preventDefault();
  const text   = document.getElementById("feedbackText").value.trim();
  const type   = document.getElementById("feedbackType").value;
  const rating = selectedRating || null;

  if (!text) { alert("Please write your feedback before submitting."); return; }

  const btn = document.getElementById("feedbackSubmitBtn");
  btn.textContent = "Submitting...";
  btn.disabled    = true;

  try {
    const response = await fetch("api/submit_feedback.php", {
      method:  "POST",
      headers: { "Content-Type": "application/json" },
      body: JSON.stringify({
        user_id:       SESSION_USER_ID,
        quiz_id:       currentQuiz?.id || null,
        feedback_text: text,
        feedback_type: type,
        rating
      })
    });
    const result = await response.json();

    if (result.success) {
      document.getElementById("feedbackForm").style.display    = "none";
      document.getElementById("feedbackSuccess").style.display = "block";
    } else {
      alert("Error: " + result.message);
      btn.textContent = "Submit Feedback";
      btn.disabled    = false;
    }
  } catch (e) {
    alert("Network error. Please try again.");
    btn.textContent = "Submit Feedback";
    btn.disabled    = false;
  }
}
