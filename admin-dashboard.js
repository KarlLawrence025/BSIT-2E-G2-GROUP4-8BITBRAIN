// ========================================
// ADMIN DASHBOARD - DATABASE CRUD OPERATIONS
// ========================================

console.log("Admin Dashboard JS loaded 🔧");

const currentUser = JSON.parse(sessionStorage.getItem('currentUser') || '{}');
if (currentUser.accountType !== 'admin') {
    console.warn('Non-admin user attempted to access admin dashboard');
}

const API_BASE = 'api/';

// ========================================
// UTILITY FUNCTIONS
// ========================================

async function fetchAPI(endpoint, method = 'GET', data = null) {
    const options = {
        method,
        headers: { 'Content-Type': 'application/json' }
    };
    if (data && method !== 'GET') {
        options.body = JSON.stringify(data);
    }

    try {
        const response = await fetch(API_BASE + endpoint, options);
        const result   = await response.json();
        return result;
    } catch (error) {
        console.error('❌ API Error:', error);
        return { success: false, message: 'Network error: ' + error.message };
    }
}

function showMessage(message, type = 'success') {
    // Use toast if available, fall back to alert
    if (typeof showToast === 'function') {
        showToast(message, type);
    } else {
        alert(message);
    }
}

function showToast(message, type = 'success') {
    const colors = { success: '#39ff14', error: '#ff1744', warning: '#ffd700', info: '#38bdf8' };
    const toast  = document.createElement('div');
    toast.textContent = message;
    toast.style.cssText = `
        position:fixed; bottom:20px; right:20px; padding:15px 20px;
        background:${colors[type] || colors.success}; color:#1a0b2e;
        border:2px solid ${colors[type] || colors.success};
        font-family:'Courier New',monospace; font-size:12px; font-weight:bold;
        z-index:9999; border-radius:4px;
        box-shadow:0 0 15px ${colors[type] || colors.success};
        animation:toastIn .3s ease;
    `;
    document.body.appendChild(toast);
    setTimeout(() => { toast.style.opacity = '0'; setTimeout(() => toast.remove(), 300); }, 3500);
}

// ========================================
// DASHBOARD STATS
// ========================================

async function loadDashboardStats() {
    const result = await fetchAPI('get_stats.php');
    if (!result.success) return;

    document.getElementById('stat-quizzes').textContent  = result.data.total_quizzes;
    document.getElementById('stat-users').textContent    = result.data.total_users;
    document.getElementById('stat-feedback').textContent = result.data.pending_feedback;

    const activityList = document.getElementById('activity-list');
    if (!result.data.recent_activity.length) {
        activityList.innerHTML = '<p class="empty-state">No activity yet</p>';
    } else {
        activityList.innerHTML = result.data.recent_activity.map(a => `
            <div class="activity-item">
                <span>${a.type === 'user' ? '👤' : '📝'}</span>
                <span>${a.name}</span>
                <span>${new Date(a.created_at).toLocaleString()}</span>
            </div>
        `).join('');
    }
}

// ========================================
// USER MANAGEMENT
// ========================================

async function loadUsers() {
    const result = await fetchAPI('get_users.php');
    const tbody  = document.getElementById('users-tbody');

    if (!result.success || !result.data.length) {
        tbody.innerHTML = '<tr><td colspan="5" class="empty-state">No users found</td></tr>';
        return;
    }

    tbody.innerHTML = result.data.map(user => `
        <tr data-id="${user.id}">
            <td>${user.id}</td>
            <td>${user.username}</td>
            <td>${user.email}</td>
            <td><span class="badge ${user.account_type}">${user.account_type}</span></td>
            <td>
                <button class="btn-action btn-edit"   onclick="editUser(${user.id})">✏️ Edit</button>
                <button class="btn-action btn-delete" onclick="deleteUser(${user.id})">🗑️ Delete</button>
            </td>
        </tr>
    `).join('');
}

let currentEditingUserId = null;

function openUserModal(userId = null) {
    const modal = document.getElementById('user-modal');
    document.getElementById('user-form').reset();
    document.getElementById('modal-title').textContent = userId ? 'Edit User' : 'New User';
    currentEditingUserId = userId;
    modal.classList.remove('hidden');
    if (userId) loadUserData(userId);
}

async function loadUserData(userId) {
    const result = await fetchAPI('get_users.php');
    if (!result.success) return;
    const user = result.data.find(u => u.id == userId);
    if (!user) return;
    document.getElementById('user-id').value       = user.id;
    document.getElementById('user-username').value = user.username;
    document.getElementById('user-email').value    = user.email;
    document.getElementById('user-role').value     = user.account_type;
}

