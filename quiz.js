// ========================================
// QUIZ SYSTEM - MAIN QUIZ LOGIC
// Handles all quiz modes: timed, ranked, memory match, endless, single player
// ========================================

console.log("Quiz system loaded 🎯");

// ========================================
// GLOBAL QUIZ STATE
// ========================================

let currentMode = '';
let currentQuiz = null;
let currentQuestions = [];
let currentQuestionIndex = 0;
let userAnswers = [];
let quizStartTime = null;
let quizTimer = null;
let score = 0;
let timeLimit = 600; // 10 minutes default
let isQuizActive = false;

// Mode-specific variables
let endlessMode = false;
let memoryMatchMode = false;
let rankedMode = false;
let timedMode = false;
let singlePlayerMode = false;

// Memory match specific
let memoryCards = [];
let flippedCards = [];
let matchedPairs = 0;

// Endless mode specific
let endlessLives = 3;
let endlessStreak = 0;

// ========================================
// INITIALIZATION
// ========================================

document.addEventListener('DOMContentLoaded', () => {
    initializeQuiz();
});

function initializeQuiz() {
    // Get mode from localStorage
    currentMode = localStorage.getItem('selectedMode') || 'single_player';

    // Set mode-specific settings
    setModeSettings();

    // Load quiz selection
    loadQuizSelection();

    // Setup event listeners
    setupEventListeners();
}

function setModeSettings() {
    // Reset all mode flags
    endlessMode = memoryMatchMode = rankedMode = timedMode = singlePlayerMode = false;

    switch (currentMode) {
        case 'timed_quiz':
            timedMode = true;
            timeLimit = 600; // 10 minutes
            break;
        case 'ranked_quiz':
            rankedMode = true;
            timeLimit = 300; // 5 minutes for competitive
            break;
        case 'memory_match':
            memoryMatchMode = true;
            timeLimit = 180; // 3 minutes
            break;
        case 'endless_quiz':
            endlessMode = true;
            timeLimit = 0; // No time limit
            break;
        case 'single_player':
        default:
            singlePlayerMode = true;
            timeLimit = 0; // No time limit
            break;
    }
}

// ========================================
// QUIZ SELECTION
// ========================================

async function loadQuizSelection() {
    try {
        const response = await fetch('api/get_quizzes.php');
        const data = await response.json();

        if (!data.success) {
            showError('Failed to load quizzes');
            return;
        }

        displayQuizSelection(data.data);
    } catch (error) {
        console.error('Error loading quizzes:', error);
        showError('Error loading quizzes');
    }
}

function displayQuizSelection(quizzes) {
    const container = document.getElementById('quizGrid');
    if (!container) return;

    container.innerHTML = '';

    quizzes.forEach(quiz => {
        const quizCard = document.createElement('div');
        quizCard.className = 'quiz-card';
        quizCard.onclick = () => selectQuiz(quiz.id);

        quizCard.innerHTML = `
            <h3>${quiz.title}</h3>
            <p class="category">${quiz.category}</p>
            <p class="difficulty ${quiz.difficulty}">${quiz.difficulty}</p>
            <p class="question-count">${quiz.question_count} questions</p>
        `;

        container.appendChild(quizCard);
    });
}

function selectQuiz(quizId) {
    // Store selected quiz
    localStorage.setItem('selectedQuizId', quizId);

    // Start the quiz
    startQuiz();
}

// ========================================
// QUIZ GAMEPLAY
// ========================================

async function startQuiz() {
    const quizId = localStorage.getItem('selectedQuizId');
    if (!quizId) {
        showError('No quiz selected');
        return;
    }

    try {
        const response = await fetch(`api/get_quiz_questions.php?quiz_id=${quizId}`);
        const data = await response.json();

        if (!data.success) {
            showError('Failed to load quiz questions');
            return;
        }

        currentQuiz = data.quiz;
        currentQuestions = data.questions;

        // Prepare questions based on mode
        prepareQuestionsForMode();

        // Initialize quiz state
        resetQuizState();

        // Start the quiz
        showQuizGame();
        loadQuestion();

        if (timedMode || rankedMode) {
            startTimer();
        }

    } catch (error) {
        console.error('Error starting quiz:', error);
        showError('Error starting quiz');
    }
}

function prepareQuestionsForMode() {
    if (memoryMatchMode) {
        // For memory match, create pairs of terms and definitions
        currentQuestions = createMemoryMatchPairs(currentQuestions);
    } else {
        // Shuffle questions for other modes
        currentQuestions = shuffleArray(currentQuestions);
    }
}

