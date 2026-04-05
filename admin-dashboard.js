// ============================================================
// ADMIN DASHBOARD — FULL CRUD
// ============================================================

console.log("Admin Dashboard JS loaded 🔧");

const API_BASE = 'api/';

// ============================================================
// UTILITY
// ============================================================

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
        z-index:9999;border-radius:4px;
        box-shadow:0 0 15px ${colors[type]||colors.success};
        animation:toastIn .3s ease;`;
    document.body.appendChild(toast);
    setTimeout(() => toast.remove(), 3500);
}

// ============================================================
// DASHBOARD STATS
// ============================================================

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

// ============================================================
// USER MANAGEMENT
// ============================================================

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
                <button class="action-btn-small edit"   onclick="editUser(${user.id})">✏️ Edit</button>
                <button class="action-btn-small delete" onclick="deleteUser(${user.id})">🗑️ Delete</button>
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
    if (currentEditingUserId) { userData.id = currentEditingUserId; result = await fetchAPI('update_user.php','POST',userData); }
    else { result = await fetchAPI('create_user.php','POST',userData); }
    if (result.success) { showToast(result.message,'success'); closeModal('user-modal'); loadUsers(); loadDashboardStats(); }
    else { showToast(result.message,'error'); }
});

function editUser(userId)  { openUserModal(userId); }

async function deleteUser(userId) {
    if (!confirm('Delete this user?')) return;
    const result = await fetchAPI('delete_user.php','POST',{id:userId});
    if (result.success) { showToast(result.message,'success'); loadUsers(); loadDashboardStats(); }
    else { showToast(result.message,'error'); }
}

// ============================================================
// QUIZ CREATION — Question Builder (shared by Create + Edit)
// ============================================================

let questionCount = 0;

function buildQuestionBlock(num, qText = '', options = ['','','',''], correctIndex = -1) {
    questionCount++;
    const id    = questionCount;
    const block = document.createElement('div');
    block.className = 'question-block';
    block.id        = `question-${id}`;

    const optLabels = ['A','B','C','D'];
    const optHtml   = options.map((opt, i) => `
        <div class="form-group">
            <label>Option ${optLabels[i]} *</label>
            <input type="text" class="option-input" required placeholder="Option ${optLabels[i]}" value="${escHtml(opt)}">
        </div>`).join('');

    const selectOpts = optLabels.map((l, i) =>
        `<option value="${i}" ${correctIndex === i ? 'selected' : ''}>Option ${l}</option>`
    ).join('');

    block.innerHTML = `
        <h4 class="question-number">Question ${num}</h4>
        <div class="form-group">
            <label>Question Text *</label>
            <input type="text" class="question-text" required
                   placeholder="Enter your question" value="${escHtml(qText)}">
        </div>
        ${optHtml}
        <div class="form-group">
            <label>Correct Answer *</label>
            <select class="correct-answer" required>
                <option value="">-- Select correct answer --</option>
                ${selectOpts}
            </select>
        </div>
        <button type="button" class="btn-admin btn-danger"
                onclick="removeQuestion(${id})">🗑️ Remove Question</button>
        <hr>`;
    return block;
}

function escHtml(str) {
    const d = document.createElement('div');
    d.appendChild(document.createTextNode(str || ''));
    return d.innerHTML;
}

function addBlankQuestion() {
    const container = document.getElementById('questions-container');
    const num       = container.querySelectorAll('.question-block').length + 1;
    container.appendChild(buildQuestionBlock(num));
    renumberQuestions();
    container.lastElementChild?.scrollIntoView({ behavior:'smooth', block:'start' });
}

document.getElementById('add-question-btn')?.addEventListener('click', addBlankQuestion);

function removeQuestion(id) {
    document.getElementById(`question-${id}`)?.remove();
    renumberQuestions();
}

function renumberQuestions() {
    document.querySelectorAll('#questions-container .question-block').forEach((block, i) => {
        const label = block.querySelector('.question-number');
        if (label) label.textContent = `Question ${i + 1}`;
    });
}

function collectQuestions(containerId = 'questions-container') {
    const blocks   = document.querySelectorAll(`#${containerId} .question-block`);
    const questions = [];
    let valid = true;

    blocks.forEach((block, index) => {
        const qText       = block.querySelector('.question-text').value.trim();
        const optInputs   = block.querySelectorAll('.option-input');
        const correctSel  = block.querySelector('.correct-answer');
        const options     = Array.from(optInputs).map(i => ({ text: i.value.trim() }));
        const correctIndex= correctSel.value !== '' ? parseInt(correctSel.value) : -1;

        if (!qText)                     { showToast(`Q${index+1}: question text is empty.`,'warning');        valid=false; return; }
        if (options.some(o => !o.text)) { showToast(`Q${index+1}: all four options are required.`,'warning'); valid=false; return; }
        if (correctIndex === -1)        { showToast(`Q${index+1}: select the correct answer.`,'warning');     valid=false; return; }

        questions.push({ text: qText, options, correctAnswer: correctIndex });
    });

    return valid ? questions : null;
}

