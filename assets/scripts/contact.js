function callToAction(form) {
  var data = {
    'name': form.firstname.value,
    'email': form.email.value,
    'comments': form.comments.value
  };
  var payload = {
    "text": data.name + " (" + data.email + ") ha enviado un comentario:\n\n" + data.comments,
    "username": 'devscola',
    "icon_url": 'http://www.devscola.org/assets/images/Logotipo-01.png'
  };

  this.post(JSON.stringify(payload));
  this.reset(form);

  return false;
}

function post(data) {
  var xhr = new XMLHttpRequest();
  xhr.open('POST', 'https://chat.devscola.org/hooks/g7a9zpkp6ig1jfjjz37qq7xhee');
  xhr.send(data);
}

function reset(form) {
  form.firstname.value = '';
  form.email.value = '';
  form.comments.value = '';
  document.querySelector("#contact-feedback").style = "display: inline-block;"
  setTimeout(function () {
    document.querySelector("#contact-feedback").style = "display: none;"
  }, 4000)
}
