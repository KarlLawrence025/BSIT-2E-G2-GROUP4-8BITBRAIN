// ========================================
// ADMIN DASHBOARD - DATABASE CRUD OPERATIONS
// ========================================

console.log("Admin Dashboard JS loaded 🔧");

// Check if user is admin
const currentUser = JSON.parse(sessionStorage.getItem('currentUser') || '{}');
if (currentUser.accountType !== 'admin') {
    console.warn('Non-admin user attempted to access admin dashboard');
    // Redirect non-admins to user dashboard or home
    // Uncomment the line below to enforce admin-only access
    // window.location.href = 'dashboard-user.html';
}

// API Base URL
const API_BASE = 'api/';

// ========================================
// UTILITY FUNCTIONS
// ========================================

async function fetchAPI(endpoint, method = 'GET', data = null) {
    const options = {
        method: method,
        headers: {
            'Content-Type': 'application/json'
        }
    };

    if (data && (method === 'POST' || method === 'PUT' || method === 'DELETE')) {
        options.body = JSON.stringify(data);
    }

    const url = API_BASE + endpoint;
    console.log('🔗 Calling API:', url, 'Method:', method);
    console.log('📦 Data:', data);

    try {
        const response = await fetch(url, options);
        console.log('📡 Response status:', response.status);
        
        const result = await response.json();
        console.log('✅ Response data:', result);
        return result;
    } catch (error) {
        console.error('❌ API Error:', error);
        console.error('URL was:', url);
        return { 
            success: false, 
            message: 'Network error: ' + error.message + '. Make sure XAMPP Apache is running and files are in the correct location.'
        };
    }
}

function showMessage(message, type = 'success') {
    alert(message); // Replace with better notification system
}

// ========================================
// LOAD DASHBOARD STATS
// ========================================

async function loadDashboardStats() {
    const result = await fetchAPI('get_stats.php');
    
    if (result.success) {
        // Update stat cards
        document.getElementById('stat-quizzes').textContent = result.data.total_quizzes;
        document.getElementById('stat-users').textContent = result.data.total_users;
        document.getElementById('stat-feedback').textContent = result.data.pending_feedback;

        // Update activity log
        const activityList = document.getElementById('activity-list');
        if (result.data.recent_activity.length === 0) {
            activityList.innerHTML = '<p class="empty-state">No activity yet</p>';
        } else {
            activityList.innerHTML = result.data.recent_activity.map(activity => `
                <div class="activity-item">
                    <span class="activity-type">${activity.type === 'user' ? '👤' : '📝'}</span>
                    <span class="activity-name">${activity.name}</span>
                    <span class="activity-time">${new Date(activity.created_at).toLocaleString()}</span>
                </div>
            `).join('');
        }
    }
}

// ========================================
// USER MANAGEMENT - READ
// ========================================

async function loadUsers() {
    const result = await fetchAPI('get_users.php');
    
    if (result.success) {
        const tbody = document.getElementById('users-tbody');
        
        if (result.data.length === 0) {
            tbody.innerHTML = '<tr><td colspan="6" class="empty-state">No users found</td></tr>';
        } else {
            tbody.innerHTML = result.data.map(user => `
                <tr data-id="${user.id}">
                    <td>${user.id}</td>
                    <td>${user.username}</td>
                    <td>${user.email}</td>
                    <td><span class="badge ${user.account_type}">${user.account_type}</span></td>
                    <td><span class="badge ${user.status}">${user.status}</span></td>
                    <td>
                        <button class="btn-action btn-edit" onclick="editUser(${user.id})">✏️ Edit</button>
                        <button class="btn-action btn-delete" onclick="deleteUser(${user.id})">🗑️ Delete</button>
                    </td>
                </tr>
            `).join('');
        }
    }
}

// ========================================
// USER MANAGEMENT - CREATE
// ========================================

let currentEditingUserId = null;

function openUserModal(userId = null) {
    const modal = document.getElementById('user-modal');
    const form = document.getElementById('user-form');
    const title = document.getElementById('modal-title');
    
    modal.classList.remove('hidden');
    form.reset();
    currentEditingUserId = userId;
    
    if (userId) {
        title.textContent = 'Edit User';
        loadUserData(userId);
    } else {
        title.textContent = 'New User';
        document.getElementById('user-id').value = 'Auto-generated';
    }
}

