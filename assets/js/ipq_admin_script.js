jQuery(document).ready( function($) {
	
	$('span.active-toggle a').click( function() {
		
		$('div.rule-meta').toggle();

		if ( $('div.rule-meta').css( 'display' ) == 'block' ) {
			$('span.active-toggle a').html( 'Show/Hide Active Rule Values &#x25B2;' );
			
		} else {
			$('span.active-toggle a').html( 'Show/Hide Active Rule Values &#x25BC;' );
		}
	});

	$('#toggle_override').change( function() {
	
		if ( $('span.wpbo_product_values').css( 'display' ) == 'block' ) {
			$('span.wpbo_product_values').hide();
		} else {
			$('span.wpbo_product_values').show();
		}
	});

});
