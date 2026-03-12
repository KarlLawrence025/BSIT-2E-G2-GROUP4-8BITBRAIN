
console.log("JS loaded 🚀");

const heroButton = document.querySelector(".hero button");
if (heroButton) {
  heroButton.addEventListener("click", () => {
    document.body.classList.add("clicked");
  });
}


document.addEventListener("keydown", (e) => {
  if (e.key === "Enter") {
    document.querySelector(".hero button")?.click();
  }
});

// navigation
const links = document.querySelectorAll(".navbar a");
links.forEach((link) => {
  if (link.href === window.location.href) {
    link.classList.add("active");
  }
});


// about page animations
function animateCounter(element) {
  const target = parseInt(element.getAttribute("data-target"));
  const duration = 2000; // 2 seconds
  const increment = target / (duration / 16); // 60fps
  let current = 0;

  const timer = setInterval(() => {
    current += increment;
    if (current >= target) {
      element.textContent = target.toLocaleString();
      clearInterval(timer);
    } else {
      element.textContent = Math.floor(current).toLocaleString();
    }
  }, 16);
}

// about page
if (document.querySelector(".stats-section")) {
  const statsObserver = new IntersectionObserver(
    (entries) => {
      entries.forEach((entry) => {
        if (entry.isIntersecting) {
          const statNumbers = entry.target.querySelectorAll(".stat-number");
          statNumbers.forEach((stat) => {
            animateCounter(stat);
          });
          statsObserver.unobserve(entry.target);
        }
      });
    },
    { threshold: 0.5 }
  );

  const statsSection = document.querySelector(".stats-section");
  statsObserver.observe(statsSection);
}


const teamCards = document.querySelectorAll(".team-card");
teamCards.forEach((card) => {
  card.addEventListener("mouseenter", function () {
    const img = this.querySelector("img");
    if (img) {
      img.style.transform = "scale(1.1)";
    }
  });

  card.addEventListener("mouseleave", function () {
    const img = this.querySelector("img");
    if (img) {
      img.style.transform = "scale(1)";
    }
  });
});

// login page funcitionality

if (document.getElementById("loginForm")) {
  console.log("Login page loaded 🔐");

  // account type selection
  const accountTypeBtns = document.querySelectorAll(".account-type-btn");
  let currentAccountType = "user";

  accountTypeBtns.forEach((btn) => {
    btn.addEventListener("click", () => {
      accountTypeBtns.forEach((b) => b.classList.remove("active"));
      btn.classList.add("active");
      currentAccountType = btn.dataset.type;

      const loginContainer = document.querySelector(".login-container");
      if (currentAccountType === "admin") {
        loginContainer.classList.add("admin-mode");
      } else {
        loginContainer.classList.remove("admin-mode");
      }
    });
  });

  const loginForm = document.getElementById("loginForm");

  loginForm.addEventListener("submit", (e) => {
    e.preventDefault();

    const formData = {
      email: document.getElementById("email").value,
      name: document.getElementById("name").value,
      age: document.getElementById("age").value,
      password: document.getElementById("password").value,
      remember: document.getElementById("remember").checked,
      accountType: currentAccountType,
    };

    if (formData.age < 1 || formData.age > 120) {
      alert("Please enter a valid age between 1 and 120");
      return;
    }

    if (formData.password.length < 6) {
      alert("Password must be at least 6 characters long");
      return;
    }

    console.log("Login attempt:", formData);

    if (currentAccountType === "admin") {
      alert(`Admin Login Successful!\nWelcome, ${formData.name}!`);
      window.location.href = "dashboard-admin.html";

    } else {
      alert(`User Login Successful!\nWelcome, ${formData.name}!`);
      window.location.href = "dashboard-user.html";
    }

    if (formData.remember) {
      localStorage.setItem(
        "rememberedUser",
        JSON.stringify({
          email: formData.email,
          name: formData.name,
          accountType: formData.accountType,
        })
      );
    }
  });

  window.addEventListener("DOMContentLoaded", () => {
    const rememberedUser = localStorage.getItem("rememberedUser");
    if (rememberedUser) {
      const userData = JSON.parse(rememberedUser);
      document.getElementById("email").value = userData.email || "";
      document.getElementById("name").value = userData.name || "";
      document.getElementById("remember").checked = true;

      if (userData.accountType === "admin") {
        accountTypeBtns.forEach((btn) => {
          if (btn.dataset.type === "admin") {
            btn.click();
          }
        });
      }
    }
  });

  document.querySelector(".forgot-password")?.addEventListener("click", (e) => {
    e.preventDefault();
    alert("Password reset functionality coming soon!");
  });
}

