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

// ── Mode display names ────────────────────────────────────────────────────────
const MODE_LABELS = {
    single_player: "Single Player",
    timed_quiz:    "Timed Quiz",
    ranked_quiz:   "Ranked Quiz",
    memory_match:  "Memory Match",
    endless_quiz:  "Endless Quiz"
};

// ── Init ──────────────────────────────────────────────────────────────────────
document.addEventListener("DOMContentLoaded", () => { initializeQuiz(); });

function initializeQuiz() {
    currentMode = localStorage.getItem("selectedMode") || "single_player";
    setModeSettings();

    const modeTitle = document.getElementById("modeTitle");
    if (modeTitle) {
        modeTitle.textContent = (MODE_LABELS[currentMode] || "Quiz") + " — Select a Quiz";
    }

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

    container.innerHTML = `<p style="color:#fff;text-align:center;grid-column:1/-1;padding:40px;">
        Loading quizzes for <strong>${MODE_LABELS[currentMode] || currentMode}</strong>...
    </p>`;

    try {
        const response = await fetch(`api/get_quizzes.php?mode=${encodeURIComponent(currentMode)}`);
        const data     = await response.json();

        if (!data.success) {
            container.innerHTML = `<p style="color:#f87171;text-align:center;grid-column:1/-1;">
                Failed to load quizzes. Is XAMPP running?
            </p>`;
            return;
        }

        displayQuizSelection(data.data);
    } catch (e) {
        console.error("loadQuizSelection error:", e);
        container.innerHTML = `<p style="color:#f87171;text-align:center;grid-column:1/-1;">
            Network error. Make sure XAMPP Apache is running.
        </p>`;
    }
}

function displayQuizSelection(quizzes) {
    const container = document.getElementById("quizGrid");
    if (!container) return;
    container.innerHTML = "";

    if (!quizzes || quizzes.length === 0) {
        container.innerHTML = `
            <div style="color:#fff;text-align:center;grid-column:1/-1;padding:60px 20px;">
                <div style="font-size:48px;margin-bottom:15px;">🎮</div>
                <h3 style="margin-bottom:10px;font-size:22px;">No quizzes yet for <em>${MODE_LABELS[currentMode] || currentMode}</em></h3>
                <p style="opacity:.6;margin-bottom:20px;">Ask your admin to create quizzes for this mode.</p>
                <a href="modes.php" style="color:#ff2fb3;text-decoration:none;border:1px solid #ff2fb3;padding:10px 24px;border-radius:50px;">← Back to Modes</a>
            </div>`;
        return;
    }

    quizzes.forEach(quiz => {
        const card     = document.createElement("div");
        card.className = "quiz-card";
        card.onclick   = () => selectQuiz(quiz.id);

        const qCount    = parseInt(quiz.question_count) || 0;
        const diffColor = { easy: "#4ade80", medium: "#fbbf24", hard: "#f87171" };

        card.innerHTML = `
            <h3>${escapeHtml(quiz.title)}</h3>
            <p class="category" style="color:#ff2fb3;font-weight:bold;margin-bottom:6px;">${escapeHtml(quiz.category)}</p>
            <span class="difficulty ${quiz.difficulty}" style="
                display:inline-block;padding:3px 10px;border-radius:10px;font-size:.8em;
                margin-bottom:10px;background:${diffColor[quiz.difficulty]||'#aaa'};color:#000;font-weight:bold;">
                ${quiz.difficulty}
            </span>
            <p class="question-count" style="color:rgba(255,255,255,.6);font-size:.9em;">
                ${qCount} question${qCount !== 1 ? "s" : ""}
            </p>`;
        container.appendChild(card);
    });
}

function escapeHtml(str) {
    const d = document.createElement("div");
    d.appendChild(document.createTextNode(str || ""));
    return d.innerHTML;
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
        if (timedMode || rankedMode || memoryMatchMode) startTimer();

    } catch (e) {
        console.error("startQuiz error:", e);
        showError("Network error loading quiz. Please check your connection.");
    }
}

function prepareQuestionsForMode() {
    if (memoryMatchMode) {
        currentQuestions = createMemoryMatchPairs(currentQuestions);
    } else {
        currentQuestions = shuffleArray([...currentQuestions]);
    }
}

function createMemoryMatchPairs(questions) {
    const pairs = [];
    questions.forEach(q => {
        const correct = q.answers.find(a => Number(a.is_correct) === 1);
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
    clearInterval(quizTimer);
    quizTimer = null;
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

    if (titleEl)   titleEl.textContent = (currentQuiz?.title || "Quiz") + ` — ${MODE_LABELS[currentMode] || currentMode}`;
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

        const shuffledAnswers = shuffleArray([...question.answers]);
        shuffledAnswers.forEach((answer) => {
            const div       = document.createElement("div");
            div.className   = "option";
            div.textContent = answer.text;
            div.onclick     = () => selectAnswer(answer);
            optionsEl.appendChild(div);
        });
    }
    updateQuizHeader();
}

function loadMemoryMatchQuestion() {
    const container = document.getElementById("questionText")?.parentElement;
    if (!container) return;
    container.innerHTML = "<h3 style='color:#fff;margin-bottom:15px;'>Match each term with its correct definition</h3>";

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

        const label = document.createElement("div");
        label.style.cssText = "font-size:10px;opacity:.5;text-transform:uppercase;margin-bottom:4px;";
        label.textContent   = item.type === "term" ? "Term" : "Definition";

        const content       = document.createElement("div");
        content.className   = "card-content";
        content.textContent = item.content;

        card.appendChild(label);
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
        correctAnswers++;
        if (matchedPairs === currentQuestions.length / 2) endQuiz();
    } else {
        c1.classList.remove("flipped");
        c2.classList.remove("flipped");
        if (endlessMode) { endlessLives--; if (endlessLives <= 0) endQuiz(); }
    }
    flippedCards = [];
    updateQuizHeader();
}