// ============================================================
// QUIZ CREATION
// ============================================================

document.getElementById('quiz-form')?.addEventListener('submit', async (e) => {
    e.preventDefault();
    const submitBtn = e.target.querySelector('[type="submit"]');
    submitBtn.textContent = '⏳ Saving...';
    submitBtn.disabled    = true;

    const title      = document.getElementById('quiz-title').value.trim();
    const category   = document.getElementById('quiz-category').value.trim();
    const difficulty = document.getElementById('quiz-difficulty').value;
    const mode       = document.getElementById('quiz-mode').value;
    const refUrl     = document.getElementById('quiz-reference-url')?.value.trim()  || '';
    const refText    = document.getElementById('quiz-reference-text')?.value.trim() || '';
    const refType    = document.getElementById('quiz-reference-type')?.value        || 'url';

    if (document.querySelectorAll('#questions-container .question-block').length === 0) {
        showToast('Please add at least one question.','warning');
        submitBtn.textContent = 'Create Quiz'; submitBtn.disabled = false; return;
    }

    const questions = collectQuestions('questions-container');
    if (!questions) { submitBtn.textContent = 'Create Quiz'; submitBtn.disabled = false; return; }

    const result = await fetchAPI('create_quiz.php','POST',{
        title, category, difficulty, mode, questions,
        reference_url: refUrl, reference_text: refText, reference_type: refType
    });

    if (result.success) {
        showToast(`✅ "${title}" saved!`,'success');
        e.target.reset();
        document.getElementById('questions-container').innerHTML = '';
        questionCount = 0;
        await loadQuizzes(); await loadDashboardStats(); await loadReferences();
        switchTab('quizzes');
    } else {
        showToast('❌ ' + result.message,'error');
    }
    submitBtn.textContent = 'Create Quiz'; submitBtn.disabled = false;
});

// ============================================================
// QUIZ TABLE
// ============================================================

async function loadQuizzes() {
    const result = await fetchAPI('get_quizzes_all.php');
    const tbody  = document.getElementById('quizzes-tbody');
    if (!result.success || !result.data.length) {
        tbody.innerHTML = '<tr><td colspan="6" class="empty-state">No quizzes in database yet.</td></tr>';
        return;
    }
    tbody.innerHTML = result.data.map(quiz => `
        <tr data-quiz-id="${quiz.id}">
            <td>${quiz.id}</td>
            <td>${escHtml(quiz.title)}</td>
            <td>${escHtml(quiz.category)}</td>
            <td><span class="badge ${quiz.difficulty}">${quiz.difficulty}</span></td>
            <td>${quiz.question_count || 0}</td>
            <td>
                <button class="action-btn-small edit"   onclick="openEditQuizModal(${quiz.id})">✏️ Edit</button>
                <button class="action-btn-small delete" onclick="deleteQuiz(${quiz.id})">🗑️ Delete</button>
            </td>
        </tr>`).join('');
}