async function saveUser(event) {
    event.preventDefault();
    const userData = {
        username:     document.getElementById('user-username').value,
        email:        document.getElementById('user-email').value,
        account_type: document.getElementById('user-role').value,
        fullname:     document.getElementById('user-username').value,
        age:          18,
        password:     'password123'
    };

    let result;
    if (currentEditingUserId) {
        userData.id = currentEditingUserId;
        result = await fetchAPI('update_user.php', 'POST', userData);
    } else {
        result = await fetchAPI('create_user.php', 'POST', userData);
    }

    if (result.success) {
        showToast(result.message, 'success');
        closeModal('user-modal');
        loadUsers();
        loadDashboardStats();
    } else {
        showToast(result.message, 'error');
    }
}

function editUser(userId)  { openUserModal(userId); }

async function deleteUser(userId) {
    if (!confirm('Are you sure you want to delete this user?')) return;
    const result = await fetchAPI('delete_user.php', 'POST', { id: userId });
    if (result.success) {
        showToast(result.message, 'success');
        loadUsers();
        loadDashboardStats();
    } else {
        showToast(result.message, 'error');
    }
}

// ========================================
// QUIZ MANAGEMENT - QUESTION BUILDER
// ========================================

let questionCount = 0;

document.getElementById('add-question-btn')?.addEventListener('click', () => {
    questionCount++;
    const container = document.getElementById('questions-container');
    const block     = document.createElement('div');
    block.className = 'question-block';
    block.id        = `question-${questionCount}`;

    block.innerHTML = `
        <h4 class="question-number">Question ${questionCount}</h4>
        <div class="form-group">
            <label>Question Text *</label>
            <input type="text" class="question-text" required placeholder="Enter your question here">
        </div>
        <div class="form-group">
            <label>Option A *</label>
            <input type="text" class="option-input" required placeholder="Option A">
        </div>
        <div class="form-group">
            <label>Option B *</label>
            <input type="text" class="option-input" required placeholder="Option B">
        </div>
        <div class="form-group">
            <label>Option C *</label>
            <input type="text" class="option-input" required placeholder="Option C">
        </div>
        <div class="form-group">
            <label>Option D *</label>
            <input type="text" class="option-input" required placeholder="Option D">
        </div>
        <div class="form-group">
            <label>Correct Answer *</label>
            <select class="correct-answer" required>
                <option value="">-- Select correct answer --</option>
                <option value="0">Option A</option>
                <option value="1">Option B</option>
                <option value="2">Option C</option>
                <option value="3">Option D</option>
            </select>
        </div>
        <button type="button" class="btn-admin btn-danger" onclick="removeQuestion(${questionCount})">
            🗑️ Remove Question
        </button>
        <hr>
    `;
    container.appendChild(block);
    renumberQuestions();

    // Scroll to new question
    block.scrollIntoView({ behavior: 'smooth', block: 'start' });
});

function removeQuestion(id) {
    const block = document.getElementById(`question-${id}`);
    if (block) { block.remove(); renumberQuestions(); }
}

function renumberQuestions() {
    document.querySelectorAll('.question-block').forEach((block, i) => {
        const label = block.querySelector('.question-number');
        if (label) label.textContent = `Question ${i + 1}`;
    });
}

// ========================================
// QUIZ FORM SUBMISSION → DATABASE
// ========================================

