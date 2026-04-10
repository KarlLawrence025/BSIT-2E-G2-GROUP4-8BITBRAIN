console.log("Endless Quiz loaded ♾️");

// ── State ─────────────────────────────────────────────────────────────────────
let questions    = [];
let qIndex       = 0;
let lives        = 3;
let score        = 0;
let streak       = 0;
let bestStreak   = 0;
let correct      = 0;
let answered     = 0;
let gameActive   = false;
let answering    = false;

// ── Boot ──────────────────────────────────────────────────────────────────────
document.addEventListener("DOMContentLoaded", () => {
    setupStarRating();
    document.getElementById("elQuitBtn").addEventListener("click", () => {
        if (confirm("Quit Endless Quiz?")) endGame(false);
    });
    loadQuestions();
});

// ── Load all questions from DB ────────────────────────────────────────────────
async function loadQuestions() {
    show("elLoading");
    try {
        const res  = await fetch("api/get_endless_questions.php");
        const data = await res.json();

        if (!data.success || !data.data?.length) {
            alert(data.message || "No questions available yet. Ask your admin to create some quizzes!");
            window.location.href = "modes.php";
            return;
        }

        questions  = data.data;
        qIndex     = 0;
        lives      = 3;
        score      = 0;
        streak     = 0;
        bestStreak = 0;
        correct    = 0;
        answered   = 0;
        gameActive = true;

        show("elGame");
        updateHearts();
        updateStats();
        renderQuestion();

    } catch (e) {
        console.error(e);
        alert("Network error. Is XAMPP running?");
        window.location.href = "modes.php";
    }
}

// ── Render current question ───────────────────────────────────────────────────
function renderQuestion() {
    if (!gameActive) return;

    if (qIndex >= questions.length) {
        questions = shuffleArray([...questions]);
        qIndex    = 0;
    }

    const q = questions[qIndex];

    const srcTitle = document.getElementById("elSourceTitle");
    const srcDiff  = document.getElementById("elSourceDiff");
    if (srcTitle) srcTitle.textContent = q.quiz_title || "Quiz";
    if (srcDiff) {
        srcDiff.textContent = q.difficulty || "";
        srcDiff.className   = `el-source-diff ${q.difficulty || ""}`;
    }

    setText("elQuestionNumber", `Question ${answered + 1}`);
    setText("elQuestionText",   q.question);

    const card = document.getElementById("elQuestionCard");
    if (card) {
        card.style.animation = "none";
        void card.offsetWidth;
        card.style.animation = "";
    }

    const optEl = document.getElementById("elOptions");
    optEl.innerHTML = "";
    shuffleArray([...q.answers]).forEach(ans => {
        const btn = document.createElement("button");
        btn.className   = "el-option";
        btn.textContent = ans.text;
        btn.onclick     = () => pickAnswer(btn, ans, q);
        optEl.appendChild(btn);
    });

    updateProgress();
}

// ── Answer picked ─────────────────────────────────────────────────────────────
function pickAnswer(btn, answer, question) {
    if (!gameActive || answering) return;
    answering = true;

    const isCorrect = answer.is_correct === 1;
    const allBtns   = document.querySelectorAll(".el-option");

    allBtns.forEach(b => b.disabled = true);

    if (isCorrect) {
        btn.classList.add("correct");
        score  += pointsForDifficulty(question.difficulty);
        streak += 1;
        correct++;
        if (streak > bestStreak) bestStreak = streak;
        showStreakBonus();
    } else {
        btn.classList.add("wrong");
        lives--;
        streak = 0;

        allBtns.forEach(b => {
            const matchQ = question.answers.find(a => a.is_correct === 1);
            if (matchQ && b.textContent === matchQ.text) {
                b.classList.add("reveal-correct");
            }
        });

        updateHearts();

        if (lives <= 0) {
            answered++;
            updateStats();
            setTimeout(() => endGame(true), 1200);
            return;
        }
    }

    answered++;
    updateStats();

    setTimeout(() => {
        qIndex++;
        answering = false;
        renderQuestion();
    }, 900);
}

function pointsForDifficulty(diff) {
    return diff === "hard" ? 20 : diff === "medium" ? 15 : 10;
}