async function deleteQuiz(quizId) {
    if (!confirm('Delete this quiz and ALL its questions?')) return;
    const result = await fetchAPI('delete_quiz.php','POST',{id:quizId});
    if (result.success) { showToast(result.message,'success'); loadQuizzes(); loadDashboardStats(); loadReferences(); }
    else { showToast(result.message,'error'); }
}

// ============================================================
// EDIT QUIZ MODAL
// ============================================================

let editingQuizId = null;

async function openEditQuizModal(quizId) {
    editingQuizId = quizId;

    // Show modal with loading state
    const modal = document.getElementById('edit-quiz-modal');
    modal.classList.remove('hidden');
    document.getElementById('edit-quiz-modal-title').textContent = 'Edit Quiz';
    document.getElementById('edit-questions-container').innerHTML = `
        <p style="color:rgba(255,255,255,.5);text-align:center;padding:20px;">⏳ Loading quiz data...</p>`;

    // Fetch full quiz detail
    const result = await fetchAPI(`get_quiz_detail.php?id=${quizId}`);
    if (!result.success) {
        showToast('Failed to load quiz: ' + result.message, 'error');
        modal.classList.add('hidden');
        return;
    }

    const quiz = result.quiz;

    // Fill meta fields
    document.getElementById('edit-quiz-id').value         = quiz.id;
    document.getElementById('edit-quiz-title').value      = quiz.title;
    document.getElementById('edit-quiz-category').value   = quiz.category;
    document.getElementById('edit-quiz-difficulty').value = quiz.difficulty;
    document.getElementById('edit-quiz-mode').value       = quiz.mode;

    // Rebuild question blocks
    const container = document.getElementById('edit-questions-container');
    container.innerHTML = '';
    questionCount = 0; // reset counter for ID generation

    result.questions.forEach((q, i) => {
        // Find which option index is correct
        const correctIndex = q.answers.findIndex(a => a.is_correct === 1);
        const optTexts     = q.answers.map(a => a.text);

        // Pad to 4 options if needed
        while (optTexts.length < 4) optTexts.push('');

        const block = buildQuestionBlock(i + 1, q.text, optTexts, correctIndex);
        container.appendChild(block);
    });

    // If no questions yet, add a blank one
    if (result.questions.length === 0) {
        container.appendChild(buildQuestionBlock(1));
    }
}

function closeEditQuizModal() {
    document.getElementById('edit-quiz-modal').classList.add('hidden');
    editingQuizId = null;
}

// Edit form submit
document.getElementById('edit-quiz-form')?.addEventListener('submit', async (e) => {
    e.preventDefault();
    const submitBtn = e.target.querySelector('[type="submit"]');
    submitBtn.textContent = '⏳ Saving...';
    submitBtn.disabled    = true;

    const title      = document.getElementById('edit-quiz-title').value.trim();
    const category   = document.getElementById('edit-quiz-category').value.trim();
    const difficulty = document.getElementById('edit-quiz-difficulty').value;
    const mode       = document.getElementById('edit-quiz-mode').value;

    if (document.querySelectorAll('#edit-questions-container .question-block').length === 0) {
        showToast('At least one question is required.','warning');
        submitBtn.textContent = 'Save Changes'; submitBtn.disabled = false; return;
    }

    const questions = collectQuestions('edit-questions-container');
    if (!questions) { submitBtn.textContent = 'Save Changes'; submitBtn.disabled = false; return; }

    const result = await fetchAPI('update_quiz_full.php','POST',{
        id: editingQuizId, title, category, difficulty, mode, questions
    });

    if (result.success) {
        showToast(`✅ "${title}" updated!`,'success');
        closeEditQuizModal();
        await loadQuizzes();
        await loadDashboardStats();
    } else {
        showToast('❌ ' + result.message,'error');
    }
    submitBtn.textContent = 'Save Changes'; submitBtn.disabled = false;
});

