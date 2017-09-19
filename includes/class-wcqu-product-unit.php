<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'WC_Quantities_and_Units_Product_Unit' ) ) :

class WC_Quantities_and_Units_Product_Unit {
	public function __construct() {
		add_filter( 'woocommerce_get_price_suffix', array( $this, 'get_price_suffix' ), 10, 3 );
		add_action( 'woocommerce_add_order_item_meta', array( $this, 'add_order_item_meta' ), 10, 2 );
		add_filter( 'woocommerce_widget_cart_item_quantity', array( $this, 'get_widget_item_quantity' ), 10, 3 );
	}

	/**
	 * @param   string $html
	 * @param   array $cart_item
	 * @param   $cart_item_key
	 *
	 * @return string
	 */
	public function get_widget_item_quantity( $html, $cart_item, $cart_item_key ) {
		$_product = $cart_item['data'];

		$unit = $this->get_unit_for_product( $cart_item['product_id'] );

		if ( $unit ) {
			$product_price = apply_filters(
				'woocommerce_cart_item_price',
				WC()->cart->get_product_price( $_product ),
				$cart_item,
				$cart_item_key
			);

			$html = '<span class="quantity">' . sprintf( '%s &times; %s', $cart_item['quantity'] . " " . $unit, $product_price ) . '</span>';
		}

		return $html;
	}

	/**
	 * @param   int $product_id
	 *
	 * @return  string
	 */
	public function get_unit_for_product( $product_id, $default = null ) {
		$unit = get_post_meta( $product_id, 'unit', true );
		$unit = $unit ? $unit : $default;

		return $unit ? apply_filters( 'wciu_default_price_suffix', __( $unit, 'woocommerce' ) ) : '';
	}

	/**
	 * Get the price suffix if the 'unit' meta key has been defined.
	 *
	 * @param   string $price_display_suffix
	 * @param   WC_Product $product
	 *
	 * @return string
	 */
	public function get_price_suffix( $price_display_suffix, $product ) {
		// todo make default unit configuarble
		if ( $unit = $this->get_unit_for_product( $product->get_id(), apply_filters( 'wciu_default_price_suffix', __( '', 'woocommerce' ) ) ) ) {
			$price_display_suffix = "/" . $unit . " " . $price_display_suffix;
		}

		return $price_display_suffix;
	}

	/**
	 * @param   string $price The sale price html
	 * @param   string $from String or float to wrap with 'from' text
	 * @param   mixed $to String or float to wrap with 'to' text
	 * @param   WC_Product $product
	 *
	 * @return  string
	 */
	public function sale_price_from_to( $price, $from, $to, $product ) {
		if ( $unit = get_post_meta( $product->get_id(), 'unit', true ) ) {
			$price = '<del>' . ( ( is_numeric( $from ) ) ? wc_price( $from ) : $from ) . '/' . $unit . '</del> <ins>' . ( ( is_numeric( $to ) ) ? wc_price( $to ) : $to ) . '/' . $unit . '</ins>';
		}

		return $price;
	}

	/**
	 * @param   string $html
	 * @param   WC_Product $product
	 *
	 * @return  string
	 */
	public function price_html( $html, $product ) {
		$unit = get_post_meta( $product->get_id(), 'unit', true );
		if ( $unit ) {
			return $html . '/' . $unit;
		}

		return $html;
	}

	public function add_order_item_meta( $item_id, $values ) {
		$unit = $this->get_unit_for_product( $values['product_id'] );
		if ( $unit ) {
			wc_add_order_item_meta( $item_id, __( "Unit", 'woocommerce' ) . " ($unit)", $values['quantity'] );
		}
	}
}

endif;

return new WC_Quantities_and_Units_Product_Unit();