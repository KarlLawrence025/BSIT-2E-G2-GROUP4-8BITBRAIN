
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

    } else {
      alert(`User Login Successful!\nWelcome, ${formData.name}!`);
      window.location.href = "modes.html";
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
