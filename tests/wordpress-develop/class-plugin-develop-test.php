<?php
/**
 * Class Plugin_Test. Tests the root plugin setup.
 *
 * @package ea-wp-aws-ses-bounce-handler
 * @author Brian Henry <BrianHenryIE@gmail.com>
 */

namespace EA_WP_AWS_SES_Bounce_Handler;

use EA_WP_AWS_SES_Bounce_Handler\includes\EA_WP_AWS_SES_Bounce_Handler;

/**
 * Verifies the plugin has been instantiated and added to PHP's $GLOBALS variable.
 */
class Plugin_Develop_Test extends \WP_UnitTestCase {

	/**
	 * Test the main plugin object is added to PHP's GLOBALS and that it is the correct class.
	 */
	public function test_plugin_instantiated() {

		$this->assertArrayHasKey( 'ea_wp_aws_ses_bounce_handler', $GLOBALS );

		$this->assertInstanceOf( EA_WP_AWS_SES_Bounce_Handler::class, $GLOBALS['ea_wp_aws_ses_bounce_handler'] );
	}

}
