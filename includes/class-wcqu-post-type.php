<?php 
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if ( ! class_exists( 'WC_Quantities_and_Units_Quantity_Rule_Post_Type' ) ) :

class WC_Quantities_and_Units_Quantity_Rule_Post_Type {
	
	public function __construct() {
		
		// Add the quantity-rule post type
		add_action( 'init', array( $this, 'quantity_rule_init' ) );
		
		// Adjust post type columns on list view
		add_action( 'manage_edit-quantity-rule_columns', array( $this, 'quantity_rule_columns' ), 10, 2 );
		add_action( 'manage_quantity-rule_posts_custom_column', array( $this, 'manage_quantity_rule_columns' ), 10, 2);
		add_filter( 'manage_edit-quantity-rule_sortable_columns', array( $this, 'sortable_quantity_rule_columns' ) ); 
		
		// Add custom meta boxes
		add_action( 'add_meta_boxes', array( $this, 'quantity_rule_meta_init' 	) );
		add_action( 'add_meta_boxes', array( $this, 'quantity_rule_tax_init' 	) );
		add_action( 'add_meta_boxes', array( $this, 'quantity_rule_tag_init' 	) );
		add_action( 'add_meta_boxes', array( $this, 'quantity_rule_role_init' 	) );
		add_action( 'add_meta_boxes', array( $this, 'rate_us_notice' 			) );
//		add_action( 'add_meta_boxes', array( $this, 'input_thumbnail_notice' 	) );
		add_action( 'add_meta_boxes', array( $this, 'company_notice' 			) );
		
		// Save post meta on post update
		add_action( 'save_post', array( $this, 'save_quantity_rule_meta'  ) );
		add_action( 'save_post', array( $this, 'save_quantity_rule_taxes' ) );
		add_action( 'save_post', array( $this, 'save_quantity_rule_tags'  ) );
		add_action( 'save_post', array( $this, 'save_quantity_rule_roles' ) );
	}
	
	/*
	*	Register Quantity Rule Post Type
	*/	
	public function quantity_rule_init() {
	
		$labels = array(
			'name'               => 'Quantity Rules',
			'singular_name'      => 'Quantity Rule',
			'add_new'            => 'Add New',
			'add_new_item'       => 'Add New Rule',
			'edit_item'          => 'Edit Rule',
			'new_item'           => 'New Rule',
			'all_items'          => 'All Rules',
			'view_item'          => 'View Rule',
			'search_items'       => 'Search Ruless',
			'not_found'          => 'No rules found',
			'not_found_in_trash' => 'No rules found in Trash',
			'parent_item_colon'  => '',
			'menu_name'          => 'Quantity Rules'
		);
		
		$args = array(
			'labels'             => $labels,
			'public'             => false,
			'publicly_queryable' => false,
			'show_ui'            => true,
			'show_in_menu'       => true,
			'query_var'          => true,
			'rewrite'            => array( 'slug' => 'quantity-rule' ),
			'capability_type'    => 'post',
			'has_archive'        => true,
			'hierarchical'       => false,
			'menu_position'      => null,
			'supports'           => array( 'title' ),
			'taxonomies' 		 => array(),
		);
		
		register_post_type( 'quantity-rule', $args );
	}
	
	/*
	*	Register Custom Columns for List View
	*/	
	public function quantity_rule_columns( $column ) {
	 	
	 	unset( $column['date'] );
	 	
	    $new_columns['priority'] = __('Priority');
	    $new_columns['min'] = __('Minimum');
	    $new_columns['max'] = __('Maximum');
	    $new_columns['step'] = __('Step Value');     
	    $new_columns['cats'] = __('Categories');
	    $new_columns['product_tags'] = __('Tags');
	    $new_columns['roles'] = __('Roles');
	    $new_columns['date'] = __('Date');

	    return array_merge( $column, $new_columns );
	}
	
