<?php
/**
 * Tests for plugin deactivation – undo changes that should not persist.
 *
 * @package ea-wp-aws-ses-bounce-handler
 * @author Brian Henry <BrianHenryIE@gmail.com>
 */

namespace EA_WP_AWS_SES_Bounce_Handler\includes;

/**
 * Make sure the added role is removed.
 *
 * Class Deactivator_Test
 *
 * @package EA_WP_AWS_SES_Bounce_Handler\includes
 */
class Deactivator_Test extends \WP_UnitTestCase {

	/**
	 * Check the role exists, run deactivation, check it is gone!
	 */
	public function test_role_removed_on_deactivation() {

		$roles = wp_roles();

		$this->assertNotNull( $roles->get_role( 'bounced_email' ) );

		deactivate_ea_wp_aws_ses_bounce_handler();

		$this->assertNull( $roles->get_role( 'bounced_email' ) );
	}
}
