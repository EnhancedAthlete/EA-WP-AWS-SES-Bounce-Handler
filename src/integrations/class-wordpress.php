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

use EA_WP_AWS_SES_Bounce_Handler\admin\Bounce_Handler_Test;
use EA_WP_AWS_SES_Bounce_Handler\WPPB\WPPB_Object;
use WP_User;

/**
 * Hook into `handle_ses_bounce` to add Bounced Email role it to user accounts.
 *
 * Class WordPress
 *
 * @package EA_WP_AWS_SES_Bounce_Handler\integrations
 */
class WordPress extends WPPB_Object implements SES_Bounce_Handler_Integration_Interface {

	public function is_enabled(): bool {
		return true;
	}

	public function init(): void {
		// Nothing needed.
	}

	public function get_description(): string {
		return 'Adds <a href=' . esc_url( admin_url( 'users.php?role=bounced_email' ) ) . '">Bounced Email</a> role to users';
	}

	/**
	 * Add Bounced Email role to user so it can be filtered on the Users admin page.
	 *
	 * @hooked handle_ses_bounce
	 *
	 * @param string $email_address     The email address that has bounced.
	 * @param object $bounced_recipient Parent object with emailAddress, status, action, diagnosticCode.
	 * @param object $message           Parent object of complete notification.
	 */
	public function handle_ses_bounce( $email_address, $bounced_recipient, $message ): void {

		$user = get_user_by( 'email', $email_address );

		if ( ! $user ) {
			return;
		}

		$user->add_role( 'bounced_email' );
	}


	public function handle_ses_complaint( $email_address, $complained_recipient, $message ): void {
		// Nothing.
	}

	/**
	 * Create a WordPress user with the AWS SES bounce simulator email address.
	 *
	 * @param Bounce_Handler_Test $test The test configuration.
	 *
	 * @return array [values to save, html to print]
	 */
	public function setup_test( $test ): ?array {

		$test_data = array();

		$user_id = wp_create_user( $test->get_email(), wp_generate_password( 30 ), $test->get_email() );

		$user       = get_user_by( 'id', $user_id );
		$user_roles = $user->roles;

		$test_data['wp_user_id']    = $user_id;
		$test_data['wp_user_roles'] = $user_roles;

		$user_url = admin_url( 'user-edit.php?user_id=' . $user_id );

		$test_html_output = '<p>WordPress <a href="' . $user_url . '">user ' . $user_id . '</a> created with roles: <em>' . implode( ', ', $user_roles ) . '</em></p>';

		return array(
			'data' => $test_data,
			'html' => $test_html_output,
		);
	}

	/**
	 * The test succeeded if the user had the bounced_email role added.
	 *
	 * @param $test_data
	 *
	 * @return array
	 */
	public function verify_test( $test_data ): ?array {

		// Get user roles
		$user       = get_user_by( 'id', intval( $test_data['wp_user_id'] ) );
		$user_roles = $user->roles;

		$previous_user_roles = (array) $test_data['wp_user_roles'];

		$new_roles = array_diff( $user_roles, $previous_user_roles );

		$success = ! empty( $new_roles ); // && in_array( 'bounced_email', $new_roles );

		$user_url = admin_url( 'user-edit.php?user_id=' . $user->ID );

		if ( $success ) {

			$html = 'WordPress <a href="' . esc_url( $user_url ) . '">user ' . $user->ID . '</a> found with new roles: <em>' . implode( ', ', $new_roles ) . '</em>.';

		} else {

			$html = 'No new roles added to <a href="' . esc_url( $user_url ) . '">user ' . $user->ID . '</a>.';
		}

		return array(
			'success' => $success,
			'html'    => $html,
		);

	}

	/**
	 * Delete the user created during the test.
	 *
	 * @param $test_data
	 */
	public function delete_test_data( $test_data ): bool {

		if ( isset( $test_data['wp_user_id'] ) ) {
			wp_delete_user( intval( $test_data['wp_user_id'] ) );

			return true;
		}
		return false;
	}
}