	/*
	*	Get Custom Columns Values for List View
	*/	 
	public function manage_quantity_rule_columns($column_name, $id) {
	    
	    switch ($column_name) {
	    
		    case 'priority':
		        echo get_post_meta( $id, '_priority', true );
		        break;
		 
		    case 'min':
	   	        echo get_post_meta( $id, '_min', true );
		        break;
		        
		    case 'max':
	   	        echo get_post_meta( $id, '_max', true );
		        break;
		        
		    case 'step':
		        echo get_post_meta( $id, '_step', true );	       
		        break;
		        
		    case 'cats':
		   		$cats = get_post_meta( $id, '_cats', false);
		   		if ( $cats != false and count( $cats[0] ) > 0 ) {	   		
			   		foreach ( $cats[0] as $cat ){
		
			   			$taxonomy = 'product_cat'; 	
				   		$term = get_term_by( 'id', $cat, $taxonomy );
			   			$link = get_term_link( $term );	
			   			
			   			echo "<a href='" . $link . "'>" . $term->name . "</a><br />";	
			   		}
			   	} 
		        break;  
		        
		    case 'product_tags':
		    	$tags = get_post_meta( $id, '_tags', false);
		   		if ( $tags != null and count( $tags[0] ) > 0) {	   		
			   		foreach ( $tags[0] as $tag ){
		
			   			$taxonomy = 'product_tag'; 	
				   		$term = get_term_by( 'id', $tag, $taxonomy );
			   			$link = get_term_link( $term );	
			   			
			   			echo "<a href='" . $link . "'>" . $term->name . "</a><br />";	
			   		}
			   	} 
		    	break;
		    
		    case 'roles':
		   		$roles = get_post_meta( $id, '_roles', false);
		   		if ( $roles != null and count( $roles[0] ) > 0) {	   		
			   		foreach ( $roles[0] as $role ){
			   			echo ucfirst( $role ) . "<br />";	
			   		}
			   	} 
		    	break;
		    	
		    default:
		        break;
	    } 
	}   
	
	/*
	*	Make Custom Columns Sortable
	*/	
	public function sortable_quantity_rule_columns( $columns ) {  
	    
	    $columns['priority'] = __('Priority');
	    $columns['min'] = __('Minimum');
	    $columns['max'] = __('Maximum');
	    $columns['step'] = __('Step Value');
	  
	    return $columns;  
	}  
	
	/*
	*	Register and Create Rule Options Meta Box for Quantity Rules
	*/	
	public function quantity_rule_meta_init() {
		add_meta_box(
			'wpbo-quantity-rule-meta', 
			'Set Quantity Rule Options', 
			array( $this, 'quantity_rule_meta' ), 
			'quantity-rule', 
			'normal', 
			'high'
		);
	}
	
	public function quantity_rule_meta( $post ) {
		
		$min  = get_post_meta( $post->ID, '_min', true);
		$max  = get_post_meta( $post->ID, '_max', true);
		$min_oos = get_post_meta( $post->ID, '_min_oos', true );
		$max_oos = get_post_meta( $post->ID, '_max_oos', true );		
		$step = get_post_meta( $post->ID, '_step', true);
		$priority = get_post_meta( $post->ID, '_priority', true);
		
		// Create Nonce Field
		wp_nonce_field( plugin_basename( __FILE__ ), '_wpbo_rule_value_nonce' );
		
		?>
			<div class="wpbo-meta">
				<label for="min">Minimum</label>
				<input type="number" name="min" id="min" value="<?php echo $min ?>" step="any" />
			
				<label for="max">Maximum</label>
				<input type="number" name="max" id="max" value="<?php echo $max ?>" step="any" />
				
				<label for="_wpbo_minimum_oos">Out of Stock Minimum</label>
				<input type="number" name="min_oos" value="<?php echo $min_oos ?>" step="any" />
				
				<label for="_wpbo_maximum_oos">Out of Stock Maximum</label>
				<input type="number" name="max_oos" value="<?php echo $max_oos ?>" step="any" />
				
				<label for="step">Step Value</label>
				<input type="number" step="any" name="step" id="step" value="<?php echo $step ?>" step="any" />
				
				<label for="step">Priority</label>
				<input type="number" name="priority" id="priority" value="<?php echo $priority ?>" />			
			</div>
			<p><em>*Note - the minimum value must be greater then or equal to the step value.</em><br />
			<em>*Note - The rule with the lowest priority number will be used if multiple rules are applied to a single product.</em></p>
		<?php	
	}
	
