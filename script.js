// ========================================
// MAIN SCRIPT - GENERAL WEBSITE CODE ONLY
// (Admin dashboard uses admin-dashboard.js)
// ========================================

console.log("JS loaded 🚀");

document.addEventListener("DOMContentLoaded", () => {
  const startBtn = document.querySelector(".hero button"); // your Start Quiz button

  startBtn.addEventListener("click", () => {
    // Define available modes
    const modes = [
      "single_player",
      "timed_quiz",
      "ranked_quiz",
      "memory_match",
      "endless_quiz",
    ];

    // Pick a random mode
    const randomMode = modes[Math.floor(Math.random() * modes.length)];

    // Save to localStorage so your quiz system picks it up
    localStorage.setItem("selectedMode", randomMode);

    // Redirect to quiz selection/game page
    window.location.href = "quiz.php"; // adjust to your quiz page
  });
});

// ========================================
// MODES
// ========================================

document.addEventListener("DOMContentLoaded", () => {
  document.getElementById("timed-btn").addEventListener("click", () => {
    localStorage.setItem("selectedMode", "timed_quiz");
    window.location.href = "quiz.php";
  });

  document.getElementById("ranked-btn").addEventListener("click", () => {
    localStorage.setItem("selectedMode", "ranked_quiz");
    window.location.href = "quiz.php";
  });

  document.getElementById("memory-btn").addEventListener("click", () => {
    localStorage.setItem("selectedMode", "memory_match");
    window.location.href = "quiz.php";
  });

  document.getElementById("endless-btn").addEventListener("click", () => {
    localStorage.setItem("selectedMode", "endless_quiz");
    window.location.href = "quiz.php";
  });

  document.getElementById("single-btn").addEventListener("click", () => {
    localStorage.setItem("selectedMode", "single_player");
    window.location.href = "quiz.php";
  });
});

// ========================================
// GENERAL PAGE FUNCTIONALITY
// ========================================

// Hero button click handler (for index.html)

const heroButton = document.querySelector(".hero button");
if (heroButton) {
  heroButton.addEventListener("click", () => {
    document.body.classList.add("clicked");
  });
}

// Enter key handler for hero button
document.addEventListener("keydown", (e) => {
  if (e.key === "Enter") {
    document.querySelector(".hero button")?.click();
  }
});

// Active navigation link highlighting
const links = document.querySelectorAll(".navbar a");
links.forEach((link) => {
  if (link.href === window.location.href) {
    link.classList.add("active");
  }
});

// ========================================
// ABOUT PAGE - ANIMATIONS & INTERACTIONS
// ========================================

// Animated counter for statistics
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

// Intersection Observer for stats animation (About page)
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
    { threshold: 0.5 },
  );

  const statsSection = document.querySelector(".stats-section");
  statsObserver.observe(statsSection);
}

// Team cards hover effect (About page)
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

// ========================================
// LOGIN PAGE FUNCTIONALITY
// ========================================

