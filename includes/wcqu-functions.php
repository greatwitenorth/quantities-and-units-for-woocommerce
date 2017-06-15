<?php 
/*
*	Given the product, this will check which rule is being applied to a product
* 	If there is a rule, the values will be returned otherwise it is inactive 
*	or overridden (from the product meta box).
*
*	@params object	$product WC_Product object
*	@param 	string	User role to get rule from, otherwise current user role is used
*	@return mixed 	String of rule status / Object top rule post 
*/
function wcqu_get_applied_rule( $product, $role = null ) {
	
	// Check for site wide rule
	$options = get_option( 'ipq_options' );
	
	if ( get_post_meta( $product->get_id(), '_wpbo_deactive', true ) == 'on' ) {
		return 'inactive';
		
	} elseif ( get_post_meta( $product->get_id(), '_wpbo_override', true ) == 'on' ) {
		return 'override';
	
	} elseif ( isset( $options['ipq_site_rule_active'] ) and $options['ipq_site_rule_active'] == 'on' ) {
		return 'sitewide';
		
	} else {
		return wcqu_get_applied_rule_obj( $product, $role );
	}
}

/*
*	Get the Rule Object thats being applied to a given product.
*	Will return null if no rule is applied.
*
*	@params object	$product WC_Product object
*	@param 	string	User role to get rule from, otherwise current user role is used
*	@return mixed 	Null if no rule applies / Object top rule post 
*/
function wcqu_get_applied_rule_obj( $product, $role = null ) {
	
	// Get Product Terms
	$product_cats = wp_get_post_terms( $product->get_id(), 'product_cat' );
	$product_tags = wp_get_post_terms( $product->get_id(), 'product_tag' );	

	// Get role if not passed
	if(!is_user_logged_in()) {
		$role = 'guest';
	} else if ( $role == NULL ) {
		$user_data = get_userdata( get_current_user_id() );
		if ( $user_data->roles ) {
			foreach ( $user_data->roles as $cap => $val ) {
				$role = $val;
			}
		}
	}

	// Combine all product terms
	$product_terms = array_merge( $product_cats, $product_tags );
	// Check for rule / role transient
	if ( false === ( $rules = get_transient( 'ipq_rules_' . $role ) ) ) {
		// Get all Rules
		$args = array(
			'posts_per_page'   	=> -1,
			'post_type'        	=> 'quantity-rule',
			'post_status'      	=> 'publish',
		); 
		
		$rules = get_posts( $args );
		
		// Remove rules not applied to current user role
		$cnt = 0; 
		$rules_to_unset = array();
		
		while ( $cnt < count( $rules ) ) {
	
			$roles = get_post_meta( $rules[$cnt]->ID, '_roles' );
		 	if ( !in_array( $role, $roles[0] ) && !empty($roles[0])) {
			 	array_push( $rules_to_unset, $cnt );
		 	} 
		 	
		 	$cnt++;
		}		

		$duration = 60 * 60 * 12; // 12 hours
		arsort( $rules_to_unset );
		
		foreach ( $rules_to_unset as $single_unset ) {
			unset( $rules[$single_unset] );
		}		
		set_transient( 'ipq_rules_' . $role, $rules, $duration );	
	} 

	$top = null;
	$top_rule = null;

	// Loop through the rules and find the ones that apply
	foreach ( $rules as $rule ) {
	 
	 	$apply_rule = false;
	 	
	 	// Get the Rule's Cats and Tags
	 	$cats = get_post_meta( $rule->ID, '_cats' );
	 	$tags = get_post_meta( $rule->ID, '_tags' );
	 	$roles = get_post_meta( $rule->ID, '_roles' );
	 		 	
	 	if( $cats != false )
		 	$cats = $cats[0];
	 	
	 	if( $tags != false )
		 	$tags = $tags[0];

		if ( $roles != false )
		 	$roles = $roles[0];

	 	$rule_taxes = array_merge( $tags, $cats );

	 	// Loop through the Product's Categories
	 	// If they are in the rule flag it
	 	foreach ( $product_terms as $term ) {
		 	if ( in_array( $term->term_id, $rule_taxes ) ) {
			 	$apply_rule = true;
		 	}
	 	}
	 	
	 	// If the rule applies, check the priority
	 	if ( $apply_rule == true ) {
	 	
	 		$priority = get_post_meta( $rule->ID, '_priority', true );	

	 		if( $priority != '' and $top > $priority or $top == null ) {
	 			$top = $priority;
	 			$top_rule = $rule;
		 	}
		}
	}
	
	return $top_rule;	
}