async function loadUserData(userId) {
    const result = await fetchAPI('get_users.php');
    if (result.success) {
        const user = result.data.find(u => u.id == userId);
        if (user) {
            document.getElementById('user-id').value = user.id;
            document.getElementById('user-username').value = user.username;
            document.getElementById('user-email').value = user.email;
            document.getElementById('user-role').value = user.account_type;
        }
    }
}

async function saveUser(event) {
    event.preventDefault();
    
    const userData = {
        username: document.getElementById('user-username').value,
        email: document.getElementById('user-email').value,
        account_type: document.getElementById('user-role').value,
        fullname: document.getElementById('user-username').value, // Can add separate fullname field
        age: 18, // Default age, can add field
        password: 'password123' // Default password, should be generated or entered
    };
    
    let result;
    if (currentEditingUserId) {
        // Update existing user
        userData.id = currentEditingUserId;
        result = await fetchAPI('update_user.php', 'POST', userData);
    } else {
        // Create new user
        result = await fetchAPI('create_user.php', 'POST', userData);
    }
    
    if (result.success) {
        showMessage(result.message, 'success');
        closeModal('user-modal');
        loadUsers();
        loadDashboardStats();
    } else {
        showMessage(result.message, 'error');
    }
}

// ========================================
// USER MANAGEMENT - UPDATE
// ========================================

function editUser(userId) {
    openUserModal(userId);
}

// ========================================
// USER MANAGEMENT - DELETE
// ========================================

async function deleteUser(userId) {
    if (!confirm('Are you sure you want to delete this user?')) {
        return;
    }
    
    const result = await fetchAPI('delete_user.php', 'POST', { id: userId });
    
    if (result.success) {
        showMessage(result.message, 'success');
        loadUsers();
        loadDashboardStats();
    } else {
        showMessage(result.message, 'error');
    }
}

// ========================================
// QUIZ MANAGEMENT
// ========================================

let questionCount = 0;

// Add question to quiz form
document.getElementById('add-question-btn')?.addEventListener('click', () => {
    questionCount++;
    const questionsContainer = document.getElementById('questions-container');
    
    const questionDiv = document.createElement('div');
    questionDiv.className = 'question-block';
    questionDiv.id = `question-${questionCount}`;
    questionDiv.setAttribute('data-question-id', questionCount);
    questionDiv.innerHTML = `
        <h4 class="question-number">Question ${questionCount}</h4>
        <div class="form-group">
            <label>Question Text</label>
            <input type="text" class="question-text" required placeholder="Enter question">
        </div>
        <div class="form-group">
            <label>Option A</label>
            <input type="text" class="option-input" required placeholder="Option A">
        </div>
        <div class="form-group">
            <label>Option B</label>
            <input type="text" class="option-input" required placeholder="Option B">
        </div>
        <div class="form-group">
            <label>Option C</label>
            <input type="text" class="option-input" required placeholder="Option C">
        </div>
        <div class="form-group">
            <label>Option D</label>
            <input type="text" class="option-input" required placeholder="Option D">
        </div>
        <div class="form-group">
            <label>Correct Answer</label>
            <select class="correct-answer" required>
                <option value="">Select correct answer</option>
                <option value="0">Option A</option>
                <option value="1">Option B</option>
                <option value="2">Option C</option>
                <option value="3">Option D</option>
            </select>
        </div>
        <button type="button" class="btn-admin btn-danger" onclick="removeQuestion(${questionCount})">Remove Question</button>
        <hr>
    `;
    
    questionsContainer.appendChild(questionDiv);
    renumberQuestions();
});

// Remove question and renumber
function removeQuestion(id) {
    const questionBlock = document.getElementById(`question-${id}`);
    if (questionBlock) {
        questionBlock.remove();
        renumberQuestions();
    }
}

// Renumber all questions after add/remove
function renumberQuestions() {
    const questionBlocks = document.querySelectorAll('.question-block');
    questionBlocks.forEach((block, index) => {
        const questionNumber = block.querySelector('.question-number');
        if (questionNumber) {
            questionNumber.textContent = `Question ${index + 1}`;
        }
    });
}