function selectAnswer(answer) {
    const question  = currentQuestions[currentQuestionIndex];
    const isCorrect = Number(answer.is_correct) === 1;

    userAnswers.push({ questionId: question.id, answerId: answer.id, isCorrect });

    // Visual feedback — lock all options
    const options = document.querySelectorAll(".option");
    options.forEach(opt => {
        opt.style.pointerEvents = "none";
        if (opt.textContent === answer.text) {
            opt.style.borderColor = isCorrect ? "#4ade80" : "#f87171";
            opt.style.background  = isCorrect ? "rgba(74,222,128,.2)" : "rgba(248,113,113,.2)";
        }
    });

    if (isCorrect) {
        score += 10;
        correctAnswers++;
        endlessStreak++;
    } else {
        // Highlight correct answer
        const correctAns = question.answers.find(a => Number(a.is_correct) === 1);
        options.forEach(opt => {
            if (correctAns && opt.textContent === correctAns.text) {
                opt.style.borderColor = "#4ade80";
                opt.style.background  = "rgba(74,222,128,.15)";
            }
        });
        if (endlessMode) {
            endlessLives--;
            endlessStreak = 0;
        }
    }

    setTimeout(() => {
        if (endlessMode && endlessLives <= 0) { endQuiz(); return; }
        currentQuestionIndex++;
        currentQuestionIndex >= currentQuestions.length ? endQuiz() : loadQuestion();
    }, 800);
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
        el.style.color = seconds <= 10 ? "#f87171" : "";
    }
}

// ── End Quiz ──────────────────────────────────────────────────────────────────
function endQuiz() {
    if (!isQuizActive) return;
    isQuizActive = false;
    clearInterval(quizTimer);

    const timeTaken      = Math.floor((Date.now() - quizStartTime) / 1000);
    const totalQuestions = memoryMatchMode
        ? currentQuestions.length / 2
        : (endlessMode ? userAnswers.length : currentQuestions.length);

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
                user_id:         typeof SESSION_USER_ID !== "undefined" ? SESSION_USER_ID : null
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

    let title = "Quiz Complete!";
    if (endlessMode)     title = endlessLives > 0 ? "You Survived! 🎉" : "Game Over 💀";
    if (memoryMatchMode) title = "Memory Match Complete! 🧠";
    if (rankedMode)      title = "Ranked Match Complete! ⚔️";
    if (timedMode)       title = "Time's Up! ⏱️";

    const titleEl = document.getElementById("resultsModeTitle");
    if (titleEl) titleEl.textContent = title;

    const pctEl  = document.getElementById("pointsEarned");
    if (pctEl)   pctEl.textContent  = `+${pointsEarned}`;

    const corrEl = document.getElementById("correctAnswers");
    if (corrEl)  corrEl.textContent = correct;

    const totEl  = document.getElementById("totalQuestions");
    if (totEl)   totEl.textContent  = total;

    const timeEl = document.getElementById("timeTaken");
    if (timeEl)  timeEl.textContent = formatTime(timeTaken);

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
        if (confirm("Are you sure you want to quit?")) {
            isQuizActive = false;
            clearInterval(quizTimer);
            document.getElementById("quizGame").style.display      = "none";
            document.getElementById("quizSelection").style.display = "block";
            loadQuizSelection();
        }
    });
    document.getElementById("submitBtn")?.addEventListener("click", () => endQuiz());
}

// ── Feedback ──────────────────────────────────────────────────────────────────
let selectedRating = 0;

function resetFeedbackForm() {
    selectedRating = 0;
    const form = document.getElementById("feedbackForm");
    if (form) form.reset();
    document.querySelectorAll(".star").forEach(s => s.classList.remove("active", "hovered"));
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
    const isHidden        = wrapper.style.display === "none" || wrapper.style.display === "";
    wrapper.style.display = isHidden ? "block" : "none";
    if (btn) btn.style.display = isHidden ? "none" : "inline-block";
}

document.addEventListener("DOMContentLoaded", () => {
    document.querySelectorAll(".star").forEach(star => {
        star.addEventListener("click", () => {
            selectedRating = parseInt(star.dataset.val);
            const ratingInput = document.getElementById("feedbackRating");
            if (ratingInput) ratingInput.value = selectedRating;
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
    const text   = document.getElementById("feedbackText")?.value.trim();
    const type   = document.getElementById("feedbackType")?.value;
    const rating = selectedRating || null;

    if (!text) { alert("Please write your feedback before submitting."); return; }

    const btn = document.getElementById("feedbackSubmitBtn");
    if (btn) { btn.textContent = "Submitting..."; btn.disabled = true; }

    try {
        const response = await fetch("api/submit_feedback.php", {
            method:  "POST",
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify({
                user_id:       typeof SESSION_USER_ID !== "undefined" ? SESSION_USER_ID : null,
                quiz_id:       currentQuiz?.id || null,
                feedback_text: text,
                feedback_type: type,
                rating
            })
        });
        const result = await response.json();

        if (result.success) {
            const formEl    = document.getElementById("feedbackForm");
            const successEl = document.getElementById("feedbackSuccess");
            if (formEl)    formEl.style.display    = "none";
            if (successEl) successEl.style.display = "block";
        } else {
            alert("Error: " + result.message);
            if (btn) { btn.textContent = "Submit Feedback"; btn.disabled = false; }
        }
    } catch (e) {
        alert("Network error. Please try again.");
        if (btn) { btn.textContent = "Submit Feedback"; btn.disabled = false; }
    }
}