function createMemoryMatchPairs(questions) {
    const pairs = [];

    questions.forEach(question => {
        const correctAnswer = question.answers.find(a => a.is_correct);
        if (correctAnswer) {
            pairs.push({
                type: 'term',
                content: question.question_text,
                pairId: question.id
            });
            pairs.push({
                type: 'definition',
                content: correctAnswer.text,
                pairId: question.id
            });
        }
    });

    return shuffleArray(pairs);
}

function resetQuizState() {
    currentQuestionIndex = 0;
    userAnswers = [];
    score = 0;
    quizStartTime = Date.now();
    isQuizActive = true;
    endlessLives = endlessMode ? 3 : 0;
    endlessStreak = 0;
    matchedPairs = 0;
    flippedCards = [];
}

function showQuizGame() {
    document.getElementById('quizSelection').style.display = 'none';
    document.getElementById('quizGame').style.display = 'block';

    updateQuizHeader();
}

function updateQuizHeader() {
    const titleEl = document.getElementById('quizTitle');
    const counterEl = document.getElementById('questionCounter');
    const scoreEl = document.getElementById('score');

    if (titleEl) titleEl.textContent = currentQuiz.title;
    if (scoreEl) scoreEl.textContent = `Score: ${score}`;

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

    if (memoryMatchMode) {
        loadMemoryMatchQuestion();
    } else {
        loadStandardQuestion();
    }
}

function loadStandardQuestion() {
    const question = currentQuestions[currentQuestionIndex];
    if (!question) {
        endQuiz();
        return;
    }

    const questionEl = document.getElementById('questionText');
    const optionsEl = document.getElementById('options');

    if (questionEl) questionEl.textContent = question.question_text;
    if (optionsEl) {
        optionsEl.innerHTML = '';
        question.answers.forEach((answer, index) => {
            const optionDiv = document.createElement('div');
            optionDiv.className = 'option';
            optionDiv.textContent = answer.text;
            optionDiv.onclick = () => selectAnswer(index);
            optionsEl.appendChild(optionDiv);
        });
    }

    updateQuizHeader();
}

function loadMemoryMatchQuestion() {
    const container = document.getElementById('questionText').parentElement;
    const memoryGrid = document.createElement('div');
    memoryGrid.className = 'memory-grid';
    memoryGrid.id = 'memoryGrid';

    // Clear previous content
    container.innerHTML = '<h3>Memory Match - Match the terms with their definitions</h3>';
    container.appendChild(memoryGrid);

    // Create memory cards
    currentQuestions.forEach((item, index) => {
        const card = document.createElement('div');
        card.className = 'memory-card';
        card.dataset.index = index;
        card.dataset.pairId = item.pairId;
        card.dataset.type = item.type;
        card.onclick = () => flipCard(card);

        const cardContent = document.createElement('div');
        cardContent.className = 'card-content';
        cardContent.textContent = item.content;

        card.appendChild(cardContent);
        memoryGrid.appendChild(card);
    });

    memoryCards = document.querySelectorAll('.memory-card');
}

function flipCard(card) {
    if (card.classList.contains('flipped') || card.classList.contains('matched') || flippedCards.length >= 2) {
        return;
    }

    card.classList.add('flipped');
    flippedCards.push(card);

    if (flippedCards.length === 2) {
        setTimeout(checkMatch, 1000);
    }
}

function checkMatch() {
    const [card1, card2] = flippedCards;

    if (card1.dataset.pairId === card2.dataset.pairId && card1.dataset.type !== card2.dataset.type) {
        // Match found
        card1.classList.add('matched');
        card2.classList.add('matched');
        matchedPairs++;
        score += 10;

        // Check if all pairs are matched
        if (matchedPairs === currentQuestions.length / 2) {
            endQuiz();
        }
    } else {
        // No match
        card1.classList.remove('flipped');
        card2.classList.remove('flipped');

        if (endlessMode) {
            endlessLives--;
            if (endlessLives <= 0) {
                endQuiz();
            }
        }
    }

    flippedCards = [];
    updateQuizHeader();
}

function selectAnswer(answerIndex) {
    const question = currentQuestions[currentQuestionIndex];
    const selectedAnswer = question.answers[answerIndex];

    userAnswers.push({
        questionId: question.id,
        selectedAnswer: answerIndex,
        isCorrect: selectedAnswer.is_correct
    });

    if (selectedAnswer.is_correct) {
        score += 10;
        endlessStreak++;
    } else {
        if (endlessMode) {
            endlessLives--;
            endlessStreak = 0;
            if (endlessLives <= 0) {
                endQuiz();
                return;
            }
        }
    }

    // Move to next question
    currentQuestionIndex++;

    if (currentQuestionIndex >= currentQuestions.length) {
        endQuiz();
    } else {
        loadQuestion();
    }
}