// Add question inside edit modal
document.getElementById('edit-add-question-btn')?.addEventListener('click', () => {
    const container = document.getElementById('edit-questions-container');
    const num       = container.querySelectorAll('.question-block').length + 1;
    const block     = buildQuestionBlock(num);
    container.appendChild(block);
    renumberEditQuestions();
    block.scrollIntoView({ behavior:'smooth', block:'start' });
});

function renumberEditQuestions() {
    document.querySelectorAll('#edit-questions-container .question-block').forEach((block, i) => {
        const label = block.querySelector('.question-number');
        if (label) label.textContent = `Question ${i + 1}`;
    });
}

// ============================================================
// SIDEBAR BUTTONS — context-aware CRUD
// ============================================================

function getActiveTab() {
    return document.querySelector('.tab-btn.active')?.getAttribute('data-tab') || '';
}

// CREATE button
document.getElementById('create-btn')?.addEventListener('click', () => {
    const tab = getActiveTab();
    if (tab === 'users')    { openUserModal(); return; }
    if (tab === 'add-quiz') { showToast('Fill the form below to create a quiz.','info'); return; }
    if (tab === 'quizzes')  { switchTab('add-quiz'); return; }
    showToast('Switch to a tab first to create a record.','info');
});

// UPDATE button
document.getElementById('update-btn')?.addEventListener('click', () => {
    const tab = getActiveTab();

    if (tab === 'users') {
        const selected = document.querySelector('#users-tbody tr.selected');
        if (selected) { editUser(parseInt(selected.dataset.id)); return; }
        showToast('Click a user row first, then press UPDATE.','warning');
        return;
    }

    if (tab === 'quizzes' || tab === 'add-quiz') {
        const selected = document.querySelector('#quizzes-tbody tr.selected');
        if (selected) { openEditQuizModal(parseInt(selected.dataset.quizId)); return; }
        showToast('Click a quiz row first, then press UPDATE.','warning');
        return;
    }

    showToast('Select a record in the table, then press UPDATE.','warning');
});

// DELETE button
document.getElementById('delete-btn')?.addEventListener('click', () => {
    const tab = getActiveTab();

    if (tab === 'users') {
        const selected = document.querySelector('#users-tbody tr.selected');
        if (selected) { deleteUser(parseInt(selected.dataset.id)); return; }
        showToast('Click a user row first, then press DELETE.','warning');
        return;
    }

    if (tab === 'quizzes') {
        const selected = document.querySelector('#quizzes-tbody tr.selected');
        if (selected) { deleteQuiz(parseInt(selected.dataset.quizId)); return; }
        showToast('Click a quiz row first, then press DELETE.','warning');
        return;
    }

    showToast('Select a record in the table, then press DELETE.','warning');
});

// Row selection highlight
function setupRowSelection(tbodyId) {
    document.getElementById(tbodyId)?.addEventListener('click', (e) => {
        const row = e.target.closest('tr');
        if (!row || e.target.closest('button')) return; // don't select when clicking action buttons
        const tbody = document.getElementById(tbodyId);
        tbody.querySelectorAll('tr').forEach(r => r.classList.remove('selected'));
        row.classList.add('selected');
    });
}

// ============================================================
// CSV IMPORT
// ============================================================

document.getElementById('import-btn')?.addEventListener('click', async () => {
    const fileInput = document.getElementById('quiz-file');
    const file      = fileInput?.files[0];
    const btn       = document.getElementById('import-btn');

    if (!file) { showToast('Please select a CSV file first.','warning'); return; }
    const ext = file.name.split('.').pop().toLowerCase();
    if (ext !== 'csv') { showToast('Only .csv files are supported.','error'); return; }

    btn.textContent = '⏳ Importing...';
    btn.disabled    = true;

    const formData = new FormData();
    formData.append('csv_file', file);

    try {
        const res    = await fetch('api/import_quiz_csv.php', { method:'POST', body: formData });
        const result = await res.json();

        if (result.success) {
            showToast(`✅ Imported ${result.quizzes_imported} quiz(zes), ${result.questions_imported} questions!`,'success');
            fileInput.value = '';
            await loadQuizzes();
            await loadDashboardStats();
        } else {
            showToast('❌ ' + result.message,'error');
        }
    } catch (err) {
        showToast('Network error: ' + err.message,'error');
    }

    btn.textContent = 'Import File';
    btn.disabled    = false;
});

