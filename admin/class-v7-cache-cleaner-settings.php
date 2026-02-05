<?php
/**
 * Settings class.
 *
 * @package V7_Cache_Cleaner
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class V7_Cache_Cleaner_Settings {

	private $plugin_name;
	private $version;

	public function __construct( $plugin_name, $version ) {
		$this->plugin_name = $plugin_name;
		$this->version     = $version;
	}

	public function add_plugin_action_links( $links ) {
		$settings_link = '<a href="' . admin_url( 'options-general.php?page=v7-cache-cleaner' ) . '">' . __( 'Settings', 'v7-cache-cleaner' ) . '</a>';
		array_unshift( $links, $settings_link );
		return $links;
	}

	public function add_settings_page() {
		add_options_page(
			__( 'V7 Cache Cleaner', 'v7-cache-cleaner' ),
			__( 'V7 Cache Cleaner', 'v7-cache-cleaner' ),
			'manage_options',
			'v7-cache-cleaner',
			array( $this, 'render_settings_page' )
		);
	}

	public function register_settings() {
		register_setting( 'v7_cache_settings', 'v7_cache_page_cache', array( 'sanitize_callback' => 'absint' ) );
		register_setting( 'v7_cache_settings', 'v7_cache_browser_cache', array( 'sanitize_callback' => 'absint' ) );
		register_setting( 'v7_cache_settings', 'v7_cache_auto_post', array( 'sanitize_callback' => 'absint' ) );
		register_setting( 'v7_cache_settings', 'v7_cache_auto_plugin', array( 'sanitize_callback' => 'absint' ) );
		register_setting( 'v7_cache_settings', 'v7_cache_woo_integration', array( 'sanitize_callback' => 'absint' ) );
		register_setting( 'v7_cache_settings', 'v7_cache_lifetime', array( 'sanitize_callback' => 'absint' ) );

		add_settings_section( 'v7_cache_general', __( 'Cache Settings', 'v7-cache-cleaner' ), null, 'v7-cache-cleaner' );
		add_settings_section( 'v7_cache_auto', __( 'Auto Clear Settings', 'v7-cache-cleaner' ), null, 'v7-cache-cleaner' );

		add_settings_field( 'v7_cache_page_cache', __( 'Enable Page Cache', 'v7-cache-cleaner' ), array( $this, 'render_checkbox' ), 'v7-cache-cleaner', 'v7_cache_general', array( 'id' => 'v7_cache_page_cache' ) );
		add_settings_field( 'v7_cache_browser_cache', __( 'Enable Browser Cache Headers', 'v7-cache-cleaner' ), array( $this, 'render_checkbox' ), 'v7-cache-cleaner', 'v7_cache_general', array( 'id' => 'v7_cache_browser_cache' ) );
		add_settings_field( 'v7_cache_lifetime', __( 'Cache Lifetime (hours)', 'v7-cache-cleaner' ), array( $this, 'render_number' ), 'v7-cache-cleaner', 'v7_cache_general', array( 'id' => 'v7_cache_lifetime' ) );
		add_settings_field( 'v7_cache_auto_post', __( 'Clear on Post Save', 'v7-cache-cleaner' ), array( $this, 'render_checkbox' ), 'v7-cache-cleaner', 'v7_cache_auto', array( 'id' => 'v7_cache_auto_post' ) );
		add_settings_field( 'v7_cache_auto_plugin', __( 'Clear on Plugin/Theme Update', 'v7-cache-cleaner' ), array( $this, 'render_checkbox' ), 'v7-cache-cleaner', 'v7_cache_auto', array( 'id' => 'v7_cache_auto_plugin' ) );
		add_settings_field( 'v7_cache_woo_integration', __( 'WooCommerce Integration', 'v7-cache-cleaner' ), array( $this, 'render_checkbox' ), 'v7-cache-cleaner', 'v7_cache_auto', array( 'id' => 'v7_cache_woo_integration' ) );
	}

	public function render_checkbox( $args ) {
		$value = get_option( $args['id'], '1' );
		echo '<input type="checkbox" name="' . esc_attr( $args['id'] ) . '" value="1" ' . checked( $value, '1', false ) . '>';
	}

	public function render_number( $args ) {
		$value = get_option( $args['id'], '24' );
		echo '<input type="number" name="' . esc_attr( $args['id'] ) . '" value="' . esc_attr( $value ) . '" min="1" max="720" class="small-text"> ' . esc_html__( 'hours', 'v7-cache-cleaner' );
	}

	public function render_settings_page() {
		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}

		$cache_size = V7_Cache_Cleaner_Cache::get_cache_size();
		?>
		<div class="wrap">
			<h1><?php esc_html_e( 'V7 Cache Cleaner Settings', 'v7-cache-cleaner' ); ?></h1>

			<div class="v7-cache-status">
				<h2><?php esc_html_e( 'Cache Status', 'v7-cache-cleaner' ); ?></h2>
				<p>
					<strong><?php esc_html_e( 'Current Cache Size:', 'v7-cache-cleaner' ); ?></strong>
					<?php echo esc_html( V7_Cache_Cleaner_Cache::format_size( $cache_size ) ); ?>
				</p>
				<p>
					<button type="button" class="button button-primary v7-clear-cache-btn" id="v7-settings-clear">
						<?php esc_html_e( 'Clear All Cache Now', 'v7-cache-cleaner' ); ?>
					</button>
				</p>
			</div>

			<form method="post" action="options.php">
				<?php
				settings_fields( 'v7_cache_settings' );
				do_settings_sections( 'v7-cache-cleaner' );
				submit_button();
				?>
			</form>
		</div>
		<?php
	}
}
