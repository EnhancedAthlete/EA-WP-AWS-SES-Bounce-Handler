<?php
/**
 * Tests for SES object.
 *
 * @package ea-wp-aws-ses-bounce-handler
 * @author Brian Henry <BrianHenryIE@gmail.com>
 */

use EA_WP_AWS_SES_Bounce_Handler\includes\Settings_Interface;

/**
 * Tests that bounce notifications to the configured ARN will be picked up by the plugin.
 *
 * Class SES_Test
 *
 * phpcs:disable WordPress.WP.AlternativeFunctions.file_get_contents_file_get_contents
 */
class SES_Test extends \WP_UnitTestCase {

	/**
	 * The plugin entry point is the `ea_aws_sns_notification` action which is called by the
	 * ea-wp-aws-sns-client-rest-endpoint plugin.
	 *
	 * This test verifies that a well formed notification passes through and calls the `handle_ses_bounce` action.
	 */
	public function test_bounce_action_called() {

		// Hypothetical ARN in wp_options/Settings.
		// TODO: What happens if this is empty?
		$bounces_arn = get_option( Settings_Interface::BOUNCES_ARN );

		global $project_root_dir;

		// Read the test data json from file.
		$bounce_file   = file_get_contents( $project_root_dir . '/tests/testdata/bounce1.json' );
		$bounce_object = json_decode( $bounce_file );

		$headers = null;
		$body    = null;

		$called = false;

		$verification_function = function( $email_address, $bounced_recipient ) use ( &$called ) {

			$called = true;
		};

		// This should be called when the notification is processed.
		add_action( 'handle_ses_bounce', $verification_function, 10, 2 );

		$handled = apply_filters( 'ea_aws_sns_notification', array(), $bounces_arn, $headers, $body, $bounce_object );

		$this->assertTrue( $called );

		$this->assertSame( $handled[0][0], 'ea-wp-aws-ses-bounce-handler' );

	}


	/**
	 * This test verifies that a well formed notification passes through and calls the `handle_ses_complaint` action.
	 */
	public function test_complaint_action_called() {

		// Hypothetical ARN in wp_options/Settings.
		// TODO: What happens if this is empty?
		$complaints_arn = get_option( Settings_Interface::COMPLAINTS_ARN );

		global $project_root_dir;

		// Read the test data json from file.
		$complaint_file   = file_get_contents( $project_root_dir . '/tests/testdata/complaint1.json' );
		$complaint_object = json_decode( $complaint_file );

		$headers = null;
		$body    = null;

		$called = false;

		$verification_function = function( $email_address, $complaint_recipient ) use ( &$called ) {

			$called = true;
		};

		// This should be called when the notification is processed.
		add_action( 'handle_ses_complaint', $verification_function, 10, 2 );

		$handled = apply_filters( 'ea_aws_sns_notification', array(), $complaints_arn, $headers, $body, $complaint_object );

		$this->assertTrue( $called );

		// Expecting the plugin name.
		$this->assertSame( $handled[0][0], 'ea-wp-aws-ses-bounce-handler' );

	}


}