	/*
	*	Register and Create Product Category Meta Box for quantity Rule
	*/	
	public function quantity_rule_tax_init() {
		add_meta_box(	
			'wpbo-quantity-rule-tax-meta', 
			'Product Categories', 
			array( $this, 'quantity_rule_tax_meta' ), 
			'quantity-rule', 
			'normal', 
			'high'
		);
	}
	
	function quantity_rule_tax_meta( $post ) {
	
		// Get selected categories
		$cats = get_post_meta( $post->ID, '_cats', false);
	
		if ( $cats != null ) {
			$cats = $cats[0];
		}
		
		// Get all possible categories
		$tax_name = 'product_cat';
		
		$args = array( 
			'parent' => 0,
			'hide_empty' => false
			);
		
		$terms = get_terms( $tax_name, $args );
		
		if ( $terms ){
			
			// Create Nonce Field
			wp_nonce_field( plugin_basename( __FILE__ ), '_wpbo_tax_nonce' );
		
			echo '<ul class="rule-product-cats level-1">';
			foreach ( $terms as $term ) {
				$this->print_tax_inputs( $term, $tax_name, $cats, 2 );
			}
			echo '</ul>';
		}
	}
		
	/*
	*	Will Recursivly Print all Product Categories with heirarcy included
	*/
	public function print_tax_inputs( $term, $taxonomy_name, $cats, $level ) { 
		
		// Echo Single Item
		?>
			<li>
				<input type="checkbox" id="_wpbo_cat_<?php echo $term->term_id ?>" name="_wpbo_cat_<?php echo $term->term_id ?>" <?php if ( is_array( $cats ) and in_array( $term->term_id, $cats )) echo 'checked="checked"' ?> /><?php echo $term->name; ?>
			</li>
		<?php 
		
		// Get any Children
		$children = get_term_children( $term->term_id, $taxonomy_name );
		
		// Continue to print children if they exist
		if ( $children ){
			echo '<ul class="level-' . $level . '">';
			$level++;
			foreach ( $children as $child_id ){
				$child = get_term_by( 'id', $child_id, $taxonomy_name );
				// If the child is at the second level relative to the last printed element, exclude it
				if ( is_object( $child ) and $child->parent == $term->term_id ) {
					$this->print_tax_inputs( $child, $taxonomy_name, $cats, $level );
				}
			}
			echo '</ul>';
		}
	}
	
	/*
	*	Allow users to apply rules by tags
	*/	
	public function quantity_rule_tag_init() {
		add_meta_box(	
			'wpbo-quantity-rule-tag-meta', 
			'Product Tags', 
			array( $this, 'quantity_rule_tag_meta' ), 
			'quantity-rule', 
			'normal', 
			'high'
		);
	}
	
	public function quantity_rule_tag_meta( $post ) {
		
		// Get all Tags
		$args = array(
		    'orderby'       => 'name', 
		    'order'         => 'ASC',
		    'hide_empty'    => false, 
	
		); 

		$tags = get_terms( 'product_tag', $args );
		
		$included_tags = get_post_meta( $post->ID, '_tags');
		if ( $included_tags != false ){
			$included_tags = $included_tags[0];
		}

		// If Tags exists, show them all
		if ( $tags ) {
		
			// Create Nonce Field
			wp_nonce_field( plugin_basename( __FILE__ ), '_wpbo_tag_nonce' );

			?>
				<ul id='product-tags' class='rule-product-cats'>
					<?php foreach ( $tags as $tag ): ?>
						<li>
							<input type='checkbox' name='_wpbo_tag_<?php echo $tag->term_id ?>' id='wpbo_tag_<?php echo $tag->term_id ?>' <?php if( in_array( $tag->term_id, $included_tags ) ) echo 'checked' ?> />
							<?php echo $tag->name ?>
						</li>
					<?php endforeach; ?>
				</ul>
			<?php 
		}
	}

