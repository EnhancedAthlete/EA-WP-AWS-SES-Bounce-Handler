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
	 * The ARN to be listened to for bounce reports.
	 *
	 * @var string
	 */
	private $bounces_arn;

	/**
	 * The ARN to be listened to for complaint reports.
	 *
	 * @var string
	 */
	private $complaints_arn;

	/**
	 * Settings constructor.
	 *
	 * Reads the setting from WordPress's options table (using get_option). No defaults provided.
	 */
	public function __construct() {

		$this->bounces_arn    = get_option( self::BOUNCES_ARN );
		$this->complaints_arn = get_option( self::COMPLAINTS_ARN );
	}

	/**
	 * Returns the AWS resource identifier used by SNS for the bounces topic.
	 *
	 * @return string
	 */
	public function get_bounces_arn() {
		return $this->bounces_arn;
	}

	/**
	 * Returns the AWS resource identifier used by SNS for the complaints topic.
	 *
	 * @return string
	 */
	public function get_complaints_arn() {
		return $this->complaints_arn;
	}

}