//sign up page functionality
  if (document.getElementById("signupForm")) {
  console.log("Sign Up page loaded 🎉");

  const accountTypeBtns = document.querySelectorAll(".account-type-btn");
  let currentAccountType = "user";

  accountTypeBtns.forEach((btn) => {
    btn.addEventListener("click", () => {
      accountTypeBtns.forEach((b) => b.classList.remove("active"));
      btn.classList.add("active");
      currentAccountType = btn.dataset.type;

      const signupContainer = document.querySelector(".signup-container");
      if (currentAccountType === "admin") {
        signupContainer.classList.add("admin-mode");
      } else {
        signupContainer.classList.remove("admin-mode");
      }
    });
  });

  const signupForm = document.getElementById("signupForm");

  signupForm.addEventListener("submit", (e) => {
    e.preventDefault();

    const formData = {
      fullname: document.getElementById("fullname").value,
      email: document.getElementById("email").value,
      username: document.getElementById("username").value,
      age: document.getElementById("age").value,
      password: document.getElementById("password").value,
      confirmPassword: document.getElementById("confirm-password").value,
      terms: document.getElementById("terms").checked,
      accountType: currentAccountType,
    };

    if (!formData.terms) {
      alert("Please accept the Terms & Conditions");
      return;
    }

    if (formData.age < 1 || formData.age > 120) {
      alert("Please enter a valid age between 1 and 120");
      return;
    }

    if (formData.username.length < 3) {
      alert("Username must be at least 3 characters long");
      return;
    }

    if (formData.password.length < 6) {
      alert("Password must be at least 6 characters long");
      return;
    }

    if (formData.password !== formData.confirmPassword) {
      alert("Passwords do not match!");
      return;
    }

    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    if (!emailRegex.test(formData.email)) {
      alert("Please enter a valid email address");
      return;
    }

    console.log("Sign up attempt:", {
      ...formData,
      password: "[HIDDEN]",
      confirmPassword: "[HIDDEN]",
    });

    alert(
      `Account Created Successfully!\n\nWelcome to 8BitBrain, ${formData.fullname}!\n\nAccount Type: ${currentAccountType === "admin" ? "Admin" : "Regular User"}\n\nYou can now login with your credentials.`
    );

    localStorage.setItem(
      "newUser",
      JSON.stringify({
        fullname: formData.fullname,
        email: formData.email,
        username: formData.username,
        accountType: formData.accountType,
      })
    );


    setTimeout(() => {
      window.location.href = "login.html";
    }, 1500);
  });

 
  const passwordInput = document.getElementById("password");
  const confirmPasswordInput = document.getElementById("confirm-password");

  if (confirmPasswordInput) {
    confirmPasswordInput.addEventListener("input", () => {
      if (confirmPasswordInput.value === "") {
        confirmPasswordInput.style.borderColor = "rgba(255, 255, 255, 0.2)";
      } else if (passwordInput.value === confirmPasswordInput.value) {
        confirmPasswordInput.style.borderColor = "#4ade80"; // Green
      } else {
        confirmPasswordInput.style.borderColor = "#f87171"; // Red
      }
    });
  }


  const usernameInput = document.getElementById("username");
  let usernameTimeout;

  if (usernameInput) {
    usernameInput.addEventListener("input", () => {
      clearTimeout(usernameTimeout);

      if (usernameInput.value.length < 3) {
        return;
      }

      usernameTimeout = setTimeout(() => {
        const takenUsernames = ["admin", "test", "user123", "8bitbrain"];

        if (takenUsernames.includes(usernameInput.value.toLowerCase())) {
          usernameInput.style.borderColor = "#f87171"; 
        } else {
          usernameInput.style.borderColor = "#4ade80"; 
        }
      }, 500);
    });
  }


  document.querySelector(".terms-link")?.addEventListener("click", (e) => {
    e.preventDefault();
    alert(
      "Terms & Conditions:\n\n1. You must be at least 13 years old to use this service.\n2. Provide accurate information during registration.\n3. Keep your password secure.\n4. No cheating or exploiting game mechanics.\n5. Be respectful to other players.\n\nFull terms coming soon!"
    );
  });
}


