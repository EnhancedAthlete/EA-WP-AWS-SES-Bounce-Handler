<?php
/**
 * Class implementing the required settings for the plugin.
 *
 * @link       https://BrianHenry.ie
 * @since      1.0.0
 *
 * @package    EA_WP_AWS_SES_Bounce_Handler
 * @subpackage EA_WP_AWS_SES_Bounce_Handler/includes
 */

namespace EA_WP_AWS_SES_Bounce_Handler\includes;

/**
 * The plugin settings.
 *
 * @since      1.0.0
 * @package    EA_WP_AWS_SES_Bounce_Handler
 * @subpackage EA_WP_AWS_SES_Bounce_Handler/includes
 * @author     BrianHenryIE <BrianHenryIE@gmail.com>
 */
class Settings implements Settings_Interface {

	/**
	 * List of ARNs which have successfully been confirmed with AWS SNS.
	 *
	 * @var string[]
	 */
	private $confirmed_arns;

	/**
	 * Settings constructor.
	 *
	 * Reads the setting from WordPress's options table (using get_option). No defaults provided.
	 */
	public function __construct() {

		$this->confirmed_arns = get_option( self::CONFIRMED_ARNS, array() );
	}

	/**
	 * List of ARNs which have successfully been confirmed with AWS SNS.
	 *
	 * @return string[]
	 */
	public function get_confirmed_arns() {
		return $this->confirmed_arns;
	}

	/**
	 * Add an ARN string to the list of confirmed ARNs and save the option.
	 *
	 * @param string $arn AWS SNS ARN.
	 */
	public function set_confirmed_arn( $arn ) {
		$this->confirmed_arns[] = $arn;
		update_option( self::CONFIRMED_ARNS, $this->confirmed_arns );
	}
}