// ── Streak bonus toast ────────────────────────────────────────────────────────
function showStreakBonus() {
    if (streak < 3) return;
    const toast = document.getElementById("streakToast");
    if (!toast) return;

    const msgs = { 3:"🔥 3 in a row!", 5:"⚡ 5 Streak!", 10:"💥 10 Streak!", 15:"🌟 15 Streak!", 20:"👑 UNSTOPPABLE!" };
    const msg = msgs[streak] || (streak % 5 === 0 ? `🔥 ${streak} Streak!` : null);
    if (!msg) return;

    toast.textContent = msg;
    toast.classList.add("show");
    setTimeout(() => toast.classList.remove("show"), 1800);
}

// ── UI helpers ────────────────────────────────────────────────────────────────
function updateHearts() {
    for (let i = 1; i <= 3; i++) {
        const h = document.getElementById(`heart${i}`);
        if (!h) continue;
        if (i <= lives) h.classList.remove("lost");
        else            h.classList.add("lost");
    }
}

function updateStats() {
    setText("elScore",    score);
    setText("elStreak",   streak);
    setText("elAnswered", answered);
}

function updateProgress() {
    setText("elProgressLabel", `Question ${answered + 1}`);
    setText("elProgressRight", `${correct} correct`);
    const pct  = answered > 0 ? Math.round((correct / answered) * 100) : 0;
    const fill = document.getElementById("elProgressFill");
    if (fill) fill.style.width = `${pct}%`;
}

// ── End Game ──────────────────────────────────────────────────────────────────
function endGame(outOfLives) {
    gameActive = false;

    const icon  = outOfLives ? "💀" : "🏳️";
    const title = outOfLives ? "Game Over!" : "You Quit";
    const sub   = outOfLives
        ? `You used all 3 lives — your final score is ${score}`
        : `You quit after ${answered} questions`;

    setText("elResultIcon",    icon);
    setText("elResultTitle",   title);
    setText("elResultSub",     sub);
    setText("elFinalScore",    score);
    setText("elFinalCorrect",  correct);
    setText("elFinalAnswered", answered);
    setText("elFinalStreak",   bestStreak);

    show("elResults");
    resetFeedbackUI();
    saveResult();
}

// ── Save result — quiz_id explicitly null, correct field names ────────────────
async function saveResult() {
    try {
        const res = await fetch("api/save_quiz_result.php", {
            method:  "POST",
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify({
                quiz_id:         null,          // NULL — no specific quiz for endless
                mode:            "endless_quiz",
                correct_answers: correct,        // matches PHP $input['correct_answers']
                total_questions: answered,       // matches PHP $input['total_questions']
                time_taken:      0,
                time_limit:      0,
                points_earned:   score,          // pre-calculated, overrides server calc
                user_id:         typeof SESSION_USER_ID !== "undefined" ? SESSION_USER_ID : null
            })
        });
        const data = await res.json();
        console.log("Save result:", data);
        if (!data.success) {
            console.error("Failed to save result:", data.message);
        }
    } catch (e) {
        console.error("Save error:", e);
    }
}

function restartEndless() {
    show("elLoading");
    setTimeout(loadQuestions, 300);
}

// ── Generic helpers ───────────────────────────────────────────────────────────
function show(id) {
    ["elLoading","elGame","elResults"].forEach(k => {
        const el = document.getElementById(k);
        if (!el) return;
        el.style.display = k === id ? "flex" : "none";
    });
    if (id === "elGame") document.getElementById("elGame").style.flexDirection = "column";
}

function setText(id, val) {
    const el = document.getElementById(id);
    if (el) el.textContent = val;
}

function shuffleArray(arr) {
    for (let i = arr.length - 1; i > 0; i--) {
        const j = Math.floor(Math.random() * (i + 1));
        [arr[i], arr[j]] = [arr[j], arr[i]];
    }
    return arr;
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

    if (ta)  ta.value = "";
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
                quiz_id:       null,
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
        alert("Network error.");
        if (btn) { btn.textContent = "Send Feedback"; btn.disabled = false; }
    }
}
