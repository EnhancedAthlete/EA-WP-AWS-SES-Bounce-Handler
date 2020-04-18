<?php
/**
 * The sns-invoked functionality of the plugin.
 *
 * @link       https://BrianHenry.ie
 * @since      1.0.0
 *
 * @package   EA_WP_AWS_SES_Bounce_Handler
 * @subpackage EA_WP_AWS_SES_Bounce_Handler/sns
 */

namespace EA_WP_AWS_SES_Bounce_Handler\sns;

use EA_WP_AWS_SES_Bounce_Handler\includes\Settings_Interface;
use EA_WP_AWS_SES_Bounce_Handler\WPPB\WPPB_Object;

/**
 * The sns-invoked functionality of the plugin.
 *
 * @package   EA_WP_AWS_SES_Bounce_Handler
 * @subpackage EA_WP_AWS_SES_Bounce_Handler/sns
 * @author     BrianHenryIE <BrianHenryIE@gmail.com>
 *
 * Ignore snake case warnings for JSON objects.
 * phpcs:disable WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase
 */
class SNS extends WPPB_Object {

	/**
	 * The settings object contains the AWS ARNs to listen to, as configured by the user.
	 *
	 * @var Settings_Interface
	 */
	private $settings;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @param string             $plugin_name The name of the plugin.
	 * @param string             $version The version of this plugin.
	 * @param Settings_Interface $settings The settings containing the ARNs to listen for.
	 *
	 * @since    1.0.0
	 */
	public function __construct( $plugin_name, $version, $settings ) {

		parent::__construct( $plugin_name, $version );

		$this->settings = $settings;

	}

	/**
	 * When a bounce notification is received from SES, this function deletes the user from the Newsletter plugin
	 * and fires an action for other plugins to hook into.
	 *
	 * @hooked filter ea_aws_sns_notification
	 *
	 * @see https://docs.aws.amazon.com/ses/latest/DeveloperGuide/notification-examples.html
	 * @see https://wordpress.org/plugins/newsletter/
	 *
	 * @param array  $handled                 Array of plugins that have handled this notification.
	 * @param string $notification_topic_arn  The ARN of the received notification.
	 * @param array  $headers                 HTTP headers received from AWS SNS.
	 * @param object $body                    HTTP body received from AWS SNS.
	 * @param object $message                 The (potential) bounce report object from AWS SES.
	 *
	 * @return array $handled
	 */
	public function handle_bounces( $handled, $notification_topic_arn, $headers, $body, $message ) {

		$bounce_arn = $this->settings->get_bounces_arn();

		if ( $bounce_arn !== $notification_topic_arn || 'Bounce' !== $message->notificationType ) {
			return $handled;
		}

		if ( 'Permanent' === $message->bounce->bounceType ) {

			foreach ( $message->bounce->bouncedRecipients as $bounced_recipient ) {

				$email_address = $bounced_recipient->emailAddress;

				/**
				 * Action to allow other plugins to act on SES bounce notification.
				 *
				 * @param string $email_address     The email address that has bounced.
				 * @param object $bounced_recipient Parent object with emailAddress, status, action, diagnosticCode.
				 * @param object $message           Parent object of complete notification.
				 *
				 * @see https://docs.aws.amazon.com/ses/latest/DeveloperGuide/notification-examples.html
				 */
				do_action( 'handle_ses_bounce', $email_address, $bounced_recipient, $message );
			}
		}

		$handled[] = array( $this->plugin_name, __FUNCTION__ );

		return $handled;
	}

	/**
	 * When a complaint is received from SES, this function unsubscribes the user from the Newsletter plugin
	 * and fires an action for other plugins to hook into.
	 *
	 * @hooked filter ea_aws_sns_notification
	 *
	 * @see https://docs.aws.amazon.com/ses/latest/DeveloperGuide/notification-examples.html
	 * @see https://wordpress.org/plugins/newsletter/
	 *
	 * @param array  $handled                 Array of plugins that have handled this notification.
	 * @param string $notification_topic_arn  The ARN of the received notification.
	 * @param array  $headers                 HTTP headers received from AWS SNS.
	 * @param object $body                    HTTP body received from AWS SNS.
	 * @param object $message                 The (potential) complaint report object from AWS SES.
	 *
	 * @return array
	 */
	public function handle_complaints( $handled, $notification_topic_arn, $headers, $body, $message ) {

		$complaints_arn = $this->settings->get_complaints_arn();

		if ( $complaints_arn !== $notification_topic_arn || 'Complaint' !== $message->notificationType ) {
			return $handled;
		}

		foreach ( $message->complaint->complainedRecipients as $complained_recipient ) {

			$email_address = $complained_recipient->emailAddress;

			/**
			 * Action to allow other plugins to act on SES complaint notifications.
			 *
			 * @param string $email_address     The email address that has complained.
			 * @param object $bounced_recipient Parent object with emailAddress, status, action, diagnosticCode.
			 * @param object $message           Parent object of complete notification.
			 *
			 * @see https://docs.aws.amazon.com/ses/latest/DeveloperGuide/notification-examples.html
			 */
			do_action( 'handle_ses_complaint', $email_address, $complained_recipient, $message );
		}

		$handled[] = array( $this->plugin_name, __FUNCTION__ );

		return $handled;

	}

}

