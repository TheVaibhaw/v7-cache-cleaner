<?php
/**
 * Core plugin class.
 *
 * @package V7_Cache_Cleaner
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class V7_Cache_Cleaner {

	protected $loader;
	protected $plugin_name;
	protected $version;

	public function __construct() {
		$this->version     = defined( 'V7_CACHE_VERSION' ) ? V7_CACHE_VERSION : '1.0.0';
		$this->plugin_name = 'v7-cache-cleaner';

		$this->load_dependencies();
		$this->define_admin_hooks();
		$this->define_public_hooks();
		$this->define_cache_hooks();
	}

	private function load_dependencies() {
		require_once V7_CACHE_DIR . 'includes/class-v7-cache-cleaner-loader.php';
		require_once V7_CACHE_DIR . 'includes/class-v7-cache-cleaner-cache.php';
		require_once V7_CACHE_DIR . 'admin/class-v7-cache-cleaner-admin.php';
		require_once V7_CACHE_DIR . 'admin/class-v7-cache-cleaner-settings.php';
		require_once V7_CACHE_DIR . 'public/class-v7-cache-cleaner-public.php';

		$this->loader = new V7_Cache_Cleaner_Loader();
	}

	private function define_admin_hooks() {
		$plugin_admin    = new V7_Cache_Cleaner_Admin( $this->plugin_name, $this->version );
		$plugin_settings = new V7_Cache_Cleaner_Settings( $this->plugin_name, $this->version );

		$this->loader->add_action( 'admin_bar_menu', $plugin_admin, 'add_admin_bar_button', 100 );
		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_styles' );
		$this->loader->add_action( 'wp_ajax_v7_clear_cache', $plugin_admin, 'ajax_clear_cache' );
		$this->loader->add_action( 'admin_notices', $plugin_admin, 'show_cache_cleared_notice' );
		$this->loader->add_filter( 'plugin_action_links_v7-cache-cleaner/v7-cache-cleaner.php', $plugin_settings, 'add_plugin_action_links' );
		$this->loader->add_action( 'admin_menu', $plugin_settings, 'add_settings_page' );
		$this->loader->add_action( 'admin_init', $plugin_settings, 'register_settings' );
	}

	private function define_public_hooks() {
		$plugin_public = new V7_Cache_Cleaner_Public( $this->plugin_name, $this->version );

		if ( get_option( 'v7_cache_browser_cache', '1' ) ) {
			$this->loader->add_action( 'send_headers', $plugin_public, 'add_browser_cache_headers' );
		}
	}

	private function define_cache_hooks() {
		$cache = new V7_Cache_Cleaner_Cache();

		if ( get_option( 'v7_cache_auto_post', '1' ) ) {
			$this->loader->add_action( 'save_post', $cache, 'clear_all_cache' );
			$this->loader->add_action( 'delete_post', $cache, 'clear_all_cache' );
			$this->loader->add_action( 'wp_trash_post', $cache, 'clear_all_cache' );
		}

		if ( get_option( 'v7_cache_auto_plugin', '1' ) ) {
			$this->loader->add_action( 'upgrader_process_complete', $cache, 'clear_all_cache' );
			$this->loader->add_action( 'switch_theme', $cache, 'clear_all_cache' );
			$this->loader->add_action( 'customize_save_after', $cache, 'clear_all_cache' );
		}

		if ( get_option( 'v7_cache_woo_integration', '1' ) && class_exists( 'WooCommerce' ) ) {
			$this->loader->add_action( 'woocommerce_update_product', $cache, 'clear_all_cache' );
			$this->loader->add_action( 'woocommerce_checkout_order_processed', $cache, 'clear_all_cache' );
		}
	}

	public function run() {
		$this->loader->run();
	}

	public function get_plugin_name() {
		return $this->plugin_name;
	}

	public function get_version() {
		return $this->version;
	}
}
