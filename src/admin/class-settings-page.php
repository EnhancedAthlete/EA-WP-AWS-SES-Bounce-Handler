<?php
/**
 * The wp-admin settings page to configure the ARNs to listen to.
 *
 * @link
 * @since      1.0.0
 *
 * @package    EA_WP_AWS_SES_Bounce_Handler
 * @subpackage EA_WP_AWS_SES_Bounce_Handler/admin
 */

namespace EA_WP_AWS_SES_Bounce_Handler\admin;

use EA_WP_AWS_SES_Bounce_Handler\admin\settings\AWS_SNS_ARN_Abstract;
use EA_WP_AWS_SES_Bounce_Handler\admin\partials\AWS_SNS_ARN_Complaints;
use EA_WP_AWS_SES_Bounce_Handler\admin\settings\Settings_Section_Element_Abstract;
use EA_WP_AWS_SES_Bounce_Handler\includes\Settings_Interface;
use EA_WP_AWS_SES_Bounce_Handler\WPPB\WPPB_Object;

/**
 * Adds a wp-admin Settings submenu. Adds a page with input for bounces ARN and copmlaints ARN.
 *
 * @package    EA_WP_AWS_SES_Bounce_Handler
 * @subpackage EA_WP_AWS_SES_Bounce_Handler/admin
 * @author     BrianHenryIE <BrianHenryIE@gmail.com>
 */
class Settings_Page extends WPPB_Object {

	/**
	 * The settings, to pass to the individual fields for populating.
	 *
	 * @var Settings_Interface $settings The previously saved settings for the plugin.
	 */
	private $settings;

	/**
	 * Settings_Page constructor.
	 *
	 * @param string             $plugin_name The plugin name.
	 * @param string             $version The plugin version.
	 * @param Settings_Interface $settings The previously saved settings for the plugin.
	 */
	public function __construct( $plugin_name, $version, $settings ) {
		parent::__construct( $plugin_name, $version );

		$this->settings = $settings;
	}

	/**
	 * Add the Autologin URLs settings menu-item/page as a submenu-item of the Settings menu.
	 *
	 * /wp-admin/options-general.php?page=bh-wp-autologin-urls
	 *
	 * @hooked admin_menu
	 */
	public function add_settings_page() {

		add_options_page(
			'AWS SES Bounce Handler',
			'AWS SES Bounce Handler',
			'manage_options',
			$this->plugin_name,
			array( $this, 'display_plugin_admin_page' )
		);
	}

	/**
	 * Registered above, called by WordPress to display the admin settings page.
	 */
	public function display_plugin_admin_page() {

		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/partials/admin-display.php';
	}

}
