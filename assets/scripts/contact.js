function callToAction(form) {
    let data = {
        'name': form.firstname.value,
        'email': form.email.value,
        'comments': form.comments.value
    }
    payload={
        "text": data.name + " (" + data.email + ") ha enviado un comentario:\n\n" + data.comments,
        "username": 'devscola',
        "icon_url": 'http://www.devscola.org/assets/images/Logotipo-01.png'
    }
  
    this.post(JSON.stringify(payload))
    this.reset(form)
    return false
  }
  function post(data) {
    var xhr = new XMLHttpRequest();
    xhr.open('POST', 'https://hooks.slack.com/services/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');
    xhr.send(data)
  }
  function reset(form) {
      form.firstname.value = ''
      form.email.value = ''
      form.comments.value = ''
  }