/* ============================================
   8BitBrain Admin Dashboard - JavaScript Logic
   Complete CRUD & State Management
   ============================================ */

// ============================================
// DATA STATE & STORAGE
// ============================================

const AdminDB = {
    users: [],
    quizzes: [],
    feedback: [],
    references: [],
    activityLog: [],

    init() {
        this.loadFromStorage();
        if (this.users.length === 0) {
            this.loadSampleData();
        }
    },

    loadSampleData() {
        this.users = [
            { id: 'U001', username: 'player_one', email: 'player1@8bitbrain.com', role: 'student', status: 'active', created: '2024-01-15' },
            { id: 'U002', username: 'admin_master', email: 'admin@8bitbrain.com', role: 'admin', status: 'active', created: '2024-01-10' },
            { id: 'U003', username: 'quiz_enthusiast', email: 'user@8bitbrain.com', role: 'student', status: 'active', created: '2024-01-20' }
        ];

        this.quizzes = [
            { id: 'Q001', title: 'JavaScript Basics', category: 'Programming', difficulty: 'easy', questions: 5, created: '2024-02-01' },
            { id: 'Q002', title: 'Advanced CSS', category: 'Design', difficulty: 'medium', questions: 8, created: '2024-02-03' }
        ];

        this.feedback = [
            { id: 'F001', userId: 'U001', message: 'Quiz is too difficult', status: 'pending', type: 'bug' },
            { id: 'F002', userId: 'U003', message: 'Love the retro aesthetic!', status: 'resolved', type: 'suggestion' }
        ];

        this.activityLog = [
            { action: 'System Initialized', timestamp: new Date().toLocaleString() }
        ];

        this.saveToStorage();
    },

    saveToStorage() {
        localStorage.setItem('adminDB', JSON.stringify({
            users: this.users,
            quizzes: this.quizzes,
            feedback: this.feedback,
            references: this.references,
            activityLog: this.activityLog
        }));
    },

    loadFromStorage() {
        const saved = localStorage.getItem('adminDB');
        if (saved) {
            const data = JSON.parse(saved);
            this.users = data.users || [];
            this.quizzes = data.quizzes || [];
            this.feedback = data.feedback || [];
            this.references = data.references || [];
            this.activityLog = data.activityLog || [];
        }
    },

    logActivity(action) {
        this.activityLog.unshift({
            action: action,
            timestamp: new Date().toLocaleString()
        });
        if (this.activityLog.length > 10) this.activityLog.pop();
        this.saveToStorage();
    }
};

// ============================================
// USER CRUD OPERATIONS
// ============================================

const UserCRUD = {
    create(userData) {
        const newId = 'U' + String(AdminDB.users.length + 1).padStart(3, '0');
        const user = {
            id: newId,
            username: userData.username,
            email: userData.email,
            role: userData.role,
            status: userData.status,
            created: new Date().toLocaleDateString()
        };
        AdminDB.users.push(user);
        AdminDB.logActivity(`User Created: ${user.username}`);
        AdminDB.saveToStorage();
        updateDashboard();
        return user;
    },

    read(userId) {
        return AdminDB.users.find(u => u.id === userId);
    },

    update(userId, userData) {
        const user = this.read(userId);
        if (user) {
            Object.assign(user, userData);
            AdminDB.logActivity(`User Updated: ${user.username}`);
            AdminDB.saveToStorage();
            updateDashboard();
            return user;
        }
        return null;
    },

    delete(userId) {
        const userIndex = AdminDB.users.findIndex(u => u.id === userId);
        if (userIndex !== -1) {
            const username = AdminDB.users[userIndex].username;
            AdminDB.users.splice(userIndex, 1);
            AdminDB.logActivity(`User Deleted: ${username}`);
            AdminDB.saveToStorage();
            updateDashboard();
            return true;
        }
        return false;
    },

    getAll() {
        return AdminDB.users;
    },

    search(query) {
        const q = query.toLowerCase();
        return AdminDB.users.filter(u =>
            u.username.toLowerCase().includes(q) ||
            u.email.toLowerCase().includes(q) ||
            u.id.includes(q)
        );
    }
};