/*
*	Get the Input Value (min/max/step/priority/role/all) for a product given a rule
*
*	@params string	$type Product type
*	@params object 	$product Product Object 
*	@params object	$rule Quantity Rule post object
*	@return mixed
*/
function wcqu_get_value_from_rule( $type, $product, $rule ) {

	// Validate $type
	if ( $type != 'min' and 
		 $type != 'max' and 
		 $type != 'step' and 
		 $type != 'all' and 
		 $type != 'priority' and 
		 $type != 'role' and
		 $type != 'min_oos' and
		 $type != 'max_oos'
		) {
		return null;
	
	// Validate for missing rule	
	} elseif ( $rule == null ) {
		return null;
	
	// Return Null if Inactive
	} elseif ( $rule == 'inactive' ) {
		return null;
	
	// Return Product Meta if Override is on
	} elseif ( $rule == 'override' ) {
		
		// Check if the product is out of stock
		$stock = $product->get_stock_quantity();

		// Check if the product is under stock management and out of stock
		if ( strlen( $stock ) != 0 and $stock <= 0 ) {
			
			// Return Out of Stock values if they exist
			switch ( $type ) {
				case 'min':
					$min_oos = get_post_meta( $product->get_id(), '_wpbo_minimum_oos', true );
					if ( $min_oos != '' )
						return $min_oos;
					break;
				
				case 'max':
					$max_oos = get_post_meta( $product->get_id(), '_wpbo_maximum_oos', true );
					if ( $max_oos != '' )
						return $max_oos;
					break;	
			}  
			// If nothing was returned, proceed as usual
		}
		
		switch ( $type ) {
			case 'all':
				return array( 
						'min_value' => get_post_meta( $product->get_id(), '_wpbo_minimum', true ),
						'max_value' => get_post_meta( $product->get_id(), '_wpbo_maximum', true ),
						'step' 		=> get_post_meta( $product->get_id(), '_wpbo_step', true ),
						'min_oos'	=> get_post_meta( $product->get_id(), '_wpbo_minimum_oos', true ),
						'max_oos'	=> get_post_meta( $product->get_id(), '_wpbo_maximum_oos', true ),
					);
				break;
			case 'min':
				return get_post_meta( $product->get_id(), '_wpbo_minimum', true );
				break;
			
			case 'max': 
				return get_post_meta( $product->get_id(), '_wpbo_maximum', true );
				break;
				
			case 'step':
				return get_post_meta( $product->get_id(), '_wpbo_step', true );
				break;
			
			case 'min_oos':
				return get_post_meta( $product->get_id(), '_wpbo_minimum_oos', true );
				break;
			
			case 'max_oos':
				return get_post_meta( $product->get_id(), '_wpbo_maximum_oos', true );
				break;
				
			case 'priority':
				return null;
				break;
		}		
	
	// Check for Site Wide Rule
	} elseif ( $rule == 'sitewide' ) {

		$options = get_option( 'ipq_options' );
		
		if( isset( $options['ipq_site_min'] ) ) {
			$min = $options['ipq_site_min'];
		} else {
			$min = '';
		}

		if( isset( $options['ipq_site_max'] ) ) {
			$max = $options['ipq_site_max'];
		} else {
			$max = '';
		}

		if( isset( $options['ipq_site_min_oos'] ) ) {
			$min_oos = $options['ipq_site_min_oos'];
		} else {
			$min_oos = '';
		}
		
		if( isset( $options['ipq_site_max_oos'] ) ) {
			$max_oos = $options['ipq_site_max_oos'];
		} else {
			$max_oos = '';
		}
		
		if( isset( $options['ipq_site_step'] ) ) {
			$step = $options['ipq_site_step'];
		} else {
			$step = '';			
		}

		switch ( $type ) {
			case 'all':
				return array( 
					'min_value' => $min, 
					'max_value' => $max,
					'min_oos' 	=> $min_oos,
					'max_oos' 	=> $max_oos,
					'step' 		=> $step
				);
				break;
				
			case 'min':
				return array( 'min' => $min );					
				break;
			
			case 'max': 
				return array( 'max' => $max );		
				break;
			
			case 'min_oos': 
				return array( 'min_oos' => $min_oos );		
				break;
				
			case 'max_oos': 
				return array( 'max_oos' => $max_oos );		
				break;
				
			case 'step':
				return array( 'step' => $step );				
				break;
				
			case 'priority':
				return null;
				break;
		
		}
		
	// Return Values from the Rule based on $type requested
	} else {
	
		switch ( $type ) {
			case 'all':
				return array( 
						'min_value' => get_post_meta( $rule->ID, '_min', true ),
						'max_value' => get_post_meta( $rule->ID, '_max', true ),
						'min_oos'	=> get_post_meta( $rule->ID, '_min_oos', true ),
						'max_oos'	=> get_post_meta( $rule->ID, '_max_oos', true ),
						'step' 		=> get_post_meta( $rule->ID, '_step', true ),
						'priority'  => get_post_meta( $rule->ID, '_priority', true ),
						'roles' 	=> get_post_meta( $rule->ID, '_roles', true )
					);
				break;
				
			case 'min':
				return get_post_meta( $rule->ID, '_min', true );
				break;
			
			case 'max': 
				return get_post_meta( $rule->ID, '_max', true );
				break;
			
			case 'min_oos': 
				return get_post_meta( $rule->ID, '_min_oos', true );
				break;
				
			case 'max_oos': 
				return get_post_meta( $rule->ID, '_max_oos', true );
				break;
				
			case 'step':
				return get_post_meta( $rule->ID, '_step', true );
				break;
			
			case 'role':
				return get_post_meta( $rule->ID, '_roles', true );
				break;
				
			case 'priority':
				return get_post_meta( $rule->ID, '_priority', true );
				break;
		}				
	}
}

