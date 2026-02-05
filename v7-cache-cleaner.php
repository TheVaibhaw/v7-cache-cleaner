<?php
/**
 * Plugin Name:       V7 Cache Cleaner
 * Plugin URI:        https://github.com/TheVaibhaw/v7-cache-cleaner
 * Description:       Lightweight cache cleaner and performance optimizer for WordPress and WooCommerce.
 * Version:           1.0.0
 * Author:            Vaibhaw Kumar
 * Author URI:        https://vaibhawkumarparashar.in
 * License:           GPL-2.0-or-later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       v7-cache-cleaner
 * Domain Path:       /languages
 * Requires at least: 6.0
 * Tested up to:      6.9
 * Requires PHP:      8.0
 *
 * @package V7_Cache_Cleaner
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'V7_CACHE_VERSION', '1.0.0' );
define( 'V7_CACHE_DIR', plugin_dir_path( __FILE__ ) );
define( 'V7_CACHE_URL', plugin_dir_url( __FILE__ ) );
define( 'V7_CACHE_FOLDER', WP_CONTENT_DIR . '/cache/v7-cache/' );

add_action( 'before_woocommerce_init', function() {
	if ( class_exists( \Automattic\WooCommerce\Utilities\FeaturesUtil::class ) ) {
		\Automattic\WooCommerce\Utilities\FeaturesUtil::declare_compatibility( 'custom_order_tables', __FILE__, true );
	}
} );

function v7_cache_activate() {
	require_once V7_CACHE_DIR . 'includes/class-v7-cache-cleaner-activator.php';
	V7_Cache_Cleaner_Activator::activate();
}

function v7_cache_deactivate() {
	require_once V7_CACHE_DIR . 'includes/class-v7-cache-cleaner-activator.php';
	V7_Cache_Cleaner_Activator::deactivate();
}

register_activation_hook( __FILE__, 'v7_cache_activate' );
register_deactivation_hook( __FILE__, 'v7_cache_deactivate' );

require V7_CACHE_DIR . 'includes/class-v7-cache-cleaner.php';

function v7_cache_run() {
	$plugin = new V7_Cache_Cleaner();
	$plugin->run();
}
v7_cache_run();
