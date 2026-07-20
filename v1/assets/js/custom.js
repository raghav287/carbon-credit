document.addEventListener("DOMContentLoaded", () => {
  const contactAlert = document.querySelector("[data-contact-alert]");
  if (contactAlert) {
    window.setTimeout(() => {
      contactAlert.classList.add("is-hiding");
      window.setTimeout(() => contactAlert.remove(), 350);
    }, 4000);
  }

  if (
    window.location.search.includes("sent=") ||
    window.location.search.includes("flash=")
  ) {
    window.history.replaceState({}, document.title, window.location.pathname);
  }

  const menuButton = document.querySelector("#menuToggle");
  const navigation = document.querySelector("#mainNavigation");

  if (!menuButton || !navigation) {
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