	/*
	*	Register and Create User Role Option for Quantity Rules
	*/	
	public function quantity_rule_role_init() {
		add_meta_box(
			'wpbo-quantity-rule-role', 
			'User Roles', 
			array( $this, 'quantity_rule_role' ), 
			'quantity-rule', 
			'normal', 
			'high'
		);
	}
	
	public function quantity_rule_role( $post ) {

		// Get all user roles
		global $wp_roles;
		$roles = $wp_roles->get_names();
		
		// Get applied roles
		$applied_roles = get_post_meta( $post->ID, '_roles' );
		if ( $applied_roles != false ){
			$applied_roles = $applied_roles[0];
		}
		// Create Nonce Field
		wp_nonce_field( plugin_basename( __FILE__ ), '_wpbo_role_nonce' );
		
		if ( $roles ): ?>
			<ul>
				<?php foreach ( $roles as $slug => $name ): ?>
					<li>
						<input type='checkbox' name='_wpbo_role_<?php echo $slug ?>' id='wpbo_role_<?php echo $slug ?>'  <?php if( in_array( $slug, $applied_roles ) || empty($applied_roles) ) echo 'checked' ?> />
						<?php echo $name; ?>
					</li>
				<?php endforeach; ?>
				<li>
					<input type='checkbox' name='_wpbo_role_guest' id='wpbo_role_guest'  <?php if( in_array( 'guest', $applied_roles ) || empty($applied_roles) ) echo 'checked' ?> />
				Guest
				</li>
			</ul>
		<?php endif;
	}