// ============================================================
// FEEDBACK
// ============================================================

const MODE_LABEL = { single_player:'Single Player', timed_quiz:'Timed Quiz', ranked_quiz:'Ranked Quiz', memory_match:'Memory Match', endless_quiz:'Endless Quiz' };
const TYPE_META  = { general:{emoji:'💬',label:'General',color:'#38bdf8'}, suggestion:{emoji:'💡',label:'Suggestion',color:'#fbbf24'}, bug:{emoji:'🐛',label:'Bug Report',color:'#f87171'}, complaint:{emoji:'⚠️',label:'Complaint',color:'#fb923c'} };

let feedbackData   = [];
let feedbackFilter = 'all';
let feedbackSearch = '';

async function loadFeedback() {
    const result = await fetchAPI('get_feedback.php');
    const list   = document.getElementById('feedback-list');
    if (!result.success) { list.innerHTML = `<p class="empty-state">Failed to load.</p>`; return; }
    feedbackData = result.data || [];
    renderFeedback();
}

function renderFeedback() {
    const list  = document.getElementById('feedback-list');
    let   items = feedbackData;
    if (feedbackFilter !== 'all') items = items.filter(f => f.status === feedbackFilter);
    if (feedbackSearch.trim()) {
        const q = feedbackSearch.toLowerCase();
        items = items.filter(f =>
            (f.feedback_text||'').toLowerCase().includes(q) ||
            (f.user_name||'').toLowerCase().includes(q) ||
            (f.quiz_title||'').toLowerCase().includes(q)
        );
    }
    if (!items.length) { list.innerHTML = `<p class="empty-state">No feedback found.</p>`; return; }
    list.innerHTML = items.map(f => buildFeedbackCard(f)).join('');
}

function buildFeedbackCard(f) {
    const meta      = TYPE_META[f.feedback_type] || TYPE_META.general;
    const stars     = f.rating ? '★'.repeat(parseInt(f.rating))+'☆'.repeat(5-parseInt(f.rating)) : 'No rating';
    const mode      = MODE_LABEL[f.quiz_mode] || f.quiz_mode || null;
    const date      = new Date(f.created_at).toLocaleDateString('en-US',{year:'numeric',month:'short',day:'numeric',hour:'2-digit',minute:'2-digit'});
    const isPending = f.status === 'pending';
    return `
    <div class="fb-card ${f.status}">
      <div class="fb-card-top">
        <span class="fb-type-badge" style="background:${meta.color}22;border-color:${meta.color};color:${meta.color};">${meta.emoji} ${meta.label}</span>
        <span class="fb-status-pill ${f.status}">${isPending?'🕐 Pending':'✅ Resolved'}</span>
      </div>
      <div class="fb-card-meta">
        <span class="fb-meta-item"><span class="fb-meta-icon">👤</span><span class="fb-meta-val">${escHtml(f.user_name||'Anonymous')}</span></span>
        ${f.quiz_title?`<span class="fb-meta-sep">·</span><span class="fb-meta-item"><span class="fb-meta-icon">📝</span><span class="fb-meta-val">${escHtml(f.quiz_title)}</span></span>`:''}
        ${f.quiz_category?`<span class="fb-meta-sep">·</span><span class="fb-meta-item"><span class="fb-meta-icon">🗂️</span><span class="fb-meta-val">${escHtml(f.quiz_category)}</span></span>`:''}
        ${mode?`<span class="fb-meta-sep">·</span><span class="fb-meta-item"><span class="fb-meta-icon">🎮</span><span class="fb-meta-val">${escHtml(mode)}</span></span>`:''}
      </div>
      ${f.rating?`<div class="fb-card-stars" style="margin-bottom:12px;font-size:16px;color:#fbbf24;">${stars}</div>`:''}
      <blockquote class="fb-card-msg">${escHtml(f.feedback_text)}</blockquote>
      <div class="fb-card-footer">
        <span class="fb-card-date">🕒 ${date}</span>
        <div class="fb-card-actions">
          ${isPending?`<button class="fb-action-btn resolve" onclick="resolveFeedback(${f.id})">✅ Resolve</button>`:''}
          <button class="fb-action-btn del" onclick="deleteFeedbackItem(${f.id})">🗑️ Delete</button>
        </div>
      </div>
    </div>`;
}

