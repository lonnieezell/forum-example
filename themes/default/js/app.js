import "./htmx.js";
import "htmx.org/dist/ext/loading-states";
import Alpine from "alpinejs";
import "./events.js";
import { initEditor } from "./components/markdownEditor.js";
import { initTags } from "./components/tags.js";
import alerts from "./components/alerts.js";

Alpine.data("alerts", alerts);

window.Alpine = Alpine;

Alpine.start();

htmx.on("htmx:load", function (evt) {
  let editorElement = htmx.find(evt.detail.elt, "#editor");
  if (editorElement && editorElement.dataset.type === "markdown") {
    initEditor(editorElement);
  }

  let tagsElement = htmx.find(evt.detail.elt, "#tags");
  if (tagsElement) {
    initTags(tagsElement);
  }
});
