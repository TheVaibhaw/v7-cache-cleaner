<?php
/**
 * Admin class.
 *
 * @package V7_Cache_Cleaner
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class V7_Cache_Cleaner_Admin {

	private $plugin_name;
	private $version;

	public function __construct( $plugin_name, $version ) {
		$this->plugin_name = $plugin_name;
		$this->version     = $version;
	}

	public function enqueue_styles() {
		wp_enqueue_style(
			$this->plugin_name . '-admin',
			V7_CACHE_URL . 'admin/css/admin.css',
			array(),
			$this->version
		);

		wp_enqueue_script(
			$this->plugin_name . '-admin',
			V7_CACHE_URL . 'admin/js/admin.js',
			array( 'jquery' ),
			$this->version,
			true
		);

		wp_localize_script(
			$this->plugin_name . '-admin',
			'v7CacheAdmin',
			array(
				'ajaxUrl' => admin_url( 'admin-ajax.php' ),
				'nonce'   => wp_create_nonce( 'v7_clear_cache_nonce' ),
				'clearing' => __( 'Clearing...', 'v7-cache-cleaner' ),
				'cleared' => __( 'Cache Cleared!', 'v7-cache-cleaner' ),
			)
		);
	}

	public function add_admin_bar_button( $wp_admin_bar ) {
		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}

		$cache_size = V7_Cache_Cleaner_Cache::get_cache_size();
		$size_text  = V7_Cache_Cleaner_Cache::format_size( $cache_size );

		$wp_admin_bar->add_node(
			array(
				'id'    => 'v7-clear-cache',
				'title' => '<span class="ab-icon dashicons dashicons-trash"></span>' . __( 'Clear Cache', 'v7-cache-cleaner' ) . ' (' . $size_text . ')',
				'href'  => '#',
				'meta'  => array(
					'class' => 'v7-clear-cache-btn',
					'title' => __( 'Click to clear all cache', 'v7-cache-cleaner' ),
				),
			)
		);
	}

	public function ajax_clear_cache() {
		check_ajax_referer( 'v7_clear_cache_nonce', 'nonce' );

		if ( ! current_user_can( 'manage_options' ) ) {
			wp_send_json_error( __( 'Permission denied.', 'v7-cache-cleaner' ) );
		}

		V7_Cache_Cleaner_Cache::clear_all_cache();

		set_transient( 'v7_cache_cleared_notice', true, 30 );

		wp_send_json_success(
			array(
				'message' => __( 'Cache cleared successfully!', 'v7-cache-cleaner' ),
				'size'    => V7_Cache_Cleaner_Cache::format_size( V7_Cache_Cleaner_Cache::get_cache_size() ),
			)
		);
	}

	public function show_cache_cleared_notice() {
		if ( get_transient( 'v7_cache_cleared_notice' ) ) {
			delete_transient( 'v7_cache_cleared_notice' );
			?>
			<div class="notice notice-success is-dismissible">
				<p><?php esc_html_e( 'V7 Cache Cleaner: All cache cleared successfully!', 'v7-cache-cleaner' ); ?></p>
			</div>
			<?php
		}
	}
}
