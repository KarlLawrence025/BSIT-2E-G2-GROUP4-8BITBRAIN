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

// Difficulty-based memory match pair counts
const MM_PAIRS = { easy: 9, medium: 12, hard: 16 };
const MM_TIME  = 300; // 5 minutes for all memory match

// Difficulty-based timed quiz timers (seconds)
const TIMED_LIMITS = { easy: 300, medium: 180, hard: 90 };

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
    setupStarRating();

    document.getElementById("quitBtn")?.addEventListener("click", () => {
        if (confirm("Quit and go back to modes?")) {
            isQuizActive = false;
            clearInterval(quizTimer);
            window.location.href = "modes.php";
        }
    });
    document.getElementById("submitBtn")?.addEventListener("click", () => endQuiz());

    const quizId = parseInt(localStorage.getItem("selectedQuizId") || "0");
    if (quizId > 0) startQuiz(quizId);
    else { alert("No quiz selected."); window.location.href = "modes.php"; }
});

function setModeSettings() {
    endlessMode = memoryMatchMode = rankedMode = timedMode = singlePlayerMode = false;
    switch (currentMode) {
        case "timed_quiz":   timedMode       = true; break;
        case "ranked_quiz":  rankedMode      = true; timeLimit = 300; break;
        case "memory_match": memoryMatchMode = true; timeLimit = MM_TIME; break;
        case "endless_quiz": endlessMode     = true; timeLimit = 0; break;
        default:             singlePlayerMode= true; timeLimit = 0; break;
    }
    // timed_quiz timeLimit is set after quiz loads (depends on difficulty)
}

// ── Quiz Load ─────────────────────────────────────────────────────────────────
async function startQuiz(quizId) {
    showSection("quizLoading");
    try {
        const res  = await fetch(`api/get_quiz_questions.php?quiz_id=${quizId}`);
        const data = await res.json();

        if (!data.success || !data.questions?.length) {
            alert(data.message || "Failed to load quiz.");
            window.location.href = "modes.php";
            return;
        }

        currentQuiz      = data.quiz;
        currentQuestions = data.questions;

        // Set timed quiz time based on difficulty
        if (timedMode) {
            const diff = (currentQuiz.difficulty || "medium").toLowerCase();
            timeLimit = TIMED_LIMITS[diff] || TIMED_LIMITS.medium;
        }

        if (memoryMatchMode) {
            const diff   = (currentQuiz.difficulty || "medium").toLowerCase();
            const maxPairs = MM_PAIRS[diff] || MM_PAIRS.medium;

            // Shuffle and cap to difficulty-based pair count
            const shuffled = shuffleArray([...currentQuestions]);
            const capped   = shuffled.slice(0, maxPairs);

            currentQuestions = createMemoryMatchPairs(capped);
            resetQuizState();
            buildMemoryMatchUI(maxPairs);
            showSection("memoryGame");
            startTimer();
        } else {
            currentQuestions = shuffleArray([...currentQuestions]);
            resetQuizState();
            showSection("quizGame");
            updateStandardHeader();
            loadStandardQuestion();
            const timerEl = document.getElementById("timer");
            if (timerEl) timerEl.style.display = (timedMode || rankedMode) ? "" : "none";
            if (timedMode || rankedMode) startTimer();
        }
    } catch (e) {
        console.error(e);
        alert("Network error. Is XAMPP running?");
        window.location.href = "modes.php";
    }
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
    ["quizLoading","quizGame","memoryGame","quizResults"].forEach(k => {
        const el = document.getElementById(k);
        if (!el) return;
        el.style.display = k === id
            ? (k === "quizLoading" ? "flex" : k === "quizResults" ? "flex" : "block")
            : "none";
    });
}

// ══════════════════════════════════════════════════════
// MEMORY MATCH UI
// ══════════════════════════════════════════════════════

