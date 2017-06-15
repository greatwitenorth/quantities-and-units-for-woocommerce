<?php 
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if ( ! class_exists( 'WC_Quantities_and_Units_Filters' ) ) :

class WC_Quantities_and_Units_Filters {
		
	public function __construct() {
		
		// Cart input box variable filters
		add_filter( 'woocommerce_quantity_input_min', array( $this, 'input_min_value' ), 10, 2);
		add_filter( 'woocommerce_quantity_input_max', array( $this, 'input_max_value' ), 10, 2);
		add_filter( 'woocommerce_quantity_input_step', array( $this, 'input_step_value' ), 10, 2);
		
		// Product input box argument filter
		add_filter( 'woocommerce_quantity_input_args', array( $this, 'input_set_all_values' ), 10, 2 );

		add_filter( 'woocommerce_loop_add_to_cart_args', array( $this, 'woocommerce_loop_add_to_cart_args' ), 10, 2 );
	}

	/**
	 * This method ensures that if a minimum has been set, that we 
	 * specify that when the ajax add to cart function is enabled.
	 * Also if a step is set, but no minimum, we'll use that.
	 * Only works with WooCommerce 2.5+
	 * 
	 * @param array $args
	 * @param WC_Product $product
	 *
	 * @return mixed
	 */
	public function woocommerce_loop_add_to_cart_args( $args, $product ) {
		// Return Defaults if it isn't a simple product 
		if( $product->get_type() != 'simple' ) {
			return $args;
		}

		// Get Rule
		$rule = wcqu_get_applied_rule( $product );

		// Get Value from Rule
		$min = wcqu_get_value_from_rule( 'min', $product, $rule );
		$min = isset($min['min']) ? $min['min'] : 1;
		
		$step = wcqu_get_value_from_rule( 'step', $product, $rule );
		$step = isset($step['step']) ? $step['step'] : 1;
		
		if(!$min && $step > 0){
			$args['quantity'] = $step;
		} 
		else if($min > 0){
			$args['quantity'] = $min;
		}
			
		
		return $args;
	}

	/*
	*	Filter Minimum Quantity Value for Input Boxes for Cart
	*
	*	@access public 
	*	@param  int 	default
	*	@param  obj		product
	*	@return int		step
	*
	*/								
	public function input_min_value( $default, $product ) {

		// Return Defaults if it isn't a simple product 
		if( $product->get_type() != 'simple' ) {
			return $default;
		}
		
		// Get Rule
		$rule = wcqu_get_applied_rule( $product );

		// Get Value from Rule
		$min = wcqu_get_value_from_rule( 'min', $product, $rule );
		
		// Return Value
		if ( $min == '' or $min == null or (isset($min['min']) and $min['min'] == "")) {
			return $default;
		} else {
			return $min;
		}
	}
	
	/*
	*	Filter Maximum Quantity Value for Input Boxes for Cart
	*
	*	@access public 
	*	@param  int 	default
	*	@param  obj		product
	*	@return int		step
	*
	*/	
	public function input_max_value( $default, $product ) {	
		
		// Return Defaults if it isn't a simple product
		if( $product->get_type() != 'simple' ) {
			return $default;
		}
		
		// Get Rule
		$rule = wcqu_get_applied_rule( $product );
		
		// Get Value from Rule
		$max = wcqu_get_value_from_rule( 'max', $product, $rule );
	
		// Return Value
		if ( $max == '' or $max == null or (isset($max['max']) and $max['max'] == "")) {
			return $default;
		} else {
			return $max;
		}
	}
	
	/*
	*	Filter Step Quantity Value for Input Boxes woocommerce_quantity_input_step for Cart
	*
	*	@access public 
	*	@param  int 	default
	*	@param  obj		product
	*	@return int		step
	*
	*/	
	public function input_step_value( $default, $product ) {
		
		// Return Defaults if it isn't a simple product
		if( $product->get_type() != 'simple' ) {
			return $default;
		}
		
		// Get Rule
		$rule = wcqu_get_applied_rule( $product );
		
		// Get Value from Rule
		$step = wcqu_get_value_from_rule( 'step', $product, $rule );
	
		// Return Value
		if ( $step == '' or $step == null or (isset($step['step']) and $step['step'] == "")) {
			return $default;
		} else {
			return isset($step['step']) ? $step['step'] : $step;
		}
	}	
	
	/*
	*	Filter Step, Min and Max for Quantity Input Boxes on product pages
	*
	*	@access public 
	*	@param  array 	args
	*	@param  obj		product
	*	@return array	vals
	*
	*/	
	public function input_set_all_values( $args, $product ) {
		
		// Return Defaults if it isn't a simple product
		/* Commented out to allow for grouped and variable products
		*  on their product pages
		if( $product->get_type() != 'simple' ) {
			return $args;
		}
		*/

		// Get Rule
		$rule = wcqu_get_applied_rule( $product );

		// Get Value from Rule
		$values = wcqu_get_value_from_rule( 'all', $product, $rule );

		if ( $values == null ) {
			return $args;
		}
		
		$vals = array();
		$vals['input_name'] = 'quantity';
		
		// Check if the product is out of stock 
		$stock = $product->get_stock_quantity();
		
		// Check stock status and if Out try Out of Stock value	
		if ( strlen( $stock ) != 0 and $stock <= 0 and isset( $values['min_oos'] ) and $values['min_oos'] != '' ) {
			$args['min_value'] = $values['min_oos'];
			
		// Otherwise just check normal min	
		} elseif ( $values['min_value'] != ''  ) {
			$args['min_value'] 	 = $values['min_value'];
		
		// If no min, try step	
		} elseif ( $values['min_value'] == '' and $values['step'] != '' ) {
			$args['min_value'] 	 = $values['step'];
		} 
		
		// Check stock status and if Out try Out of Stock value	
		if ( $stock <= 0 and isset( $values['min_oos'] ) and $values['max_oos'] != '' ) {
			$args['max_value'] = $values['max_oos'];
		
		// Otherwise just check normal max	
		} elseif ($values['max_value'] != ''  ) {
			$args['max_value'] 	 = $values['max_value'];
		}
		
		// Set step value
		if ( $values['step'] != '' ) {
			$args['step'] = $values['step'];
		}
		
		return $args;
	}

}

endif;

return new WC_Quantities_and_Units_Filters();
