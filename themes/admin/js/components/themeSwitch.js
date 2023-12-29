htmx.on("htmx:load", function (evt) {
  // Sets the default theme to use on page load.
  const cookieTheme = getCookie("theme");
  const systemSettingDark = window.matchMedia("(prefers-color-scheme: dark)");

  let currentThemeSetting = calculateSettingAsThemeString({
    cookieTheme,
    systemSettingDark,
  });

  // Set the appropriate checked state for the theme toggler
  document.querySelector("input.theme-controller").checked =
    currentThemeSetting === "light";

  // Store the current theme in a cookie so it can be accessed by both us and the server
  createCookie("theme", currentThemeSetting, "365");

  // Add an onClick event to the link
  document.querySelectorAll("input.theme-controller").forEach((e) => {
    e.addEventListener("click", () => {
      const newTheme = currentThemeSetting === "dark" ? "light" : "dark";

      // Update our cookie
      createCookie("theme", newTheme, "365");

      // update the currentThemeSetting in memory
      currentThemeSetting = newTheme;
    });
  });

  function calculateSettingAsThemeString({ cookieTheme, systemSettingDark }) {
    if (cookieTheme !== null) {
      return cookieTheme;
    }

    if (systemSettingDark.matches) {
      return "dark";
    }

    return "light";
  }
});

function createCookie(name, value, days) {
  var expires;
  if (days) {
    var date = new Date();
    date.setTime(date.getTime() + days * 24 * 60 * 60 * 1000);
    expires = "; expires=" + date.toGMTString();
  } else {
    expires = "";
  }

  document.cookie = name + "=" + value + expires + "; path=/";
}

function getCookie(cookiename) {
  // Get name followed by anything except a semicolon
  var cookiestring = RegExp(cookiename + "=[^;]+").exec(document.cookie);
  // Return everything after the equal sign, or an empty string if the cookie name not found
  return decodeURIComponent(
    !!cookiestring ? cookiestring.toString().replace(/^[^=]+./, "") : "",
  );
}
