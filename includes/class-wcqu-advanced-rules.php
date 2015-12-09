<?php 
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if ( ! class_exists( 'WC_Quantities_and_Units_Advanced_Rules' ) ) :

class WC_Quantities_and_Units_Advanced_Rules {
	
	public function __construct() {
		
		// Add Advanced Rules link under quantity rules
		add_action( 'admin_menu', array( $this, 'add_advanced_rule_page' ) );
	}
	
	/*
	* Add Import Page
	*/
	public function add_advanced_rule_page() {
		
		$slug = add_submenu_page(
			'edit.php?post_type=quantity-rule', 
			'Advanced Rules', 
			'Advanced Rules', 
			'edit_posts', 
			basename(__FILE__), 
			array( $this, 'advanced_rules_page_content')
		);
		
		// Load action, checks for posted form
		add_action( "load-{$slug}", array( $this, 'page_loaded') );
		
	}
	
	/*
	* 	Processes save settings if applicable and redirect the user with a success messsage
	*/
	public function page_loaded() {
		
		if ( isset( $_POST["ipq-advanced-rules-submit"] ) and $_POST["ipq-advanced-rules-submit"] == 'Y' ) {
			
			check_admin_referer( "ipq-advanced-rules" );
			$this->save_settings();
			$url_parameters = 'updated=true';
			wp_redirect(admin_url('edit.php?post_type=quantity-rule&page=class-wcqu-advanced-rules.php&'.$url_parameters));
			exit;
		}
	}
	
	/*
	*	Update the settings based on the post values
	*/
	public function save_settings() {
		
		// Get Settings
		$settings = get_option( 'ipq_options' );
		
		// Minimum Product Notification 
		if ( isset( $_POST['ipq_show_qty_note'] ) and $_POST['ipq_show_qty_note'] == 'on' ) {
			$settings['ipq_show_qty_note'] = 'on';
		} else {
			$settings['ipq_show_qty_note'] = '';
		}
		
		// Minimum Note Text 
		if ( isset( $_POST['ipq_qty_text'] ) and $_POST['ipq_qty_text'] != '' ) {
			$settings['ipq_qty_text'] = stripslashes( $_POST['ipq_qty_text'] );
		} else {
			$settings['ipq_qty_text'] = '';
		}
		
		// Minimum Note Position
		if ( isset( $_POST['ipq_show_qty_note_pos'] ) and $_POST['ipq_show_qty_note_pos'] == 'below' ) {
			$settings['ipq_show_qty_note_pos'] = 'below';
		} else {
			$settings['ipq_show_qty_note_pos'] = 'above';
		}
		
		// Minimum Note Class
		if ( isset( $_POST['ipq_qty_class'] ) ) {
			$settings['ipq_qty_class'] = stripslashes( $_POST['ipq_qty_class'] );
		} 
		
		// Active Rule
		if ( isset( $_POST['ipq_site_rule_active'] ) and $_POST['ipq_site_rule_active'] == 'on' ) {
			$settings['ipq_site_rule_active'] = 'on';
		} else {
			$settings['ipq_site_rule_active'] = '';
		}

		if ( isset( $_POST['ipq_site_min'] )) {
			$min  = wcqu_validate_number( $_POST['ipq_site_min'] );
		}
		
		if ( isset( $_POST['ipq_site_step'] )) {
			$step = wcqu_validate_number( $_POST['ipq_site_step'] );
		}
		
		if ( isset( $_POST['ipq_site_max'] )) {
			$max = wcqu_validate_number( $_POST['ipq_site_max'] );
		}
		
		if ( isset( $_POST['ipq_site_min_oos'] )) {
			$min_oos = wcqu_validate_number( $_POST['ipq_site_min_oos'] );
		}
		
		if ( isset( $_POST['ipq_site_max_oos'] )) {
			$max_oos = wcqu_validate_number( $_POST['ipq_site_max_oos'] );
		}

		// Make sure min >= step
		if ( isset( $step ) and isset( $min ) ) {
			if ( $min < $step ) {
				$min = $step;
			}
		}
		
		// Make sure min <= max
		if ( isset( $step ) and isset( $max ) ) {
			if ( $min > $max and $max != '' and $max != 0 ) {
				$max = $min;
			}
		}
		
		// Make sure min_oos <= max and max_oos
		if ( isset( $min_oos ) and $min_oos != 0 ) {
			if ( isset( $max_oos ) and $max_oos != 0 and
				$min_oos > $max_oos ) {

				$max_oos = $min_oos;
			} else if ( !isset( $max_oos ) and isset ( $max ) and
				$max != 0 and $min_oos > $max ) {
				$min_oos = $max;
			}
		}

		// Site Minimum
		if ( isset( $_POST['ipq_site_min'] ) ) {
			$settings['ipq_site_min'] = strip_tags( $min );
		} 
		
		// Site Step 
		if ( isset( $_POST['ipq_site_step'] ) ) {
			$settings['ipq_site_step'] = strip_tags( $step );
		} 
		
		// Site Max
		if( isset( $_POST['ipq_site_max'] )) {
			$settings['ipq_site_max'] = strip_tags( $max );
		}
		
		// Site Min OOS
		if( isset( $_POST['ipq_site_min_oos'] )) {
			$settings['ipq_site_min_oos'] = strip_tags( $min_oos );
		}
		
				// Site Max
		if( isset( $_POST['ipq_site_max_oos'] )) {
			$settings['ipq_site_max_oos'] = strip_tags( $max_oos );
		}

		// Update Settings
		$updated = update_option( 'ipq_options', $settings );

	}
	
