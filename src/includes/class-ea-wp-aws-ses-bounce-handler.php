<?php
/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * sns-facing side of the site and the admin area.
 *
 * @link       https://BrianHenry.ie
 * @since      1.0.0
 *
 * @package   EA_WP_AWS_SES_Bounce_Handler
 * @subpackage EA_WP_AWS_SES_Bounce_Handler/includes
 */

namespace EA_WP_AWS_SES_Bounce_Handler\includes;

use EA_WP_AWS_SES_Bounce_Handler\admin\Admin;
use EA_WP_AWS_SES_Bounce_Handler\admin\Plugins_Page;
use EA_WP_AWS_SES_Bounce_Handler\admin\Settings_Page;
use EA_WP_AWS_SES_Bounce_Handler\integrations\Newsletter;
use EA_WP_AWS_SES_Bounce_Handler\integrations\WooCommerce;
use EA_WP_AWS_SES_Bounce_Handler\integrations\WordPress;
use EA_WP_AWS_SES_Bounce_Handler\sns\SNS;
use EA_WP_AWS_SES_Bounce_Handler\WPPB\WPPB_Loader_Interface;
use EA_WP_AWS_SES_Bounce_Handler\WPPB\WPPB_Object;

/**
 * The core plugin class.
 *
 * This is used to define internationalization, admin-specific hooks, and
 * sns-facing site hooks.
 *
 * Also maintains the unique identifier of this plugin as well as the current
 * version of the plugin.
 *
 * @since      1.0.0
 * @package    EA_WP_AWS_SES_Bounce_Handler
 * @subpackage EA_WP_AWS_SES_Bounce_Handler/includes
 * @author     BrianHenryIE <BrianHenryIE@gmail.com>
 *
 * phpcs:disable Squiz.PHP.DisallowMultipleAssignments.Found
 */
class EA_WP_AWS_SES_Bounce_Handler extends WPPB_Object {

	/**
	 * The loader that's responsible for maintaining and registering all hooks that power
	 * the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var     WPPB_Loader_Interface    $loader    Maintains and registers all hooks for the plugin.
	 */
	protected $loader;

	/**
	 * Public variable to allow accessing the i18n object.
	 *
	 * @var I18n
	 */
	public $i18n;

	/**
	 * Public variable to allow accessing the plugin's settings object.
	 *
	 * @var Settings_Interface
	 */
	public $settings;

	/**
	 * Public variable to allow modifying the SNS handler object.
	 *
	 * @var SNS
	 */
	public $sns;

	/**
	 * Public variable to allow modifying the settings page object.
	 *
	 * @var Settings_Page
	 */
	public $settings_page;

	/**
	 * Define the core functionality of the plugin.
	 *
	 * Set the plugin name and the plugin version that can be used throughout the plugin.
	 * Load the dependencies, define the locale, and set the hooks for the admin area and
	 * the sns-facing side of the site.
	 *
	 * @since    1.1.0
	 *
	 * @param WPPB_Loader_Interface $loader The WordPress Plugin Boilerplate loader object.
	 * @param Settings_Interface    $settings The setting the plugin should be run with.
	 */
	public function __construct( $loader, $settings ) {
		if ( defined( 'EA_WP_AWS_SES_BOUNCE_HANDLER_VERSION' ) ) {
			$version = EA_WP_AWS_SES_BOUNCE_HANDLER_VERSION;
		} else {
			$version = '1.0.0';
		}
		$plugin_name = 'ea-wp-aws-ses-bounce-handler';

		parent::__construct( $plugin_name, $version );

		$this->loader   = $loader;
		$this->settings = $settings;

		$this->set_locale();
		$this->define_admin_hooks();
		$this->define_sns_hooks();
		$this->define_integration_hooks();

	}

	/**
	 * Define the locale for this plugin for internationalization.
	 *
	 * Uses the I18n class in order to set the domain and to register the hook
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function set_locale() {

		$this->i18n = $plugin_i18n = new I18n();

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

		$plugin_admin = new Admin( $this->get_plugin_name(), $this->get_version() );

		$this->loader->add_action( 'admin_notices', $plugin_admin, 'requirements_notice' );
		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_styles' );

		$this->settings_page = $plugin_settings_page = new Settings_Page( $this->get_plugin_name(), $this->get_version(), $this->settings );

		$this->loader->add_action( 'admin_menu', $plugin_settings_page, 'add_settings_page' );
		$this->loader->add_action( 'admin_init', $plugin_settings_page, 'setup_sections' );
		$this->loader->add_action( 'admin_init', $plugin_settings_page, 'setup_fields' );

		$this->plugins_page = $plugins_page = new Plugins_Page( $this->get_plugin_name(), $this->get_version() );

		$plugin_basename = $this->get_plugin_name() . '/' . $this->get_plugin_name() . '.php';

		$this->loader->add_filter( 'plugin_action_links_' . $plugin_basename, $plugins_page, 'action_links' );
		$this->loader->add_filter( 'plugin_row_meta', $plugins_page, 'row_meta', 20, 4 );
	}

	/**
	 * Register all of the hooks related to the sns-facing functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_sns_hooks() {

		$this->sns = $plugin_sns = new SNS( $this->get_plugin_name(), $this->get_version(), $this->settings );

		$this->loader->add_filter( 'ea_aws_sns_notification', $plugin_sns, 'handle_complaints', 10, 5 );
		$this->loader->add_filter( 'ea_aws_sns_notification', $plugin_sns, 'handle_bounces', 10, 5 );

	}

	/**
	 * Instantiate integrations that will be called when an email bounces.
	 */
	private function define_integration_hooks() {

		$woocommerce_integration = new WooCommerce( $this->get_plugin_name(), $this->get_version() );
		$this->loader->add_action( 'handle_ses_bounce', $woocommerce_integration, 'mark_order_email_bounced', 10, 3 );
		$this->loader->add_action( 'admin_notices', $woocommerce_integration, 'display_bounce_notification' );

		$newsletter_integration = new Newsletter( $this->get_plugin_name(), $this->get_version() );
		$this->loader->add_action( 'handle_ses_bounce', $newsletter_integration, 'mark_as_bounced', 10, 3 );
		$this->loader->add_action( 'handle_ses_complaint', $newsletter_integration, 'mark_as_complaint', 10, 3 );

		$wordpress_integration = new WordPress( $this->get_plugin_name(), $this->get_version() );
		$this->loader->add_action( 'init', $wordpress_integration, 'add_bounced_role_to_wordpress' );
		$this->loader->add_action( 'handle_ses_bounce', $wordpress_integration, 'add_bounced_role_to_user', 10, 3 );

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
	 * The reference to the class that orchestrates the hooks with the plugin.
	 *
	 * @since     1.0.0
	 * @return   WPPB_Loader_Interface   Orchestrates the hooks of the plugin.
	 */
	public function get_loader() {
		return $this->loader;
	}

}
