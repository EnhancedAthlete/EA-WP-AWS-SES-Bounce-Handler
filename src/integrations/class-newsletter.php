<?php
/**
 * Functionality for the Newsletter plugin to mark users as bounced and unsubscribe users who complain.
 *
 * @see https://wordpress.org/plugins/newsletter
 *
 * @link       https://BrianHenry.ie
 * @since      1.0.0
 *
 * @package   EA_WP_AWS_SES_Bounce_Handler
 * @subpackage EA_WP_AWS_SES_Bounce_Handler/integrations
 */

namespace EA_WP_AWS_SES_Bounce_Handler\integrations;

use EA_WP_AWS_SES_Bounce_Handler\WPPB\WPPB_Object;
use TNP;

/**
 * `handle_ses_bounce` => Mark the user bounced.
 * `handle_ses_complaint` => Unsubscribe the user.
 *
 * Class Newsletter
 *
 * @package EA_WP_AWS_SES_Bounce_Handler\integrations
 */
class Newsletter extends WPPB_Object {

	/**
	 * Mark email addresses as bounced in Newsletter plugin.
	 *
	 * @hooked handle_ses_bounce
	 *
	 * @param string $email_address     The email address that has bounced.
	 * @param object $bounced_recipient Parent object with emailAddress, status, action, diagnosticCode.
	 * @param object $message           Parent object of complete notification.
	 *
	 * phpcs:disable WordPress.DB.DirectDatabaseQuery.DirectQuery
	 * phpcs:disable WordPress.DB.DirectDatabaseQuery.NoCaching
	 */
	public function mark_as_bounced( $email_address, $bounced_recipient, $message ) {

		if ( ! class_exists( TNP::class ) ) {
			return;
		}

		global $wpdb;
		$updated = $wpdb->update( NEWSLETTER_USERS_TABLE, array( 'status' => 'B' ), array( 'email' => $email_address ) );
	}

	/**
	 * Unsubscribe user from future emails.
	 *
	 * @hooked handle_ses_complaint
	 *
	 * @param string $email_address     The email address that has bounced.
	 * @param object $complained_recipient Parent object with emailAddress, status, action, diagnosticCode.
	 * @param object $message           Parent object of complete notification.
	 */
	public function mark_as_complaint( $email_address, $complained_recipient, $message ) {

		if ( ! class_exists( TNP::class ) ) {
			return;
		}

		$params          = array();
		$params['email'] = $email_address;

		TNP::unsubscribe( $params );

		// TODO: Associate the complaint with the particular newsletter sent.
	}

}