if (document.getElementById("loginForm")) {
  console.log("Login page loaded 🔐");

  // Account type selection
  const accountTypeBtns = document.querySelectorAll(".account-type-btn");
  let selectedAccountType = "user"; // Default to user

  accountTypeBtns.forEach((btn) => {
    btn.addEventListener("click", () => {
      // Remove active class from all buttons
      accountTypeBtns.forEach((b) => b.classList.remove("active"));
      // Add active class to clicked button
      btn.classList.add("active");
      // Update selected account type
      selectedAccountType = btn.dataset.type;

      // Update form styling based on account type
      const loginContainer = document.querySelector(".login-container");
      if (selectedAccountType === "admin") {
        loginContainer.classList.add("admin-mode");
      } else {
        loginContainer.classList.remove("admin-mode");
      }
    });
  });

  // Form submission
  const loginForm = document.getElementById("loginForm");

  loginForm.addEventListener("submit", async (e) => {
    e.preventDefault();

    // Get form data
    const formData = {
      email: document.getElementById("email").value,
      password: document.getElementById("password").value,
      remember: document.getElementById("remember").checked,
    };

    // Validate password length
    if (formData.password.length < 6) {
      alert("Password must be at least 6 characters long");
      return;
    }

    try {
      // Call login API to verify credentials
      const response = await fetch("api/login.php", {
        method: "POST",
        headers: {
          "Content-Type": "application/json",
        },
        body: JSON.stringify({
          email: formData.email,
          password: formData.password,
        }),
      });

      const result = await response.json();

      if (result.success) {
        const user = result.user;

        // CHECK: Account type from database must match selected type
        if (user.account_type !== selectedAccountType) {
          alert(
            `❌ Account Type Mismatch!\n\n` +
              `You selected: ${selectedAccountType === "admin" ? "Admin" : "Regular User"}\n` +
              `But this account is: ${user.account_type === "admin" ? "Admin" : "Regular User"}\n\n` +
              `Please select the correct account type and try again.`,
          );
          return;
        }

        // Store user data in session
        sessionStorage.setItem(
          "currentUser",
          JSON.stringify({
            id: user.id,
            name: user.fullname,
            email: user.email,
            username: user.username,
            accountType: user.account_type,
          }),
        );

        // Store in localStorage if "remember me" is checked
        if (formData.remember) {
          localStorage.setItem(
            "rememberedUser",
            JSON.stringify({
              email: user.email,
              name: user.fullname,
              accountType: user.account_type,
            }),
          );
        }

        // Redirect based on account type
        if (user.account_type === "admin") {
          alert(`✅ Admin Login Successful!\nWelcome, ${user.fullname}!`);
          window.location.href = "dashboard-admin.php";
        } else {
          alert(`✅ Login Successful!\nWelcome, ${user.fullname}!`);
          window.location.href = "dashboard-user.php";
        }
      } else {
        alert("❌ Login Failed: " + result.message);
      }
    } catch (error) {
      console.error("Login error:", error);
      alert(
        "Network error. Please make sure XAMPP Apache is running and try again.",
      );
    }
  });

  // Check if user data is stored and pre-fill the form
  window.addEventListener("DOMContentLoaded", () => {
    const rememberedUser = localStorage.getItem("rememberedUser");
    if (rememberedUser) {
      const userData = JSON.parse(rememberedUser);
      document.getElementById("email").value = userData.email || "";
      document.getElementById("remember").checked = true;

      // Set account type button based on remembered data
      if (userData.accountType) {
        accountTypeBtns.forEach((btn) => {
          if (btn.dataset.type === userData.accountType) {
            btn.click(); // This will activate the correct button
          }
        });
      }
    }
  });

  // Forgot password link
  document.querySelector(".forgot-password")?.addEventListener("click", (e) => {
    e.preventDefault();
    alert("Password reset functionality coming soon!");
  });
}

// ========================================
// SIGN UP PAGE FUNCTIONALITY
// ========================================