document.querySelectorAll('.filter-btn').forEach(btn => {
    btn.addEventListener('click', () => {
        document.querySelectorAll('.filter-btn').forEach(b => b.classList.remove('active'));
        btn.classList.add('active');
        feedbackFilter = btn.dataset.filter || 'all';
        renderFeedback();
    });
});

document.getElementById('feedback-search')?.addEventListener('input', function() { feedbackSearch = this.value; renderFeedback(); });

async function resolveFeedback(id) {
    const r = await fetchAPI('resolve_feedback.php','POST',{id});
    if (r.success) { showToast('Resolved','success'); loadFeedback(); loadDashboardStats(); }
    else showToast(r.message,'error');
}

async function deleteFeedbackItem(id) {
    if (!confirm('Delete this feedback?')) return;
    const r = await fetchAPI('delete_feedback.php','POST',{id});
    if (r.success) { showToast('Deleted','success'); loadFeedback(); loadDashboardStats(); }
    else showToast(r.message,'error');
}

// ============================================================
// REFERENCES
// ============================================================

const DIFF_COLOR = { easy:'#4ade80', medium:'#fbbf24', hard:'#f87171' };

async function loadReferences() {
    const result = await fetchAPI('get_references.php');
    const tbody  = document.getElementById('references-tbody');
    if (!result.success) { tbody.innerHTML=`<tr><td colspan="7" class="empty-state">Error</td></tr>`; return; }
    if (!result.data?.length) { tbody.innerHTML=`<tr><td colspan="7" class="empty-state">No references yet.</td></tr>`; return; }
    tbody.innerHTML = result.data.map(ref => {
        const diffBadge = ref.quiz_difficulty ? `<span style="display:inline-block;padding:2px 8px;border-radius:6px;font-size:10px;font-weight:bold;color:#000;background:${DIFF_COLOR[ref.quiz_difficulty]||'#aaa'};">${ref.quiz_difficulty}</span>` : '—';
        const modeBadge = ref.quiz_mode ? `<span style="display:inline-block;padding:2px 8px;border-radius:6px;font-size:10px;font-weight:bold;border:1px solid rgba(0,217,255,.5);color:#00d9ff;">${MODE_LABEL[ref.quiz_mode]||ref.quiz_mode}</span>` : '—';
        const refLink   = ref.reference_url ? `<a href="${escHtml(ref.reference_url)}" target="_blank" style="color:#38bdf8;word-break:break-all;font-size:11px;">🔗 ${truncate(ref.reference_url,45)}</a>` : (ref.reference_text?`<span style="font-size:11px;color:#e0e0e0;">${escHtml(truncate(ref.reference_text,60))}</span>`:'<span style="opacity:.4">—</span>');
        const typeBadge = `<span style="display:inline-block;padding:2px 8px;border-radius:6px;font-size:10px;font-weight:bold;background:rgba(181,55,242,.3);color:#d87ef5;">${escHtml(ref.reference_type||'url')}</span>`;
        const dateStr   = new Date(ref.created_at).toLocaleDateString('en-US',{year:'numeric',month:'short',day:'numeric'});
        return `<tr>
            <td><div style="font-weight:bold;color:#fff;font-size:12px;">${escHtml(ref.quiz_title||'—')}</div><div style="font-size:10px;opacity:.5;">ID #${ref.quiz_id}</div></td>
            <td>${ref.quiz_category?escHtml(ref.quiz_category):'<span style="opacity:.4">—</span>'}</td>
            <td>${diffBadge}</td><td>${modeBadge}</td><td>${refLink}</td><td>${typeBadge}</td>
            <td><div style="font-size:11px;opacity:.5;margin-bottom:6px;">${dateStr}</div>
                <button class="action-btn-small delete" onclick="deleteReference(${ref.id})">🗑️ Delete</button></td>
        </tr>`;
    }).join('');
}

