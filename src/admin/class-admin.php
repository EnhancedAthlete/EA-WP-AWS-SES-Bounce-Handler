<?php
/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://BrianHenry.ie
 * @since      1.0.0
 *
 * @package    EA_WP_AWS_SES_Bounce_Handler
 * @subpackage EA_WP_AWS_SES_Bounce_Handler/admin
 */

namespace EA_WP_AWS_SES_Bounce_Handler\admin;

use EA_WP_AWS_SES_Bounce_Handler\WPPB\WPPB_Object;

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package   EA_WP_AWS_SES_Bounce_Handler
 * @subpackage EA_WP_AWS_SES_Bounce_Handler/admin
 * @author     BrianHenryIE <BrianHenryIE@gmail.com>
 */
class Admin extends WPPB_Object {

	// TODO: Check ea-wp-aws-sns-client-rest-endpoint is installed.

	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/ea-wp-aws-ses-bounce-handler-admin.css', array(), $this->version, 'all' );

	}

	/**
	 * Checks EA WP AWS SNS Client REST Endpoint required plugin is installed.
	 * Checks its version is ~2.
	 *
	 * @hooked action admin_notices
	 * @see https://github.com/EnhancedAthlete/ea-wp-aws-sns-client-rest-endpoint
	 */
	public function requirements_notice() {

		// Don't irritate users with plugin install suggestions while they're already installing plugins.
		// We're not really "Processing form data without nonce verification." here...
		// phpcs:disable WordPress.Security.NonceVerification.Recommended
		if ( isset( $_GET['action'] ) && ( 'upload-plugin' === $_GET['action'] || 'install-plugin' === $_GET['action'] ) ) {
			return;
		}

		$required_version = '2.0.0';

		if ( ! is_plugin_active( 'ea-wp-aws-sns-client-rest-endpoint/ea-wp-aws-sns-client-rest-endpoint.php' ) ) {

			echo '<div class="notice notice-warning is-dismissible"><p><b><i>' . esc_attr( $this->plugin_name ) . '</i></b> requires <a href="https://github.com/EnhancedAthlete/ea-wp-aws-sns-client-rest-endpoint"><i>EA WP AWS SNS - Client REST Endpoint</i></a> installed and active to function correctly.</p></div>';

			return;
		}

		if ( intval( $required_version ) > intval( EA_WP_AWS_SNS_CLIENT_REST_ENDPOINT_VERSION ) ) {

			echo '<div class="notice notice-warning is-dismissible"><p><b><i>' . esc_attr( $this->plugin_name ) . '</i></b> requires <a href="https://github.com/EnhancedAthlete/ea-wp-aws-sns-client-rest-endpoint"><i>EA WP AWS SNS - Client REST Endpoint</i></a> ' . esc_attr( $required_version ) . ' or greater installed and active to function correctly.</p></div>';

			return;
		}

		if ( intval( $required_version ) < intval( EA_WP_AWS_SNS_CLIENT_REST_ENDPOINT_VERSION ) ) {

			echo '<div class="notice notice-warning is-dismissible"><p><b><i>' . esc_attr( $this->plugin_name ) . '</i></b> has not been tested with the current version (' . esc_attr( EA_WP_AWS_SNS_CLIENT_REST_ENDPOINT_VERSION ) . ') of <a href="https://github.com/EnhancedAthlete/ea-wp-aws-sns-client-rest-endpoint"><i>EA WP AWS SNS - Client REST Endpoint</i></a>. Please check its changelog for breaking changes.</p></div>';

			return;
		}

	}


}
