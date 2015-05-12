 $(document).foundation(); 

 !function(d,s,id){var js,fjs=d.getElementsByTagName(s)[0],p=/^http:/.test(d.location)?'http':'https';if(!d.getElementById(id)){js=d.createElement(s);js.id=id;js.src=p+"://platform.twitter.com/widgets.js";fjs.parentNode.insertBefore(js,fjs);}}(document,"script","twitter-wjs");

	$(document).ready(function() {
	 
	  $("#owl-landing").owlCarousel({
	    autoPlay : 3000,
	    stopOnHover : true,
	    navigation: false,
	    paginationSpeed : 5000,
	    goToFirstSpeed : 2000,
	    singleItem : true,
	    autoHeight : true,
	    transitionStyle:"fade"
	  });
	 
	});





$(document).ready(function() {
	 
	  $("#owl-demo").owlCarousel({
	  		paginationSpeed : 5000,
	    	goToFirstSpeed : 2000,
	    	navigationText: ["<",">"],
	 	
	      items : 3,
	      itemsDesktop : [1199,3],
	      itemsDesktopSmall : [979,3],
	      navigation: true
	 
	  });
	 
	});


$(function() {

  $('a[href*=#]:not([href=#])').click(function() {
    if (location.pathname.replace(/^\//,'') == this.pathname.replace(/^\//,'') && location.hostname == this.hostname) {
      var target = $(this.hash);
      target = target.length ? target : $('[name=' + this.hash.slice(1) +']');
      if (target.length) {
        $('html,body').animate({
          scrollTop: target.offset().top -20
        }, 1000);
        return false;
      }
    }
  });
});