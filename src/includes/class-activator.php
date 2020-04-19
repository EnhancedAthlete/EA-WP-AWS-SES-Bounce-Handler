<?php
/**
 * Fired during plugin activation
 *
 * @link       https://BrianHenry.ie
 * @since      1.2.0
 *
 * @package    EA_WP_AWS_SES_Bounce_Handler
 * @subpackage EA_WP_AWS_SES_Bounce_Handler/includes
 */

namespace EA_WP_AWS_SES_Bounce_Handler\includes;

/**
 * Fired during plugin activation.
 *
 * This class defines all code necessary to run during the plugin's activation.
 *
 * Class Activator
 *
 * @package EA_WP_AWS_SES_Bounce_Handler\includes
 */
class Activator {


	/**
	 * Register the Bounced Email WordPress user role.
	 * The role has no capabilities.
	 *
	 * @since 1.2.0
	 */
	public static function activate() {
		add_role( 'bounced_email', 'Bounced Email' );
	}

}

