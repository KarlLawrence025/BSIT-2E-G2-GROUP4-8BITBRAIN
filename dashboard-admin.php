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
    <link
      href="https://fonts.googleapis.com/css2?family=Bitcount+Single:wght@100..900&display=swap"
      rel="stylesheet"
    />
  </head>

  <body>
    <div class="bg"></div>

    <header class="header">
      <a href="index.php" class="logo">
        8BitBrain
        <img src="imgs/Sans_Favi.png" alt="logo" class="logoimg" />
      </a>
      <?php include("navbar.php"); ?>
    </header>

  <main class="admin-main">
    <!-- Game Banner Section -->
    <section class="banner-section">
      <div class="carousel-container">
        <div class="carousel">
          <div class="slides-wrapper">
            <div class="slide"><div class="slide-content">🎮 Admin Dashboard v1.0</div></div>
            <div class="slide"><div class="slide-content">📊 Manage Users &amp; Quizzes</div></div>
            <div class="slide"><div class="slide-content">✨ Track Feedback &amp; Stats</div></div>
            <div class="slide"><div class="slide-content">🔧 Configure Quiz Resources</div></div>
            <div class="slide"><div class="slide-content">⚡ Power-Up Your Platform</div></div>
            <!-- Duplicate first slide for seamless loop -->
            <div class="slide"><div class="slide-content">🎮 Admin Dashboard v1.0</div></div>
          </div>
        </div>
      </div>
    </section>

    <!-- Content Section -->
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

          <!-- Tab 1: Dashboard Overview -->
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
              <div id="activity-list" class="activity-items">
                <p class="empty-state">No activity yet</p>
              </div>
            </div>
          </div>

          <!-- Tab 2: User Management -->
          <div class="tab-content" id="users-tab">
            <h2 class="tab-title">User Management</h2>
            <div class="table-wrapper">
              <input type="text" id="user-search" class="search-input" placeholder="Search users...">
              <table class="data-table">
                <thead>
                  <tr>
                    <th>ID</th>
                    <th>Username</th>
                    <th>Email</th>
                    <th>Role</th>
                    <th>Actions</th>
                  </tr>
                </thead>
                <tbody id="users-tbody">
                  <tr><td colspan="5" class="empty-state">No users found</td></tr>
                </tbody>
              </table>
            </div>
          </div>

          <!-- Tab 3: Add Quiz -->
          <div class="tab-content" id="add-quiz-tab">
            <h2 class="tab-title">Add New Quiz</h2>
            <form id="quiz-form" class="quiz-form">
              <div class="form-group">
                <label for="quiz-title">Quiz Title</label>
                <input type="text" id="quiz-title" required placeholder="Enter quiz title">
              </div>
              <div class="form-group">
                <label for="quiz-category">Category</label>
                <input type="text" id="quiz-category" required placeholder="Enter category">
              </div>
              <div class="form-group">
                <label for="quiz-difficulty">Difficulty</label>
                <select id="quiz-difficulty" required>
                  <option value="">Select Difficulty</option>
                  <option value="easy">Easy</option>
                  <option value="medium">Medium</option>
                  <option value="hard">Hard</option>
                </select>
              </div>
              <!-- FIX: Mode field added so quizzes appear in the correct game mode -->
              <div class="form-group">
                <label for="quiz-mode">Game Mode</label>
                <select id="quiz-mode" required>
                  <option value="single_player">Single Player</option>
                  <option value="timed_quiz">Timed Quiz</option>
                  <option value="ranked_quiz">Ranked Quiz</option>
                  <option value="memory_match">Memory Match</option>
                  <option value="endless_quiz">Endless Quiz</option>
                </select>
              </div>
              <div class="form-group">
                <label for="quiz-reference">Reference Link (Optional)</label>
                <input type="url" id="quiz-reference" placeholder="https://example.com/reference">
                <small style="color: #38bdf8; display: block; margin-top: 5px;">
                  Add a reference URL for this quiz (e.g., study material, documentation, article)
                </small>
              </div>
              <div id="questions-container"></div>
              <button type="button" class="btn-admin btn-secondary" id="add-question-btn">+ Add Question</button>
              <button type="submit" class="btn-admin btn-primary">Create Quiz</button>
            </form>
          </div>

          <!-- Tab 4: Quiz Management -->
          <div class="tab-content" id="quizzes-tab">
            <h2 class="tab-title">Quiz Management &amp; Import</h2>
            <div class="import-section">
              <h3>Import Quizzes</h3>
              <div class="import-controls">
                <input type="file" id="quiz-file" accept=".json,.csv" class="file-input">
                <button type="button" class="btn-admin btn-secondary" id="import-btn">Import File</button>
              </div>
            </div>
            <div class="table-wrapper">
              <input type="text" id="quiz-search" class="search-input" placeholder="Search quizzes...">
              <table class="data-table">
                <thead>
                  <tr>
                    <th>ID</th>
                    <th>Title</th>
                    <th>Category</th>
                    <th>Difficulty</th>
                    <th>Questions</th>
                    <th>Actions</th>
                  </tr>
                </thead>
                <tbody id="quizzes-tbody">
                  <tr><td colspan="6" class="empty-state">No quizzes found</td></tr>
                </tbody>
              </table>
            </div>
          </div>

          <!-- Tab 5: Feedback Management -->
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
            <div id="feedback-list" class="feedback-items">
              <p class="empty-state">No feedback found</p>
            </div>
          </div>

          <!-- Tab 6: Quiz References -->
          <div class="tab-content" id="references-tab">
            <h2 class="tab-title">Quiz References</h2>
            <div class="table-wrapper">
              <input type="text" id="reference-search" class="search-input" placeholder="Search references...">
              <table class="data-table">
                <thead>
                  <tr>
                    <th>Quiz ID</th>
                    <th>Question</th>
                    <th>Reference URL/Text</th>
                    <th>Type</th>
                    <th>Actions</th>
                  </tr>
                </thead>
                <tbody id="references-tbody">
                  <tr><td colspan="5" class="empty-state">No references found</td></tr>
                </tbody>
              </table>
            </div>
          </div>

        </div><!-- end content-area -->
      </div><!-- end tabs-container -->

      <!-- Action Buttons Sidebar -->
      <aside class="action-sidebar">
        <div class="button-group">
          <button class="action-btn create-btn" id="create-btn">
            <span class="btn-icon">➕</span>
            <span class="btn-label">CREATE</span>
          </button>
          <button class="action-btn update-btn" id="update-btn">
            <span class="btn-icon">✏️</span>
            <span class="btn-label">UPDATE</span>
          </button>
          <button class="action-btn delete-btn" id="delete-btn">
            <span class="btn-icon">🗑️</span>
            <span class="btn-label">DELETE</span>
          </button>
        </div>
      </aside>
    </section>

    <!-- User Modal -->
    <div id="user-modal" class="modal hidden">
      <div class="modal-content">
        <div class="modal-header">
          <h3 id="modal-title">New User</h3>
          <button class="modal-close">&times;</button>
        </div>
        <form id="user-form">
          <div class="form-group">
            <label for="user-id">User ID</label>
            <input type="text" id="user-id" readonly>
          </div>
          <div class="form-group">
            <label for="user-username">Username</label>
            <input type="text" id="user-username" required>
          </div>
          <div class="form-group">
            <label for="user-email">Email</label>
            <input type="email" id="user-email" required>
          </div>
          <!-- FIX: role options match DB enum ('user','admin') — removed 'student' -->
          <div class="form-group">
            <label for="user-role">Role</label>
            <select id="user-role" required>
              <option value="">Select Role</option>
              <option value="user">User</option>
              <option value="admin">Admin</option>
            </select>
          </div>
          <div class="modal-actions">
            <button type="submit" class="btn-admin btn-primary">Save</button>
            <button type="button" class="btn-admin btn-secondary modal-close-btn">Cancel</button>
          </div>
        </form>
      </div>
    </div>

    <!-- Confirm Modal -->
    <div id="confirm-modal" class="modal hidden">
      <div class="modal-content modal-confirm">
        <div class="modal-header">
          <h3>Confirm Action</h3>
        </div>
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