	/*
	*	Register and Create Meta Box to encourage user to install our thumbnail plugin
	*/	
	public function input_thumbnail_notice() {
	
		// Only show meta box if user has not installed thumbnail plugin
		if ( !in_array( 'woocommerce-thumbnail-input-quantities/woocommerce-thumbnail-input-quantity.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) {
		
			add_meta_box(	
				'wpbo-input-thumbnail-notice', 
				'Urgent Notice', 
				array( $this, 'input_thumbnail_notice_meta' ), 
				'quantity-rule', 
				'side', 
				'high'
			);
		}
	}
	
	public function input_thumbnail_notice_meta( $post ) {
		
		echo "We've noticed you do not have <a href='http://wordpress.org/plugins/woocommerce-thumbnail-input-quantities/' target='_blank'>WooCommerce Thumbnail Input Quantity</a> installed. <br /><br />Installation is <strong>highly recommended</strong> as it shows an input box (with your quantity rules) from all product thumbnails such as on the shop page and in the related prodcuts section.";
		
	}
	
	/*
	*	Register and Create Meta Box to encourage user to install our thumbnail plugin
	*/	
	public function company_notice() {
	
		add_meta_box(	
			'wpqu-company-notice', 
			'Rapid Order', 
			array( $this, 'company_notice_meta' ), 
			'quantity-rule', 
			'side', 
			'low'
		);
	}
	
	public function company_notice_meta( $post ) {
		
		?>
			<a href="http://rapidorderplugin.com/?utm_source=QU%20Plugin%20Admin&utm_medium=sidebar&utm_campaign=Fast%20order%20form%20for%20WooCommerce" target="_blank"><img align="center" src="<?php echo plugins_url() ?>/quantities-and-units-for-woocommerce/assets/img/rapid-order-logo.png" /></a>
			<p>
				<a href="http://rapidorderplugin.com/?utm_source=QU%20Plugin%20Admin&utm_medium=sidebar&utm_campaign=Fast%20order%20form%20for%20WooCommerce" target="_blank">Fast order form for WooCommerce</a>
			</p>
		<?php 
	}
	
		/*
	*	Register and Create Meta Box to encourage user to install our thumbnail plugin
	*/	
	public function rate_us_notice() {
	
		add_meta_box(	
			'wpbo-additional-info', 
			'Additional Information', 
			array( $this, 'additional_info_notice_meta' ), 
			'quantity-rule', 
			'side', 
			'low'
		);
	}
	
	public function additional_info_notice_meta( $post ) {
		?>
			<div style="text-align: center">
				<h3>Enjoy this plugin?</h3>
				<a href="http://wordpress.org/support/view/plugin-reviews/quantities-and-units-for-woocommerce" target="_blank">Rate us on Wordpress.org!</a>
			
				<h3>Need Support?</h3>
				<a href="http://wordpress.org/support/plugin/quantities-and-units-for-woocommerce" target="_blank">Visit our Support Forum</a> 
			</div>
		<?php 
	}
	
	/*
	*	Save Rule Meta Values
	*/	
	public function save_quantity_rule_meta( $post_id ) {
		
		// Validate Post Type
		if ( ! isset( $_POST['post_type'] ) or $_POST['post_type'] !== 'quantity-rule' ) {
			return;
		}
		
		// Validate User
		if ( !current_user_can( 'edit_post', $post_id ) ) {
	        return;
	    }
	
		// Verify Nonce
	    if ( ! isset( $_POST["_wpbo_rule_value_nonce"] ) or ! wp_verify_nonce( $_POST["_wpbo_rule_value_nonce"], plugin_basename( __FILE__ ) ) ) {
	        return;
	    }
	
		// Remove the rule/role transient
		global $wp_roles;
		$roles = $wp_roles->get_names();
		foreach ( $roles as $slug => $name ) {
			delete_transient( 'ipq_rules_' . $slug );
		}
	
		//Also delete the guest transient, which is not a role

		delete_transient( 'ipq_rules_guest' );

		// Make sure $min >= step
		if( isset( $_POST['min'] ) ) {
			$min = $_POST['min'];
		}
		
		// Update Step 
		if ( isset( $_POST['step'] ) and isset( $min ) ) {
			if ( $min < $_POST['step'] && $min > 0) {
				$min = $_POST['step'];
			}
		}
		
		// Get Min Out of Stock
		if( isset( $_POST['min_oos'] ) ) {
			$min_oos = $_POST['min_oos'];
			update_post_meta( $post_id, '_min_oos', stripslashes( $min_oos ) );
		}
		
		// Update Min
		if ( isset( $min ) ) {
			update_post_meta( $post_id, '_min', stripslashes( $min ) );
		}
		
		// Update Max
		if ( isset( $_POST['max'] ) ) {
			$max = $_POST['max'];
			
			// Validate Max is not less then Min
			if ( isset( $min ) and $max < $min and $max != 0 ) {
				$max = $min;
			}
			
			update_post_meta( $post_id, '_max', wcqu_validate_number( $max ) );
		}
		
		// Update Max OOS
		if ( isset( $_POST['max_oos'] ) ) {
			$max_oos = $_POST['max_oos'];
				
			// Max must be bigger then min
			if ( $max_oos != '' and isset( $min_oos ) and $min_oos != 0 ) {
				if ( $min_oos > $max_oos )
					$max_oos = $min_oos;
				
			} elseif ( $max_oos != '' and isset( $min ) and $min != 0 ){
				if ( $min > $max_oos ) {
					$max_oos = $min;
				}
			}
				
			update_post_meta( $post_id, '_max_oos', wcqu_validate_number( $max_oos ) );
		}
		
		// Update Step
		if ( isset( $_POST['step'] ) ) {
			update_post_meta( $post_id, '_step', wcqu_validate_number( $_POST['step'] ) );
		}
		
		// Update Priority
		if ( isset( $_POST['priority'] ) ) {
			update_post_meta( $post_id, '_priority', wcqu_validate_number( $_POST['priority'] ) );
		}
		
	}
	
	/*
	*	Save Rule Taxonomy Values
	*/	
	public function save_quantity_rule_taxes( $post_id ) {
		
		// Validate Post Type
		if ( ! isset( $_POST['post_type'] ) or $_POST['post_type'] !== 'quantity-rule' ) {
			return;
		}
		
		// Validate User
		if ( !current_user_can( 'edit_post', $post_id ) ) {
	        return;
	    }
	
		// Verify Nonce
	    if ( ! isset( $_POST["_wpbo_tax_nonce"] ) or ! wp_verify_nonce( $_POST["_wpbo_tax_nonce"], plugin_basename( __FILE__ ) ) ) {
	        return;
	    }
	
		// Check which Categories have been selected
		$tax_name = 'product_cat';
		$args = array( 'hide_empty' => false );
		$terms = get_terms( $tax_name, $args );
		$cats = array();
	
		// See which terms were included
		foreach ( $terms as $term ) {
			$term_name = '_wpbo_cat_' . $term->term_id;
			if ( isset( $_POST[ $term_name ] ) and $_POST[ $term_name ] == 'on' ) {
				array_push( $cats, $term->term_id );		
			} 
		}
		
		// Add them to the post meta
		delete_post_meta( $post_id, '_cats' );
		update_post_meta( $post_id, '_cats', $cats, false );
	} 
	
	/*
	*	Save Rule Tag Values
	*/	
	public function save_quantity_rule_tags( $post_id ) {
		
		// Validate Post Type
		if ( ! isset( $_POST['post_type'] ) or $_POST['post_type'] !== 'quantity-rule' ) {
			return;
		}
		
		// Validate User
		if ( !current_user_can( 'edit_post', $post_id ) ) {
	        return;
	    }
	
		// Verify Nonce
	    if ( ! isset( $_POST["_wpbo_tag_nonce"] ) or ! wp_verify_nonce( $_POST["_wpbo_tag_nonce"], plugin_basename( __FILE__ ) ) ) {
	        return;
	    }
	    
	    // Get all Tags
		$args = array (
		    'orderby'       => 'name', 
		    'order'         => 'ASC',
		    'hide_empty'    => false, 
		); 

		$tags = get_terms( 'product_tag', $args );
		
		$tags_included = array();
		
		// If the tags are set, loop through them
		if ( $tags ) {
			foreach ( $tags as $tag ) {
				
				$tag_name = '_wpbo_tag_' . $tag->term_id;
				if ( isset( $_POST[ $tag_name ] ) and $_POST[ $tag_name ] == 'on' ) {
					array_push( $tags_included, $tag->term_id );		
				} 
			}
			
			// Add them to the post meta
			delete_post_meta( $post_id, '_tags' );
			update_post_meta( $post_id, '_tags', $tags_included, false );
		}
	}
	
	/*
	*	Save Rule Role Values
	*/	
	public function save_quantity_rule_roles( $post_id ) {
		
		// Validate Post Type
		if ( ! isset( $_POST['post_type'] ) or $_POST['post_type'] !== 'quantity-rule' ) {
			return;
		}
		
		// Validate User
		if ( !current_user_can( 'edit_post', $post_id ) ) {
	        return;
	    }
	
		// Verify Nonce
	    if ( ! isset( $_POST["_wpbo_role_nonce"] ) or ! wp_verify_nonce( $_POST["_wpbo_role_nonce"], plugin_basename( __FILE__ ) ) ) {
	        return;
	    }
	    
	    // Get available user roles
	    global $wp_roles;
		$roles = $wp_roles->get_names();
		$applied_roles = array();
		
		// Loop through roles
		foreach ( $roles as $slug => $name ) {
			$role_name = '_wpbo_role_' . $slug;
			
			// If role is set add it to the applied list
			if ( isset( $_POST[ $role_name ] ) and $_POST[ $role_name ] == 'on' ) {
				array_push( $applied_roles, $slug );
			}
		}
		
		// If guest role is set add it to the applied list
		if ( isset( $_POST[ '_wpbo_role_guest' ] ) and $_POST[ '_wpbo_role_guest' ] == 'on' ) {
			array_push( $applied_roles, 'guest' );
		}


		// Add them to the post meta
		delete_post_meta( $post_id, '_roles' );
		update_post_meta( $post_id, '_roles', $applied_roles, false );
    
	}	
}

endif;

return new WC_Quantities_and_Units_Quantity_Rule_Post_Type();
