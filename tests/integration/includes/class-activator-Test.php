<?php
/**
 * Tests for plugin activation – code that need only run once.
 *
 * @package ea-wp-aws-ses-bounce-handler
 * @author Brian Henry <BrianHenryIE@gmail.com>
 */

namespace EA_WP_AWS_SES_Bounce_Handler\includes;

/**
 * Add a WordPress role for tagging users.
 *
 * Class Activator_Test
 *
 * @package EA_WP_AWS_SES_Bounce_Handler\includes
 */
class Activator_Test extends \Codeception\TestCase\WPTestCase {

	/**
	 * Check the role does not exists, run activation, then verify it does.
	 */
	public function test_role_added_on_activation() {

		$roles = wp_roles();

		$this->assertNull( $roles->get_role( 'bounced_email' ) );

		activate_ea_wp_aws_ses_bounce_handler();

		$this->assertNotNull( $roles->get_role( 'bounced_email' ) );
	}
}
