document.addEventListener("DOMContentLoaded", () => {
  const menuButton = document.querySelector("#menuToggle");
  const navigation = document.querySelector("#mainNavigation");

  if (!menuButton || !navigation) {
    console.error("Menu button or navigation not found.");
    return;
  }

  menuButton.addEventListener("click", () => {
    const isOpen = navigation.classList.toggle("is-open");

    menuButton.classList.toggle("is-active", isOpen);
    menuButton.setAttribute("aria-expanded", String(isOpen));
    document.body.classList.toggle("menu-open", isOpen);
  });

  navigation.querySelectorAll(".nav-link").forEach((link) => {
    link.addEventListener("click", () => {
      navigation.classList.remove("is-open");
      menuButton.classList.remove("is-active");
      menuButton.setAttribute("aria-expanded", "false");
      document.body.classList.remove("menu-open");
    });
  });
});