if (document.getElementById("signupForm")) {
  console.log("Sign Up page loaded 🎉");

  // Account type selection
  const accountTypeBtns = document.querySelectorAll(".account-type-btn");
  let currentAccountType = "user";

  accountTypeBtns.forEach((btn) => {
    btn.addEventListener("click", () => {
      // Remove active class from all buttons
      accountTypeBtns.forEach((b) => b.classList.remove("active"));
      // Add active class to clicked button
      btn.classList.add("active");
      // Update current account type
      currentAccountType = btn.dataset.type;

      // Update form styling based on account type
      const signupContainer = document.querySelector(".signup-container");
      if (currentAccountType === "admin") {
        signupContainer.classList.add("admin-mode");
      } else {
        signupContainer.classList.remove("admin-mode");
      }
    });
  });

  // Form submission
  const signupForm = document.getElementById("signupForm");

  signupForm.addEventListener("submit", async (e) => {
    e.preventDefault();

    // Get form data
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

    // Validation checks
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

    // Email validation
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    if (!emailRegex.test(formData.email)) {
      alert("Please enter a valid email address");
      return;
    }

    // Prepare data for API
    const userData = {
      fullname: formData.fullname,
      email: formData.email,
      username: formData.username,
      age: parseInt(formData.age),
      password: formData.password,
      account_type: formData.accountType,
    };

    // Send to API to create user in database
    try {
      const response = await fetch("api/create_user.php", {
        method: "POST",
        headers: {
          "Content-Type": "application/json",
        },
        body: JSON.stringify(userData),
      });

      const result = await response.json();

      if (result.success) {
        // Show success message
        alert(
          `Account Created Successfully!\n\nWelcome to 8BitBrain, ${formData.fullname}!\n\nAccount Type: ${currentAccountType === "admin" ? "Admin" : "Regular User"}\n\nYour account has been saved to the database.\n\nYou can now login with your credentials.`,
        );

        // Redirect to login page
        setTimeout(() => {
          window.location.href = "login.php";
        }, 1000);
      } else {
        alert("Error creating account: " + result.message);
      }
    } catch (error) {
      console.error("Signup error:", error);
      alert(
        "Network error. Please make sure XAMPP Apache is running and try again.",
      );
    }
  });

  // Real-time password match validation
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

  // Username availability check (simulated)
  const usernameInput = document.getElementById("username");
  let usernameTimeout;

  if (usernameInput) {
    usernameInput.addEventListener("input", () => {
      clearTimeout(usernameTimeout);

      if (usernameInput.value.length < 3) {
        return;
      }

      // Simulate checking username availability with a delay
      usernameTimeout = setTimeout(() => {
        // In a real app, this would check against a database
        const takenUsernames = ["admin", "test", "user123", "8bitbrain"];

        if (takenUsernames.includes(usernameInput.value.toLowerCase())) {
          usernameInput.style.borderColor = "#f87171"; // Red
        } else {
          usernameInput.style.borderColor = "#4ade80"; // Green
        }
      }, 500);
    });
  }

  // Terms link
  document.querySelector(".terms-link")?.addEventListener("click", (e) => {
    e.preventDefault();
    alert(
      "Terms & Conditions:\n\n1. You must be at least 13 years old to use this service.\n2. Provide accurate information during registration.\n3. Keep your password secure.\n4. No cheating or exploiting game mechanics.\n5. Be respectful to other players.\n\nFull terms coming soon!",
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
      {
        id: "U001",
        username: "player_one",
        email: "player1@8bitbrain.com",
        role: "student",
        status: "active",
        created: "2024-01-15",
      },
      {
        id: "U002",
        username: "admin_master",
        email: "admin@8bitbrain.com",
        role: "admin",
        status: "active",
        created: "2024-01-10",
      },
      {
        id: "U003",
        username: "quiz_enthusiast",
        email: "user@8bitbrain.com",
        role: "student",
        status: "active",
        created: "2024-01-20",
      },
    ];

    this.quizzes = [
      {
        id: "Q001",
        title: "JavaScript Basics",
        category: "Programming",
        difficulty: "easy",
        questions: 5,
        created: "2024-02-01",
      },
      {
        id: "Q002",
        title: "Advanced CSS",
        category: "Design",
        difficulty: "medium",
        questions: 8,
        created: "2024-02-03",
      },
    ];

    this.feedback = [
      {
        id: "F001",
        userId: "U001",
        message: "Quiz is too difficult",
        status: "pending",
        type: "bug",
      },
      {
        id: "F002",
        userId: "U003",
        message: "Love the retro aesthetic!",
        status: "resolved",
        type: "suggestion",
      },
    ];

    this.activityLog = [
      { action: "System Initialized", timestamp: new Date().toLocaleString() },
    ];

    this.saveToStorage();
  },

  saveToStorage() {
    localStorage.setItem(
      "adminDB",
      JSON.stringify({
        users: this.users,
        quizzes: this.quizzes,
        feedback: this.feedback,
        references: this.references,
        activityLog: this.activityLog,
      }),
    );
  },

  loadFromStorage() {
    const saved = localStorage.getItem("adminDB");
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
      timestamp: new Date().toLocaleString(),
    });
    if (this.activityLog.length > 10) this.activityLog.pop();
    this.saveToStorage();
  },
};

// ============================================
// USER CRUD OPERATIONS
// ============================================

const UserCRUD = {
  create(userData) {
    const newId = "U" + String(AdminDB.users.length + 1).padStart(3, "0");
    const user = {
      id: newId,
      username: userData.username,
      email: userData.email,
      role: userData.role,
      status: userData.status,
      created: new Date().toLocaleDateString(),
    };
    AdminDB.users.push(user);
    AdminDB.logActivity(`User Created: ${user.username}`);
    AdminDB.saveToStorage();
    updateDashboard();
    return user;
  },

  read(userId) {
    return AdminDB.users.find((u) => u.id === userId);
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
    const userIndex = AdminDB.users.findIndex((u) => u.id === userId);
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
    return AdminDB.users.filter(
      (u) =>
        u.username.toLowerCase().includes(q) ||
        u.email.toLowerCase().includes(q) ||
        u.id.includes(q),
    );
  },
};

