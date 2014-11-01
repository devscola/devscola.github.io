jQuery(document).ready(function(){
	// UL = .wooTabs
	// Tab contents = .inside

	jQuery( '.pptwj-tabs-wrap .inside ul.list li:last-child').css( 'border-bottom','0px' ); // remove last border-bottom from list in tab content
	jQuery( '.pptwj-tabs-wrap .tab-links').each(function(){
		jQuery(this).children( 'li').children( 'a:first').addClass( 'selected' ); // Add .selected class to first tab on load
	});
	jQuery( '.inside > *').hide();
	jQuery( '.inside > *:first-child').show();

	jQuery( '.pptwj-tabs-wrap .tab-links li a').click(function(evt){ // Init Click funtion on Tabs

		var clicked_tab_ref = jQuery(this).attr( 'href' ); // Strore Href value

		jQuery(this).parent().parent().children( 'li').children( 'a').removeClass( 'selected' ); //Remove selected from all tabs
		jQuery(this).addClass( 'selected' );
		jQuery(this).parent().parent().parent().children( '.inside').children( '*').hide();

		jQuery( '.inside ' + clicked_tab_ref).fadeIn(500);

		 evt.preventDefault();

	});
	
	/** Ajax list filter **/
	jQuery( 'ul.tab-filter-list a' ).click( function(e){
		var $this = jQuery(this);
		var $tabWidgetData = {
			'time' : $this.data('time'),
			'numberposts' : $this.data('numberposts'),
			'thumb' : $this.data('thumb'),
			'tab' : $this.data('tab'),
			'action' : "pptwj_tabwidget_list"
		};

		/**
		 * Display ajax loader/spinner
		 */
		var box_height = jQuery('.pptwj-tabs-wrap .boxes').outerHeight();
		var spinner = jQuery('.pptwj-loader img');
		var spinner_div = jQuery('.pptwj-loader');

		spinner.css('margin-top', box_height/2);
		spinner_div.css('height', box_height);
		spinner_div.show();

		var $tabWidgetHandler = function( $data ){
			//console.log( $data );
			spinner_div.hide();
			if( $data == "" ) return;

			jQuery('ul.tab-filter-list a').removeClass('selected');
			$this.addClass('selected');
			
			var $list = $this.parent().parent().siblings('.list');
			$list.html( $data );
		};
		
		jQuery.post(
			PPTWJ.ajaxUrl,
			$tabWidgetData,
			$tabWidgetHandler
		);
		
		e.preventDefault();
	});
});