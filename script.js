console.log("JS loaded 🚀");

document.addEventListener("DOMContentLoaded", () => {
  const startBtn = document.querySelector(".hero button");
  
  if (startBtn) {
    startBtn.addEventListener("click", (e) => {
      const modes = ["single_player", "timed_quiz", "ranked_quiz", "memory_match", "endless_quiz"];
      const randomMode = modes[Math.floor(Math.random() * modes.length)];
      
      localStorage.setItem("selectedMode", randomMode);
      
      if (startBtn.tagName !== 'A') {
          window.location.href = "quiz.php";
      }
    });
  }
});

document.addEventListener("DOMContentLoaded", () => {
  document.addEventListener("click", (e) => {
    const target = e.target.closest(".mode-card, button, a");
    if (!target) return;

    if (target.closest(".hero")) return;

    const text = target.textContent.toLowerCase();
    const id = (target.id || "").toLowerCase();
    
    let exactMode = null;

    if (text.includes("single") || id.includes("single")) exactMode = "single_player";
    else if (text.includes("timed") || id.includes("timed")) exactMode = "timed_quiz";
    else if (text.includes("ranked") || id.includes("ranked")) exactMode = "ranked_quiz";
    else if (text.includes("memory") || id.includes("memory")) exactMode = "memory_match";
    else if (text.includes("endless") || id.includes("endless")) exactMode = "endless_quiz";

    if (exactMode) {
      e.preventDefault(); 
      localStorage.setItem("selectedMode", exactMode);
      window.location.href = "quiz.php";
    }
  });
});


// ========================================
// NAVIGATION - Active link highlighting
// ========================================

document.addEventListener("DOMContentLoaded", () => {
  document.querySelectorAll(".navbar a").forEach((link) => {
    if (link.href === window.location.href) link.classList.add("active");
  });
});

// ========================================
// ABOUT PAGE - Animated counters
// ========================================

function animateCounter(element) {
  const target    = parseInt(element.getAttribute("data-target"));
  const increment = target / (2000 / 16);
  let current     = 0;
  const timer = setInterval(() => {
    current += increment;
    if (current >= target) { element.textContent = target.toLocaleString(); clearInterval(timer); }
    else { element.textContent = Math.floor(current).toLocaleString(); }
  }, 16);
}

document.addEventListener("DOMContentLoaded", () => {
  const statsSection = document.querySelector(".stats-section");
  if (statsSection) {
    new IntersectionObserver((entries) => {
      entries.forEach((entry) => {
        if (entry.isIntersecting) {
          entry.target.querySelectorAll(".stat-number").forEach(animateCounter);
        }
      });
    }, { threshold: 0.5 }).observe(statsSection);
  }

  // Team card hover
  document.querySelectorAll(".team-card").forEach((card) => {
    card.addEventListener("mouseenter", () => { const img = card.querySelector("img"); if (img) img.style.transform = "scale(1.1)"; });
    card.addEventListener("mouseleave", () => { const img = card.querySelector("img"); if (img) img.style.transform = "scale(1)"; });
  });
});

// ========================================
// LOGIN PAGE
// ========================================

document.addEventListener("DOMContentLoaded", () => {
  if (!document.getElementById("loginForm")) return;

  const accountTypeBtns   = document.querySelectorAll(".account-type-btn");
  let selectedAccountType = "user";

  accountTypeBtns.forEach((btn) => {
    btn.addEventListener("click", () => {
      accountTypeBtns.forEach((b) => b.classList.remove("active"));
      btn.classList.add("active");
      selectedAccountType = btn.dataset.type;
      document.querySelector(".login-container")?.classList.toggle("admin-mode", selectedAccountType === "admin");
    });
  });

  // Pre-fill remembered user
  const remembered = localStorage.getItem("rememberedUser");
  if (remembered) {
    const u = JSON.parse(remembered);
    const emailEl = document.getElementById("email");
    const remEl   = document.getElementById("remember");
    if (emailEl) emailEl.value   = u.email || "";
    if (remEl)   remEl.checked   = true;
    if (u.accountType) {
      accountTypeBtns.forEach((btn) => { if (btn.dataset.type === u.accountType) btn.click(); });
    }
  }

  document.getElementById("loginForm").addEventListener("submit", async (e) => {
    e.preventDefault();
    const email    = document.getElementById("email").value;
    const password = document.getElementById("password").value;
    const remember = document.getElementById("remember").checked;

    if (password.length < 6) { alert("Password must be at least 6 characters long"); return; }

    try {
      const res    = await fetch("api/login.php", { method:"POST", headers:{"Content-Type":"application/json"}, body: JSON.stringify({email, password}) });
      const result = await res.json();

      if (result.success) {
        const user = result.user;
        if (user.account_type !== selectedAccountType) {
          alert(`❌ Account Type Mismatch!\nYou selected: ${selectedAccountType}\nThis account is: ${user.account_type}\nPlease select the correct type.`);
          return;
        }
        sessionStorage.setItem("currentUser", JSON.stringify({ id: user.id, name: user.fullname, email: user.email, username: user.username, accountType: user.account_type }));
        if (remember) localStorage.setItem("rememberedUser", JSON.stringify({ email: user.email, name: user.fullname, accountType: user.account_type }));
        alert(`✅ Welcome, ${user.fullname}!`);
        window.location.href = user.account_type === "admin" ? "dashboard-admin.php" : "dashboard-user.php";
      } else {
        alert("❌ Login Failed: " + result.message);
      }
    } catch (err) { alert("Network error. Is XAMPP running?"); }
  });

  document.querySelector(".forgot-password")?.addEventListener("click", (e) => { e.preventDefault(); alert("Password reset coming soon!"); });
});