// ============================================
// QUIZ CRUD OPERATIONS
// ============================================

const QuizCRUD = {
    create(quizData) {
        const newId = 'Q' + String(AdminDB.quizzes.length + 1).padStart(3, '0');
        const quiz = {
            id: newId,
            title: quizData.title,
            category: quizData.category,
            difficulty: quizData.difficulty,
            questions: quizData.questions || [],
            created: new Date().toLocaleDateString()
        };
        AdminDB.quizzes.push(quiz);
        AdminDB.logActivity(`Quiz Created: ${quiz.title}`);
        AdminDB.saveToStorage();
        updateDashboard();
        return quiz;
    },

    read(quizId) {
        return AdminDB.quizzes.find(q => q.id === quizId);
    },

    update(quizId, quizData) {
        const quiz = this.read(quizId);
        if (quiz) {
            Object.assign(quiz, quizData);
            AdminDB.logActivity(`Quiz Updated: ${quiz.title}`);
            AdminDB.saveToStorage();
            updateDashboard();
            return quiz;
        }
        return null;
    },

    delete(quizId) {
        const quizIndex = AdminDB.quizzes.findIndex(q => q.id === quizId);
        if (quizIndex !== -1) {
            const title = AdminDB.quizzes[quizIndex].title;
            AdminDB.quizzes.splice(quizIndex, 1);
            AdminDB.logActivity(`Quiz Deleted: ${title}`);
            AdminDB.saveToStorage();
            updateDashboard();
            return true;
        }
        return false;
    },

    getAll() {
        return AdminDB.quizzes;
    },

    search(query) {
        const q = query.toLowerCase();
        return AdminDB.quizzes.filter(quiz =>
            quiz.title.toLowerCase().includes(q) ||
            quiz.category.toLowerCase().includes(q) ||
            quiz.id.includes(q)
        );
    }
};

// ============================================
// FEEDBACK CRUD OPERATIONS
// ============================================

const FeedbackCRUD = {
    create(feedbackData) {
        const newId = 'F' + String(AdminDB.feedback.length + 1).padStart(3, '0');
        const feedback = {
            id: newId,
            userId: feedbackData.userId,
            message: feedbackData.message,
            type: feedbackData.type,
            status: 'pending',
            created: new Date().toLocaleString()
        };
        AdminDB.feedback.push(feedback);
        AdminDB.logActivity(`Feedback Received from ${feedback.userId}`);
        AdminDB.saveToStorage();
        updateDashboard();
        return feedback;
    },

    updateStatus(feedbackId, status) {
        const feedback = AdminDB.feedback.find(f => f.id === feedbackId);
        if (feedback) {
            feedback.status = status;
            AdminDB.logActivity(`Feedback ${feedbackId} marked as ${status}`);
            AdminDB.saveToStorage();
            updateDashboard();
            return true;
        }
        return false;
    },

    delete(feedbackId) {
        const index = AdminDB.feedback.findIndex(f => f.id === feedbackId);
        if (index !== -1) {
            AdminDB.feedback.splice(index, 1);
            AdminDB.logActivity(`Feedback Deleted: ${feedbackId}`);
            AdminDB.saveToStorage();
            updateDashboard();
            return true;
        }
        return false;
    },

    getByStatus(status) {
        if (status === 'all') return AdminDB.feedback;
        return AdminDB.feedback.filter(f => f.status === status);
    },

    search(query) {
        const q = query.toLowerCase();
        return AdminDB.feedback.filter(f =>
            f.message.toLowerCase().includes(q) ||
            f.userId.includes(q) ||
            f.id.includes(q)
        );
    }
};

