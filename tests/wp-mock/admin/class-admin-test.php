<?php
/**
 * Tests for Admin.
 *
 * @see Admin
 *
 * @package bh-wp-autologin-urls
 * @author Brian Henry <BrianHenryIE@gmail.com>
 */

namespace EA_WP_AWS_SES_Bounce_Handler\admin;

/**
 * Class Admin_Test
 */
class Admin_Test extends \WP_Mock\Tools\TestCase {

	/**
	 * The plugin name. Unlikely to change.
	 *
	 * @var string Plugin name.
	 */
	private $plugin_name = 'ea-wp-aws-ses-bounce-handler';

	/**
	 * The plugin version, matching the version these tests were written against.
	 *
	 * @var string Plugin version.
	 */
	private $version = '1.0.0';

	/**
	 * Verifies enqueue_styles() calls wp_enqueue_style() with appropriate parameters.
	 * Verifies the .css file exists.
	 *
	 * @see Admin::enqueue_styles()
	 * @see wp_enqueue_style()
	 */
	public function test_enqueue_styles() {

		global $plugin_root_dir;

		// Return any old url.
		\WP_Mock::userFunction(
			'plugin_dir_url',
			array(
				'return' => $plugin_root_dir . '/admin/',
			)
		);

		$css_file = $plugin_root_dir . '/admin/css/ea-wp-aws-ses-bounce-handler-admin.css';

		\WP_Mock::userFunction(
			'wp_enqueue_style',
			array(
				'times' => 1,
				'args'  => array( $this->plugin_name, $css_file, array(), $this->version, 'all' ),
			)
		);

		$ea_wp_aws_ses_bounce_handler_admin = new Admin( $this->plugin_name, $this->version );

		$ea_wp_aws_ses_bounce_handler_admin->enqueue_styles();

		$this->assertFileExists( $css_file );
	}

	/**
	 * Verifies enqueue_scripts() calls wp_enqueue_script() with appropriate parameters.
	 * Verifies the .js file exists.
	 *
	 * @see Admin::enqueue_scripts()
	 * @see wp_enqueue_script()
	 */
	public function test_enqueue_scripts() {

		global $plugin_root_dir;

		// Return any old url.
		\WP_Mock::userFunction(
			'plugin_dir_url',
			array(
				'return' => $plugin_root_dir . '/admin/',
			)
		);

		$handle    = $this->plugin_name;
		$src       = $plugin_root_dir . '/admin/js/ea-wp-aws-ses-bounce-handler-admin.js';
		$deps      = array( 'jquery' );
		$ver       = $this->version;
		$in_footer = true;

		\WP_Mock::userFunction(
			'wp_enqueue_script',
			array(
				'times' => 1,
				'args'  => array( $handle, $src, $deps, $ver, $in_footer ),
			)
		);

		$ea_wp_aws_ses_bounce_handler_admin = new Admin( $this->plugin_name, $this->version );

		$ea_wp_aws_ses_bounce_handler_admin->enqueue_scripts();

		$this->assertFileExists( $src );
	}
}
