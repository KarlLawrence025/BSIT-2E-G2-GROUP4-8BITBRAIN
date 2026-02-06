console.log("JS loaded 🚀");

document.querySelector(".hero button").addEventListener("click", () => {
  document.body.classList.add("clicked");
});

document.addEventListener("keydown", (e) => {
  if (e.key === "Enter") {
    document.querySelector(".hero button")?.click();
  }
});

const links = document.querySelectorAll(".navbar a");
links.forEach((link) => {
  if (link.href === window.location.href) {
    link.classList.add("active");
  }
});
