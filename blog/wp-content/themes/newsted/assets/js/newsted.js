(function($) {
    "use strict"; 
    $(function() { 
    	var winWidth = $(window).width();   	
    	// header navigation    	
    	dropDown();	
		$(window).resize(function() {
			dropDown();
		});						
		function dropDown() {			
			if (winWidth > 768) {
				$('header nav .menu-item-has-children').hover(
					function() {
				    	$(this).find('ul').fadeIn('fast');
				    	$(this).addClass('sub-active');
					}, function() {
				    	$(this).find('ul').fadeOut('fast');
				    	$(this).removeClass('sub-active');
				  }
				);
				$('header nav').css('display','block');	
			}	
		}
 	}); 	
}(jQuery));