// ========================================
// TIMER FUNCTIONS
// ========================================

function startTimer() {
    let timeLeft = timeLimit;
    updateTimerDisplay(timeLeft);

    quizTimer = setInterval(() => {
        timeLeft--;
        updateTimerDisplay(timeLeft);

        if (timeLeft <= 0) {
            clearInterval(quizTimer);
            endQuiz();
        }
    }, 1000);
}

function updateTimerDisplay(seconds) {
    const timerEl = document.getElementById('timer');
    if (timerEl) {
        const minutes = Math.floor(seconds / 60);
        const secs = seconds % 60;
        timerEl.textContent = `Time: ${minutes}:${secs.toString().padStart(2, '0')}`;
    }
}

// ========================================
// QUIZ ENDING
// ========================================

function endQuiz() {
    isQuizActive = false;
    clearInterval(quizTimer);

    const timeTaken = Math.floor((Date.now() - quizStartTime) / 1000);
    const correctAnswers = userAnswers.filter(a => a.isCorrect).length;
    const totalQuestions = endlessMode ? userAnswers.length : currentQuestions.length;

    // Save result
    saveQuizResult(score, correctAnswers, totalQuestions, timeTaken);

    // Show results
    showQuizResults(score, correctAnswers, totalQuestions, timeTaken);
}

async function saveQuizResult(score, correctAnswers, totalQuestions, timeTaken) {
    try {
        const result = {
            quiz_id: currentQuiz.id,
            mode: currentMode,
            score: score,
            correct_answers: correctAnswers,
            total_questions: totalQuestions,
            time_taken: timeTaken
        };

        // Add user_id if logged in (you can implement user session management)
        // result.user_id = getCurrentUserId();

        const response = await fetch('api/save_quiz_result.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify(result)
        });

        const data = await response.json();
        if (!data.success) {
            console.error('Failed to save quiz result:', data.message);
        }
    } catch (error) {
        console.error('Error saving quiz result:', error);
    }
}

function showQuizResults(finalScore, correctAnswers, totalQuestions, timeTaken) {
    document.getElementById('quizGame').style.display = 'none';
    document.getElementById('quizResults').style.display = 'block';

    // Update result elements
    document.getElementById('finalScore').textContent = finalScore;
    document.getElementById('correctAnswers').textContent = correctAnswers;
    document.getElementById('totalQuestions').textContent = totalQuestions;
    document.getElementById('timeTaken').textContent = formatTime(timeTaken);

    // Mode-specific messages
    const modeTitle = document.getElementById('modeTitle');
    if (modeTitle) {
        let title = 'Quiz Complete!';
        if (endlessMode) {
            title = `Endless Mode - ${endlessLives > 0 ? 'Survived!' : 'Game Over!'}`;
        } else if (memoryMatchMode) {
            title = 'Memory Match Complete!';
        } else if (rankedMode) {
            title = 'Ranked Quiz Complete!';
        }
        modeTitle.textContent = title;
    }
}

function formatTime(seconds) {
    const minutes = Math.floor(seconds / 60);
    const secs = seconds % 60;
    return `${minutes}:${secs.toString().padStart(2, '0')}`;
}

// ========================================
// UTILITY FUNCTIONS
// ========================================

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
    // Quit button
    const quitBtn = document.getElementById('quitBtn');
    if (quitBtn) {
        quitBtn.addEventListener('click', () => {
            if (confirm('Are you sure you want to quit the quiz?')) {
                endQuiz();
            }
        });
    }

    // Navigation buttons
    const prevBtn = document.getElementById('prevBtn');
    const nextBtn = document.getElementById('nextBtn');
    const submitBtn = document.getElementById('submitBtn');

    if (prevBtn) prevBtn.addEventListener('click', () => {
        // Implement previous question logic if needed
    });

    if (nextBtn) nextBtn.addEventListener('click', () => {
        // Implement next question logic if needed
    });

    if (submitBtn) submitBtn.addEventListener('click', () => {
        endQuiz();
    });
}

// ========================================
// MODE SPECIFIC LOGIC
// ========================================

// Additional mode-specific functions can be added here
// For example, special scoring for ranked mode, life system for endless, etc.