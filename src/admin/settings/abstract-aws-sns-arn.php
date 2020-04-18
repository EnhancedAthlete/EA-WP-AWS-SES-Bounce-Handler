<?php
/**
 * This settings field is a text field to configure the AWS ARNs the plugin will listen to.
 *
 * @link       https://BrianHenry.ie
 * @since      1.0.0
 *
 * @package EA_WP_AWS_SES_Bounce_Handler\admin\settings
 */

namespace EA_WP_AWS_SES_Bounce_Handler\admin\settings;

use EA_WP_AWS_SES_Bounce_Handler\includes\Settings_Interface;

/**
 * Class AWS_ARN
 *
 * @package EA_WP_AWS_SES_Bounce_Handler\admin\partials
 */
abstract class AWS_SNS_ARN_Abstract extends Settings_Section_Element_Abstract {

	/**
	 * AWS_ARN constructor.
	 *
	 * @param Settings_Interface $settings The existing settings saved in the database.
	 * @param string             $section  The section as registered with WordPress using `add_settings_section`.
	 * @param string             $settings_page_slug_name The slug of the page this setting is being displayed on.
	 * @param string             $plugin_name The plugin slug.
	 * @param string             $version  The plugin version.
	 */
	public function __construct( $settings, $section, $settings_page_slug_name, $plugin_name, $version ) {

		parent::__construct( $settings, $section, $settings_page_slug_name, $plugin_name, $version );

		$this->title = 'AWS SNS ARN:';

		$this->add_settings_field_args['helper']      = 'The AWS SNS ARN that has been subscribed to.';
		$this->add_settings_field_args['placeholder'] = '';
	}

	/**
	 * The function used by WordPress Settings API to output the field.
	 *
	 * @param array $arguments Settings passed from WordPress do_settings_fields() function.
	 */
	public function print_field_callback( $arguments ) {

		$value = $this->get_value();

		printf( '<input name="%1$s" id="%1$s" type="text" placeholder="%2$s" value="%3$s" />', esc_attr( $this->get_id() ), esc_attr( $arguments['placeholder'] ), esc_attr( $value ) );

		printf( '<span class="helper">%s</span>', esc_html( $arguments['helper'] ) );

		printf( '<p class="description">%s</p>', esc_html( $arguments['supplemental'] ) );

	}

	/**
	 * Check the ARN is not empty and it conforms to the :::: format.
	 *
	 * @param int $value The value POSTed by the Settings API.
	 *
	 * @return int
	 */
	public function sanitize_callback( $value ) {

		if ( empty( $value ) ) {

			$message = 'The ARN was empty.';

			if ( ! empty( $this->get_value() ) ) {
				$message .= ' Previous value ' . $this->get_value() . ' was saved.';
			}

			add_settings_error( $this->get_id(), 'arn-empty-error', $message, 'error' );

			return $this->get_value();
		}

		if ( 1 !== preg_match( '/^arn:aws:sns:[\w-]*:\d*:.*/', $value ) ) {

			$message = 'The ARN `' . $value . '` does not appear to be a correctly formatted AWS SNS ARN.';

			if ( ! empty( $this->get_value() ) ) {
				$message .= ' Previous value ' . $this->get_value() . ' was saved.';
			}

			add_settings_error( $this->get_id(), 'arn-regex-error', $message, 'error' );

			return $this->get_value();
		}

		return $value;
	}

}