// ============================================
// UI RENDERING
// ============================================

function renderUsersTable(users = UserCRUD.getAll()) {
    const tbody = document.getElementById('users-tbody');

    if (users.length === 0) {
        tbody.innerHTML = '<tr><td colspan="6" class="empty-state">No users found</td></tr>';
        return;
    }

    tbody.innerHTML = users.map(user => `
        <tr data-user-id="${user.id}">
            <td>${user.id}</td>
            <td>${user.username}</td>
            <td>${user.email}</td>
            <td>${user.role}</td>
            <td>${user.status}</td>
            <td>
                <div class="action-btn-row">
                    <button class="action-btn-small edit" onclick="editUser('${user.id}')">Edit</button>
                    <button class="action-btn-small delete" onclick="deleteUser('${user.id}')">Delete</button>
                </div>
            </td>
        </tr>
    `).join('');
}

function renderQuizzesTable(quizzes = QuizCRUD.getAll()) {
    const tbody = document.getElementById('quizzes-tbody');

    if (quizzes.length === 0) {
        tbody.innerHTML = '<tr><td colspan="6" class="empty-state">No quizzes found</td></tr>';
        return;
    }

    tbody.innerHTML = quizzes.map(quiz => `
        <tr data-quiz-id="${quiz.id}">
            <td>${quiz.id}</td>
            <td>${quiz.title}</td>
            <td>${quiz.category}</td>
            <td>${quiz.difficulty}</td>
            <td>${quiz.questions.length}</td>
            <td>
                <div class="action-btn-row">
                    <button class="action-btn-small edit" onclick="editQuiz('${quiz.id}')">Edit</button>
                    <button class="action-btn-small delete" onclick="deleteQuiz('${quiz.id}')">Delete</button>
                </div>
            </td>
        </tr>
    `).join('');
}

function renderFeedbackList(feedbackList = AdminDB.feedback) {
    const container = document.getElementById('feedback-list');

    if (feedbackList.length === 0) {
        container.innerHTML = '<p class="empty-state">No feedback found</p>';
        return;
    }

    container.innerHTML = feedbackList.map(f => `
        <div class="feedback-item ${f.status}" data-feedback-id="${f.id}">
            <div class="feedback-header">
                <div class="feedback-title">${f.message.substring(0, 50)}...</div>
                <span class="feedback-status ${f.status}">${f.status.toUpperCase()}</span>
            </div>
            <div class="feedback-body">${f.message}</div>
            <div class="feedback-meta">
                <span>From: ${f.userId}</span>
                <span>${f.created}</span>
                ${f.status === 'pending' ? `<button class="action-btn-small edit" onclick="resolveFeedback('${f.id}')">Resolve</button>` : ''}
                <button class="action-btn-small delete" onclick="deleteFeedback('${f.id}')">Delete</button>
            </div>
        </div>
    `).join('');
}

function updateDashboard() {
    document.getElementById('stat-users').textContent = AdminDB.users.length;
    document.getElementById('stat-quizzes').textContent = AdminDB.quizzes.length;
    document.getElementById('stat-feedback').textContent = AdminDB.feedback.filter(f => f.status === 'pending').length;

    const actList = document.getElementById('activity-list');
    actList.innerHTML = AdminDB.activityLog.map((log, idx) => `
        <div class="activity-item">
            <strong>${idx + 1}.</strong> ${log.action}
            <br><small>${log.timestamp}</small>
        </div>
    `).join('');
}

// ============================================
// USER MANAGEMENT INTERACTIONS
// ============================================

let currentEditingUserId = null;

function openUserModal(mode = 'create', userId = null) {
    const modal = document.getElementById('user-modal');
    const form = document.getElementById('user-form');
    const title = document.getElementById('modal-title');

    form.reset();
    currentEditingUserId = userId;

    if (mode === 'create') {
        title.textContent = 'New User';
        document.getElementById('user-id').value = '';
    } else if (mode === 'edit' && userId) {
        title.textContent = 'Edit User';
        const user = UserCRUD.read(userId);
        if (user) {
            document.getElementById('user-id').value = user.id;
            document.getElementById('user-username').value = user.username;
            document.getElementById('user-email').value = user.email;
            document.getElementById('user-role').value = user.role;
            document.getElementById('user-status').value = user.status;
        }
    }

    modal.classList.remove('hidden');
}

