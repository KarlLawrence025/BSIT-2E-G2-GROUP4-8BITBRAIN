// ========================================
// ADMIN DASHBOARD - DATABASE CRUD OPERATIONS
// This is the ONLY file that handles admin quiz/user/feedback logic.
// script.js must NOT contain any of this code.
// ========================================

console.log("Admin Dashboard JS loaded 🔧");

const API_BASE = 'api/';

// ========================================
// UTILITY
// ========================================

async function fetchAPI(endpoint, method = 'GET', data = null) {
    const options = { method, headers: { 'Content-Type': 'application/json' } };
    if (data && method !== 'GET') options.body = JSON.stringify(data);
    try {
        const response = await fetch(API_BASE + endpoint, options);
        return await response.json();
    } catch (error) {
        console.error('API Error:', error);
        return { success: false, message: 'Network error: ' + error.message };
    }
}

function showToast(message, type = 'success') {
    const colors = { success: '#39ff14', error: '#ff1744', warning: '#ffd700', info: '#38bdf8' };
    const toast  = document.createElement('div');
    toast.textContent = message;
    toast.style.cssText = `
        position:fixed;bottom:20px;right:20px;padding:15px 20px;
        background:${colors[type]||colors.success};color:#1a0b2e;
        border:2px solid ${colors[type]||colors.success};
        font-family:'Courier New',monospace;font-size:12px;font-weight:bold;
        z-index:9999;border-radius:4px;box-shadow:0 0 15px ${colors[type]||colors.success};`;
    document.body.appendChild(toast);
    setTimeout(() => toast.remove(), 3500);
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
                <span>${a.type === 'user' ? '👤' : '📝'} ${a.name}</span>
                <span style="font-size:10px;opacity:.6;">${new Date(a.created_at).toLocaleString()}</span>
            </div>`).join('');
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
        </tr>`).join('');
}

let currentEditingUserId = null;

