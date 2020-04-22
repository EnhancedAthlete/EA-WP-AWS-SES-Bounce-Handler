<?php
/**
 * A WordPress plugin to unsubscribe users from email lists when AWS SES sends a bounce or complaint report.
 *
 * @link              https://BrianHenry.ie
 * @since             1.0.0
 * @package           EA_WP_AWS_SES_Bounce_Handler
 *
 * @wordpress-plugin
 * Plugin Name:       AWS SES Bounce Handler
 * Plugin URI:        https://github.com/EnhancedAthlete/ea-wp-aws-ses-bounce-handler
 * Description:       When AWS SES sends a bounce or complaint report, users & orders are marked; Newsletter users are unsubscribed.
 * Version:           1.2.0
 * Author:            BrianHenryIE
 * Author URI:        https://BrianHenry.ie
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       ea-wp-aws-ses-bounce-handler
 * Domain Path:       /languages
 */

namespace {

	use EA_WP_AWS_SES_Bounce_Handler\includes\Activator;
	use EA_WP_AWS_SES_Bounce_Handler\includes\Deactivator;

	// If this file is called directly, abort.
	if ( ! defined( 'WPINC' ) ) {
		die;
	}

	/**
	 * The code that runs during plugin activation.
	 * This action is documented in includes/class-activator.php
	 */
	function activate_ea_wp_aws_ses_bounce_handler() {

		Activator::activate();
	}

	/**
	 * The code that runs during plugin deactivation.
	 * This action is documented in includes/class-deactivator.php
	 */
	function deactivate_ea_wp_aws_ses_bounce_handler() {

		Deactivator::deactivate();
	}

	register_activation_hook( __FILE__, 'activate_ea_wp_aws_ses_bounce_handler' );
	register_deactivation_hook( __FILE__, 'deactivate_ea_wp_aws_ses_bounce_handler' );

}

namespace EA_WP_AWS_SES_Bounce_Handler {

	use EA_WP_AWS_SES_Bounce_Handler\includes\EA_WP_AWS_SES_Bounce_Handler;
	use EA_WP_AWS_SES_Bounce_Handler\includes\Settings;
	use EA_WP_AWS_SES_Bounce_Handler\WPPB\WPPB_Loader;

	require_once plugin_dir_path( __FILE__ ) . 'autoload.php';

	/**
	 * Currently plugin version.
	 */
	define( 'EA_WP_AWS_SES_BOUNCE_HANDLER_VERSION', '1.1.0' );


	/**
	 * Function to keep the loader and settings objects out of the namespace.
	 *
	 * @return EA_WP_AWS_SES_Bounce_Handler;
	 */
	function instantiate_ea_wp_aws_ses_bounce_handler() {

		$loader = new WPPB_Loader();

		$settings = new Settings();

		$ea_wp_aws_ses_bounce_handler = new EA_WP_AWS_SES_Bounce_Handler( $loader, $settings );

		return $ea_wp_aws_ses_bounce_handler;
	}

	/**
	 * Begins execution of the plugin.
	 *
	 * Since everything within the plugin is registered via hooks,
	 * then kicking off the plugin from this point in the file does
	 * not affect the page life cycle.
	 *
	 * @since    1.0.0
	 *
	 * phpcs:disable Squiz.PHP.DisallowMultipleAssignments.Found
	 */
	$GLOBALS['ea_wp_aws_ses_bounce_handler'] = $ea_wp_aws_ses_bounce_handler = instantiate_ea_wp_aws_ses_bounce_handler();
	$ea_wp_aws_ses_bounce_handler->run();
}
