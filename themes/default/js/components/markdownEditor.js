import EasyMDE from "easymde";

export function initEditor(elem) {
  const easyMDE = new EasyMDE({
    element: elem,
    toolbar: ["heading", "bold", "italic", "|", "quote", "code", "link", "|", "unordered-list", "ordered-list"],
  });

  easyMDE.codemirror.on("change", () => {
    elem.value = easyMDE.value();
  });
}