function closeUserModal() {
    document.getElementById('user-modal').classList.add('hidden');
    currentEditingUserId = null;
}

function editUser(userId) {
    openUserModal('edit', userId);
}

function deleteUser(userId) {
    showConfirmModal(`Delete user ${UserCRUD.read(userId).username}?`, () => {
        UserCRUD.delete(userId);
        renderUsersTable();
        showToast('User deleted');
    });
}

function resolveFeedback(feedbackId) {
    FeedbackCRUD.updateStatus(feedbackId, 'resolved');
    renderFeedbackList();
    showToast('Feedback resolved');
}

function deleteFeedback(feedbackId) {
    showConfirmModal('Delete this feedback?', () => {
        FeedbackCRUD.delete(feedbackId);
        renderFeedbackList();
        updateDashboard();
        showToast('Feedback deleted');
    });
}

function editQuiz(quizId) {
    const quiz = QuizCRUD.read(quizId);
    alert(`Edit quiz: ${quiz.title}\n\nFeature coming soon!`);
}

function deleteQuiz(quizId) {
    const quiz = QuizCRUD.read(quizId);
    showConfirmModal(`Delete quiz "${quiz.title}"?`, () => {
        QuizCRUD.delete(quizId);
        renderQuizzesTable();
        showToast('Quiz deleted');
    });
}

// ============================================
// FORM HANDLING
// ============================================

document.getElementById('user-form').addEventListener('submit', (e) => {
    e.preventDefault();

    const userData = {
        username: document.getElementById('user-username').value,
        email: document.getElementById('user-email').value,
        role: document.getElementById('user-role').value,
        status: document.getElementById('user-status').value
    };

    if (currentEditingUserId) {
        UserCRUD.update(currentEditingUserId, userData);
        showToast('User updated');
    } else {
        UserCRUD.create(userData);
        showToast('User created');
    }

    renderUsersTable();
    closeUserModal();
});

document.getElementById('quiz-form').addEventListener('submit', (e) => {
    e.preventDefault();

    const questions = [];
    document.querySelectorAll('.question-block').forEach(block => {
        const text = block.querySelector('.question-text')?.value || '';
        const options = [];
        let correctAnswer = 0;

        const optInputs = block.querySelectorAll('.option-item input[type="text"]');
        optInputs.forEach((input, idx) => {
            options.push({ text: input.value, index: idx });
        });

        const radios = block.querySelectorAll('.option-item input[type="radio"]');
        radios.forEach((radio, idx) => {
            if (radio.checked) correctAnswer = idx;
        });

        if (text && options.length > 0) {
            questions.push({
                text: text,
                options: options,
                correctAnswer: correctAnswer
            });
        }
    });

    const quizData = {
        title: document.getElementById('quiz-title').value,
        category: document.getElementById('quiz-category').value,
        difficulty: document.getElementById('quiz-difficulty').value,
        questions: questions
    };

    QuizCRUD.create(quizData);
    document.getElementById('quiz-form').reset();
    document.getElementById('questions-container').innerHTML = '';
    renderQuizzesTable();
    showToast('Quiz created successfully!');
    switchTab('quizzes');
});

// ============================================
// QUESTION BUILDER
// ============================================

let questionCount = 0;