function buildMemoryMatchUI(maxPairs) {
    const section = document.getElementById("memoryGame");
    const total   = currentQuestions.length; // already paired & shuffled

    // Determine column count based on pair count
    // pairs: 9→18 cards, 12→24 cards, 16→32 cards
    let cols;
    const pairs = total / 2;
    if (pairs <= 9)       cols = 6;   // 9 pairs → 3 rows of 6
    else if (pairs <= 12) cols = 6;   // 12 pairs → 4 rows of 6
    else                  cols = 8;   // 16 pairs → 4 rows of 8

    const diffLabel = (currentQuiz?.difficulty || "medium").toUpperCase();

    section.innerHTML = `
        <div class="mm-page">
            <div class="mm-topbar">
                <div class="mm-topbar-left">
                    <div class="mm-title">${escHtml(currentQuiz?.title || "Quiz")} — Memory Match</div>
                    <div class="mm-stats">
                        <span class="mm-stat" id="mmPairs">Pairs: 0 / ${total / 2}</span>
                        <span class="mm-stat timer-stat" id="mmTimer">⏱ 5:00</span>
                        <span class="mm-stat" id="mmScore">Score: 0</span>
                        <span class="mm-stat" style="color:#fbbf24;">📊 ${diffLabel}</span>
                    </div>
                </div>
                <button class="mm-quit-btn" id="mmQuitBtn">← Back to Modes</button>
            </div>

            <div class="mm-instructions">
                Flip two cards to match a
                <span class="mm-tag term">TERM</span>
                with its
                <span class="mm-tag def">DEFINITION</span>
                &nbsp;·&nbsp; ${total / 2} pairs · 5 minutes
            </div>

            <div class="mm-grid" id="mmGrid" style="
                display: grid;
                grid-template-columns: repeat(${cols}, 1fr);
                gap: 16px;
                width: 100%;
                max-width: ${cols * 160}px;
                margin: 0 auto;
                justify-items: center;
            "></div>

            <div class="mm-progress-wrap">
                <div class="mm-progress-label" id="mmProgressLabel">0 of ${total / 2} pairs found</div>
                <div class="mm-progress-bar">
                    <div class="mm-progress-fill" id="mmProgressFill" style="width:0%"></div>
                </div>
            </div>
        </div>`;

    document.getElementById("mmQuitBtn").addEventListener("click", () => {
        if (confirm("Quit and go back to modes?")) {
            isQuizActive = false;
            clearInterval(quizTimer);
            window.location.href = "modes.php";
        }
    });

    const grid = document.getElementById("mmGrid");
    currentQuestions.forEach((item, index) => {
        const card = document.createElement("div");
        card.className      = `mm-card ${item.type}`;
        card.dataset.index  = index;
        card.dataset.pairId = item.pairId;
        card.dataset.type   = item.type;
        card.style.cssText  = "width:100%; min-height:160px;";

        card.innerHTML = `
            <div class="mm-card-inner">
                <div class="mm-card-front">
                    <div class="mm-card-question-mark">?</div>
                </div>
                <div class="mm-card-back">
                    <div class="mm-card-type-label">${item.type === "term" ? "TERM" : "DEFINITION"}</div>
                    <div class="mm-card-text">${escHtml(item.content)}</div>
                </div>
            </div>`;

        card.addEventListener("click", () => flipCard(card));
        grid.appendChild(card);
    });
}

function updateMMHeader() {
    const total = currentQuestions.length / 2;
    const pairsEl   = document.getElementById("mmPairs");
    const scoreEl   = document.getElementById("mmScore");
    const progLabel = document.getElementById("mmProgressLabel");
    const progFill  = document.getElementById("mmProgressFill");

    if (pairsEl)   pairsEl.textContent   = `Pairs: ${matchedPairs} / ${total}`;
    if (scoreEl)   scoreEl.textContent   = `Score: ${score}`;
    if (progLabel) progLabel.textContent = `${matchedPairs} of ${total} pairs found`;
    if (progFill)  progFill.style.width  = `${(matchedPairs / total) * 100}%`;
}

