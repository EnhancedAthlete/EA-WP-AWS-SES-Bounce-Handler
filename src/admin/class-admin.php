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
	 * Register the JavaScript for the admin area.
	 *
	 * @since    1.2.0
	 */
	public function enqueue_scripts() {

		$version = defined( WP_DEBUG ) && WP_DEBUG ? time() : $this->get_version();

		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/ea-wp-aws-ses-bounce-handler-admin.js', array( 'jquery' ), $version, false );
	}

}
