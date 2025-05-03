const hamburger = document.querySelector(".toggle-btn");
const sidebar = document.querySelector("#sidebar"); // Selecting the sidebar element

hamburger.addEventListener("click", function () {
  // Only toggle the 'expand' class on the sidebar element
  sidebar.classList.toggle("expand");

  // The line below that modified the icon's class has been removed:
  // toggler.classList.toggle("bi-list");
});
