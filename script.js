// ========================================
// MAIN SCRIPT - GENERAL WEBSITE CODE ONLY
// (Admin dashboard uses admin-dashboard.js)
// ========================================

console.log("JS loaded 🚀");

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
    { threshold: 0.5 }
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
      const response = await fetch('api/login.php', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json'
        },
        body: JSON.stringify({
          email: formData.email,
          password: formData.password
        })
      });

      const result = await response.json();

      if (result.success) {
        const user = result.user;
        
        // CHECK: Account type from database must match selected type
        if (user.account_type !== selectedAccountType) {
          alert(
            `❌ Account Type Mismatch!\n\n` +
            `You selected: ${selectedAccountType === 'admin' ? 'Admin' : 'Regular User'}\n` +
            `But this account is: ${user.account_type === 'admin' ? 'Admin' : 'Regular User'}\n\n` +
            `Please select the correct account type and try again.`
          );
          return;
        }
        
        // Store user data in session
        sessionStorage.setItem('currentUser', JSON.stringify({
          id: user.id,
          name: user.fullname,
          email: user.email,
          username: user.username,
          accountType: user.account_type
        }));

        // Store in localStorage if "remember me" is checked
        if (formData.remember) {
          localStorage.setItem('rememberedUser', JSON.stringify({
            email: user.email,
            name: user.fullname,
            accountType: user.account_type
          }));
        }

        // Redirect based on account type
        if (user.account_type === "admin") {
          alert(`✅ Admin Login Successful!\nWelcome, ${user.fullname}!`);
          window.location.href = "dashboard-admin.html";
        } else {
          alert(`✅ Login Successful!\nWelcome, ${user.fullname}!`);
          window.location.href = "modes.html";
        }
      } else {
        alert("❌ Login Failed: " + result.message);
      }
    } catch (error) {
      console.error("Login error:", error);
      alert("Network error. Please make sure XAMPP Apache is running and try again.");
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
      account_type: formData.accountType
    };

    // Send to API to create user in database
    try {
      const response = await fetch('api/create_user.php', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json'
        },
        body: JSON.stringify(userData)
      });

      const result = await response.json();

      if (result.success) {
        // Show success message
        alert(
          `Account Created Successfully!\n\nWelcome to 8BitBrain, ${formData.fullname}!\n\nAccount Type: ${currentAccountType === "admin" ? "Admin" : "Regular User"}\n\nYour account has been saved to the database.\n\nYou can now login with your credentials.`
        );

        // Redirect to login page
        setTimeout(() => {
          window.location.href = "login.html";
        }, 1000);
      } else {
        alert("Error creating account: " + result.message);
      }
    } catch (error) {
      console.error("Signup error:", error);
      alert("Network error. Please make sure XAMPP Apache is running and try again.");
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
      "Terms & Conditions:\n\n1. You must be at least 13 years old to use this service.\n2. Provide accurate information during registration.\n3. Keep your password secure.\n4. No cheating or exploiting game mechanics.\n5. Be respectful to other players.\n\nFull terms coming soon!"
    );
  });
}
