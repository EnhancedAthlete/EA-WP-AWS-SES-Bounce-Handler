<?php
/**
 * An abstract settings element for extending.
 *
 * @link       https://BrianHenry.ie
 * @since      1.0.0
 *
 * @package   EA_WP_AWS_SES_Bounce_Handler
 * @subpackage EA_WP_AWS_SES_Bounce_Handler/admin/settings
 */

namespace EA_WP_AWS_SES_Bounce_Handler\admin\settings;

use EA_WP_AWS_SES_Bounce_Handler\WPPB\WPPB_Object;

/**
 * Code common across setting elements.
 *
 * @see https://github.com/reside-eng/wordpress-custom-plugin
 * @see register_setting()
 * @see add_settings_field()
 *
 * Class Settings_Section_Element
 */
abstract class Settings_Section_Element_Abstract extends WPPB_Object {

	/**
	 * The previously save plugin settings.
	 *
	 * @var object
	 */
	protected $settings;

	/**
	 * The slug of the settings page this setting is shown on.
	 *
	 * @var string $page The settings page page slug.
	 */
	protected $page;

	/**
	 * The section name as used with add_settings_section().
	 *
	 * @var string $section The section/tab the setting is displayed in.
	 */
	protected $section = 'default';

	/**
	 * The data array the WordPress Settings API passes to print_field_callback().
	 *
	 * @var array Array of data available to print_field_callback()
	 */
	protected $add_settings_field_args = array();

	/**
	 * The options array used when registering the setting.
	 *
	 * @var array Configuration options for register_setting()
	 */
	protected $register_setting_args;

	/**
	 * Settings_Section_Element constructor.
	 *
	 * @param object $settings Plugin settings.
	 * @param string $section The name of the section the settings are displayed in.
	 * @param string $settings_page_slug_name The page slug the settings section is on.
	 * @param string $plugin_name The name of this plugin.
	 * @param string $version     The version of this plugin.
	 */
	public function __construct( $settings, $section, $settings_page_slug_name, $plugin_name, $version ) {
		parent::__construct( $plugin_name, $version );

		$this->settings = $settings;
		$this->page     = $settings_page_slug_name;
		$this->section  = $section ?? 'default';

		$this->register_setting_args = array(
			'description'       => '',
			'sanitize_callback' => array( $this, 'sanitize_callback' ),
			'show_in_rest'      => false,
		);
	}

	/**
	 * The name of the setting as it is printed in the left column of the settings table.
	 *
	 * @var string $title The title of the setting.
	 */
	abstract public function get_title();

	/**
	 * The unique setting id, as used in the wp_options table.
	 *
	 * @var string The id of the setting in the database.
	 */
	abstract public function get_id();

	/**
	 * The setting's existing value. Used in HTML value="".
	 *
	 * @return mixed The value.
	 */
	public function get_value() {
		return $this->settings->get_by_id( $this->get_id() );
	}

	/**
	 * The page slug the settings section is on.
	 *
	 * @return string
	 */
	public function get_page() {
		return $this->page;
	}

	/**
	 * The name of the section the settings are displayed in.
	 *
	 * @return string
	 */
	public function get_section() {
		return $this->section;
	}

	/**
	 * Arguments to pass to WordPress's `add_settings_field` function.
	 *
	 * @return array
	 */
	public function get_add_settings_field_args() {

		return array(
			$this->get_id(),
			$this->get_title(),
			array( $this, 'print_field_callback' ),
			$this->get_page(),
			$this->get_section(),
			$this->add_settings_field_args,
		);
	}

	/**
	 * Arguments to pass to WordPress's `register_setting` function.
	 *
	 * @return array
	 */
	public function get_register_setting_args() {

		return array(
			$this->get_page(),
			$this->get_id(),
			$this->register_setting_args,
		);

	}

	/**
	 * Return the settings for populating previously saved settings.
	 *
	 * @return object
	 */
	protected function get_settings() {
		return $this->settings;
	}

	/**
	 * Echo the HTML for configuring this setting.
	 *
	 * @param array $arguments The field data as registered with add_settings_field().
	 */
	abstract public function print_field_callback( $arguments );

	/**
	 * Carry out any sanitization and pre-processing of the POSTed data before it is saved in the database.
	 *
	 * @param mixed $value The value entered by the user as POSTed to WordPress.
	 *
	 * @return mixed
	 */
	abstract public function sanitize_callback( $value );

}
