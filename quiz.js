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

let flippedCards  = [];
let matchedPairs  = 0;
let endlessLives  = 3;
let endlessStreak = 0;

const MODE_LABELS = {
    single_player: "Single Player",
    timed_quiz:    "Timed Quiz",
    ranked_quiz:   "Ranked Quiz",
    memory_match:  "Memory Match",
    endless_quiz:  "Endless Quiz"
};

// ── Init ──────────────────────────────────────────────────────────────────────
document.addEventListener("DOMContentLoaded", () => {
    currentMode = localStorage.getItem("selectedMode") || "single_player";
    setModeSettings();
    setupEventListeners();
    setupStarRating();

    const quizId = parseInt(localStorage.getItem("selectedQuizId") || "0");
    if (quizId > 0) {
        startQuiz(quizId);
    } else {
        alert("No quiz selected. Redirecting to modes.");
        window.location.href = "modes.php";
    }
});

function setModeSettings() {
    endlessMode = memoryMatchMode = rankedMode = timedMode = singlePlayerMode = false;
    switch (currentMode) {
        case "timed_quiz":   timedMode       = true; timeLimit = 60;  break;
        case "ranked_quiz":  rankedMode      = true; timeLimit = 300; break;
        case "memory_match": memoryMatchMode = true; timeLimit = 180; break;
        case "endless_quiz": endlessMode     = true; timeLimit = 0;   break;
        default:             singlePlayerMode= true; timeLimit = 0;   break;
    }
}

// ── Quiz Load ─────────────────────────────────────────────────────────────────
async function startQuiz(quizId) {
    showSection("quizLoading");
    try {
        const res  = await fetch(`api/get_quiz_questions.php?quiz_id=${quizId}`);
        const data = await res.json();

        if (!data.success || !data.questions?.length) {
            alert(data.message || "Failed to load quiz. Going back to modes.");
            window.location.href = "modes.php";
            return;
        }

        currentQuiz      = data.quiz;
        currentQuestions = data.questions;

        prepareQuestionsForMode();
        resetQuizState();
        showSection("quizGame");
        updateQuizHeader();
        loadQuestion();

        const timerEl = document.getElementById("timer");
        if (timerEl) timerEl.style.display = (timedMode || rankedMode || memoryMatchMode) ? "" : "none";
        if (timedMode || rankedMode || memoryMatchMode) startTimer();

    } catch (e) {
        console.error(e);
        alert("Network error. Is XAMPP running?");
        window.location.href = "modes.php";
    }
}

function prepareQuestionsForMode() {
    currentQuestions = memoryMatchMode
        ? createMemoryMatchPairs(currentQuestions)
        : shuffleArray([...currentQuestions]);
}