async function deleteReference(refId) {
    if (!confirm('Delete this reference?')) return;
    const r = await fetchAPI('delete_reference.php','POST',{id:refId});
    if (r.success) { showToast('Deleted','success'); loadReferences(); }
    else showToast(r.message,'error');
}

function truncate(str, max) { return str&&str.length>max ? str.substring(0,max)+'…':(str||''); }

document.getElementById('reference-search')?.addEventListener('input', function() {
    const q = this.value.toLowerCase();
    document.querySelectorAll('#references-tbody tr').forEach(row => {
        row.style.display = row.textContent.toLowerCase().includes(q) ? '' : 'none';
    });
});

// ============================================================
// TAB NAVIGATION
// ============================================================

function switchTab(tabName) {
    document.querySelectorAll('.tab-btn').forEach(b => b.classList.remove('active'));
    document.querySelectorAll('.tab-content').forEach(c => c.classList.remove('active'));
    document.getElementById(tabName+'-tab')?.classList.add('active');
    document.querySelector(`[data-tab="${tabName}"]`)?.classList.add('active');
}

document.querySelectorAll('.tab-btn').forEach(btn => {
    btn.addEventListener('click', () => {
        const tab = btn.getAttribute('data-tab');
        switchTab(tab);
        switch(tab) {
            case 'dashboard':  loadDashboardStats(); break;
            case 'users':      loadUsers();          break;
            case 'quizzes':    loadQuizzes();        break;
            case 'feedback':   loadFeedback();       break;
            case 'references': loadReferences();     break;
        }
    });
});

// ============================================================
// MODALS
// ============================================================

function closeModal(modalId) { document.getElementById(modalId)?.classList.add('hidden'); }

document.querySelectorAll('.modal-close, .modal-close-btn').forEach(btn => {
    btn.addEventListener('click', function() { this.closest('.modal')?.classList.add('hidden'); });
});

// ============================================================
// SEARCH
// ============================================================

document.getElementById('user-search')?.addEventListener('input', function() {
    const q = this.value.toLowerCase();
    document.querySelectorAll('#users-tbody tr').forEach(row => { row.style.display = row.textContent.toLowerCase().includes(q)?'':'none'; });
});

document.getElementById('quiz-search')?.addEventListener('input', function() {
    const q = this.value.toLowerCase();
    document.querySelectorAll('#quizzes-tbody tr').forEach(row => { row.style.display = row.textContent.toLowerCase().includes(q)?'':'none'; });
});

// ============================================================
// INIT
// ============================================================

