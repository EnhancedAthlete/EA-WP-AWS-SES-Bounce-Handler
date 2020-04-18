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

	/**
	 * Register the one settings section with WordPress.
	 *
	 * @hooked admin_init
	 */
	public function setup_sections() {

		$settings_page_slug_name = $this->plugin_name;

		add_settings_section(
			'default',
			'Settings',
			null,
			$settings_page_slug_name
		);
	}

	/**
	 * Field Configuration, each item in this array is one field/setting we want to capture.
	 *
	 * @hooked admin_init
	 *
	 * @see https://github.com/reside-eng/wordpress-custom-plugin/blob/master/admin/class-wordpress-custom-plugin-admin.php
	 *
	 * @since    1.0.0
	 *
	 * phpcs:disable Generic.Commenting.DocComment.MissingShort
	 */
	public function setup_fields() {

		$settings_page_slug_name = $this->plugin_name;

		/** @var Settings_Section_Element_Abstract[] $fields */
		$fields = array();

		$fields[] = new class( $this->settings, 'default', $settings_page_slug_name, $this->get_plugin_name(), $this->get_version() ) extends AWS_SNS_ARN_Abstract {

			/**
			 * @inheritDoc
			 */
			public function get_value() {
				return $this->get_settings()->get_bounces_arn();
			}

			/**
			 * @inheritDoc
			 */
			public function get_id() {
				return Settings_Interface::BOUNCES_ARN;
			}

			/**
			 * @inheritDoc
			 */
			public function get_title() {
				return __( 'Bounces ARN:', 'ea-wp-aws-ses-bounce-handler' );
			}
		};

		$fields[] = new class( $this->settings, 'default', $settings_page_slug_name, $this->get_plugin_name(), $this->get_version() ) extends AWS_SNS_ARN_Abstract {

			/**
			 * @inheritDoc
			 */
			public function get_value() {
				return $this->get_settings()->get_complaints_arn();
			}

			/**
			 * @inheritDoc
			 */
			public function get_id() {
				return Settings_Interface::COMPLAINTS_ARN;
			}

			/**
			 * @inheritDoc
			 */
			public function get_title() {
				return __( 'Complaints ARN:', 'ea-wp-aws-ses-bounce-handler' );
			}
		};

		foreach ( $fields as $field ) {

			call_user_func_array(
				'add_settings_field',
				$field->get_add_settings_field_args()
			);

			call_user_func_array(
				'register_setting',
				$field->get_register_setting_args()
			);

		}
	}

}
