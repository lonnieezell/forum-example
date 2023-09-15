import './htmx.js';
import 'htmx.org/dist/ext/loading-states'
import Alpine from 'alpinejs';
import { initEditor } from "./components/markdownEditor.js";
import "./events.js";

window.Alpine = Alpine;

Alpine.start();

htmx.on("htmx:load", function(evt) {

  let editorElement = htmx.find(evt.detail.elt, '#editor')
  if (editorElement && editorElement.dataset.type === 'markdown') {
    initEditor(editorElement);
  }

});