// Handle quiz form submission
document.getElementById('quiz-form')?.addEventListener('submit', async (e) => {
    e.preventDefault();
    
    // Get quiz data
    const quizData = {
        title: document.getElementById('quiz-title').value,
        category: document.getElementById('quiz-category').value,
        difficulty: document.getElementById('quiz-difficulty').value,
        reference_url: document.getElementById('quiz-reference').value || null,
        created_by: 1, // Admin user ID
        questions: []
    };
    
    // Get all questions
    const questionBlocks = document.querySelectorAll('.question-block');
    questionBlocks.forEach(block => {
        const questionText = block.querySelector('.question-text').value;
        const options = Array.from(block.querySelectorAll('.option-input')).map(input => ({
            text: input.value
        }));
        const correctAnswer = parseInt(block.querySelector('.correct-answer').value);
        
        quizData.questions.push({
            text: questionText,
            options: options,
            correctAnswer: correctAnswer
        });
    });
    
    // Send to API
    const result = await fetchAPI('create_quiz.php', 'POST', quizData);
    
    if (result.success) {
        showMessage('Quiz created successfully!', 'success');
        document.getElementById('quiz-form').reset();
        document.getElementById('questions-container').innerHTML = '';
        questionCount = 0;
        loadQuizzes();
        loadDashboardStats();
    } else {
        showMessage('Error: ' + result.message, 'error');
    }
});

async function loadQuizzes() {
    const result = await fetchAPI('get_quizzes.php');
    
    if (result.success) {
        const tbody = document.getElementById('quizzes-tbody');
        
        if (result.data.length === 0) {
            tbody.innerHTML = '<tr><td colspan="6" class="empty-state">No quizzes found</td></tr>';
        } else {
            tbody.innerHTML = result.data.map(quiz => `
                <tr>
                    <td>${quiz.id}</td>
                    <td>${quiz.title}</td>
                    <td>${quiz.category}</td>
                    <td><span class="badge ${quiz.difficulty}">${quiz.difficulty}</span></td>
                    <td>${quiz.question_count || 0}</td>
                    <td>
                        <button class="btn-action btn-edit" onclick="editQuiz(${quiz.id})">✏️ Edit</button>
                        <button class="btn-action btn-delete" onclick="deleteQuiz(${quiz.id})">🗑️ Delete</button>
                    </td>
                </tr>
            `).join('');
        }
    }
}

async function deleteQuiz(quizId) {
    if (!confirm('Are you sure you want to delete this quiz? This will also delete all questions and answers.')) {
        return;
    }
    
    const result = await fetchAPI('delete_quiz.php', 'POST', { id: quizId });
    
    if (result.success) {
        showMessage(result.message, 'success');
        loadQuizzes();
        loadDashboardStats();
    } else {
        showMessage(result.message, 'error');
    }
}

function editQuiz(quizId) {
    showMessage('Quiz editing coming soon!', 'info');
    // TODO: Implement quiz editing
}

// ========================================
// FEEDBACK MANAGEMENT
// ========================================

async function loadFeedback() {
    const result = await fetchAPI('get_feedback.php');
    
    if (result.success) {
        const feedbackList = document.getElementById('feedback-list');
        
        if (result.data.length === 0) {
            feedbackList.innerHTML = '<p class="empty-state">No feedback found</p>';
        } else {
            feedbackList.innerHTML = result.data.map(feedback => `
                <div class="feedback-item ${feedback.status}">
                    <div class="feedback-header">
                        <span class="feedback-user">👤 ${feedback.user_name || 'Anonymous'}</span>
                        <span class="feedback-quiz">📝 ${feedback.quiz_title || 'General Feedback'}</span>
                        <span class="feedback-rating">${'⭐'.repeat(feedback.rating || 0)}</span>
                    </div>
                    <p class="feedback-text">${feedback.feedback_text}</p>
                    <div class="feedback-footer">
                        <span class="feedback-date">${new Date(feedback.created_at).toLocaleDateString()}</span>
                        <span class="feedback-status badge ${feedback.status}">${feedback.status}</span>
                    </div>
                </div>
            `).join('');
        }
    }
}

