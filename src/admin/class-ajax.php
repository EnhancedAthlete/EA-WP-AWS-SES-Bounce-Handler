<?php


namespace EA_WP_AWS_SES_Bounce_Handler\admin;

use EA_WP_AWS_SES_Bounce_Handler\integrations\WooCommerce;
use EA_WP_AWS_SES_Bounce_Handler\WPPB\WPPB_Object;
use TNP;

/**
 * Code to run the ses test and to poll for its completion.
 */
class Ajax extends WPPB_Object {

	public function run_ses_bounce_test() {

		$result         = array();
		$result['data'] = array();

		// Verify nonce.
		if ( ! check_ajax_referer( 'run-ses-bounce-test-form', false, false ) ) {

			$result['success']         = false;
			$result['data']['message'] = 'Referrer/nonce failure';

			wp_send_json_error( $result, 400 );
		}

		// Verify settings.

		$bounce_test_id    = time();
		$bounce_test_email = "bounce+{$bounce_test_id}@simulator.amazonses.com";

		$html  = '<p>Test started at time: <em>' . $bounce_test_id . '</em></p>';
		$html .= '<p>Using email address: <em>' . $bounce_test_email . '</em></p>';

		// Created user id 123
		$user_id = wp_create_user( $bounce_test_email, wp_generate_password( 31 ), $bounce_test_email );

		$user       = get_user_by( 'id', $user_id );
		$user_roles = $user->roles;

		$user_url = admin_url( 'user-edit.php?user_id=' . $user_id );

		$html .= '<p>WordPress <a href="' . $user_url . '">user ' . $user_id . '</a> created with roles: <em>' . implode( ', ', $user_roles ) . '</em></p>';

		if ( class_exists( \WooCommerce::class ) ) {
			// Created dummy order
			$order   = wc_create_order();
			$address = array(
				'email' => $bounce_test_email,
			);
			$order->set_address( $address, 'billing' );
			$order_bounced_meta_value = $order->get_meta( WooCommerce::BOUNCED_META_KEY );
			$order_bounced_meta_value = empty( $order_bounced_meta_value ) ? 'empty' : $order_bounced_meta_value;

			$order_url = admin_url( 'post.php?post=' . $order->get_id() . '&action=edit' );

			$html .= '<p>WooCommerce <a href="' . $order_url . '">order ' . $order->get_id() . '</a> created with meta key <em>' . WooCommerce::BOUNCED_META_KEY . '</em> value: <em>' . $order_bounced_meta_value . '</em></p>';

		}

		// Create dummy Newsletter subscriber
		if ( class_exists( TNP::class ) ) {

			$params = array();

			$params['email'] = $bounce_test_email;

			/** @var \TNP_User $tnp_user */
			$tnp_user = TNP::add_subscriber( $params );

			$tnp_user_status = $tnp_user->status;

			$tnp_user_url = admin_url( 'admin.php?page=newsletter_users_edit&id=' . $tnp_user->id );
			$html        .= '<p>Newsletter <a href="' . $tnp_user_url . '">subscriber ' . $tnp_user->id . '</a> created with status ' . $tnp_user_status . '</p>';

		}

		// Record all this so it can be deleted.
		// key with $bounce_test_id

		// Send email to bounce  + time @sim
		$to      = $bounce_test_email;
		$subject = 'EA WP AWS SES Bounce Handler Test Email';
		$message = $html;

		$mail_send = wp_mail( $to, $subject, $message );

		if ( ! $mail_send ) {
			$result['success']         = false;
			$result['data']['message'] = 'wp_mail() failed';
			wp_send_json_error( $result, 500 );
		}

		$html .= '<p>Test email sent to: <em>' . $bounce_test_email . '</em></p>';

		$result['success']                = true;
		$result['data']['notice']         = 'info';
		$result['data']['bounce_test_id'] = $bounce_test_id;
		$result['data']['html']           = $html;

		wp_send_json( $result );
	}

	public function check_ses_bounce_test_result() {

		// Get user roles

		// Get order meta

		// Get Newsletter subscriber status

		// Delete test data button (not automatically, so admins can view the order, user...)
	}

}
