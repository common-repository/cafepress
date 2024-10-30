<?php

/**
 * The public-facing functionality of the plugin.
 *
 * @link       https://vinamian.com
 * @since      1.0.0
 *
 * @package    Cafepress
 * @subpackage Cafepress/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the public-facing stylesheet and JavaScript.
 *
 * @package    Cafepress
 * @subpackage Cafepress/public
 * @author     Vinamian <vinamian@gmail.com>
 */
class Cafepress_Public {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	private static $allowed_image_html;
	private static $allowed_price_html;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of the plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;

		self::$allowed_price_html = [
			'del' => [
				'aria-hidden' => []
			],
			'ins' => [
				'aria-hidden' => []
			],
			'span' => [
				'class' => []
			],
			'bdi' => [],
		];

		self::$allowed_image_html = [
			'img' => [
				'width' => [],
				'height' => [],
				'src' => [],
				'class' => [],
				'alt' => [],
				'decoding' => [],
				'loading' => [],
				'srcset' => [],
			],
		];

	}

	/**
	 * Register the stylesheets for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Cafepress_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Cafepress_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/cafepress-public.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Cafepress_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Cafepress_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */
		
		$inline_script = 'var ajax_url = "' . admin_url( 'admin-ajax.php' ) . '";';

		wp_add_inline_script( $this->plugin_name, $inline_script, 'before' );

		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/cafepress-public.js', array( 'jquery' ), $this->version, true );
		
		wp_localize_script($this->plugin_name,'cafepress_js_data', array(
            'ajax_url' => admin_url('admin-ajax.php'))
		);

	}

	public static function display_menu_shortcode() {
		$args = array(
			'taxonomy' => 'product_cat',
			'hide_empty' => true,
			'orderby' => 'menu_order',
        	'order' => 'ASC',
		);
	
		$categories = get_terms( $args );

		$cuisine_table_id = 0;

		if ('cfpr_cuisine_table' === get_post_type()) {
			$cuisine_table_id = get_the_ID();
		}

		ob_start();

		include('partials/cafepress-public-shortcode.php');

		$output = ob_get_clean();

		return $output;
	}

	public function display_menu_shortcode_in_cuisine_table($content) {
		global $post;
	
		if ('cfpr_cuisine_table' === $post->post_type) {
			return do_shortcode('[cafepress_menu]');
		}
	
		return $content;
	}

	public static function display_bottom_mini_cart() {
		global $woocommerce , $product;

		$product_id = get_the_ID($product);
        $tax_enabled  = wc_tax_enabled() && WC()->cart->get_cart_tax() !== '';
        $has_shipping = WC()->cart->needs_shipping() && WC()->cart->show_shipping();

		WC()->cart->calculate_totals();

		

		
		include('partials/cafepress-public-display.php');

	}

	public static function ajax_add_item_to_cart() {
		check_ajax_referer('cafepress_add_to_cart', 'security');
		ob_start();
		$product_id = apply_filters('woocommerce_add_to_cart_product_id', isset($_POST['product_id']) ? absint($_POST['product_id']) : 0);
		$quantity = empty($_POST['quantity']) ? 1 : wc_stock_amount(sanitize_text_field(wp_unslash($_POST['quantity'])));
		$product_status = get_post_status($product_id);

		$cuisine_table_id = isset($_POST['cuisine_table_id']) ? sanitize_text_field(wp_unslash($_POST['cuisine_table_id'])) : 0;

		// Create a new cart item data array
		$cart_item_data = array(
			'cafepress_cuisine_table' => $cuisine_table_id,
		);

		$cart_item_key = WC()->cart->add_to_cart($product_id, $quantity, NULL, $cart_item_data);		

		if ($cart_item_key && 'publish' === $product_status) {

			$data = self::update_cart_fragments();
			$product = wc_get_product($product_id);
			$new_item = [
				'key' => $cart_item_key,
				'qty' => $quantity,
				'price' => WC()->cart->get_product_price($product),
				'product' => [
					'id' => $product_id,
					'image' => $product->get_image('thumbnail'),
					'name' => $product->get_name()
				]
			];
			$data['new_item'] = $new_item;
		} else {

			$data = array(
				'error' => 'add_item_to_cart error',
			);

		}

		ob_get_clean();

		wp_send_json($data);

		wp_die();
	}
	
	public static function ajax_update_item_in_cart()
	{
		check_ajax_referer('cafepress_update_mini_cart', 'security');
		$quantity = isset($_POST['quality']) ? sanitize_text_field(wp_unslash($_POST['quality'])) : 0;

		// Get mini cart
		ob_start();

		foreach (WC()->cart->get_cart() as $cart_item_key => $cart_item) {
			$post_cart_item_key = isset($_POST['cart_item_key']) ? sanitize_text_field(wp_unslash($_POST['cart_item_key'])) : 0;
			if ($cart_item_key == $post_cart_item_key) {
				WC()->cart->set_quantity($cart_item_key, $quantity, $refresh_totals = true);
				$_product = apply_filters('woocommerce_cart_item_product', $cart_item['data'], $cart_item, $cart_item_key);
				$get_product_subtotal = apply_filters('woocommerce_cart_item_subtotal', WC()->cart->get_product_subtotal($_product, $quantity), $cart_item, $cart_item_key);
			}
		}

		$data = self::update_cart_fragments();
		ob_get_clean();
		wp_send_json($data);

		die();
	}

	public static function ajax_remove_item_from_cart() {
		check_ajax_referer('cafepress_update_mini_cart', 'security');
		$post_product_id = isset($_POST['product_id']) ? sanitize_text_field(wp_unslash($_POST['product_id'])) : 0;
		$post_cart_item_key = isset($_POST['cart_item_key']) ? sanitize_text_field(wp_unslash($_POST['cart_item_key'])) : 0;
		// Get mini cart
		ob_start();

		foreach (WC()->cart->get_cart() as $cart_item_key => $cart_item) {
			if ($cart_item['product_id'] == $post_product_id && $cart_item_key == $post_cart_item_key) {
				WC()->cart->remove_cart_item($cart_item_key);
			}
		}

		$data = self::update_cart_fragments();
		ob_get_clean();
		wp_send_json($data);

		die();
	}

	public static function update_cart_fragments() {
		
		WC()->cart->calculate_totals();
		$total = WC()->cart->get_cart_subtotal();
		// Fragments and mini cart are returned
		$data = array(
			'total' => $total,
			'total_qty' => WC()->cart->cart_contents_count,
		);
		return $data;
	}

	public function add_cuisine_table_to_order_meta( $order_id, $order ) {
	
		$order_cuisine_table_id = $order->get_meta( 'cafepress_cuisine_table');

		$order_items = $order->get_items();

		foreach ( $order_items as $item_id => $order_item ) {
			$cuisine_table_id = $order_item->get_meta( 'cafepress_cuisine_table' );

            if ( $cuisine_table_id &&!$order_cuisine_table_id ) {
                $order->update_meta_data( 'cafepress_cuisine_table', $cuisine_table_id );
				break;
            }
		}

	}
}