document.getElementById('add-question-btn').addEventListener('click', () => {
    questionCount++;
    const container = document.getElementById('questions-container');
    const block = document.createElement('div');
    block.className = 'question-block';
    block.innerHTML = `
        <div class="question-num">Q${questionCount}</div>
        <div class="form-group">
            <label>Question Text</label>
            <input type="text" class="question-text" placeholder="Enter question" required>
        </div>
        <div class="options-grid">
            <div class="option-item">
                <input type="text" placeholder="Option A" required>
                <input type="radio" name="correct-q${questionCount}" value="0">
            </div>
            <div class="option-item">
                <input type="text" placeholder="Option B" required>
                <input type="radio" name="correct-q${questionCount}" value="1">
            </div>
            <div class="option-item">
                <input type="text" placeholder="Option C" required>
                <input type="radio" name="correct-q${questionCount}" value="2">
            </div>
            <div class="option-item">
                <input type="text" placeholder="Option D" required>
                <input type="radio" name="correct-q${questionCount}" value="3">
            </div>
        </div>
        <button type="button" class="btn-admin btn-secondary" onclick="this.parentElement.remove()">Remove Question</button>
    `;
    container.appendChild(block);
});

// ============================================
// SEARCH FUNCTIONALITY
// ============================================

document.getElementById('user-search').addEventListener('input', (e) => {
    const results = UserCRUD.search(e.target.value);
    renderUsersTable(results);
});

document.getElementById('quiz-search').addEventListener('input', (e) => {
    const results = QuizCRUD.search(e.target.value);
    renderQuizzesTable(results);
});

document.getElementById('feedback-search').addEventListener('input', (e) => {
    const results = FeedbackCRUD.search(e.target.value);
    renderFeedbackList(results);
});

// ============================================
// FEEDBACK FILTERING
// ============================================

document.querySelectorAll('.filter-btn').forEach(btn => {
    btn.addEventListener('click', (e) => {
        document.querySelectorAll('.filter-btn').forEach(b => b.classList.remove('active'));
        e.target.classList.add('active');

        const filter = e.target.dataset.filter;
        const results = FeedbackCRUD.getByStatus(filter);
        renderFeedbackList(results);
    });
});

// ============================================
// TAB SWITCHING
// ============================================

function switchTab(tabName) {
    // Hide all tabs
    document.querySelectorAll('.tab-content').forEach(tab => {
        tab.classList.remove('active');
    });

    // Remove active from all buttons
    document.querySelectorAll('.tab-btn').forEach(btn => {
        btn.classList.remove('active');
    });

    // Show selected tab
    const tabId = tabName + '-tab';
    const tab = document.getElementById(tabId);
    if (tab) {
        tab.classList.add('active');
    }

    // Highlight active button
    const btn = document.querySelector(`[data-tab="${tabName}"]`);
    if (btn) {
        btn.classList.add('active');
    }
}

document.querySelectorAll('.tab-btn').forEach(btn => {
    btn.addEventListener('click', () => {
        switchTab(btn.dataset.tab);
    });
});

// ============================================
// ACTION SIDEBAR BUTTONS
// ============================================

document.getElementById('create-btn').addEventListener('click', () => {
    const activeTab = document.querySelector('.tab-content.active').id;

    if (activeTab === 'users-tab') {
        openUserModal('create');
    } else if (activeTab === 'add-quiz-tab') {
        document.getElementById('quiz-form').reset();
    }
});

document.getElementById('update-btn').addEventListener('click', () => {
    const activeTab = document.querySelector('.tab-content.active').id;

    if (activeTab === 'users-tab') {
        const selectedRow = document.querySelector('tr[data-user-id]');
        if (selectedRow) {
            editUser(selectedRow.dataset.userId);
        } else {
            showToast('Select a user to edit', 'warning');
        }
    }
});

document.getElementById('delete-btn').addEventListener('click', () => {
    const activeTab = document.querySelector('.tab-content.active').id;

    if (activeTab === 'users-tab') {
        const selectedRow = document.querySelector('tr[data-user-id]');
        if (selectedRow) {
            deleteUser(selectedRow.dataset.userId);
        } else {
            showToast('Select a user to delete', 'warning');
        }
    }
});

// ============================================
// MODAL INTERACTIONS
// ============================================

document.querySelectorAll('.modal-close').forEach(btn => {
    btn.addEventListener('click', () => {
        closeUserModal();
        document.getElementById('confirm-modal').classList.add('hidden');
    });
});

document.querySelectorAll('.modal-close-btn').forEach(btn => {
    btn.addEventListener('click', () => {
        closeUserModal();
        document.getElementById('confirm-modal').classList.add('hidden');
    });
});