// ── Card flip ─────────────────────────────────────────────────────────────────
function flipCard(card) {
    if (!isQuizActive) return;
    if (card.classList.contains("flipped") ||
        card.classList.contains("matched") ||
        flippedCards.length >= 2) return;

    card.classList.add("flipped");
    flippedCards.push(card);

    if (flippedCards.length === 2) setTimeout(checkMatch, 950);
}

function checkMatch() {
    const [c1, c2] = flippedCards;
    const isMatch  = c1.dataset.pairId === c2.dataset.pairId
                  && c1.dataset.type   !== c2.dataset.type;

    if (isMatch) {
        c1.classList.add("matched");
        c2.classList.add("matched");
        matchedPairs++;
        score += 10;
        correctAnswers++;
        updateMMHeader();
        if (matchedPairs === currentQuestions.length / 2) setTimeout(endQuiz, 700);
    } else {
        [c1, c2].forEach(c => c.classList.add("shake"));
        setTimeout(() => {
            c1.classList.remove("flipped", "shake");
            c2.classList.remove("flipped", "shake");
        }, 600);
    }
    flippedCards = [];
}

// ══════════════════════════════════════════════════════
// STANDARD QUIZ
// ══════════════════════════════════════════════════════

function updateStandardHeader() {
    const s = (id, v) => { const el = document.getElementById(id); if (el) el.textContent = v; };
    s("quizTitle", `${currentQuiz?.title || "Quiz"} — ${MODE_LABELS[currentMode]}`);
    s("score", `Score: ${score}`);
    if (endlessMode) s("questionCounter", `Lives: ${endlessLives} | Streak: ${endlessStreak}`);
    else s("questionCounter", `Question ${Math.min(currentQuestionIndex + 1, currentQuestions.length)} of ${currentQuestions.length}`);
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
            oEl.innerHTML = `<p style="color:#f87171;">No answer options.</p>`;
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
    updateStandardHeader();
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

    if (isCorrect) {
        score += 10;
        correctAnswers++;
        endlessStreak++;
    } else {
        const correctAns = question.answers.find(a => Number(a.is_correct) === 1);
        document.querySelectorAll(".option").forEach(opt => {
            if (correctAns && opt.textContent === correctAns.text) {
                opt.style.borderColor = "#4ade80";
                opt.style.background  = "rgba(74,222,128,.12)";
            }
        });
        if (endlessMode) { endlessLives--; endlessStreak = 0; }
    }

    updateStandardHeader();
    setTimeout(() => {
        if (endlessMode && endlessLives <= 0) { endQuiz(); return; }
        currentQuestionIndex++;
        currentQuestionIndex >= currentQuestions.length ? endQuiz() : loadStandardQuestion();
    }, 900);
}

// ── Timer ─────────────────────────────────────────────────────────────────────
function startTimer() {
    clearInterval(quizTimer);
    let timeLeft = timeLimit;
    updateTimerDisplay(timeLeft);

    quizTimer = setInterval(() => {
        timeLeft--;
        updateTimerDisplay(timeLeft);
        if (timeLeft <= 0) {
            clearInterval(quizTimer);
            handleTimeUp();
        }
    }, 1000);
}

function updateTimerDisplay(s) {
    const el = memoryMatchMode
        ? document.getElementById("mmTimer")
        : document.getElementById("timer");
    if (!el) return;

    const m = Math.floor(s / 60);
    el.textContent = `⏱ ${m}:${(s % 60).toString().padStart(2, "0")}`;

    if (memoryMatchMode) {
        el.classList.toggle("danger", s <= 30);
    } else {
        el.style.color = s <= 10 ? "#f87171" : "";
    }
}