document.getElementById('quiz-form')?.addEventListener('submit', async (e) => {
    e.preventDefault();

    const submitBtn = e.target.querySelector('[type="submit"]');
    submitBtn.textContent = '⏳ Saving...';
    submitBtn.disabled    = true;

    // ── Collect quiz meta ────────────────────────────────────────────────────
    const title      = document.getElementById('quiz-title').value.trim();
    const category   = document.getElementById('quiz-category').value.trim();
    const difficulty = document.getElementById('quiz-difficulty').value;
    const mode       = document.getElementById('quiz-mode').value;
    const refUrl     = document.getElementById('quiz-reference')?.value.trim() || null;

    // ── Collect questions ────────────────────────────────────────────────────
    const questionBlocks = document.querySelectorAll('.question-block');
    const questions      = [];
    let   valid          = true;

    if (questionBlocks.length === 0) {
        showToast('Please add at least one question.', 'warning');
        submitBtn.textContent = 'Create Quiz';
        submitBtn.disabled    = false;
        return;
    }

    questionBlocks.forEach((block, index) => {
        const qText        = block.querySelector('.question-text').value.trim();
        const optionInputs = block.querySelectorAll('.option-input');
        const correctSel   = block.querySelector('.correct-answer');
        const options      = Array.from(optionInputs).map(i => ({ text: i.value.trim() }));
        const correctIndex = correctSel.value !== '' ? parseInt(correctSel.value) : -1;

        // Validate
        if (!qText) {
            showToast(`Question ${index + 1}: question text is empty.`, 'warning');
            valid = false; return;
        }
        if (options.some(o => !o.text)) {
            showToast(`Question ${index + 1}: all four options are required.`, 'warning');
            valid = false; return;
        }
        if (correctIndex === -1) {
            showToast(`Question ${index + 1}: please select the correct answer.`, 'warning');
            valid = false; return;
        }

        questions.push({ text: qText, options, correctAnswer: correctIndex });
    });

    if (!valid) {
        submitBtn.textContent = 'Create Quiz';
        submitBtn.disabled    = false;
        return;
    }

    // ── Send to API ──────────────────────────────────────────────────────────
    const payload = { title, category, difficulty, mode, reference_url: refUrl, questions };

    const result = await fetchAPI('create_quiz.php', 'POST', payload);

    if (result.success) {
        showToast(`✅ "${title}" saved — ${result.questions} question(s) added!`, 'success');

        // Reset form and question builder
        e.target.reset();
        document.getElementById('questions-container').innerHTML = '';
        questionCount = 0;

        // Refresh quiz table and stats
        loadQuizzes();
        loadDashboardStats();

        // Switch to Quizzes tab so admin can see it immediately
        switchTab('quizzes');

    } else {
        showToast('❌ ' + result.message, 'error');
    }

    submitBtn.textContent = 'Create Quiz';
    submitBtn.disabled    = false;
});

// ========================================
// QUIZ TABLE
// ========================================

async function loadQuizzes() {
    const result = await fetchAPI('get_quizzes_all.php');
    const tbody  = document.getElementById('quizzes-tbody');

    if (!result.success || !result.data.length) {
        tbody.innerHTML = '<tr><td colspan="6" class="empty-state">No quizzes found</td></tr>';
        return;
    }

    tbody.innerHTML = result.data.map(quiz => `
        <tr>
            <td>${quiz.id}</td>
            <td>${quiz.title}</td>
            <td>${quiz.category}</td>
            <td><span class="badge ${quiz.difficulty}">${quiz.difficulty}</span></td>
            <td>${quiz.question_count || 0}</td>
            <td>
                <button class="btn-action btn-edit"   onclick="editQuiz(${quiz.id})">✏️ Edit</button>
                <button class="btn-action btn-delete" onclick="deleteQuiz(${quiz.id})">🗑️ Delete</button>
            </td>
        </tr>
    `).join('');
}

async function deleteQuiz(quizId) {
    if (!confirm('Delete this quiz and all its questions?')) return;
    const result = await fetchAPI('delete_quiz.php', 'POST', { id: quizId });
    if (result.success) {
        showToast(result.message, 'success');
        loadQuizzes();
        loadDashboardStats();
    } else {
        showToast(result.message, 'error');
    }
}

function editQuiz(quizId) {
    showToast('Quiz editing coming soon!', 'info');
}

// ========================================
// FEEDBACK MANAGEMENT
// ========================================

async function loadFeedback() {
    const result = await fetchAPI('get_feedback.php');
    const list   = document.getElementById('feedback-list');

    if (!result.success || !result.data.length) {
        list.innerHTML = '<p class="empty-state">No feedback found</p>';
        return;
    }

    list.innerHTML = result.data.map(f => `
        <div class="feedback-item ${f.status}">
            <div class="feedback-header">
                <span>👤 ${f.user_name || 'Anonymous'}</span>
                <span>📝 ${f.quiz_title || 'General'}</span>
                <span>${'⭐'.repeat(f.rating || 0)}</span>
            </div>
            <p class="feedback-text">${f.feedback_text}</p>
            <div class="feedback-footer">
                <span>${new Date(f.created_at).toLocaleDateString()}</span>
                <span class="badge ${f.status}">${f.status}</span>
            </div>
        </div>
    `).join('');
}

// ========================================
// REFERENCES
// ========================================

async function loadReferences() {
    const result = await fetchAPI('get_references.php');
    const tbody  = document.getElementById('references-tbody');

    if (!result.success || !result.data.length) {
        tbody.innerHTML = '<tr><td colspan="5" class="empty-state">No references found</td></tr>';
        return;
    }

    tbody.innerHTML = result.data.map(ref => `
        <tr>
            <td>${ref.quiz_id}</td>
            <td>${ref.question_text}</td>
            <td>${ref.reference_url
                ? `<a href="${ref.reference_url}" target="_blank" style="color:#38bdf8">${ref.reference_url}</a>`
                : ref.reference_text || 'N/A'}</td>
            <td><span class="badge">${ref.reference_type}</span></td>
            <td><button class="btn-action btn-delete" onclick="deleteReference(${ref.id})">🗑️</button></td>
        </tr>
    `).join('');
}

