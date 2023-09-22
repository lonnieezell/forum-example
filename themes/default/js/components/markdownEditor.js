import EasyMDE from "easymde";

export function initEditor(elem) {
  const easyMDE = new EasyMDE({
    element: elem,
    toolbar: ["heading", "bold", "italic", "|", "quote", "code", "link", "|", "unordered-list", "ordered-list"],
    uploadImage: true,
    imageMaxSize: 1024 * 1024 * 2,
    imageAccept: ['image/png', 'image/jpeg'],
    imageUploadEndpoint: '/images/upload',
  });

  easyMDE.codemirror.on("change", () => {
    elem.value = easyMDE.value();
  });
}

