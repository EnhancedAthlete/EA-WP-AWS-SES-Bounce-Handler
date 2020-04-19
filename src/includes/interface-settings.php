<?php
/**
 * Interface defining the required settings the plugin needs, and the constants used.
 *
 * @link       https://BrianHenry.ie
 * @since      1.0.0
 *
 * @package   EA_WP_AWS_SES_Bounce_Handler
 * @subpackage EA_WP_AWS_SES_Bounce_Handler/includes
 */

namespace EA_WP_AWS_SES_Bounce_Handler\includes;

/**
 * The settings interface.
 *
 * @since      1.0.0
 * @package    EA_WP_AWS_SES_Bounce_Handler
 * @subpackage EA_WP_AWS_SES_Bounce_Handler/includes
 * @author     BrianHenryIE <BrianHenryIE@gmail.com>
 */
interface Settings_Interface {

	const BOUNCES_ARN    = 'ea-wp-aws-ses-bounce-handler-bounces-arn';
	const COMPLAINTS_ARN = 'ea-wp-aws-ses-bounce-handler-complaints-arn';

	const CONFIRMED_ARNS = 'ea-wp-aws-ses-bounce-handler-confirmed-arns';

	/**
	 * Return the AWS resource identifier used by SNS for the bounces topic.
	 *
	 * @return string
	 */
	public function get_bounces_arn();

	/**
	 * Return the AWS resource identifier used by SNS for the complaints topic.
	 *
	 * @return string
	 */
	public function get_complaints_arn();

	/**
	 * Return the list of ARNs that have been successfully confirmed.
	 *
	 * @return string[]
	 */
	public function get_confirmed_arns();

	/**
	 * Record a successfully confirmed ARN subscription.
	 *
	 * @param string $arn AWS SNS ARN.
	 */
	public function set_confirmed_arn( $arn );

}