function createMemoryMatchPairs(questions) {
    const pairs = [];
    questions.forEach(q => {
        const correct = q.answers.find(a => Number(a.is_correct) === 1);
        if (correct) {
            pairs.push({ type: "term",       content: q.question,   pairId: q.id });
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

function showSection(id) {
    ["quizLoading","quizGame","quizResults"].forEach(k => {
        const el = document.getElementById(k);
        if (!el) return;
        if (k === id) {
            el.style.display = k === "quizLoading" ? "flex" : (k === "quizResults" ? "flex" : "block");
        } else {
            el.style.display = "none";
        }
    });
}

// ── Header ────────────────────────────────────────────────────────────────────
function updateQuizHeader() {
    const s = (id, v) => { const el = document.getElementById(id); if (el) el.textContent = v; };
    s("quizTitle", `${currentQuiz?.title || "Quiz"} — ${MODE_LABELS[currentMode] || currentMode}`);
    s("score", `Score: ${score}`);

    if (memoryMatchMode)  s("questionCounter", `Pairs: ${matchedPairs} / ${currentQuestions.length / 2}`);
    else if (endlessMode) s("questionCounter", `Lives: ${endlessLives} | Streak: ${endlessStreak}`);
    else                  s("questionCounter", `Question ${Math.min(currentQuestionIndex + 1, currentQuestions.length)} of ${currentQuestions.length}`);
}

// ── Questions ─────────────────────────────────────────────────────────────────
function loadQuestion() {
    if (!isQuizActive) return;
    memoryMatchMode ? loadMemoryMatchQuestion() : loadStandardQuestion();
}

function loadStandardQuestion() {
    const question = currentQuestions[currentQuestionIndex];
    if (!question) { endQuiz(); return; }

    const qEl = document.getElementById("questionText");
    const oEl = document.getElementById("options");
    if (qEl) qEl.textContent = question.question;

    if (oEl) {
        oEl.innerHTML = "";
        if (!question.answers?.length) {
            oEl.innerHTML = `<p style="color:#f87171;">No answer options for this question.</p>`;
            return;
        }
        shuffleArray([...question.answers]).forEach(answer => {
            const div = document.createElement("div");
            div.className   = "option";
            div.textContent = answer.text;
            div.onclick     = () => selectAnswer(answer, question);
            oEl.appendChild(div);
        });
    }
    updateQuizHeader();
}

function loadMemoryMatchQuestion() {
    const container = document.getElementById("questionText")?.parentElement;
    if (!container) return;
    container.innerHTML = `<h3 style="color:#fff;margin-bottom:20px;">Match each term with its correct definition</h3>`;

    const grid = document.createElement("div");
    grid.className = "memory-grid";

    currentQuestions.forEach((item, index) => {
        const card = document.createElement("div");
        card.className = "memory-card";
        card.dataset.index  = index;
        card.dataset.pairId = item.pairId;
        card.dataset.type   = item.type;
        card.onclick = () => flipCard(card);

        const lbl = document.createElement("div");
        lbl.style.cssText = "font-size:10px;opacity:.4;text-transform:uppercase;letter-spacing:.5px;margin-bottom:6px;";
        lbl.textContent   = item.type === "term" ? "Term" : "Definition";

        const content = document.createElement("div");
        content.className   = "card-content";
        content.textContent = item.content;

        card.appendChild(lbl);
        card.appendChild(content);
        grid.appendChild(card);
    });
    container.appendChild(grid);
}

function flipCard(card) {
    if (card.classList.contains("flipped") || card.classList.contains("matched") || flippedCards.length >= 2) return;
    card.classList.add("flipped");
    flippedCards.push(card);
    if (flippedCards.length === 2) setTimeout(checkMatch, 900);
}

function checkMatch() {
    const [c1, c2] = flippedCards;
    const isMatch  = c1.dataset.pairId === c2.dataset.pairId && c1.dataset.type !== c2.dataset.type;

    if (isMatch) {
        c1.classList.add("matched"); c2.classList.add("matched");
        matchedPairs++; score += 10; correctAnswers++;
        updateQuizHeader();
        if (matchedPairs === currentQuestions.length / 2) endQuiz();
    } else {
        c1.style.background = "rgba(248,113,113,.25)";
        c2.style.background = "rgba(248,113,113,.25)";
        setTimeout(() => {
            c1.classList.remove("flipped"); c1.style.background = "";
            c2.classList.remove("flipped"); c2.style.background = "";
            if (endlessMode) { endlessLives--; updateQuizHeader(); if (endlessLives <= 0) endQuiz(); }
        }, 600);
    }
    flippedCards = [];
}

function selectAnswer(answer, question) {
    const isCorrect = Number(answer.is_correct) === 1;
    userAnswers.push({ questionId: question.id, answerId: answer.id, isCorrect });

    document.querySelectorAll(".option").forEach(opt => {
        opt.style.pointerEvents = "none";
        if (opt.textContent === answer.text) {
            opt.style.borderColor = isCorrect ? "#4ade80" : "#f87171";
            opt.style.background  = isCorrect ? "rgba(74,222,128,.2)" : "rgba(248,113,113,.2)";
        }
    });

    if (isCorrect) { score += 10; correctAnswers++; endlessStreak++; }
    else {
        const correctAns = question.answers.find(a => Number(a.is_correct) === 1);
        document.querySelectorAll(".option").forEach(opt => {
            if (correctAns && opt.textContent === correctAns.text) {
                opt.style.borderColor = "#4ade80";
                opt.style.background  = "rgba(74,222,128,.12)";
            }
        });
        if (endlessMode) { endlessLives--; endlessStreak = 0; }
    }

    updateQuizHeader();
    setTimeout(() => {
        if (endlessMode && endlessLives <= 0) { endQuiz(); return; }
        currentQuestionIndex++;
        currentQuestionIndex >= currentQuestions.length ? endQuiz() : loadQuestion();
    }, 900);
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

function updateTimerDisplay(s) {
    const el = document.getElementById("timer");
    if (!el) return;
    const m = Math.floor(s / 60);
    el.textContent = `⏱ ${m}:${(s % 60).toString().padStart(2, "0")}`;
    el.style.color = s <= 10 ? "#f87171" : "";
}

// ── End Quiz ──────────────────────────────────────────────────────────────────
function endQuiz() {
    if (!isQuizActive) return;
    isQuizActive = false;
    clearInterval(quizTimer);

    const timeTaken = Math.floor((Date.now() - quizStartTime) / 1000);
    const total     = memoryMatchMode
        ? currentQuestions.length / 2
        : (endlessMode ? userAnswers.length : currentQuestions.length);

    saveQuizResult(correctAnswers, total, timeTaken)
        .then(pts => showResults(correctAnswers, total, timeTaken, pts));
}

async function saveQuizResult(correct, total, timeTaken) {
    try {
        const res  = await fetch("api/save_quiz_result.php", {
            method: "POST", headers: { "Content-Type": "application/json" },
            body: JSON.stringify({
                quiz_id: currentQuiz?.id || 0, mode: currentMode,
                correct_answers: correct, total_questions: total,
                time_taken: timeTaken, time_limit: timeLimit,
                user_id: typeof SESSION_USER_ID !== "undefined" ? SESSION_USER_ID : null
            })
        });
        const data = await res.json();
        return data.points_earned || 0;
    } catch (e) { console.error(e); return 0; }
}

function showResults(correct, total, timeTaken, pts) {
    showSection("quizResults");

    const titles = {
        single_player: "Quiz Complete! 🎉",
        timed_quiz:    "Time's Up! ⏱️",
        ranked_quiz:   "Ranked Match Done! ⚔️",
        memory_match:  "Memory Master! 🧠",
        endless_quiz:  endlessLives > 0 ? "You Survived! 🎉" : "Game Over 💀"
    };

    const s = (id, v) => { const el = document.getElementById(id); if (el) el.textContent = v; };
    s("resultsModeTitle", titles[currentMode] || "Quiz Complete!");
    s("pointsEarned",    `+${pts}`);
    s("correctAnswers",  correct);
    s("totalQuestions",  total);
    s("timeTaken",       formatTime(timeTaken));

    resetFeedbackUI();
}

// ── Utilities ─────────────────────────────────────────────────────────────────
function formatTime(s) { return `${Math.floor(s/60)}:${(s%60).toString().padStart(2,"0")}`; }

function shuffleArray(arr) {
    for (let i = arr.length - 1; i > 0; i--) {
        const j = Math.floor(Math.random() * (i + 1));
        [arr[i], arr[j]] = [arr[j], arr[i]];
    }
    return arr;
}

function setupEventListeners() {
    document.getElementById("quitBtn")?.addEventListener("click", () => {
        if (confirm("Quit and go back to modes?")) {
            isQuizActive = false; clearInterval(quizTimer);
            window.location.href = "modes.php";
        }
    });
    document.getElementById("submitBtn")?.addEventListener("click", () => endQuiz());
}

// ── Feedback ──────────────────────────────────────────────────────────────────
let selectedRating = 0;

function setupStarRating() {
    document.querySelectorAll(".star").forEach(star => {
        star.addEventListener("click", () => {
            selectedRating = parseInt(star.dataset.val);
            const inp = document.getElementById("feedbackRating");
            if (inp) inp.value = selectedRating;
            document.querySelectorAll(".star").forEach(s =>
                s.classList.toggle("active", parseInt(s.dataset.val) <= selectedRating));
        });
        star.addEventListener("mouseenter", () => {
            const v = parseInt(star.dataset.val);
            document.querySelectorAll(".star").forEach(s =>
                s.classList.toggle("hovered", parseInt(s.dataset.val) <= v));
        });
        star.addEventListener("mouseleave", () =>
            document.querySelectorAll(".star").forEach(s => s.classList.remove("hovered")));
    });
}

function toggleFeedback() {
    document.getElementById("feedbackPrompt")?.classList.toggle("open");
}

function updateCharCount(el) {
    const counter = document.getElementById("charCount");
    if (!counter) return;
    const len = el.value.length;
    const max = parseInt(el.getAttribute("maxlength") || "500");
    counter.textContent = `${len} / ${max}`;
    counter.className   = "fb-char-count" + (len >= max ? " over" : len > max * .8 ? " warn" : "");
}

function resetFeedbackUI() {
    selectedRating = 0;
    const prompt   = document.getElementById("feedbackPrompt");
    const formWrap = document.getElementById("fbFormWrap");
    const success  = document.getElementById("fbSuccess");
    const textarea = document.getElementById("feedbackText");
    const counter  = document.getElementById("charCount");
    const errEl    = document.getElementById("fbTextError");
    const btn      = document.getElementById("feedbackSubmitBtn");
    const sel      = document.getElementById("feedbackType");

    if (prompt)   prompt.classList.remove("open");
    if (formWrap) formWrap.style.display = "flex";
    if (success)  success.style.display  = "none";
    if (textarea) textarea.value = "";
    if (counter)  { counter.textContent = "0 / 500"; counter.className = "fb-char-count"; }
    if (errEl)    errEl.classList.remove("show");
    if (btn)      { btn.textContent = "Send Feedback"; btn.disabled = false; }
    if (sel)      sel.value = "general";

    document.querySelectorAll(".star").forEach(s => s.classList.remove("active","hovered"));
    const inp = document.getElementById("feedbackRating");
    if (inp) inp.value = "";
}

async function submitFeedback() {
    const text  = document.getElementById("feedbackText")?.value.trim();
    const type  = document.getElementById("feedbackType")?.value;
    const errEl = document.getElementById("fbTextError");

    if (!text) { errEl?.classList.add("show"); document.getElementById("feedbackText")?.focus(); return; }
    errEl?.classList.remove("show");

    const btn = document.getElementById("feedbackSubmitBtn");
    if (btn) { btn.textContent = "Sending..."; btn.disabled = true; }

    try {
        const res    = await fetch("api/submit_feedback.php", {
            method: "POST", headers: { "Content-Type": "application/json" },
            body: JSON.stringify({
                user_id:       typeof SESSION_USER_ID !== "undefined" ? SESSION_USER_ID : null,
                quiz_id:       currentQuiz?.id || null,
                feedback_text: text, feedback_type: type,
                rating:        selectedRating || null
            })
        });
        const result = await res.json();

        if (result.success) {
            document.getElementById("fbFormWrap").style.display = "none";
            document.getElementById("fbSuccess").style.display  = "flex";
        } else {
            alert("Error: " + result.message);
            if (btn) { btn.textContent = "Send Feedback"; btn.disabled = false; }
        }
    } catch (e) {
        alert("Network error. Please try again.");
        if (btn) { btn.textContent = "Send Feedback"; btn.disabled = false; }
    }
}