window.addEventListener('DOMContentLoaded', () => {
    loadDashboardStats();
    loadUsers();
    loadQuizzes();
    loadReferences();

    // Update import label/filter for CSV
    const importH3 = document.querySelector('.import-section h3');
    if (importH3) importH3.textContent = '📂 Import Quizzes from CSV';
    const fileInput = document.getElementById('quiz-file');
    if (fileInput) fileInput.setAttribute('accept','.csv');

    // Setup row selection for sidebar buttons
    setupRowSelection('users-tbody');
    setupRowSelection('quizzes-tbody');

    // Inject styles
    const style = document.createElement('style');
    style.textContent = `
    @keyframes toastIn { from{transform:translateX(400px);opacity:0} to{transform:translateX(0);opacity:1} }

    /* Selected row highlight */
    #users-tbody tr.selected,
    #quizzes-tbody tr.selected {
        background: rgba(0,217,255,.12) !important;
        outline: 2px solid rgba(0,217,255,.5);
        outline-offset: -2px;
    }

    /* Edit quiz modal */
    #edit-quiz-modal .modal-content {
        max-width: 720px;
        max-height: 90vh;
        overflow-y: auto;
    }

    #edit-questions-container {
        max-height: 420px;
        overflow-y: auto;
        margin: 12px 0;
    }

    /* Feedback cards */
    #feedback-list { display:flex; flex-direction:column; gap:16px; }
    .fb-card { background:var(--bg-darker,#0f0520); border-left:4px solid #ff006e; border-radius:10px; padding:20px 22px; transition:box-shadow .2s,transform .2s; border:1px solid rgba(255,0,110,.25); border-left:4px solid #ff006e; }
    .fb-card.resolved { border-left-color:#39ff14; border-color:rgba(57,255,20,.2); opacity:.85; }
    .fb-card:hover { box-shadow:0 0 20px rgba(255,0,110,.2); transform:translateX(3px); }
    .fb-card-top { display:flex; justify-content:space-between; align-items:center; margin-bottom:14px; flex-wrap:wrap; gap:8px; }
    .fb-type-badge { display:inline-flex; align-items:center; gap:5px; padding:4px 12px; border-radius:20px; font-size:12px; font-weight:700; border:1px solid; letter-spacing:.3px; font-family:'Courier New',monospace; }
    .fb-status-pill { font-size:11px; font-weight:700; padding:4px 12px; border-radius:20px; font-family:'Courier New',monospace; }
    .fb-status-pill.pending{background:#ffd700;color:#1a0b2e;} .fb-status-pill.resolved{background:#39ff14;color:#1a0b2e;}
    .fb-card-meta { display:flex; flex-wrap:wrap; align-items:center; gap:6px; margin-bottom:12px; }
    .fb-meta-item { display:inline-flex; align-items:center; gap:5px; background:rgba(255,255,255,.05); padding:4px 10px; border-radius:6px; border:1px solid rgba(255,255,255,.08); }
    .fb-meta-icon{font-size:13px;line-height:1;} .fb-meta-val{font-size:12px;color:rgba(255,255,255,.85);font-family:'Courier New',monospace;} .fb-meta-sep{color:rgba(255,255,255,.2);font-size:14px;}
    .fb-card-msg { font-size:14px; color:rgba(255,255,255,.9); line-height:1.65; margin:0 0 16px; padding:12px 16px; background:rgba(255,255,255,.04); border-radius:8px; border-left:3px solid rgba(255,0,110,.5); font-style:italic; font-family:'Courier New',monospace; word-break:break-word; }
    .fb-card-footer { display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:10px; }
    .fb-card-date{font-size:11px;color:rgba(255,255,255,.35);font-family:'Courier New',monospace;} .fb-card-actions{display:flex;gap:8px;}
    .fb-action-btn{padding:6px 14px;font-size:11px;font-weight:bold;font-family:'Courier New',monospace;border:1px solid;border-radius:4px;cursor:pointer;transition:all .15s;text-transform:uppercase;letter-spacing:.5px;}
    .fb-action-btn.resolve{background:rgba(57,255,20,.1);border-color:#39ff14;color:#39ff14;} .fb-action-btn.resolve:hover{background:#39ff14;color:#1a0b2e;}
    .fb-action-btn.del{background:rgba(255,23,68,.1);border-color:#ff1744;color:#ff1744;} .fb-action-btn.del:hover{background:#ff1744;color:#fff;}
    `;
    document.head.appendChild(style);

    console.log('✨ Admin Dashboard ready — full CRUD enabled');
});
