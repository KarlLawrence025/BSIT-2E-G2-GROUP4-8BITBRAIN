console.log("JS loaded 🚀");

document.addEventListener("DOMContentLoaded", () => {
  // Nav active link
  document.querySelectorAll(".navbar a").forEach((link) => {
    if (link.href === window.location.href) link.classList.add("active");
  });

  // Hero start quiz button
  const startBtn = document.querySelector(".hero button");
  if (startBtn) {
    startBtn.addEventListener("click", () => {
      window.location.href = "modes.php";
    });
  }

  // About page animated counters
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

  // Admin carousel
  const wrapper = document.querySelector(".slides-wrapper");
  const slides  = document.querySelectorAll(".slide");
  if (wrapper && slides.length) {
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
  }
});

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

// Login page
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

  document.getElementById("loginForm").addEventListener("submit", async (e) => {
    e.preventDefault();
    const email    = document.getElementById("email").value;
    const password = document.getElementById("password").value;
    if (password.length < 6) { alert("Password must be at least 6 characters"); return; }

    try {
      const res    = await fetch("api/login.php", { method:"POST", headers:{"Content-Type":"application/json"}, body: JSON.stringify({email, password}) });
      const result = await res.json();
      if (result.success) {
        const user = result.user;
        if (user.account_type !== selectedAccountType) {
          alert(`Account type mismatch. This account is: ${user.account_type}`);
          return;
        }
        sessionStorage.setItem("currentUser", JSON.stringify(user));
        alert(`✅ Welcome, ${user.fullname}!`);
        window.location.href = user.account_type === "admin" ? "dashboard-admin.php" : "dashboard-user.php";
      } else {
        alert("❌ Login Failed: " + result.message);
      }
    } catch (err) { alert("Network error. Is XAMPP running?"); }
  });

  document.querySelector(".forgot-password")?.addEventListener("click", (e) => { e.preventDefault(); alert("Password reset coming soon!"); });
});

// Signup page
document.addEventListener("DOMContentLoaded", () => {
  if (!document.getElementById("signupForm")) return;

  const accountTypeBtns  = document.querySelectorAll(".account-type-btn");
  let currentAccountType = "user";

  accountTypeBtns.forEach((btn) => {
    btn.addEventListener("click", () => {
      accountTypeBtns.forEach((b) => b.classList.remove("active"));
      btn.classList.add("active");
      currentAccountType = btn.dataset.type;
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

    if (!terms)                       { alert("Please accept the Terms & Conditions"); return; }
    if (username.length < 3)          { alert("Username must be at least 3 characters"); return; }
    if (password.length < 6)          { alert("Password must be at least 6 characters"); return; }
    if (password !== confirmPassword)  { alert("Passwords do not match!"); return; }

    try {
      const res    = await fetch("api/create_user.php", { method:"POST", headers:{"Content-Type":"application/json"}, body: JSON.stringify({ fullname, email, username, age: parseInt(age), password, account_type: currentAccountType }) });
      const result = await res.json();
      if (result.success) {
        alert(`Account created! Welcome, ${fullname}! You can now log in.`);
        window.location.href = "login.html";
      } else {
        alert("Error: " + result.message);
      }
    } catch (err) { alert("Network error. Is XAMPP running?"); }
  });
});
