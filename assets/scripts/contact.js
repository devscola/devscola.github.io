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
    return false
  }
  function post(data) {
    var xhr = new XMLHttpRequest();
    xhr.open('POST', 'https://hooks.slack.com/services/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');
    xhr.onload = function() {
        if (xhr.status === 200) {
            alert('Enviado!')
        }
    };
    xhr.send(data)
  }