// ── Handle time up — mark remaining as wrong, save attempt, show results ──────
function handleTimeUp() {
    if (!isQuizActive) return;
    isQuizActive = false;

    if (memoryMatchMode) {
        // For memory match — unmatched pairs count as wrong
        const totalPairs   = currentQuestions.length / 2;
        const wrongPairs   = totalPairs - matchedPairs;
        // correctAnswers already tracked via matched pairs
        const timeTaken    = Math.floor((Date.now() - quizStartTime) / 1000);
        saveQuizResult(correctAnswers, totalPairs, timeTaken).then(pts => {
            showResults(correctAnswers, totalPairs, timeTaken, pts, true);
        });
        return;
    }

    // Standard quiz (timed / ranked) — mark all remaining unanswered questions as wrong
    const remaining = currentQuestions.slice(currentQuestionIndex);
    remaining.forEach(q => {
        userAnswers.push({ questionId: q.id, answerId: null, isCorrect: false });
    });

    // Flash the current options red to show time ran out
    document.querySelectorAll(".option").forEach(opt => {
        opt.style.pointerEvents = "none";
        opt.style.opacity       = "0.5";
    });

    const timeTaken  = Math.floor((Date.now() - quizStartTime) / 1000);
    const total      = currentQuestions.length;

    // Small delay so user sees "time's up" state before results
    setTimeout(() => {
        saveQuizResult(correctAnswers, total, timeTaken).then(pts => {
            showResults(correctAnswers, total, timeTaken, pts, true);
        });
    }, 800);
}

// ── End Quiz (called when all questions done OR user quits) ───────────────────
function endQuiz() {
    if (!isQuizActive) return;
    isQuizActive = false;
    clearInterval(quizTimer);

    const timeTaken = Math.floor((Date.now() - quizStartTime) / 1000);
    const total     = memoryMatchMode
        ? currentQuestions.length / 2
        : (endlessMode ? userAnswers.length : currentQuestions.length);

    saveQuizResult(correctAnswers, total, timeTaken)
        .then(pts => showResults(correctAnswers, total, timeTaken, pts, false));
}

async function saveQuizResult(correct, total, timeTaken) {
    try {
        const res  = await fetch("api/save_quiz_result.php", {
            method: "POST",
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
        const data = await res.json();
        return data.points_earned || 0;
    } catch (e) {
        console.error(e);
        return 0;
    }
}

function showResults(correct, total, timeTaken, pts, timedOut) {
    showSection("quizResults");

    const titles = {
        single_player: "Quiz Complete! 🎉",
        timed_quiz:    timedOut ? "Time's Up! ⏱️" : "Quiz Complete! 🎉",
        ranked_quiz:   timedOut ? "Time's Up! ⏱️" : "Ranked Match Done! ⚔️",
        memory_match:  timedOut ? "Time's Up! ⏱️" : "Memory Master! 🧠",
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

function escHtml(str) {
    const d = document.createElement("div");
    d.appendChild(document.createTextNode(str || ""));
    return d.innerHTML;
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
    document.getElementById("feedbackPrompt")?.classList.remove("open");
    const fw = document.getElementById("fbFormWrap");
    const fs = document.getElementById("fbSuccess");
    if (fw) fw.style.display = "flex";
    if (fs) fs.style.display = "none";

    const ta  = document.getElementById("feedbackText");
    const cnt = document.getElementById("charCount");
    const err = document.getElementById("fbTextError");
    const btn = document.getElementById("feedbackSubmitBtn");
    const sel = document.getElementById("feedbackType");
    const inp = document.getElementById("feedbackRating");

    if (ta)  ta.value      = "";
    if (cnt) { cnt.textContent = "0 / 500"; cnt.className = "fb-char-count"; }
    if (err) err.classList.remove("show");
    if (btn) { btn.textContent = "Send Feedback"; btn.disabled = false; }
    if (sel) sel.value = "general";
    if (inp) inp.value = "";

    document.querySelectorAll(".star").forEach(s => s.classList.remove("active","hovered"));
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