// ========================================
// REFERENCES MANAGEMENT
// ========================================

async function loadReferences() {
    const result = await fetchAPI('get_references.php');
    
    if (result.success) {
        const tbody = document.getElementById('references-tbody');
        
        if (result.data.length === 0) {
            tbody.innerHTML = '<tr><td colspan="5" class="empty-state">No references found</td></tr>';
        } else {
            tbody.innerHTML = result.data.map(ref => `
                <tr>
                    <td>${ref.quiz_id}</td>
                    <td>${ref.question_text}</td>
                    <td>
                        ${ref.reference_url ? 
                            `<a href="${ref.reference_url}" target="_blank" style="color: #38bdf8;">${ref.reference_url}</a>` : 
                            ref.reference_text || 'N/A'}
                    </td>
                    <td><span class="badge">${ref.reference_type}</span></td>
                    <td>
                        <button class="btn-action btn-delete" onclick="deleteReference(${ref.id})">🗑️ Delete</button>
                    </td>
                </tr>
            `).join('');
        }
    }
}

async function deleteReference(refId) {
    if (!confirm('Are you sure you want to delete this reference?')) {
        return;
    }
    
    // TODO: Create delete_reference.php API
    showMessage('Delete reference API not yet implemented', 'warning');
}

// ========================================
// TAB NAVIGATION
// ========================================

document.querySelectorAll('.tab-btn').forEach(btn => {
    btn.addEventListener('click', () => {
        // Remove active class from all tabs
        document.querySelectorAll('.tab-btn').forEach(b => b.classList.remove('active'));
        document.querySelectorAll('.tab-content').forEach(c => c.classList.remove('active'));
        
        // Add active class to clicked tab
        btn.classList.add('active');
        const tabId = btn.getAttribute('data-tab') + '-tab';
        document.getElementById(tabId).classList.add('active');
        
        // Load data based on tab
        switch(btn.getAttribute('data-tab')) {
            case 'dashboard':
                loadDashboardStats();
                break;
            case 'users':
                loadUsers();
                break;
            case 'quizzes':
                loadQuizzes();
                break;
            case 'feedback':
                loadFeedback();
                break;
            case 'references':
                loadReferences();
                break;
        }
    });
});

// ========================================
// ACTION SIDEBAR BUTTONS
// ========================================

document.getElementById('create-btn')?.addEventListener('click', () => {
    const activeTab = document.querySelector('.tab-btn.active').getAttribute('data-tab');
    
    switch(activeTab) {
        case 'users':
            openUserModal();
            break;
        case 'add-quiz':
            // Quiz creation logic
            break;
    }
});

// ========================================
// MODAL FUNCTIONS
// ========================================

function closeModal(modalId) {
    document.getElementById(modalId).classList.add('hidden');
}

document.querySelectorAll('.modal-close, .modal-close-btn').forEach(btn => {
    btn.addEventListener('click', function() {
        this.closest('.modal').classList.add('hidden');
    });
});

// ========================================
// FORM SUBMISSIONS
// ========================================

document.getElementById('user-form')?.addEventListener('submit', saveUser);

// ========================================
// SEARCH FUNCTIONS
// ========================================

document.getElementById('user-search')?.addEventListener('input', function(e) {
    const searchTerm = e.target.value.toLowerCase();
    document.querySelectorAll('#users-tbody tr').forEach(row => {
        const text = row.textContent.toLowerCase();
        row.style.display = text.includes(searchTerm) ? '' : 'none';
    });
});

document.getElementById('quiz-search')?.addEventListener('input', function(e) {
    const searchTerm = e.target.value.toLowerCase();
    document.querySelectorAll('#quizzes-tbody tr').forEach(row => {
        const text = row.textContent.toLowerCase();
        row.style.display = text.includes(searchTerm) ? '' : 'none';
    });
});

// ========================================
// INITIALIZE ON LOAD
// ========================================

window.addEventListener('DOMContentLoaded', () => {
    loadDashboardStats();
    loadUsers();
    loadReferences();
});
