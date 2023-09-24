import EasyMDE from "easymde";

export function initEditor(elem) {
  const uploadEnabled = Boolean(elem.dataset.uploadEnabled || false);
  const uploadSize = Number(elem.dataset.uploadSize * 1024 || 1024 * 1024 * 2);
  const uploadMime = (elem.dataset.uploadMime || 'image/png,image/jpeg').split(',');
  const uploadUrl = elem.dataset.uploadUrl || '/images/upload';

  const easyMDE = new EasyMDE({
    element: elem,
    toolbar: ["heading", "bold", "italic", "|", "quote", "code", "link", "|", "unordered-list", "ordered-list"],
    uploadImage: uploadEnabled,
    imageMaxSize: uploadSize,
    imageAccept: uploadMime,
    imageUploadEndpoint: uploadUrl,
  });

  easyMDE.codemirror.on("change", () => {
    elem.value = easyMDE.value();
  });
}

