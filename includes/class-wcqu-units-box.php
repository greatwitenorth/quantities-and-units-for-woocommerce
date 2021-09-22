<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if ( ! class_exists( 'WC_Quantities_and_Units_Units_Box' ) ) :

	class WC_Quantities_and_Units_Units_Box {
		public function __construct() {
			add_action( 'woocommerce_product_data_panels', array( $this, 'units_box_create' ) );
			add_action( 'save_post', array( $this, 'save_unit_meta_data' ) );
			add_action( 'woocommerce_product_write_panel_tabs', array($this, 'units_box_tab'), 99 );
		}

		function units_box_tab() {
			echo '<li class="wciu_units_panel hide_if_grouped"><a href="#wciu_units_panel">Unit</a></li>';
		}

		public function units_box_create() {
			global $post,$woocommerce;
			$unit = get_post_meta($post->ID, 'unit', true);
			?>
			<div id="wciu_units_panel" class="panel woocommerce_options_panel">
			<div class="options_group hide_if_grouped">
				<p class="form-field _unit_field">
					<label for="unit">Unit of measurement</label>
					<input type="text" name="unit" value="<?php echo $unit ?>" placeholder="Unit ie. kg, lbs, oz">
					<input type="hidden" name="_wciu_nonce" value="<?php echo wp_create_nonce(plugin_basename( __FILE__ )) ?>">
				</p>
			</div>
			</div>
			<?php
		}

		public function save_unit_meta_data( $post_id ) {
			// Validate Post Type
			if ( ! isset( $_POST['post_type'] ) or $_POST['post_type'] !== 'product' ) {
				return;
			}

			// Validate User
			if ( !current_user_can( 'edit_post', $post_id ) ) {
				return;
			}

			// Verify Nonce
			if ( ! isset( $_POST["_wciu_nonce"] ) or ! wp_verify_nonce( $_POST["_wciu_nonce"], plugin_basename( __FILE__ ) ) ) {
				return;
			}

			// Update Unit Meta Values
			if( isset( $_POST['unit'] )) {
				update_post_meta(
					$post_id,
					'unit',
					strip_tags( $_POST['unit'] )
				);

			} else {
				update_post_meta(
					$post_id,
					'unit',
					''
				);
			}
		}
	}

endif;

return new WC_Quantities_and_Units_Units_Box();
