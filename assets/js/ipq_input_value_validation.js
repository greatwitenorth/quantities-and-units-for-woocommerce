if (!String.prototype.getDecimals) {
	String.prototype.getDecimals = function() {
		var num = this,
			match = ('' + num).match(/(?:\.(\d+))?(?:[eE]([+-]?\d+))?$/);
		if (!match)
			return 0;
		return Math.max(0, (match[1] ? match[1].length : 0) - (match[2] ? +match[2] : 0));
	}
}

jQuery(document).ready( function($) {

	/*
	*	Quantity Rule Validation
	*	
	*	If user enters a value that is out of bounds, 
	*	it will be auto corrected to a valid value.
	*/		
	$(document).on('change', '.qty', function(e) {
		var $input = $(e.currentTarget);
		// Get values from input box
		var step = $input.attr( 'step' );
		var stepOrig = step;
		var new_qty = $input.val();
		var max = $input.attr( 'max' );
		var min = $input.attr( 'min' );

		var multiplier = function(){
			var zeros = stepOrig.getDecimals();
			var mult = "1";
			for (i = 0; i <= zeros; i++) mult = mult + "0";

			return parseInt(mult);
		};
		
		// Adjust default values if values are blank
		if ( min == '' || typeof min == 'undefined' ) 
			min = 1;
		
		if ( step == '' || typeof step == 'undefined') 
			step = 1;
		
		// Max Value Validation
		if ( +new_qty > +max && max != '' ) {
			new_qty = max;
		
		// Min Value Validation
		} else if ( +new_qty < +min && min != '' ) {
			new_qty = min;
		}
		
		// Calculate remainder
		step = step * multiplier();
		new_qty = new_qty * multiplier();
		min = min * multiplier();
		max = max* multiplier();
		
		var rem = ( new_qty - min ) % step;
				
		// Step Value Value Validation
		if ( rem != 0 ) {
			new_qty = +new_qty + (+step - +rem);
			
			// Max Value Validation
			if ( max > 0 && +new_qty > +max ) {
				new_qty = +new_qty - +step;
			}
		}
				
		// Set the new value
		$input.val( (new_qty/multiplier()).toFixed(stepOrig.getDecimals()) );
	});
	
	/*
	*	Make sure minimum equals value 
	*	To Fix: when min = 0 and val = 1 
	*/
	if ( $("body.single-product .qty").val() != $("body.single-product .qty").attr('min') && $("body.single-product .qty").attr('min') != '' ) {
		$("body.single-product .qty").val( $("body.single-product .qty").attr('min') );
	}
	
	/*
	*	Variable Product Support
	*	
	*	Need to overwrite what WC changes with their js
	*/
	
	// Get localized Variables
	if ( typeof ipq_validation !== 'undefined' ) {
		var start_min = ipq_validation.min;
		var start_max = ipq_validation.max;
		var start_step = ipq_validation.step;
	}
	
	// Update input box after variaiton selects are blured
	$('.variations select').bind('blur',function() {
	
		// Update min
		if ( start_min != $('.qty').attr('min') && start_min != '' ) {
			$('.qty').attr('min', start_min );
		}
	
		// Update max
		if ( start_max != $('.qty').attr('max') && start_max != '' ) {
			$('.qty').attr('max', start_max );
		}
		
		// Update step
		if ( start_step != $('.qty').attr('step') && start_step != '' ) {
			$('.qty').attr('step', start_step );
		}
		
		// Make sure intput value is in bounds
		if ( start_min > $('.qty').attr('value') && start_min != '' ) {
			$('.qty').attr('value', start_min );
		}

	});

});