// ============================================
// LEADERBOARD PAGE - Dynamic Top 50
// ============================================

async function loadLeaderboard(mode = "") {
  const endpoint = mode
    ? `api/get_leaderboard.php?mode=${encodeURIComponent(mode)}`
    : "api/get_leaderboard.php";
  try {
    const response = await fetch(endpoint);
    const result = await response.json();

    const tableBody = document.querySelector("#leaderboardTable tbody");
    const emptyState = document.querySelector("#leaderboardEmpty");

    if (!tableBody || !emptyState) return;

    tableBody.innerHTML = "";

    if (
      !result.success ||
      !Array.isArray(result.data) ||
      result.data.length === 0
    ) {
      emptyState.style.display = "block";
      return;
    }

    emptyState.style.display = "none";

    result.data.forEach((entry) => {
      const row = document.createElement("tr");
      row.innerHTML = `
        <td>${entry.rank}</td>
        <td>${entry.fullname || entry.username}</td>
        <td>${entry.mode}</td>
        <td>${entry.score}</td>
        <td>${entry.correct != null && entry.total != null ? `${entry.correct}/${entry.total}` : "–"}</td>
        <td>${new Date(entry.created_at).toLocaleString()}</td>
      `;
      tableBody.appendChild(row);
    });
  } catch (error) {
    console.error("Could not load leaderboard", error);
  }
}

if (document.querySelector(".leaderboards-section")) {
  const modeSelect = document.getElementById("modeSelect");
  modeSelect?.addEventListener("change", (e) => {
    loadLeaderboard(e.target.value);
  });
  loadLeaderboard();
}

// ============================================
// QUIZ CRUD OPERATIONS
// ============================================

const QuizCRUD = {
  create(quizData) {
    const newId = "Q" + String(AdminDB.quizzes.length + 1).padStart(3, "0");
    const quiz = {
      id: newId,
      title: quizData.title,
      category: quizData.category,
      difficulty: quizData.difficulty,
      questions: quizData.questions || [],
      created: new Date().toLocaleDateString(),
    };
    AdminDB.quizzes.push(quiz);
    AdminDB.logActivity(`Quiz Created: ${quiz.title}`);
    AdminDB.saveToStorage();
    updateDashboard();
    return quiz;
  },

  read(quizId) {
    return AdminDB.quizzes.find((q) => q.id === quizId);
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
    const quizIndex = AdminDB.quizzes.findIndex((q) => q.id === quizId);
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
    return AdminDB.quizzes.filter(
      (quiz) =>
        quiz.title.toLowerCase().includes(q) ||
        quiz.category.toLowerCase().includes(q) ||
        quiz.id.includes(q),
    );
  },
};

// ============================================
// FEEDBACK CRUD OPERATIONS
// ============================================

const FeedbackCRUD = {
  create(feedbackData) {
    const newId = "F" + String(AdminDB.feedback.length + 1).padStart(3, "0");
    const feedback = {
      id: newId,
      userId: feedbackData.userId,
      message: feedbackData.message,
      type: feedbackData.type,
      status: "pending",
      created: new Date().toLocaleString(),
    };
    AdminDB.feedback.push(feedback);
    AdminDB.logActivity(`Feedback Received from ${feedback.userId}`);
    AdminDB.saveToStorage();
    updateDashboard();
    return feedback;
  },

  updateStatus(feedbackId, status) {
    const feedback = AdminDB.feedback.find((f) => f.id === feedbackId);
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
    const index = AdminDB.feedback.findIndex((f) => f.id === feedbackId);
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
    if (status === "all") return AdminDB.feedback;
    return AdminDB.feedback.filter((f) => f.status === status);
  },

  search(query) {
    const q = query.toLowerCase();
    return AdminDB.feedback.filter(
      (f) =>
        f.message.toLowerCase().includes(q) ||
        f.userId.includes(q) ||
        f.id.includes(q),
    );
  },
};

// ============================================
// UI RENDERING
// ============================================

