<?php

/**
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 * @link       https://vinamian.com
 * @since      1.0.0
 *
 * @package    Cafepress
 * @subpackage Cafepress/includes
 */

/**
 * The core plugin class.
 *
 * This is used to define internationalization, admin-specific hooks, and
 * public-facing site hooks.
 *
 * Also maintains the unique identifier of this plugin as well as the current
 * version of the plugin.
 *
 * @since      1.0.0
 * @package    Cafepress
 * @subpackage Cafepress/includes
 * @author     Vinamian <vinamian@gmail.com>
 */
class Cafepress {

	/**
	 * The loader that's responsible for maintaining and registering all hooks that power
	 * the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      Cafepress_Loader    $loader    Maintains and registers all hooks for the plugin.
	 */
	protected $loader;

	/**
	 * The unique identifier of this plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $plugin_name    The string used to uniquely identify this plugin.
	 */
	protected $plugin_name;

	/**
	 * The current version of the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $version    The current version of the plugin.
	 */
	protected $version;

	/**
	 * Define the core functionality of the plugin.
	 *
	 * Set the plugin name and the plugin version that can be used throughout the plugin.
	 * Load the dependencies, define the locale, and set the hooks for the admin area and
	 * the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function __construct() {
		if ( defined( 'CAFEPRESS_VERSION' ) ) {
			$this->version = CAFEPRESS_VERSION;
		} else {
			$this->version = '1.0.0';
		}
		$this->plugin_name = 'cafepress';

		$this->load_dependencies();
		$this->register_post_types();
		$this->set_locale();
		$this->define_admin_hooks();
		$this->define_public_hooks();

	}

	/**
	 * Load the required dependencies for this plugin.
	 *
	 * Include the following files that make up the plugin:
	 *
	 * - Cafepress_Loader. Orchestrates the hooks of the plugin.
	 * - Cafepress_i18n. Defines internationalization functionality.
	 * - Cafepress_Admin. Defines all hooks for the admin area.
	 * - Cafepress_Public. Defines all hooks for the public side of the site.
	 *
	 * Create an instance of the loader which will be used to register the hooks
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function load_dependencies() {

		/**
		 * The class responsible for orchestrating the actions and filters of the
		 * core plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-cafepress-loader.php';

		/**
		 * The class responsible for defining internationalization functionality
		 * of the plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-cafepress-i18n.php';

		/**
		 * The class responsible for defining all actions that occur in the admin area.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-cafepress-admin.php';

		/**
		 * The class responsible for defining all actions that occur in the public-facing
		 * side of the site.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/class-cafepress-public.php';

		$this->loader = new Cafepress_Loader();

	}

	/**
	 * Define the locale for this plugin for internationalization.
	 *
	 * Uses the Cafepress_i18n class in order to set the domain and to register the hook
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function set_locale() {

		$plugin_i18n = new Cafepress_i18n();

		$this->loader->add_action( 'plugins_loaded', $plugin_i18n, 'load_plugin_textdomain' );

	}

	/**
	 * Register all of the hooks related to the admin area functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_admin_hooks() {

		$plugin_admin = new Cafepress_Admin( $this->get_plugin_name(), $this->get_version() );

		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_styles' );
		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts' );

	}

	/**
	 * Register all of the hooks related to the public-facing functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_public_hooks() {

		$plugin_public = new Cafepress_Public( $this->get_plugin_name(), $this->get_version() );

		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_styles' );
		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_scripts' );

		add_shortcode( 'cafepress_menu', ['Cafepress_Public', 'display_menu_shortcode'] );

		$this->loader->add_action( 'wp_footer', $plugin_public, 'display_bottom_mini_cart', 10 );
		
		$this->loader->add_action('wp_ajax_cafepress_add_to_cart', $plugin_public, 'ajax_add_item_to_cart');
		$this->loader->add_action('wp_ajax_nopriv_cafepress_add_to_cart', $plugin_public, 'ajax_add_item_to_cart');
		
		$this->loader->add_action('wp_ajax_update_item_in_cart', $plugin_public, 'ajax_update_item_in_cart');
		$this->loader->add_action('wp_ajax_nopriv_update_item_in_cart', $plugin_public, 'ajax_update_item_in_cart');

		$this->loader->add_action('wp_ajax_remove_item_from_cart', $plugin_public, 'ajax_remove_item_from_cart');
		$this->loader->add_action('wp_ajax_nopriv_remove_item_from_cart', $plugin_public, 'ajax_remove_item_from_cart');

		$this->loader->add_filter('the_content', $plugin_public, 'display_menu_shortcode_in_cuisine_table', 10, 1);

		$this->loader->add_filter('woocommerce_add_to_cart_fragments', $plugin_public, 'add_to_cart_fragments', 10, 1);
        $this->loader->add_filter('woocommerce_update_order_review_fragments', $plugin_public, 'add_to_cart_fragments', 10, 1);

		$this->loader->add_action( 'woocommerce_update_order', $plugin_public, 'add_cuisine_table_to_order_meta' , 10, 3 );

	}

	/**
	 * Run the loader to execute all of the hooks with WordPress.
	 *
	 * @since    1.0.0
	 */
	public function run() {
		$this->loader->run();
	}

	/**
	 * The name of the plugin used to uniquely identify it within the context of
	 * WordPress and to define internationalization functionality.
	 *
	 * @since     1.0.0
	 * @return    string    The name of the plugin.
	 */
	public function get_plugin_name() {
		return $this->plugin_name;
	}

	/**
	 * The reference to the class that orchestrates the hooks with the plugin.
	 *
	 * @since     1.0.0
	 * @return    Cafepress_Loader    Orchestrates the hooks of the plugin.
	 */
	public function get_loader() {
		return $this->loader;
	}

	/**
	 * Retrieve the version number of the plugin.
	 *
	 * @since     1.0.0
	 * @return    string    The version number of the plugin.
	 */
	public function get_version() {
		return $this->version;
	}

	private function register_post_types() {
		$plugin_admin = new Cafepress_Admin( $this->get_plugin_name(), $this->get_version() );

		$this->loader->add_action('init', $plugin_admin, 'register_post_types' );

		$this->loader->add_action('add_meta_boxes', $plugin_admin, 'remove_default_floor_metabox', 10, 1);
		$this->loader->add_action('add_meta_boxes', $plugin_admin, 'add_floor_meta_box');
		$this->loader->add_action('add_meta_boxes', $plugin_admin, 'add_qr_code_meta_box');
    	$this->loader->add_action('save_post', $plugin_admin, 'save_floor_meta_box', 10, 2);

		$this->loader->add_action('admin_init', $plugin_admin, 'generate_qr_code_ajax_action');

		$this->loader->add_action('woocommerce_admin_order_data_after_order_details', $plugin_admin, 'add_custom_fields_to_order_form', 10, 2);
		$this->loader->add_action("woocommerce_process_shop_order_meta", $plugin_admin, "save_custom_fields_to_order");
	}

}
