import "./htmx.js";
import "htmx.org/dist/ext/loading-states";
import Alpine from "alpinejs";
import alerts from "./components/alerts.js";
import "./components/themeSwitch.js";

// Alpine
Alpine.data("alerts", alerts);
window.Alpine = Alpine;
Alpine.start();