document.querySelectorAll('.modal').forEach(modal => {
    modal.addEventListener('click', (e) => {
        if (e.target === modal) {
            modal.classList.add('hidden');
        }
    });
});

// ============================================
// CONFIRMATION MODAL
// ============================================

let confirmCallback = null;

function showConfirmModal(message, callback) {
    const modal = document.getElementById('confirm-modal');
    document.getElementById('confirm-message').textContent = message;
    confirmCallback = callback;
    modal.classList.remove('hidden');
}

document.getElementById('confirm-yes').addEventListener('click', () => {
    if (confirmCallback) confirmCallback();
    document.getElementById('confirm-modal').classList.add('hidden');
});

// ============================================
// IMPORT FUNCTIONALITY
// ============================================

document.getElementById('import-btn').addEventListener('click', () => {
    const file = document.getElementById('quiz-file').files[0];
    if (!file) {
        showToast('Select a file first', 'warning');
        return;
    }

    const reader = new FileReader();
    reader.onload = (e) => {
        try {
            const data = JSON.parse(e.target.result);
            const quizzes = Array.isArray(data) ? data : [data];

            quizzes.forEach(q => {
                QuizCRUD.create({
                    title: q.title,
                    category: q.category,
                    difficulty: q.difficulty || 'medium',
                    questions: q.questions || []
                });
            });

            renderQuizzesTable();
            document.getElementById('quiz-file').value = '';
            showToast(`Imported ${quizzes.length} quiz(zes)!`);
        } catch (err) {
            showToast('Error parsing file: ' + err.message, 'error');
        }
    };
    reader.readAsText(file);
});

// ============================================
// CAROUSEL FUNCTIONALITY
// ============================================

let currentSlide = 0;
const slides = document.querySelectorAll('.slide');
const wrapper = document.querySelector('.slides-wrapper');

function showSlide(index){

    currentSlide = index;
    wrapper.style.transform = `translateX(-${currentSlide * 100}%)`;

}

setInterval(() => {

    currentSlide++;

    wrapper.style.transform = `translateX(-${currentSlide * 100}%)`;

    if(currentSlide === slides.length - 1){

        setTimeout(() => {

            wrapper.style.transition = "none";
            currentSlide = 0;
            wrapper.style.transform = `translateX(0%)`;

            setTimeout(() => {
                wrapper.style.transition = "transform 0.5s ease";
            },200);

        },700);
    }

},4000);

// ============================================
// TOAST NOTIFICATIONS
// ============================================

function showToast(message, type = 'success') {
    const toast = document.createElement('div');
    toast.className = `toast ${type}`;
    toast.textContent = message;
    toast.style.cssText = `
        position: fixed;
        bottom: 20px;
        right: 20px;
        padding: 15px 20px;
        background: ${type === 'success' ? '#39ff14' : type === 'error' ? '#ff1744' : '#ffd700'};
        color: #1a0b2e;
        border: 2px solid;
        border-color: ${type === 'success' ? '#39ff14' : type === 'error' ? '#ff1744' : '#ffd700'};
        font-family: 'Courier New', monospace;
        font-size: 11px;
        font-weight: bold;
        z-index: 9999;
        animation: toastSlide 0.3s ease;
        box-shadow: 0 0 15px currentColor;
    `;

    document.body.appendChild(toast);

    setTimeout(() => {
        toast.style.animation = 'toastSlide 0.3s ease reverse';
        setTimeout(() => toast.remove(), 300);
    }, 3000);
}

// ============================================
// INITIALIZATION
// ============================================

document.addEventListener('DOMContentLoaded', () => {
    // Add animation styles
    const style = document.createElement('style');
    style.textContent = `
        @keyframes toastSlide {
            from {
                transform: translateX(400px);
                opacity: 0;
            }
            to {
                transform: translateX(0);
                opacity: 1;
            }
        }
    `;
    document.head.appendChild(style);

    // Initialize
    AdminDB.init();
    renderUsersTable();
    renderQuizzesTable();
    renderFeedbackList();
    updateDashboard();

    console.log('✨ 8BitBrain Admin Dashboard Ready!');
});
