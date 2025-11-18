import "./htmx.js";
import "htmx.org/dist/ext/loading-states";
import Alpine from "alpinejs";
import alerts from "./components/alerts.js";
import "./components/themeSwitch.js";

// Alpine
Alpine.data("alerts", alerts);
window.Alpine = Alpine;
Alpine.start();

// Loading indicator error handling
const loadingBar = document.querySelector('.loading-bar');
htmx.on('htmx:responseError', () => {
  if (loadingBar) {
    loadingBar.classList.add('error');
    setTimeout(() => {
      loadingBar.classList.remove('error');
    }, 2000);
  }
});
