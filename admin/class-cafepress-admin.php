<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://vinamian.com
 * @since      1.0.0
 *
 * @package    Cafepress
 * @subpackage Cafepress/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Cafepress
 * @subpackage Cafepress/admin
 * @author     Vinamian <vinamian@gmail.com>
 */

require_once plugin_dir_path( dirname( __FILE__ ) ) . 'vendor/autoload.php';

use chillerlan\QRCode\QRCode;
use chillerlan\QRCode\QROptions;

class Cafepress_Admin {

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

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of this plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;

	}

	/**
	 * Register the stylesheets for the admin area.
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

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/cafepress-admin.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the admin area.
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

		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/cafepress-admin.js', array( 'jquery' ), $this->version, false );

	}

	/**
     * Register custom post types for the admin area.
     *
     * @since    1.0.0
     */
	public function register_post_types() {
		$labels = array(
			'name' => 'Tables',
			'singular_name' => 'Table',
			'add_new' => 'Add New',
			'add_new_item' => 'Add New Table',
			'edit_item' => 'Edit Table',
			'new_item' => 'New Table',
			'view_item' => 'View Table',
			'search_items' => 'Search Tables',
			'not_found' => 'No Tables found',
			'not_found_in_trash' => 'No Tables found in Trash',
			'menu_name' => 'Cuisine Tables'
		);
	
		$args = array(
			'labels' => $labels,
			'public' => true, 
			'publicly_queryable' => true,
			'show_ui' => true,
			'show_in_menu' => true,
			'query_var' => true,
			'rewrite' => array('slug' => 'cfpr-cuisine-table'),
			'capability_type' => 'post',
			'has_archive' => true,
			'supports' => array('title'),
		);
	
		register_post_type('cfpr_cuisine_table', $args);

		$labels = array(
			'name' => 'Floors',
			'singular_name' => 'Floor',
			'search_items' => 'Search Floors',
			'all_items' => 'All Floors',
			'parent_item' => 'Parent Floor',
			'parent_item_colon' => 'Parent Floor:',
			'edit_item' => 'Edit Floor',
			'update_item' => 'Update Floor',
			'add_new_item' => 'Add New Floor',
			'new_item_name' => 'New Floor Name',
			'menu_name' => 'Floors'
		);
	
		$args = array(
			'labels' => $labels,
			'hierarchical' => true, 
			'public' => true,
			'show_ui' => true,
			'show_admin_column' => true,
			'query_var' => true,
			'rewrite' => array('slug' => 'cfpr-floor'),
			'exclude_below' => 1,
			'show_in_quick_edit' => false
		);
	
		register_taxonomy('cfpr_floor', 'cfpr_cuisine_table', $args);
	}

	public function remove_default_floor_metabox() {
		remove_meta_box('cfpr_floordiv', 'cfpr_cuisine_table', 'side');
	}

	public function add_floor_meta_box() {
		add_meta_box(
			'cafepress_floor_metabox',
			'Floor',
			array($this, 'render_floor_metabox'),
			'cfpr_cuisine_table',
			'side',
			'default'
		);
	}
	
	public function render_floor_metabox($post) {
		$floors = get_terms(array(
			'taxonomy' => 'cfpr_floor',
			'hide_empty' => false,
		));
	
		$selected_floor = wp_get_object_terms($post->ID, 'cfpr_floor', array('fields' => 'ids'));
    	$selected_floor_id = isset($selected_floor[0]) ? $selected_floor[0] : 0;
	
		echo '<div>';
		foreach ($floors as $floor) {
			$checked = ($selected_floor_id == $floor->term_id) ? 'checked' : '';
			echo '<label>';
			echo '<input type="radio" name="cafepress_floor" value="' . esc_html($floor->term_id) . '" ' . esc_html($checked) . '>';
			echo '<input type="hidden" id="cafepress_floor_metabox_nonce" name="cafepress_floor_metabox_nonce" value="' . esc_attr(wp_create_nonce('cafepress_floor_metabox')) . '" />';
			echo esc_html($floor->name);
			echo '</label><br>';
		}
		echo '</div>';
	}

	public function save_floor_meta_box($post_id, $post) {
		if (isset($_POST['cafepress_floor'], $_POST['cafepress_floor_metabox_nonce']) 
			&& wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['cafepress_floor_metabox_nonce'])), 'cafepress_floor_metabox')) {
			$floor_id = (int)$_POST['cafepress_floor'];
        	wp_set_object_terms($post_id, $floor_id, 'cfpr_floor');
		}
	}

	public function add_qr_code_meta_box() {
		add_meta_box(
			'cafepress_qr_code_metabox',
			'QR Code',
			array($this, 'render_qr_code_metabox'),
			'cfpr_cuisine_table',
			'side',
			'default'
		);
	}

	public function render_qr_code_metabox($post) {
		$qr_code = 'cafepress_cuisine_table_' . $post->ID . '.png';
		$qr_code_url = wp_upload_dir()['baseurl'] . '/cafepress_qr_codes/' . $qr_code;
	
		echo '<div style="text-align: center">';
		$response = wp_remote_head($qr_code_url);
		

		$has_qr = (!is_wp_error($response) && isset($response['response']) && 200 === $response['response']['code']) ? true : false;

		if($has_qr) {
			echo '<img id="qr-code-image" src="' . esc_url($qr_code_url) . '" alt="QR Code" style="max-width:250px;">';
			echo '<br>';
			echo '<a href="' . esc_url($qr_code_url) . '" download="' . esc_html($qr_code) . '" class="button">Download</a>';
			echo '<button id="print-qr-code" class="button" style="margin: 0 20px;">Print</button>';
		}	
		
		if(!$has_qr) {
			echo '<br>';
			echo '<input type="hidden" id="cafepress_qr_code_metabox_nonce" name="cafepress_qr_code_metabox_nonce" value="' . esc_attr(wp_create_nonce('cafepress_qr_code_metabox')) . '" />';
			echo '<button id="generate-qr-code" class="button button-primary">Generate QR Code</button>';
		}
		echo '</div>';
	}

	public function generate_qr_code_url($post_id) {
		$data = get_permalink($post_id); 
	
		$qr_code = 'cafepress_cuisine_table_' . $post_id . '.png';
		$qr_code_dir = wp_upload_dir()['basedir'] . '/cafepress_qr_codes/';
		$qr_code_file = $qr_code_dir . $qr_code;
	
		if (!file_exists($qr_code_dir)) {
			wp_mkdir_p( $qr_code_dir );
		}

		// Set up QR code settings
		$options = new QROptions([
			'outputType'       => QRCode::OUTPUT_IMAGE_PNG,
			'eccLevel'         => QRCode::ECC_H,
			'scale'            => 5,
			'imageTransparent' => false,  // No transparency
			'imageBase64'      => false,  // Output raw image data
		]);

		// Instantiate QRCode object with options
		$qrcode = new QRCode($options);

		$qrcode->render($data, $qr_code_file);
	
		return wp_upload_dir()['baseurl'] . '/cafepress_qr_codes/' . $qr_code_file;
	}

	public function generate_qr_code_ajax_action() {
		if (isset($_POST['action'], $_POST['security']) && ($_POST['action'] === 'generate_qr_code') 
			&& wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['security'])), 'cafepress_qr_code_metabox')) {
			$post_id = isset($_POST['post_id']) ? (int)$_POST['post_id'] : 0;
			$qr_code_url = $this->generate_qr_code_url($post_id);
			echo esc_url($qr_code_url);
			die();
		}
	}	

	public function add_custom_fields_to_order_form($order_id) {
		$order = wc_get_order( $order_id );
		// display cuisine table selection
		$cuisine_tables = get_posts( array(
			'post_type' => 'cfpr_cuisine_table',
			'numberposts' => -1,
			'orderby' => 'title',
			'order' => 'ASC',
		) );

		wp_nonce_field('cafepress_cuisine_table', 'cafepress_cuisine_table_metabox_nonce');

		$cuisine_table_options[0] = 'Select a Table';
		foreach ( $cuisine_tables as $cuisine_table ) {
			$floor_title = '';
			$floors = get_the_terms( $cuisine_table->ID, 'cfpr_floor');
			if($floors) {
                $floor_title = $floors[0]->name;
            }

			$display_title = $cuisine_table->post_title;

			if($floor_title) $display_title .= ' - ' . $floor_title;

			$cuisine_table_options[$cuisine_table->ID] = $display_title;
		}
		
		woocommerce_form_field( 'cafepress_cuisine_table', 
			[
				'type' => 'select',
				'label' => 'Table',
				'class' => 'form-field form-field-wide',
				'options' => $cuisine_table_options,
			], 
			$order->get_meta('cafepress_cuisine_table', true ));
	}

	public function save_custom_fields_to_order( $order_id ) {
        $order = wc_get_order( $order_id );

		if (isset($_POST['cafepress_cuisine_table'], $_POST['cafepress_cuisine_table_metabox_nonce']) 
			&& wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['cafepress_cuisine_table_metabox_nonce'])), 'cafepress_cuisine_table')) {
			$order->update_meta_data( 'cafepress_cuisine_table', esc_attr(sanitize_text_field(wp_unslash($_POST['cafepress_cuisine_table']))) );
			$order->save();
		}
	}
}