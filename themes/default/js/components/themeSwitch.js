// Sets the default theme to use on page load.
const localStorageTheme = localStorage.getItem("theme");
const systemSettingDark = window.matchMedia("(prefers-color-scheme: dark)");

let currentThemeSetting = calculateSettingAsThemeString({
  localStorageTheme,
  systemSettingDark,
});

// Set the text on the menu correctly on page load
const newCta = currentThemeSetting === "my_dark" ? "Light Theme" : "Dark Theme";
document.querySelectorAll("[data-theme-toggle] > span").innerText = newCta;
document.querySelector("html").setAttribute("data-theme", currentThemeSetting);

// Add an onClick event to the link
document.querySelectorAll("[data-theme-toggle]").forEach((e) => {
  e.addEventListener("click", () => {
    const newTheme = currentThemeSetting === "my_dark" ? "my_light" : "my_dark";

    // Update the link text
    const newCta = newTheme === "my_dark" ? "Light Theme" : "Dark Theme";
    document
      .querySelectorAll("[data-theme-toggle] > span")
      .forEach((e) => (e.innerText = newCta));

    // update theme attribute on HTML to switch theme in CSS
    document.querySelector("html").setAttribute("data-theme", newTheme);

    // update in local storage
    localStorage.setItem("theme", newTheme);

    // update the currentThemeSetting in memory
    currentThemeSetting = newTheme;
  });
});

function calculateSettingAsThemeString({
  localStorageTheme,
  systemSettingDark,
}) {
  if (localStorageTheme !== null) {
    return localStorageTheme;
  }

  if (systemSettingDark.matches) {
    return "my_dark";
  }

  return "my_light";
}
