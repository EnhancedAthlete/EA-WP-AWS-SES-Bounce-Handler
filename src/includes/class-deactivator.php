<?php
/**
 * Fired during plugin deactivation
 *
 * @link       https://BrianHenry.ie
 * @since      1.2.0
 *
 * @package    EA_WP_AWS_SES_Bounce_Handler
 * @subpackage EA_WP_AWS_SES_Bounce_Handler/includes
 */

namespace EA_WP_AWS_SES_Bounce_Handler\includes;

/**
 * Fired during plugin deactivation.
 *
 * This class defines all code necessary to run during the plugin's deactivation.
 *
 * Class Deactivator
 *
 * @package EA_WP_AWS_SES_Bounce_Handler\includes
 */
class Deactivator {

	/**
	 * Remove the previously registered Bounced Email user role.
	 *
	 * @since 1.2.0
	 */
	public static function deactivate() {
		remove_role( 'bounced_email' );
	}

}