function renderUsersTable(users = UserCRUD.getAll()) {
  const tbody = document.getElementById("users-tbody");

  if (users.length === 0) {
    tbody.innerHTML =
      '<tr><td colspan="6" class="empty-state">No users found</td></tr>';
    return;
  }

  tbody.innerHTML = users
    .map(
      (user) => `
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
    `,
    )
    .join("");
}

function renderQuizzesTable(quizzes = QuizCRUD.getAll()) {
  const tbody = document.getElementById("quizzes-tbody");

  if (quizzes.length === 0) {
    tbody.innerHTML =
      '<tr><td colspan="6" class="empty-state">No quizzes found</td></tr>';
    return;
  }

  tbody.innerHTML = quizzes
    .map(
      (quiz) => `
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
    `,
    )
    .join("");
}

function renderFeedbackList(feedbackList = AdminDB.feedback) {
  const container = document.getElementById("feedback-list");

  if (feedbackList.length === 0) {
    container.innerHTML = '<p class="empty-state">No feedback found</p>';
    return;
  }

  container.innerHTML = feedbackList
    .map(
      (f) => `
        <div class="feedback-item ${f.status}" data-feedback-id="${f.id}">
            <div class="feedback-header">
                <div class="feedback-title">${f.message.substring(0, 50)}...</div>
                <span class="feedback-status ${f.status}">${f.status.toUpperCase()}</span>
            </div>
            <div class="feedback-body">${f.message}</div>
            <div class="feedback-meta">
                <span>From: ${f.userId}</span>
                <span>${f.created}</span>
                ${f.status === "pending" ? `<button class="action-btn-small edit" onclick="resolveFeedback('${f.id}')">Resolve</button>` : ""}
                <button class="action-btn-small delete" onclick="deleteFeedback('${f.id}')">Delete</button>
            </div>
        </div>
    `,
    )
    .join("");
}

function updateDashboard() {
  document.getElementById("stat-users").textContent = AdminDB.users.length;
  document.getElementById("stat-quizzes").textContent = AdminDB.quizzes.length;
  document.getElementById("stat-feedback").textContent =
    AdminDB.feedback.filter((f) => f.status === "pending").length;

  const actList = document.getElementById("activity-list");
  actList.innerHTML = AdminDB.activityLog
    .map(
      (log, idx) => `
        <div class="activity-item">
            <strong>${idx + 1}.</strong> ${log.action}
            <br><small>${log.timestamp}</small>
        </div>
    `,
    )
    .join("");
}

// ============================================
// USER MANAGEMENT INTERACTIONS
// ============================================

let currentEditingUserId = null;

function openUserModal(mode = "create", userId = null) {
  const modal = document.getElementById("user-modal");
  const form = document.getElementById("user-form");
  const title = document.getElementById("modal-title");

  form.reset();
  currentEditingUserId = userId;

  if (mode === "create") {
    title.textContent = "New User";
    document.getElementById("user-id").value = "";
  } else if (mode === "edit" && userId) {
    title.textContent = "Edit User";
    const user = UserCRUD.read(userId);
    if (user) {
      document.getElementById("user-id").value = user.id;
      document.getElementById("user-username").value = user.username;
      document.getElementById("user-email").value = user.email;
      document.getElementById("user-role").value = user.role;
      document.getElementById("user-status").value = user.status;
    }
  }

  modal.classList.remove("hidden");
}

function closeUserModal() {
  document.getElementById("user-modal").classList.add("hidden");
  currentEditingUserId = null;
}

function editUser(userId) {
  openUserModal("edit", userId);
}

function deleteUser(userId) {
  showConfirmModal(`Delete user ${UserCRUD.read(userId).username}?`, () => {
    UserCRUD.delete(userId);
    renderUsersTable();
    showToast("User deleted");
  });
}

function resolveFeedback(feedbackId) {
  FeedbackCRUD.updateStatus(feedbackId, "resolved");
  renderFeedbackList();
  showToast("Feedback resolved");
}

function deleteFeedback(feedbackId) {
  showConfirmModal("Delete this feedback?", () => {
    FeedbackCRUD.delete(feedbackId);
    renderFeedbackList();
    updateDashboard();
    showToast("Feedback deleted");
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
    showToast("Quiz deleted");
  });
}

