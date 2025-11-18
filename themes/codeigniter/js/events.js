document.addEventListener("removePostForm", function(evt){
  if (document.getElementById(evt.detail.id)) {
    document.getElementById(evt.detail.id).innerHTML = '';
  }
})

document.addEventListener("closeModal", function(evt){
  document.getElementById('modal-dialog').close();
  setTimeout(function() {
    document.getElementById('modal-container').innerHTML = '';
  }, 500);

})

htmx.on("htmx:afterSwap", function(evt) {
  const eventIdTarget = evt.target.id;
  if (eventIdTarget === 'modal-container') {
    document.getElementById('modal-dialog').showModal();
  }
})