// ========================================
// SIGN UP PAGE
// ========================================

document.addEventListener("DOMContentLoaded", () => {
  if (!document.getElementById("signupForm")) return;

  const accountTypeBtns  = document.querySelectorAll(".account-type-btn");
  let currentAccountType = "user";

  accountTypeBtns.forEach((btn) => {
    btn.addEventListener("click", () => {
      accountTypeBtns.forEach((b) => b.classList.remove("active"));
      btn.classList.add("active");
      currentAccountType = btn.dataset.type;
      document.querySelector(".signup-container")?.classList.toggle("admin-mode", currentAccountType === "admin");
    });
  });

  document.getElementById("signupForm").addEventListener("submit", async (e) => {
    e.preventDefault();
    const fullname        = document.getElementById("fullname").value;
    const email           = document.getElementById("email").value;
    const username        = document.getElementById("username").value;
    const age             = document.getElementById("age").value;
    const password        = document.getElementById("password").value;
    const confirmPassword = document.getElementById("confirm-password").value;
    const terms           = document.getElementById("terms").checked;

    if (!terms)                          { alert("Please accept the Terms & Conditions"); return; }
    if (age < 1 || age > 120)            { alert("Please enter a valid age between 1 and 120"); return; }
    if (username.length < 3)             { alert("Username must be at least 3 characters long"); return; }
    if (password.length < 6)             { alert("Password must be at least 6 characters long"); return; }
    if (password !== confirmPassword)    { alert("Passwords do not match!"); return; }
    if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) { alert("Please enter a valid email address"); return; }

    try {
      const res    = await fetch("api/create_user.php", { method:"POST", headers:{"Content-Type":"application/json"}, body: JSON.stringify({ fullname, email, username, age: parseInt(age), password, account_type: currentAccountType }) });
      const result = await res.json();
      if (result.success) {
        alert(`Account created! Welcome, ${fullname}! You can now log in.`);
        setTimeout(() => { window.location.href = "login.php"; }, 1000);
      } else {
        alert("Error: " + result.message);
      }
    } catch (err) { alert("Network error. Is XAMPP running?"); }
  });

  const confirmInput = document.getElementById("confirm-password");
  if (confirmInput) {
    confirmInput.addEventListener("input", () => {
      const match = document.getElementById("password").value === confirmInput.value;
      confirmInput.style.borderColor = confirmInput.value ? (match ? "#4ade80" : "#f87171") : "rgba(255,255,255,0.2)";
    });
  }

  document.querySelector(".terms-link")?.addEventListener("click", (e) => { e.preventDefault(); alert("Terms & Conditions: Be respectful, no cheating, keep your password secure."); });
});

// ========================================
// ADMIN DASHBOARD - Carousel only
// All CRUD is in admin-dashboard.js
// ========================================

document.addEventListener("DOMContentLoaded", () => {
  const wrapper = document.querySelector(".slides-wrapper");
  const slides  = document.querySelectorAll(".slide");
  if (!wrapper || !slides.length) return;

  let current = 0;
  setInterval(() => {
    current++;
    wrapper.style.transform = `translateX(-${current * 100}%)`;
    if (current === slides.length - 1) {
      setTimeout(() => {
        wrapper.style.transition = "none";
        current = 0;
        wrapper.style.transform = "translateX(0%)";
        setTimeout(() => { wrapper.style.transition = "transform 0.5s ease"; }, 50);
      }, 700);
    }
  }, 4000);
});