/*
*	Validate inputs as numbers and set them to null if 0
*/
function wcqu_validate_number( $number ) {
	
	$number = stripslashes( $number );
//	$number = intval( $number );
	
	if ( $number == 0 ) {
		return null;
	} elseif ( $number < 0 ) {
		return null;
	} 
	
	return $number;
}

/**
 * Provides a fmod function that actually works as intended.
 * 
 * @param float $x The dividend
 * @param float $y The divisor
 *
 * @return float
 */
function wcqu_fmod_round($x, $y) {
	$places = strlen(substr(strrchr((string)$y, "."), 1));
	$i = round($x / $y, $places);
	return round(($x - $i * $y), $places);
}

if ( ! function_exists( 'wpbo_get_applied_rule' ) ) {

	/**
	 * Provides backwards compatibility for plugins that tied into this plugin pre-fork
	 *
	 * @params object    $product WC_Product object
	 * @param  string    User role to get rule from, otherwise current user role is used
	 *
	 * @return mixed     String of rule status / Object top rule post
	 *
	 * @deprecated
	 */
	function wpbo_get_applied_rule( $product ) {
		return wcqu_get_applied_rule( $product );
	}
}

if ( ! function_exists( 'wpbo_get_value_from_rule' ) ) {

	/**
	 * Provides backwards compatibility for plugins that tied into this plugin pre-fork
	 *
	 * @params string    $type Product type
	 * @params object    $product Product Object
	 * @params object    $rule Quantity Rule post object
	 *
	 * @return mixed
	 *
	 * @deprecated
	 */
	function wpbo_get_value_from_rule( $type, $product, $rule ) {
		return wcqu_get_value_from_rule( $type, $product, $rule );
	}
}


/**
 * This is ugly, but we need to pretend that the pre-forked version of this plugin is active since Thumbnail Quantities 
 * does a check whether it active. It'd be nice if they just made it a filter instead. Also TQ has some incorrect 
 * javascript rounding on decimal values, so we may just have to fork it at some point as well.
 */
add_filter( 'active_plugins', function($plugins){
	if(!in_array('woocommerce-incremental-product-quantities/product-quantity-rules.php', $plugins)){
		$plugins[] = 'woocommerce-incremental-product-quantities/product-quantity-rules.php';
	}
	return $plugins;
});
