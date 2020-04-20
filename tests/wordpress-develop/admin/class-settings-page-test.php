<?php

namespace EA_WP_AWS_SES_Bounce_Handler\admin;

use EA_WP_AWS_SES_Bounce_Handler\includes\Settings;

class Settings_Page_Test extends \WP_UnitTestCase {


	/**
	 * This should tell us the WordPress core pluggable.php.
	 */
	public function test_get_wp_mail_info() {

		// $this->markTestIncomplete();

		$settings = new Settings();

		$settings_page = new Settings_Page( 'plugin_name', 'version', $settings );

		$wp_mail_info = $settings_page->get_wp_mail_info();

		$eg = '<div class="notice inline notice-warning"><p>WordPress is sending mail using <em>/Users/BrianHenryIE/Sites/Enhanced Athlete/common/ea-wp-aws-ses-bounce-handler/vendor/wordpress/wordpress/tests/phpunit/includes/mock-mailer.php</em>.</p></div>';

		$pattern = '/.*mock-mailer.php.*/';

		$this->assertTrue( 1 === preg_match( $pattern, $wp_mail_info ) );
	}


}