function openUserModal(userId = null) {
    document.getElementById('user-form').reset();
    document.getElementById('modal-title').textContent = userId ? 'Edit User' : 'New User';
    currentEditingUserId = userId;
    document.getElementById('user-modal').classList.remove('hidden');
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

document.getElementById('user-form')?.addEventListener('submit', async (e) => {
    e.preventDefault();
    const userData = {
        username:     document.getElementById('user-username').value,
        email:        document.getElementById('user-email').value,
        account_type: document.getElementById('user-role').value,
        fullname:     document.getElementById('user-username').value,
        age: 18, password: 'password123'
    };
    let result;
    if (currentEditingUserId) { userData.id = currentEditingUserId; result = await fetchAPI('update_user.php', 'POST', userData); }
    else { result = await fetchAPI('create_user.php', 'POST', userData); }

    if (result.success) { showToast(result.message, 'success'); closeModal('user-modal'); loadUsers(); loadDashboardStats(); }
    else { showToast(result.message, 'error'); }
});

function editUser(userId)  { openUserModal(userId); }

async function deleteUser(userId) {
    if (!confirm('Delete this user?')) return;
    const result = await fetchAPI('delete_user.php', 'POST', { id: userId });
    if (result.success) { showToast(result.message, 'success'); loadUsers(); loadDashboardStats(); }
    else { showToast(result.message, 'error'); }
}

// ========================================
// QUIZ CREATION - Question Builder
// ========================================

let questionCount = 0;

document.getElementById('add-question-btn')?.addEventListener('click', () => {
    questionCount++;
    const block = document.createElement('div');
    block.className = 'question-block';
    block.id        = `question-${questionCount}`;
    block.innerHTML = `
        <h4 class="question-number">Question ${questionCount}</h4>
        <div class="form-group"><label>Question Text *</label>
            <input type="text" class="question-text" required placeholder="Enter your question here"></div>
        <div class="form-group"><label>Option A *</label>
            <input type="text" class="option-input" required placeholder="Option A"></div>
        <div class="form-group"><label>Option B *</label>
            <input type="text" class="option-input" required placeholder="Option B"></div>
        <div class="form-group"><label>Option C *</label>
            <input type="text" class="option-input" required placeholder="Option C"></div>
        <div class="form-group"><label>Option D *</label>
            <input type="text" class="option-input" required placeholder="Option D"></div>
        <div class="form-group"><label>Correct Answer *</label>
            <select class="correct-answer" required>
                <option value="">-- Select correct answer --</option>
                <option value="0">Option A</option>
                <option value="1">Option B</option>
                <option value="2">Option C</option>
                <option value="3">Option D</option>
            </select></div>
        <button type="button" class="btn-admin btn-danger" onclick="removeQuestion(${questionCount})">🗑️ Remove</button>
        <hr>`;
    document.getElementById('questions-container').appendChild(block);
    renumberQuestions();
    block.scrollIntoView({ behavior: 'smooth', block: 'start' });
});

function removeQuestion(id) {
    document.getElementById(`question-${id}`)?.remove();
    renumberQuestions();
}

function renumberQuestions() {
    document.querySelectorAll('.question-block').forEach((block, i) => {
        const label = block.querySelector('.question-number');
        if (label) label.textContent = `Question ${i + 1}`;
    });
}

// ========================================
// QUIZ FORM SUBMIT → DATABASE
// ========================================

document.getElementById('quiz-form')?.addEventListener('submit', async (e) => {
    e.preventDefault();

    const submitBtn = e.target.querySelector('[type="submit"]');
    submitBtn.textContent = '⏳ Saving to DB...';
    submitBtn.disabled = true;

    // FIX: Define variables by grabbing values from the DOM
    const title      = document.getElementById('quiz-title').value.trim();
    const category   = document.getElementById('quiz-category').value.trim();
    const difficulty = document.getElementById('quiz-difficulty').value;
    const mode       = document.getElementById('quiz-mode').value;
    const refUrl     = document.getElementById('quiz-reference')?.value.trim() || null;

    const questionBlocks = document.querySelectorAll('.question-block');
    if (questionBlocks.length === 0) {
        showToast('Please add at least one question.', 'warning');
        submitBtn.textContent = 'Create Quiz';
        submitBtn.disabled = false;
        return;
    }

    const questions = [];
    let valid = true;

    questionBlocks.forEach((block, index) => {
        const qText        = block.querySelector('.question-text').value.trim();
        const optionInputs = block.querySelectorAll('.option-input');
        const correctSel   = block.querySelector('.correct-answer');
        const options      = Array.from(optionInputs).map(i => ({ text: i.value.trim() }));
        const correctIndex = correctSel.value !== '' ? parseInt(correctSel.value) : -1;

        if (!qText) { 
            showToast(`Question ${index+1}: text is empty.`, 'warning'); 
            valid = false; return; 
        }
        if (options.some(o => !o.text)) { 
            showToast(`Question ${index+1}: all options are required.`, 'warning'); 
            valid = false; return; 
        }
        if (correctIndex === -1) { 
            showToast(`Question ${index+1}: select the correct answer.`, 'warning'); 
            valid = false; return; 
        }

        questions.push({ text: qText, options, correctAnswer: correctIndex });
    });

    if (!valid) { 
        submitBtn.textContent = 'Create Quiz'; 
        submitBtn.disabled = false; 
        return; 
    }

    // The Fetch Call now uses the defined variables
    const result = await fetchAPI('create_quiz.php', 'POST', {
        title, 
        category, 
        difficulty, 
        mode,
        reference_url: refUrl,
        questions
    });

    if (result.success) {
        showToast(`✅ "${title}" successfully saved to Database!`, 'success');
        e.target.reset();
        document.getElementById('questions-container').innerHTML = '';
        questionCount = 0;
        
        await loadQuizzes(); 
        await loadDashboardStats();
        switchTab('quizzes');
    } else {
        showToast('❌ Database Error: ' + result.message, 'error');
        console.error("Quiz Save Failed:", result.message);
    }

    submitBtn.textContent = 'Create Quiz';
    submitBtn.disabled = false;
});

// ========================================
// QUIZ TABLE - loads from DATABASE
// ========================================

async function loadQuizzes() {
    const result = await fetchAPI('get_quizzes_all.php');
    const tbody  = document.getElementById('quizzes-tbody');
    if (!result.success || !result.data.length) {
        tbody.innerHTML = '<tr><td colspan="6" class="empty-state">No quizzes in database yet.</td></tr>';
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
        </tr>`).join('');
}

async function deleteQuiz(quizId) {
    if (!confirm('Delete this quiz and all its questions?')) return;
    const result = await fetchAPI('delete_quiz.php', 'POST', { id: quizId });
    if (result.success) { showToast(result.message, 'success'); loadQuizzes(); loadDashboardStats(); }
    else { showToast(result.message, 'error'); }
}

function editQuiz(quizId) { showToast('Quiz editing coming soon!', 'info'); }

// ========================================
// IMPORT
// ========================================

document.getElementById('import-btn')?.addEventListener('click', () => {
    const file = document.getElementById('quiz-file').files[0];
    if (!file) { showToast('Select a file first', 'warning'); return; }
    const reader = new FileReader();
    reader.onload = async (ev) => {
        try {
            const data    = JSON.parse(ev.target.result);
            const quizzes = Array.isArray(data) ? data : [data];
            let imported  = 0;
            for (const q of quizzes) {
                const result = await fetchAPI('create_quiz.php', 'POST', {
                    title: q.title || 'Imported Quiz', category: q.category || 'General',
                    difficulty: q.difficulty || 'medium', mode: q.mode || 'single_player',
                    questions: q.questions || []
                });
                if (result.success) imported++;
            }
            loadQuizzes(); loadDashboardStats();
            document.getElementById('quiz-file').value = '';
            showToast(`Imported ${imported}/${quizzes.length} quiz(zes)!`, 'success');
        } catch (err) { showToast('Error parsing file: ' + err.message, 'error'); }
    };
    reader.readAsText(file);
});

// ========================================
// FEEDBACK
// ========================================

async function loadFeedback() {
    const result = await fetchAPI('get_feedback.php');
    const list   = document.getElementById('feedback-list');
    if (!result.success || !result.data.length) { list.innerHTML = '<p class="empty-state">No feedback yet.</p>'; return; }

    const typeEmoji = { general:'💬', suggestion:'💡', bug:'🐛', complaint:'⚠️' };
    const modeLabel = { single_player:'Single Player', timed_quiz:'Timed Quiz', ranked_quiz:'Ranked Quiz', memory_match:'Memory Match', endless_quiz:'Endless Quiz' };

    list.innerHTML = result.data.map(f => {
        const stars = f.rating ? '★'.repeat(f.rating) + '☆'.repeat(5-f.rating) : 'No rating';
        return `
        <div class="feedback-item ${f.status}">
            <div class="feedback-meta-top">
                <span class="feedback-type-badge">${typeEmoji[f.feedback_type]||'💬'} ${f.feedback_type||'general'}</span>
                <span class="feedback-status-badge ${f.status}">${f.status.toUpperCase()}</span>
            </div>
            <div class="feedback-labels">
                <span>👤 <strong>${f.user_name||'Anonymous'}</strong></span>
                <span>📝 ${f.quiz_title||'No quiz linked'}</span>
                <span>🗂️ ${f.quiz_category||'—'}</span>
                <span>🎮 ${modeLabel[f.quiz_mode]||f.quiz_mode||'—'}</span>
                <span>⭐ ${stars}</span>
            </div>
            <p class="feedback-body">"${f.feedback_text}"</p>
            <div class="feedback-footer">
                <span style="font-size:11px;opacity:.5;">🕒 ${new Date(f.created_at).toLocaleString()}</span>
                <div style="display:flex;gap:8px;">
                    ${f.status==='pending' ? `<button class="btn-action btn-edit" onclick="resolveFeedback(${f.id})">✅ Resolve</button>` : ''}
                    <button class="btn-action btn-delete" onclick="deleteFeedbackItem(${f.id})">🗑️ Delete</button>
                </div>
            </div>
        </div>`; }).join('');
}

async function resolveFeedback(id) {
    const result = await fetchAPI('resolve_feedback.php', 'POST', { id });
    if (result.success) { showToast('Resolved', 'success'); loadFeedback(); }
    else { showToast(result.message, 'error'); }
}

async function deleteFeedbackItem(id) {
    if (!confirm('Delete this feedback?')) return;
    const result = await fetchAPI('delete_feedback.php', 'POST', { id });
    if (result.success) { showToast('Deleted', 'success'); loadFeedback(); loadDashboardStats(); }
    else { showToast(result.message, 'error'); }
}

// ========================================
// REFERENCES
// ========================================

async function loadReferences() {
    const result = await fetchAPI('get_references.php');
    const tbody  = document.getElementById('references-tbody');
    if (!result.success || !result.data.length) { tbody.innerHTML = '<tr><td colspan="5" class="empty-state">No references found</td></tr>'; return; }
    tbody.innerHTML = result.data.map(ref => `
        <tr>
            <td>${ref.quiz_id}</td>
            <td>${ref.question_text}</td>
            <td>${ref.reference_url ? `<a href="${ref.reference_url}" target="_blank" style="color:#38bdf8">${ref.reference_url}</a>` : ref.reference_text||'N/A'}</td>
            <td><span class="badge">${ref.reference_type}</span></td>
            <td><button class="btn-action btn-delete" onclick="deleteReference(${ref.id})">🗑️</button></td>
        </tr>`).join('');
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
    document.getElementById(tabName + '-tab')?.classList.add('active');
    document.querySelector(`[data-tab="${tabName}"]`)?.classList.add('active');
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
});

// ========================================
// MODALS
// ========================================

function closeModal(modalId) { document.getElementById(modalId)?.classList.add('hidden'); }

document.querySelectorAll('.modal-close, .modal-close-btn').forEach(btn => {
    btn.addEventListener('click', function() { this.closest('.modal')?.classList.add('hidden'); });
});

// ========================================
// SEARCH
// ========================================

document.getElementById('user-search')?.addEventListener('input', function() {
    const q = this.value.toLowerCase();
    document.querySelectorAll('#users-tbody tr').forEach(row => {
        row.style.display = row.textContent.toLowerCase().includes(q) ? '' : 'none';
    });
});

document.getElementById('quiz-search')?.addEventListener('input', function() {
    const q = this.value.toLowerCase();
    document.querySelectorAll('#quizzes-tbody tr').forEach(row => {
        row.style.display = row.textContent.toLowerCase().includes(q) ? '' : 'none';
    });
});

// ========================================
// INIT — loads everything from DATABASE
// ========================================

window.addEventListener('DOMContentLoaded', () => {
    loadDashboardStats();
    loadUsers();
    loadQuizzes();      
    loadReferences();

    // Toast animation
    const style = document.createElement('style');
    style.textContent = `@keyframes toastIn { from{transform:translateX(400px);opacity:0} to{transform:translateX(0);opacity:1} }`;
    document.head.appendChild(style);

    console.log('✨ Admin Dashboard Ready — all data from database');
});