// ============================================
// FORM HANDLING
// ============================================

document.getElementById("user-form").addEventListener("submit", (e) => {
  e.preventDefault();

  const userData = {
    username: document.getElementById("user-username").value,
    email: document.getElementById("user-email").value,
    role: document.getElementById("user-role").value,
    status: document.getElementById("user-status").value,
  };

  if (currentEditingUserId) {
    UserCRUD.update(currentEditingUserId, userData);
    showToast("User updated");
  } else {
    UserCRUD.create(userData);
    showToast("User created");
  }

  renderUsersTable();
  closeUserModal();
});

document.getElementById("quiz-form").addEventListener("submit", (e) => {
  e.preventDefault();

  const questions = [];
  document.querySelectorAll(".question-block").forEach((block) => {
    const text = block.querySelector(".question-text")?.value || "";
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
        correctAnswer: correctAnswer,
      });
    }
  });

  const quizData = {
    title: document.getElementById("quiz-title").value,
    category: document.getElementById("quiz-category").value,
    difficulty: document.getElementById("quiz-difficulty").value,
    questions: questions,
  };

  QuizCRUD.create(quizData);
  document.getElementById("quiz-form").reset();
  document.getElementById("questions-container").innerHTML = "";
  renderQuizzesTable();
  showToast("Quiz created successfully!");
  switchTab("quizzes");
});

// ============================================
// QUESTION BUILDER
// ============================================

let questionCount = 0;

