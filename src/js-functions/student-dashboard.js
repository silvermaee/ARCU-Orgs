const hamburger = document.querySelector(".toggle-btn");
const sidebar = document.querySelector("#sidebar"); // Selecting the sidebar element

hamburger.addEventListener("click", function () {
  sidebar.classList.toggle("expand");
});
