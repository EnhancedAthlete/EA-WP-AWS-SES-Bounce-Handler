<?php
/**
 * Functionality to mark WordPress user accounts as Bounced Email.
 *
 * @link       https://BrianHenry.ie
 * @since      1.1.0
 *
 * @package   EA_WP_AWS_SES_Bounce_Handler
 * @subpackage EA_WP_AWS_SES_Bounce_Handler/integrations
 */

namespace EA_WP_AWS_SES_Bounce_Handler\integrations;

use EA_WP_AWS_SES_Bounce_Handler\WPPB\WPPB_Object;
use WP_User;

/**
 * Hook into `handle_ses_bounce` to add Bounced Email role it to user accounts.
 *
 * Class WordPress
 *
 * @package EA_WP_AWS_SES_Bounce_Handler\integrations
 */
class WordPress extends WPPB_Object {

	/**
	 * Add Bounced Email role to user so it can be filtered on the Users admin page.
	 *
	 * @hooked handle_ses_bounce
	 *
	 * @param string $email_address     The email address that has bounced.
	 * @param object $bounced_recipient Parent object with emailAddress, status, action, diagnosticCode.
	 * @param object $message           Parent object of complete notification.
	 */
	public function add_bounced_role_to_user( $email_address, $bounced_recipient, $message ) {

		/**
		 * The WordPress user account for the bounced email address.
		 *
		 * @var WP_User $user
		 */
		$user = get_user_by( 'email', $email_address );

		if ( ! $user ) {
			return;
		}

		$user->add_role( 'bounced_email' );
	}

}