document.getElementById("add-question-btn").addEventListener("click", () => {
  questionCount++;
  const container = document.getElementById("questions-container");
  const block = document.createElement("div");
  block.className = "question-block";
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

document.getElementById("user-search").addEventListener("input", (e) => {
  const results = UserCRUD.search(e.target.value);
  renderUsersTable(results);
});

document.getElementById("quiz-search").addEventListener("input", (e) => {
  const results = QuizCRUD.search(e.target.value);
  renderQuizzesTable(results);
});

document.getElementById("feedback-search").addEventListener("input", (e) => {
  const results = FeedbackCRUD.search(e.target.value);
  renderFeedbackList(results);
});

// ============================================
// FEEDBACK FILTERING
// ============================================

document.querySelectorAll(".filter-btn").forEach((btn) => {
  btn.addEventListener("click", (e) => {
    document
      .querySelectorAll(".filter-btn")
      .forEach((b) => b.classList.remove("active"));
    e.target.classList.add("active");

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
  document.querySelectorAll(".tab-content").forEach((tab) => {
    tab.classList.remove("active");
  });

  // Remove active from all buttons
  document.querySelectorAll(".tab-btn").forEach((btn) => {
    btn.classList.remove("active");
  });

  // Show selected tab
  const tabId = tabName + "-tab";
  const tab = document.getElementById(tabId);
  if (tab) {
    tab.classList.add("active");
  }

  // Highlight active button
  const btn = document.querySelector(`[data-tab="${tabName}"]`);
  if (btn) {
    btn.classList.add("active");
  }
}

document.querySelectorAll(".tab-btn").forEach((btn) => {
  btn.addEventListener("click", () => {
    switchTab(btn.dataset.tab);
  });
});

// ============================================
// ACTION SIDEBAR BUTTONS
// ============================================

document.getElementById("create-btn").addEventListener("click", () => {
  const activeTab = document.querySelector(".tab-content.active").id;

  if (activeTab === "users-tab") {
    openUserModal("create");
  } else if (activeTab === "add-quiz-tab") {
    document.getElementById("quiz-form").reset();
  }
});

document.getElementById("update-btn").addEventListener("click", () => {
  const activeTab = document.querySelector(".tab-content.active").id;

  if (activeTab === "users-tab") {
    const selectedRow = document.querySelector("tr[data-user-id]");
    if (selectedRow) {
      editUser(selectedRow.dataset.userId);
    } else {
      showToast("Select a user to edit", "warning");
    }
  }
});

document.getElementById("delete-btn").addEventListener("click", () => {
  const activeTab = document.querySelector(".tab-content.active").id;

  if (activeTab === "users-tab") {
    const selectedRow = document.querySelector("tr[data-user-id]");
    if (selectedRow) {
      deleteUser(selectedRow.dataset.userId);
    } else {
      showToast("Select a user to delete", "warning");
    }
  }
});

// ============================================
// MODAL INTERACTIONS
// ============================================

document.querySelectorAll(".modal-close").forEach((btn) => {
  btn.addEventListener("click", () => {
    closeUserModal();
    document.getElementById("confirm-modal").classList.add("hidden");
  });
});

document.querySelectorAll(".modal-close-btn").forEach((btn) => {
  btn.addEventListener("click", () => {
    closeUserModal();
    document.getElementById("confirm-modal").classList.add("hidden");
  });
});

document.querySelectorAll(".modal").forEach((modal) => {
  modal.addEventListener("click", (e) => {
    if (e.target === modal) {
      modal.classList.add("hidden");
    }
  });
});

// ============================================
// CONFIRMATION MODAL
// ============================================

let confirmCallback = null;

function showConfirmModal(message, callback) {
  const modal = document.getElementById("confirm-modal");
  document.getElementById("confirm-message").textContent = message;
  confirmCallback = callback;
  modal.classList.remove("hidden");
}

document.getElementById("confirm-yes").addEventListener("click", () => {
  if (confirmCallback) confirmCallback();
  document.getElementById("confirm-modal").classList.add("hidden");
});

// ============================================
// IMPORT FUNCTIONALITY
// ============================================

document.getElementById("import-btn").addEventListener("click", () => {
  const file = document.getElementById("quiz-file").files[0];
  if (!file) {
    showToast("Select a file first", "warning");
    return;
  }

  const reader = new FileReader();
  reader.onload = (e) => {
    try {
      const data = JSON.parse(e.target.result);
      const quizzes = Array.isArray(data) ? data : [data];

      quizzes.forEach((q) => {
        QuizCRUD.create({
          title: q.title,
          category: q.category,
          difficulty: q.difficulty || "medium",
          questions: q.questions || [],
        });
      });

      renderQuizzesTable();
      document.getElementById("quiz-file").value = "";
      showToast(`Imported ${quizzes.length} quiz(zes)!`);
    } catch (err) {
      showToast("Error parsing file: " + err.message, "error");
    }
  };
  reader.readAsText(file);
});

// ============================================
// CAROUSEL FUNCTIONALITY
// ============================================

let currentSlide = 0;
const slides = document.querySelectorAll(".slide");
const wrapper = document.querySelector(".slides-wrapper");

function showSlide(index) {
  currentSlide = index;
  wrapper.style.transform = `translateX(-${currentSlide * 100}%)`;
}

setInterval(() => {
  currentSlide++;

  wrapper.style.transform = `translateX(-${currentSlide * 100}%)`;

  if (currentSlide === slides.length - 1) {
    setTimeout(() => {
      wrapper.style.transition = "none";
      currentSlide = 0;
      wrapper.style.transform = `translateX(0%)`;

      setTimeout(() => {
        wrapper.style.transition = "transform 0.5s ease";
      }, 200);
    }, 700);
  }
}, 4000);

// ============================================
// TOAST NOTIFICATIONS
// ============================================

function showToast(message, type = "success") {
  const toast = document.createElement("div");
  toast.className = `toast ${type}`;
  toast.textContent = message;
  toast.style.cssText = `
        position: fixed;
        bottom: 20px;
        right: 20px;
        padding: 15px 20px;
        background: ${type === "success" ? "#39ff14" : type === "error" ? "#ff1744" : "#ffd700"};
        color: #1a0b2e;
        border: 2px solid;
        border-color: ${type === "success" ? "#39ff14" : type === "error" ? "#ff1744" : "#ffd700"};
        font-family: 'Courier New', monospace;
        font-size: 11px;
        font-weight: bold;
        z-index: 9999;
        animation: toastSlide 0.3s ease;
        box-shadow: 0 0 15px currentColor;
    `;

  document.body.appendChild(toast);

  setTimeout(() => {
    toast.style.animation = "toastSlide 0.3s ease reverse";
    setTimeout(() => toast.remove(), 300);
  }, 3000);
}

// ============================================
// INITIALIZATION
// ============================================

document.addEventListener("DOMContentLoaded", () => {
  // Add animation styles
  const style = document.createElement("style");
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

  console.log("✨ 8BitBrain Admin Dashboard Ready!");
});
