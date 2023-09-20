document.addEventListener("removePostForm", function(evt){
  if (document.getElementById(evt.detail.id)) {
    document.getElementById(evt.detail.id).innerHTML = '';
  }
})