	/**
	*	Advanced Rules Page Content
	*/
	public function advanced_rules_page_content() {
		
		$options = get_option( 'ipq_options' );

		if ($options == false) {
			$options = array();
		}

		extract($options);
		$qty_text_default = "Minimum Qty: %MIN%";
		
		?>
		<h2>Advanced Rules</h2>
		<form method="post" action="<?php admin_url( 'edit.php?post_type=quantity-rule&page=class-wcqu-advanced-rules.php' ); ?>">
			<?php wp_nonce_field( "ipq-advanced-rules" ); ?>
			
			<table class="form-table">
				<tr>
					<th>Activate Site Wide Rules?</th>
					<td><input type='checkbox' name='ipq_site_rule_active' id='ipq_site_rule_active'
						<?php if ( isset( $ipq_site_rule_active ) and $ipq_site_rule_active != '' ) echo 'checked'; ?>
					 /></td>
				</tr>

				<?php if ( isset( $ipq_site_rule_active ) and $ipq_site_rule_active != '' ): ?>
				
					<tr>
						<th>Site Wide Product Minimum</th>
						<td><input type='number' name='ipq_site_min' id='ipq_site_min'
							value='<?php if ( isset( $ipq_site_min ) and $ipq_site_min != '' ) echo $ipq_site_min; ?>'
						 /></td>
					</tr>
					
					<tr>
						<th>Site Wide Product Maximum</th>
						<td><input type='number' name='ipq_site_max' id='ipq_site_max'
							value='<?php if ( isset( $ipq_site_max ) and $ipq_site_max != '' ) echo $ipq_site_max; ?>'
						 /></td>
					</tr>
					
					<tr>
						<th>Site Wide Product Minimum Out of Stock</th>
						<td><input type='number' name='ipq_site_min_oos' id='ipq_site_min_oos'
							value='<?php if ( isset( $ipq_site_min_oos ) and $ipq_site_min_oos != '' ) echo $ipq_site_min_oos; ?>'
						 /></td>
					</tr>
					
					<tr>
						<th>Site Wide Product Maximum Out of Stock</th>
						<td><input type='number' name='ipq_site_max_oos' id='ipq_site_max_oos'
							value='<?php if ( isset( $ipq_site_max_oos ) and $ipq_site_max_oos != '' ) echo $ipq_site_max_oos; ?>'
						 /></td>
					</tr>
					
					<tr>
						<th>Site Wide Step Value</th>
						<td><input type='number' step='any' name='ipq_site_step' id='ipq_site_step'
							value='<?php if ( isset( $ipq_site_step ) and $ipq_site_step != '' ) echo $ipq_site_step; ?>'
						 /></td>
					</tr>
					
					<tr>
						<th></th>
						<td>
							<em>*Note - the minimum value must be greater then or equal to the step value.</em>
						</td>
					</tr>
				
				<?php endif; ?>
				
				<tr>
					<th>Show Quantity Notification on Product Page?</th>
					<td><input type='checkbox' name='ipq_show_qty_note' id='ipq_show_qty_note' 
						<?php if ( isset( $ipq_show_qty_note ) and $ipq_show_qty_note != '' ) echo 'checked'; ?>
						/></td>
				</tr>
				
				<tr>
					<th>Notification Position</th>
					<td>
						<select name='ipq_show_qty_note_pos' id='ipq_show_qty_note_pos'>
							<option value='above' <?php if ( isset( $ipq_show_qty_note_pos ) and $ipq_show_qty_note_pos == 'above' ) echo 'selected' ?>>Above Add To Cart</option>
							<option value='below' <?php if ( isset( $ipq_show_qty_note_pos ) and  $ipq_show_qty_note_pos == 'below' ) echo 'selected' ?>>Below Add To Cart</option>
						</select>
					</td>
				</tr>
				
				<tr>
					<th>Quantity Notification Text</th>
					<td><input type='text' name='ipq_qty_text' id='ipq_qty_text' value='<?php 
							if ( isset( $ipq_qty_text ) and $ipq_qty_text != '' ) {
								echo $ipq_qty_text; 
							} else {
								echo $qty_text_default;	
							}
						?>' /></td>
				</tr>
				
				<tr>
					<th></th>
					<td>%MIN% = Minimum Value<br />
						%MAX% = Maximum Value<br />
						%STEP% = Step Value
					</td>
				</tr>
				<tr>
					<th>Custom Quantity Note HTML Class</th>
					<td><input type='text' name='ipq_qty_class' id='ipq_qty_class' value='<?php if ( isset( $ipq_qty_class ) and $ipq_qty_class != '' ) echo $ipq_qty_class; ?>' /></td>
				</tr>
				
				<tr>
					<th>Message Shortcode</th>
					<td>Place in product content to display message <strong>[wpbo_quantity_message]</strong></td>
				</tr>
			</table>
			
			<p class="submit" style="clear: both;">
				<input type="submit" name="Submit"  class="button-primary" value="Update Settings" />
				<input type="hidden" name="ipq-advanced-rules-submit" value="Y" />
			</p>
		</form>
		
		<?php	
	}
}

endif;

return new WC_Quantities_and_Units_Advanced_Rules();