async function deleteReference(refId) {
    if (!confirm('Delete this reference?')) return;
    showToast('Delete reference API not yet implemented', 'warning');
}

// ========================================
// TAB NAVIGATION
// ========================================

function switchTab(tabName) {
    document.querySelectorAll('.tab-btn').forEach(b => b.classList.remove('active'));
    document.querySelectorAll('.tab-content').forEach(c => c.classList.remove('active'));

    const tab = document.getElementById(tabName + '-tab');
    const btn = document.querySelector(`[data-tab="${tabName}"]`);
    if (tab) tab.classList.add('active');
    if (btn) btn.classList.add('active');
}

document.querySelectorAll('.tab-btn').forEach(btn => {
    btn.addEventListener('click', () => {
        const tab = btn.getAttribute('data-tab');
        switchTab(tab);

        switch (tab) {
            case 'dashboard':  loadDashboardStats(); break;
            case 'users':      loadUsers();          break;
            case 'quizzes':    loadQuizzes();        break;
            case 'feedback':   loadFeedback();       break;
            case 'references': loadReferences();     break;
        }
    });
});

// ========================================
// ACTION SIDEBAR
// ========================================

document.getElementById('create-btn')?.addEventListener('click', () => {
    const active = document.querySelector('.tab-btn.active')?.getAttribute('data-tab');
    if (active === 'users') openUserModal();
    if (active === 'add-quiz') document.getElementById('quiz-form')?.reset();
});

// ========================================
// MODALS
// ========================================

function closeModal(modalId) {
    document.getElementById(modalId)?.classList.add('hidden');
}

document.querySelectorAll('.modal-close, .modal-close-btn').forEach(btn => {
    btn.addEventListener('click', function () {
        this.closest('.modal')?.classList.add('hidden');
    });
});

// ========================================
// FORM SUBMISSIONS
// ========================================

document.getElementById('user-form')?.addEventListener('submit', saveUser);

// ========================================
// SEARCH
// ========================================

document.getElementById('user-search')?.addEventListener('input', function () {
    const q = this.value.toLowerCase();
    document.querySelectorAll('#users-tbody tr').forEach(row => {
        row.style.display = row.textContent.toLowerCase().includes(q) ? '' : 'none';
    });
});

document.getElementById('quiz-search')?.addEventListener('input', function () {
    const q = this.value.toLowerCase();
    document.querySelectorAll('#quizzes-tbody tr').forEach(row => {
        row.style.display = row.textContent.toLowerCase().includes(q) ? '' : 'none';
    });
});

// ========================================
// IMPORT
// ========================================

document.getElementById('import-btn')?.addEventListener('click', () => {
    const file = document.getElementById('quiz-file').files[0];
    if (!file) { showToast('Select a file first', 'warning'); return; }

    const reader = new FileReader();
    reader.onload = async (ev) => {
        try {
            const data   = JSON.parse(ev.target.result);
            const quizzes = Array.isArray(data) ? data : [data];
            let imported = 0;

            for (const q of quizzes) {
                const payload = {
                    title:      q.title      || 'Imported Quiz',
                    category:   q.category   || 'General',
                    difficulty: q.difficulty || 'medium',
                    mode:       q.mode       || 'single_player',
                    questions:  q.questions  || []
                };
                const result = await fetchAPI('create_quiz.php', 'POST', payload);
                if (result.success) imported++;
            }

            loadQuizzes();
            loadDashboardStats();
            document.getElementById('quiz-file').value = '';
            showToast(`Imported ${imported} of ${quizzes.length} quiz(zes)!`, 'success');
        } catch (err) {
            showToast('Error parsing file: ' + err.message, 'error');
        }
    };
    reader.readAsText(file);
});

// ========================================
// INIT
// ========================================

window.addEventListener('DOMContentLoaded', () => {
    loadDashboardStats();
    loadUsers();
    loadReferences();

    // Toast animation style
    const style = document.createElement('style');
    style.textContent = `
        @keyframes toastIn {
            from { transform: translateX(400px); opacity: 0; }
            to   { transform: translateX(0);     opacity: 1; }
        }
    `;
    document.head.appendChild(style);

    console.log('✨ 8BitBrain Admin Dashboard Ready!');
});
