<?php
/**
 * Tests for Settings object.
 *
 * @package ea-wp-aws-ses-bounce-handler
 * @author Brian Henry <BrianHenryIE@gmail.com>
 */

namespace EA_WP_AWS_SES_Bounce_Handler\includes;

/**
 * Tests the Settings object functions and the interface's consts definitions.
 *
 * Class Settings_Test
 *
 * @package EA_WP_AWS_SES_Bounce_Handler\includes
 */
class Settings_Test extends \WP_UnitTestCase {

	/**
	 * Check Settings class is pulling from WordPress options correctly.
	 */
	public function test_settings_constructor() {

		$expected_bounces_arn_1    = 'expected bounces arn 1';
		$expected_complaints_arn_1 = 'expected complaints arn 1';

		$expected_bounces_arn_2    = 'expected bounces arn 2';
		$expected_complaints_arn_2 = 'expected complaints arn 2';

		update_option( Settings_Interface::BOUNCES_ARN, $expected_bounces_arn_1 );
		update_option( Settings_Interface::COMPLAINTS_ARN, $expected_complaints_arn_1 );

		$settings_1 = new Settings();

		$this->assertSame( $expected_bounces_arn_1, $settings_1->get_bounces_arn() );
		$this->assertSame( $expected_complaints_arn_1, $settings_1->get_complaints_arn() );

		update_option( Settings_Interface::BOUNCES_ARN, $expected_bounces_arn_2 );
		update_option( Settings_Interface::COMPLAINTS_ARN, $expected_complaints_arn_2 );

		$settings_2 = new Settings();

		$this->assertSame( $expected_bounces_arn_2, $settings_2->get_bounces_arn() );
		$this->assertSame( $expected_complaints_arn_2, $settings_2->get_complaints_arn() );

	}

}
