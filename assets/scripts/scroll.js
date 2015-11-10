//Soft Scroll------------/
$('a').click(function(){$('html, body').animate({scrollTop: $( $.attr(this, 'href') ).offset().top -90}, 500);return false;});
