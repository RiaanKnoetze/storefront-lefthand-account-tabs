<?php
/**
 * Plugin Name:			Storefront - Lefthand Account Tabs
 * Plugin URI:			
 * Description:			Restores the account tabs on the "My Account" page to the left hand-side when using the fullwidth page template
 * Version:				1.0
 * Author:				Riaan K. | WooCommerce
 * Author URI:			http://woocommerce.com/
 * Requires at least:	5.0
 * Tested up to:		5.2
 *
 * Text Domain: storefront-lefthand-account-tabs
 * Domain Path: /languages/
 *
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * Returns the main instance of Storefront_Lefthand_Account_Tabs to prevent the need to use globals.
 *
 * @since  1.0.0
 * @return object Storefront_Lefthand_Account_Tabs
 */
function Storefront_Lefthand_Account_Tabs() {
	return Storefront_Lefthand_Account_Tabs::instance();
} // End Storefront_Lefthand_Account_Tabs()

Storefront_Lefthand_Account_Tabs();

/**
 * Main Storefront_Hamburger_Menu Class
 *
 * @class Storefront_Hamburger_Menu
 * @version	1.0.0
 * @since 1.0.0
 * @package	Storefront_Hamburger_Menu
 */
final class Storefront_Lefthand_Account_Tabs {
	/**
	 * Storefront_Lefthand_Account_Tabs The single instance of Storefront_Lefthand_Account_Tabs.
	 * @var 	object
	 * @access  private
	 * @since 	1.0.0
	 */
	private static $_instance = null;

	/**
	 * The token.
	 * @var     string
	 * @access  public
	 * @since   1.0.0
	 */
	public $token;

	/**
	 * The version number.
	 * @var     string
	 * @access  public
	 * @since   1.0.0
	 */
	public $version;

	/**
	 * Constructor function.
	 * @access  public
	 * @since   1.0.0
	 * @return  void
	 */
	public function __construct() {
		$this->token 			= 'storefront-lefthand-account-tabs';
		$this->plugin_url 		= plugin_dir_url( __FILE__ );
		$this->plugin_path 		= plugin_dir_path( __FILE__ );
		$this->version 			= '1.0';

		register_activation_hook( __FILE__, array( $this, 'install' ) );

		add_action( 'init', array( $this, 'slhat_load_plugin_textdomain' ) );

		add_action( 'init', array( $this, 'slhat_setup' ) );

		add_filter( 'plugin_action_links_' . plugin_basename( __FILE__ ), array( $this, 'slhat_plugin_links' ) );
	}

	/**
	 * Main Storefront_Lefthand_Account_Tabs Instance
	 *
	 * Ensures only one instance of Storefront_Lefthand_Account_Tabs is loaded or can be loaded.
	 *
	 * @since 1.0.0
	 * @static
	 * @see Storefront_Lefthand_Account_Tabs()
	 * @return Main Storefront_Lefthand_Account_Tabs instance
	 */
	public static function instance() {
		if ( is_null( self::$_instance ) )
			self::$_instance = new self();
		return self::$_instance;
	} // End instance()

	/**
	 * Load the localisation file.
	 * @access  public
	 * @since   1.0.0
	 * @return  void
	 */
	public function slhat_load_plugin_textdomain() {
		load_plugin_textdomain( 'storefront-lefthand-account-tabs', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );
	}

	/**
	 * Cloning is forbidden.
	 *
	 * @since 1.0.0
	 */
	public function __clone() {
		_doing_it_wrong( __FUNCTION__, __( 'Cheatin&#8217; huh?' ), '1.0.0' );
	}

	/**
	 * Unserializing instances of this class is forbidden.
	 *
	 * @since 1.0.0
	 */
	public function __wakeup() {
		_doing_it_wrong( __FUNCTION__, __( 'Cheatin&#8217; huh?' ), '1.0.0' );
	}

	/**
	 * Plugin page links
	 *
	 * @since  1.0.0
	 */
	public function slhat_plugin_links( $links ) {
		$plugin_links = array(
			'<a href="https://woocommerce.com/my-account/tickets/">' . __( 'Support', 'storefront-lefthand-account-tabs' ) . '</a>',
		);

		return array_merge( $plugin_links, $links );
	}

	/**
	 * Installation.
	 * Runs on activation. Logs the version number and assigns a notice message to a WordPress option.
	 * @access  public
	 * @since   1.0.0
	 * @return  void
	 */
	public function install() {
		$this->_log_version_number();
	}

	/**
	 * Log the plugin version number.
	 * @access  private
	 * @since   1.0.0
	 * @return  void
	 */
	private function _log_version_number() {
		// Log the version number.
		update_option( $this->token . '-version', $this->version );
	}

	/**
	 * Setup all the things.
	 * Only executes if Storefront or a child theme using Storefront as a parent is active and the extension specific filter returns true.
	 * Child themes can disable this extension using the storefront_lefthand_account_tabs_supported filter
	 * @return void
	 */
	public function slhat_setup() {
		$theme = wp_get_theme();

		if ( 'Storefront' == $theme->name || 'storefront' == $theme->template && apply_filters( 'storefront_lefthand_account_tabs_supported', true ) ) {
			add_action( 'wp_enqueue_scripts', array( $this, 'slhat_styles' ), 999 );
			add_filter( 'body_class', array( $this, 'slhat_body_class' ) );
		}
		 else {
			add_action( 'admin_notices', array( $this, 'slhat_install_storefront_notice' ) );
		}
	}

	/**
	 * Storefront install
	 * If the user activates the plugin while having a different parent theme active, prompt them to install Storefront.
	 * @since   1.0.0
	 * @return  void
	 */
	public function slhat_install_storefront_notice() {
		echo '<div class="notice is-dismissible updated">
				<p>' . __( 'Storefront Lefthand Account Tabs requires that you use Storefront as your parent theme.', 'storefront-lefthand-account-tabs' ) . ' <a href="' . esc_url( wp_nonce_url( self_admin_url( 'update.php?action=install-theme&theme=storefront' ), 'install-theme_storefront' ) ) .'">' . __( 'Install Storefront now', 'storefront-lefthand-account-tabs' ) . '</a></p>
			</div>';
	}

	/**
	 * Enqueue CSS and custom styles.
	 * @since   1.0.0
	 * @return  void
	 */
	public function slhat_styles() {
		wp_enqueue_style( 'slhat-styles', plugins_url( '/assets/css/style.css', __FILE__ ), '', $this->version );
	}

	/**
	 * Storefront Extension Boilerplate Body Class
	 * Adds a class based on the extension name and any relevant settings.
	 */
	public function slhat_body_class( $classes ) {
		global $storefront_version;

		if ( version_compare( $storefront_version, '2.4.0', '>=' ) ) {
			$classes[] = 'storefront-2-4';
		}

		$classes[] = 'storefront-lefthand-account-tabs';

		return $classes;
	}
} // End Class