<?php
/**
 * Tests for I18n. Tests load_plugin_textdomain.
 *
 * @package ea-wp-aws-ses-bounce-handler
 * @author Brian Henry <BrianHenryIE@gmail.com>
 */

namespace EA_WP_AWS_SES_Bounce_Handler\includes;

/**
 * Class I18n_Test
 *
 * @see I18n
 */
class I18n_Test extends \WP_UnitTestCase {

	/**
	 * AFAICT, this will fail until a translation has been added.
	 *
	 * @see load_plugin_textdomain()
	 * @see https://gist.github.com/GaryJones/c8259da3a4501fd0648f19beddce0249
	 */
	public function test_load_plugin_textdomain() {

		$this->markTestSkipped( 'Needs translation.' );

		global $plugin_root_dir;

		$this->assertTrue( file_exists( $plugin_root_dir . '/languages/' ), '/languages/ folder does not exist.' );

		// Seems to fail because there are no translations to load.
		$this->assertTrue( is_textdomain_loaded( 'ea-wp-aws-ses-bounce-handler' ), 'i18n text domain not loaded.' );

	}

	/**
	 * Checks if the filter run by WordPress in the load_plugin_textdomain() function is called.
	 *
	 * @see load_plugin_textdomain()
	 */
	public function test_load_plugin_textdomain_function() {

		$called        = false;
		$actual_domain = null;

		$filter = function( $locale, $domain ) use ( &$called, &$actual_domain ) {

			$called        = true;
			$actual_domain = $domain;

			return $locale;
		};

		add_filter( 'plugin_locale', $filter, 10, 2 );

		/**
		 * Get the main plugin class.
		 *
		 * @var EA_WP_AWS_SES_Bounce_Handler $ea_wp_aws_ses_bounce_handler
		 */
		$ea_wp_aws_ses_bounce_handler = $GLOBALS['ea_wp_aws_ses_bounce_handler'];
		$i18n                         = $ea_wp_aws_ses_bounce_handler->i18n;

		$i18n->load_plugin_textdomain();

		$this->assertTrue( $called, 'plugin_locale filter not called within load_plugin_textdomain() suggesting it has not been set by the plugin.' );
		$this->assertEquals( 'ea-wp-aws-ses-bounce-handler', $actual_domain );

	}
}
