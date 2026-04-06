<?php
session_start();
if (!isset($_SESSION['logged_in']) || $_SESSION['account_type'] !== 'admin') {
    header("Location: index.php");
    exit();
}
?>
<!doctype html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>8BitBrain - Admin Dashboard</title>
    <link rel="stylesheet" href="style.css" />
    <link rel="icon" href="imgs/Sans_Favi.png" type="image/png" />
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=Bitcount+Single:wght@100..900&display=swap" rel="stylesheet" />
    <style>
      /* password strength bar */
      .pwd-strength-bar {
        height: 4px;
        border-radius: 4px;
        margin-top: 6px;
        transition: width .3s, background .3s;
        width: 0%;
      }
      .pwd-strength-label {
        font-size: 11px;
        margin-top: 4px;
        font-family: 'Courier New', monospace;
      }
      /* show/hide password toggle */
      .pwd-wrap {
        position: relative;
      }
      .pwd-wrap input { padding-right: 42px !important; }
      .pwd-toggle {
        position: absolute;
        right: 12px;
        top: 50%;
        transform: translateY(-50%);
        background: none;
        border: none;
        color: rgba(255,255,255,.5);
        cursor: pointer;
        font-size: 16px;
        padding: 0;
        line-height: 1;
        transition: color .2s;
      }
      .pwd-toggle:hover { color: #fff; }
    </style>
  </head>
  <body>
    <div class="bg"></div>
    <header class="header">
      <a href="index.php" class="logo">
        8BitBrain <img src="imgs/Sans_Favi.png" alt="logo" class="logoimg" />
      </a>
      <?php include("navbar.php"); ?>
    </header>

  <main class="admin-main">

    <!-- Banner -->
    <section class="banner-section">
      <div class="carousel-container">
        <div class="carousel">
          <div class="slides-wrapper">
            <div class="slide"><div class="slide-content">🎮 Admin Dashboard v1.0</div></div>
            <div class="slide"><div class="slide-content">📊 Manage Users &amp; Quizzes</div></div>
            <div class="slide"><div class="slide-content">✨ Track Feedback &amp; Stats</div></div>
            <div class="slide"><div class="slide-content">🔗 Manage Quiz References</div></div>
            <div class="slide"><div class="slide-content">⚡ Full CRUD Operations</div></div>
            <div class="slide"><div class="slide-content">🎮 Admin Dashboard v1.0</div></div>
          </div>
        </div>
      </div>
    </section>

    <!-- Content -->
    <section class="content-section">
      <div class="tabs-container">
        <div class="tabs-header">
          <button class="tab-btn active" data-tab="dashboard">📊 Dashboard</button>
          <button class="tab-btn" data-tab="users">👥 Users</button>
          <button class="tab-btn" data-tab="add-quiz">➕ Add Quiz</button>
          <button class="tab-btn" data-tab="quizzes">📝 Quizzes</button>
          <button class="tab-btn" data-tab="feedback">💬 Feedback</button>
          <button class="tab-btn" data-tab="references">🔗 References</button>
        </div>

        <div class="content-area">

          <!-- Tab: Dashboard -->
          <div class="tab-content active" id="dashboard-tab">
            <h2 class="tab-title">Dashboard Overview</h2>
            <div class="stats-grid">
              <div class="stat-card total-quizzes">
                <div class="stat-icon">📝</div>
                <div class="stat-label">Total Quizzes</div>
                <div class="stat-value" id="stat-quizzes">0</div>
              </div>
              <div class="stat-card total-users">
                <div class="stat-icon">👥</div>
                <div class="stat-label">Total Users</div>
                <div class="stat-value" id="stat-users">0</div>
              </div>
              <div class="stat-card pending-feedback">
                <div class="stat-icon">💬</div>
                <div class="stat-label">Pending Feedback</div>
                <div class="stat-value" id="stat-feedback">0</div>
              </div>
            </div>
            <div class="activity-log">
              <h3>Recent Activity</h3>
              <div id="activity-list" class="activity-items"><p class="empty-state">No activity yet</p></div>
            </div>
          </div>

          <!-- Tab: Users -->
          <div class="tab-content" id="users-tab">
            <h2 class="tab-title">User Management</h2>
            <p style="font-size:12px;color:rgba(255,255,255,.45);margin-bottom:14px;">
              💡 Click a row to select it, then use the <strong>UPDATE</strong> or <strong>DELETE</strong> sidebar buttons.
            </p>
            <div class="table-wrapper">
              <input type="text" id="user-search" class="search-input" placeholder="Search users...">
              <table class="data-table">
                <thead>
                  <tr><th>ID</th><th>Username</th><th>Email</th><th>Role</th><th>Actions</th></tr>
                </thead>
                <tbody id="users-tbody">
                  <tr><td colspan="5" class="empty-state">No users found</td></tr>
                </tbody>
              </table>
            </div>
          </div>

          <!-- Tab: Add Quiz -->
          <div class="tab-content" id="add-quiz-tab">
            <h2 class="tab-title">Add New Quiz</h2>
            <form id="quiz-form" class="quiz-form">
              <div class="form-group">
                <label for="quiz-title">Quiz Title *</label>
                <input type="text" id="quiz-title" required placeholder="e.g. Philippine History 101">
              </div>
              <div class="form-group">
                <label for="quiz-category">Category *</label>
                <input type="text" id="quiz-category" required placeholder="e.g. History, Science, Math">
              </div>
              <div class="form-group">
                <label for="quiz-difficulty">Difficulty *</label>
                <select id="quiz-difficulty" required>
                  <option value="">Select Difficulty</option>
                  <option value="easy">Easy</option>
                  <option value="medium">Medium</option>
                  <option value="hard">Hard</option>
                </select>
              </div>
              <div class="form-group">
                <label for="quiz-mode">Game Mode *</label>
                <select id="quiz-mode" required>
                  <option value="single_player">Single Player</option>
                  <option value="timed_quiz">Timed Quiz</option>
                  <option value="ranked_quiz">Ranked Quiz</option>
                  <option value="memory_match">Memory Match</option>
                  <option value="endless_quiz">Endless Quiz</option>
                </select>
              </div>
              <fieldset style="border:2px dashed rgba(0,217,255,.4);border-radius:8px;padding:20px;margin-bottom:20px;">
                <legend style="color:#00d9ff;padding:0 10px;font-size:13px;font-weight:bold;letter-spacing:1px;">🔗 REFERENCE (Optional)</legend>
                <div class="form-group">
                  <label for="quiz-reference-url">Reference URL</label>
                  <input type="url" id="quiz-reference-url" placeholder="https://example.com/study-material">
                </div>
                <div class="form-group">
                  <label for="quiz-reference-text">Reference Description</label>
                  <input type="text" id="quiz-reference-text" placeholder="e.g. Chapter 5 of textbook">
                </div>
                <div class="form-group">
                  <label for="quiz-reference-type">Reference Type</label>
                  <select id="quiz-reference-type">
                    <option value="url">URL / Website</option>
                    <option value="book">Book / Textbook</option>
                    <option value="article">Article</option>
                    <option value="video">Video</option>
                    <option value="document">Document</option>
                    <option value="other">Other</option>
                  </select>
                </div>
              </fieldset>
              <div id="questions-container"></div>
              <button type="button" class="btn-admin btn-secondary" id="add-question-btn">+ Add Question</button>
              <button type="submit" class="btn-admin btn-primary">Create Quiz</button>
            </form>
          </div>

          <!-- Tab: Quiz Management -->
          <div class="tab-content" id="quizzes-tab">
            <h2 class="tab-title">Quiz Management</h2>
            <p style="font-size:12px;color:rgba(255,255,255,.45);margin-bottom:14px;">
              💡 Click a row to select it, then use the sidebar buttons — or use the inline ✏️ / 🗑️ buttons.
            </p>
            <div class="import-section">
              <h3>📂 Import Quizzes from CSV</h3>
              <div class="import-controls">
                <input type="file" id="quiz-file" accept=".csv" class="file-input">
                <button type="button" class="btn-admin btn-secondary" id="import-btn">Import File</button>
              </div>
              <div style="margin-top:10px;font-size:11px;color:rgba(255,255,255,.4);line-height:1.7;">
                <strong style="color:#00d9ff;">Column order:</strong>
                <code style="color:#e0e0e0;">title, category, difficulty, mode, question, optionA, optionB, optionC, optionD, correct</code>
              </div>
            </div>
            <div class="table-wrapper">
              <input type="text" id="quiz-search" class="search-input" placeholder="Search quizzes...">
              <table class="data-table">
                <thead>
                  <tr><th>ID</th><th>Title</th><th>Category</th><th>Difficulty</th><th>Questions</th><th>Actions</th></tr>
                </thead>
                <tbody id="quizzes-tbody">
                  <tr><td colspan="6" class="empty-state">No quizzes found</td></tr>
                </tbody>
              </table>
            </div>
          </div>

          <!-- Tab: Feedback -->
          <div class="tab-content" id="feedback-tab">
            <h2 class="tab-title">Feedback Management</h2>
            <div class="filter-buttons">
              <button class="filter-btn active" data-filter="all">All</button>
              <button class="filter-btn" data-filter="pending">Pending</button>
              <button class="filter-btn" data-filter="resolved">Resolved</button>
            </div>
            <div class="search-bar">
              <input type="text" id="feedback-search" class="search-input" placeholder="Search feedback...">
            </div>
            <div id="feedback-list" class="feedback-items"><p class="empty-state">No feedback found</p></div>
          </div>

          <!-- Tab: References -->
          <div class="tab-content" id="references-tab">
            <h2 class="tab-title">Quiz References</h2>
            <div class="table-wrapper">
              <input type="text" id="reference-search" class="search-input" placeholder="Search references...">
              <table class="data-table">
                <thead>
                  <tr><th>Quiz Name</th><th>Category</th><th>Difficulty</th><th>Mode</th><th>Reference</th><th>Type</th><th>Actions</th></tr>
                </thead>
                <tbody id="references-tbody">
                  <tr><td colspan="7" class="empty-state">No references found</td></tr>
                </tbody>
              </table>
            </div>
          </div>

        </div><!-- end content-area -->
      </div><!-- end tabs-container -->

      <!-- Action Sidebar -->
      <aside class="action-sidebar">
        <div class="button-group">
          <button class="action-btn create-btn" id="create-btn">
            <span class="btn-icon">➕</span><span class="btn-label">CREATE</span>
          </button>
          <button class="action-btn update-btn" id="update-btn">
            <span class="btn-icon">✏️</span><span class="btn-label">UPDATE</span>
          </button>
          <button class="action-btn delete-btn" id="delete-btn">
            <span class="btn-icon">🗑️</span><span class="btn-label">DELETE</span>
          </button>
        </div>
      </aside>
    </section>

    <!-- ══════════════════════════════════
         USER MODAL — Create & Edit
    ══════════════════════════════════ -->
    <div id="user-modal" class="modal hidden">
      <div class="modal-content">
        <div class="modal-header">
          <h3 id="modal-title">New User</h3>
          <button class="modal-close">&times;</button>
        </div>
        <form id="user-form">
          <input type="hidden" id="user-id">

          <div class="form-group">
            <label for="user-fullname">Full Name *</label>
            <input type="text" id="user-fullname" required placeholder="Enter full name">
          </div>

          <div class="form-group">
            <label for="user-username">Username *</label>
            <input type="text" id="user-username" required placeholder="Enter username" minlength="3">
          </div>

          <div class="form-group">
            <label for="user-email">Email *</label>
            <input type="email" id="user-email" required placeholder="Enter email address">
          </div>

          <div class="form-group">
            <label for="user-age">Age *</label>
            <input type="number" id="user-age" required placeholder="Enter age" min="1" max="120" value="18">
          </div>

          <div class="form-group">
            <label for="user-role">Role *</label>
            <select id="user-role" required>
              <option value="">Select Role</option>
              <option value="user">User</option>
              <option value="admin">Admin</option>
            </select>
          </div>

          <!-- Password fields — shown for CREATE, optional for EDIT -->
          <div id="password-section">
            <div class="form-group">
              <label for="user-password" id="password-label">Password *</label>
              <div class="pwd-wrap">
                <input type="password" id="user-password" placeholder="Enter password (min 6 characters)" minlength="6">
                <button type="button" class="pwd-toggle" onclick="togglePwd('user-password', this)">👁</button>
              </div>
              <!-- Strength bar -->
              <div class="pwd-strength-bar" id="pwdStrengthBar"></div>
              <div class="pwd-strength-label" id="pwdStrengthLabel"></div>
            </div>

            <div class="form-group" id="confirm-pwd-group">
              <label for="user-password-confirm">Confirm Password *</label>
              <div class="pwd-wrap">
                <input type="password" id="user-password-confirm" placeholder="Re-enter password">
                <button type="button" class="pwd-toggle" onclick="togglePwd('user-password-confirm', this)">👁</button>
              </div>
              <div id="pwd-match-msg" style="font-size:11px;margin-top:4px;font-family:'Courier New',monospace;"></div>
            </div>
          </div>

          <div class="modal-actions">
            <button type="submit" class="btn-admin btn-primary" id="user-submit-btn">Save User</button>
            <button type="button" class="btn-admin btn-secondary modal-close-btn">Cancel</button>
          </div>
        </form>
      </div>
    </div>

    <!-- ══════════════════════════════════
         EDIT QUIZ MODAL
    ══════════════════════════════════ -->
    <div id="edit-quiz-modal" class="modal hidden">
      <div class="modal-content" style="max-width:720px;">
        <div class="modal-header">
          <h3 id="edit-quiz-modal-title">Edit Quiz</h3>
          <button class="modal-close" onclick="closeEditQuizModal()">&times;</button>
        </div>
        <form id="edit-quiz-form">
          <input type="hidden" id="edit-quiz-id">
          <div style="display:grid;grid-template-columns:1fr 1fr;gap:14px;margin-bottom:4px;">
            <div class="form-group">
              <label for="edit-quiz-title">Quiz Title *</label>
              <input type="text" id="edit-quiz-title" required placeholder="Quiz title">
            </div>
            <div class="form-group">
              <label for="edit-quiz-category">Category *</label>
              <input type="text" id="edit-quiz-category" required placeholder="Category">
            </div>
            <div class="form-group">
              <label for="edit-quiz-difficulty">Difficulty *</label>
              <select id="edit-quiz-difficulty" required>
                <option value="easy">Easy</option>
                <option value="medium">Medium</option>
                <option value="hard">Hard</option>
              </select>
            </div>
            <div class="form-group">
              <label for="edit-quiz-mode">Game Mode *</label>
              <select id="edit-quiz-mode" required>
                <option value="single_player">Single Player</option>
                <option value="timed_quiz">Timed Quiz</option>
                <option value="ranked_quiz">Ranked Quiz</option>
                <option value="memory_match">Memory Match</option>
                <option value="endless_quiz">Endless Quiz</option>
              </select>
            </div>
          </div>
          <div style="margin:16px 0 8px;font-size:12px;font-weight:800;color:#00d9ff;letter-spacing:1px;text-transform:uppercase;">Questions</div>
          <div id="edit-questions-container"></div>
          <button type="button" class="btn-admin btn-secondary" id="edit-add-question-btn" style="margin-bottom:16px;">+ Add Question</button>
          <div class="modal-actions">
            <button type="submit" class="btn-admin btn-primary">💾 Save Changes</button>
            <button type="button" class="btn-admin btn-secondary modal-close-btn" onclick="closeEditQuizModal()">Cancel</button>
          </div>
        </form>
      </div>
    </div>

    <!-- Confirm Modal -->
    <div id="confirm-modal" class="modal hidden">
      <div class="modal-content modal-confirm">
        <div class="modal-header"><h3>Confirm Action</h3></div>
        <p id="confirm-message">Are you sure?</p>
        <div class="modal-actions">
          <button type="button" class="btn-admin btn-primary" id="confirm-yes">Confirm</button>
          <button type="button" class="btn-admin btn-secondary modal-close-btn">Cancel</button>
        </div>
      </div>
    </div>

  </main>

  <script src="script.js"></script>
  <script src="admin-dashboard.js"></script>
  </body>
</html>
