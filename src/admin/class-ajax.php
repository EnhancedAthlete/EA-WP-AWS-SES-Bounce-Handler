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

		$data = array();

		// Verify nonce.
		if ( ! check_ajax_referer( 'run-ses-bounce-test-form', false, false ) ) {

			$data['message'] = 'Referrer/nonce failure';

			wp_send_json_error( $data, 400 );
		}

		// TODO Verify settings: ARN, wp_mail

		$wp_options_data = array();

		$bounce_test_id    = time();
		$bounce_test_email = "bounce+{$bounce_test_id}@simulator.amazonses.com";

		$wp_options_data['bounce_test_id']    = $bounce_test_id;
		$wp_options_data['bounce_test_email'] = $bounce_test_email;

		$html  = '<p>Test started at time: <em>' . $bounce_test_id . '</em></p>';
		$html .= '<p>Using email address: <em>' . $bounce_test_email . '</em></p>';

		// Created user id 123
		$user_id = wp_create_user( $bounce_test_email, wp_generate_password( 31 ), $bounce_test_email );

		$user       = get_user_by( 'id', $user_id );
		$user_roles = $user->roles;

		$user_url = admin_url( 'user-edit.php?user_id=' . $user_id );

		$wp_options_data['wp_user_id']    = $user_id;
		$wp_options_data['wp_user_roles'] = $user_roles;

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

			$wp_options_data['wc_order']                    = $order->get_id();
			$wp_options_data['wc_order_bounced_meta_value'] = $order_bounced_meta_value;

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

			$wp_options_data['tnp_user']        = $tnp_user->id;
			$wp_options_data['tnp_user_status'] = $tnp_user_status;

			$html .= '<p>Newsletter <a href="' . $tnp_user_url . '">subscriber ' . $tnp_user->id . '</a> created with status ' . $tnp_user_status . '</p>';

		}

		// Record all this so it can be deleted.
		// key with $bounce_test_id

		// Send email to bounce  + time @sim
		$to      = $bounce_test_email;
		$subject = 'EA WP AWS SES Bounce Handler Test Email';
		$message = $html;

		$mail_send = wp_mail( $to, $subject, $message );

		if ( ! $mail_send ) {

			$data['message'] = 'wp_mail() failed';
			wp_send_json_error( $data, 500 );
		}

		$html .= '<p>Test email sent to: <em>' . $bounce_test_email . '</em></p>';


		$data['notice']       = 'info';
		$data['bounceTestId'] = $bounce_test_id;
		$data['html']         = $html;

		update_option( 'aws_ses_bounce_test_' . $bounce_test_id, $wp_options_data );

		$data['newNonce'] = wp_create_nonce('run-ses-bounce-test-form');

		wp_send_json( $data );
	}

	/**
	 *
	 */
	public function fetch_test_results() {

		$data         = array();
		
		

		$html = '';

		// Verify nonce.

		if( isset($_POST['_wpnonce']) ) {

			$nonce = isset($_POST['_wpnonce'] );
			$result = wp_verify_nonce( $nonce, 'run-ses-bounce-test-form');

			if( false == $result ) {

				$data['message'] = 'Referrer/nonce failure';

				wp_send_json_error( $data, 400 );
			}
		}


		if ( ! isset( $_POST['bounce_test_id'] ) ) {

			$data['message'] = 'bounce_test_id not set.';

			wp_send_json_error( $data, 400 );
		}

		$bounce_test_id = intval( $_POST['bounce_test_id'] );

		$bounce_test_data = get_option( 'aws_ses_bounce_test_' . $bounce_test_id, array() );

		if ( empty( $bounce_test_data )
			|| ! isset( $bounce_test_data['wp_user_id'] )
			|| ! isset( $bounce_test_data['wp_user_roles'] ) ) {

			$data['message'] = 'Test data did not save correctly.';

			wp_send_json_error( $data, 500 );
		}

		$data['testComplete'] = true; // Unless otherwise stated.
		$data['testSuccess'] = true;

		// Get user roles
		$user       = get_user_by( 'id', intval( $bounce_test_data['wp_user_id'] ) );
		$user_roles = $user->roles;

		$previous_user_roles = (array) $bounce_test_data['wp_user_roles'];

		$new_roles = array_diff( $user_roles, $previous_user_roles );

		if ( empty( $new_roles ) ) {
			$data['testComplete'] = false;
			$data['message']      = 'No new roles added to user';
		}

		$user_url = admin_url( 'user-edit.php?user_id=' . $user->ID );

		$html .= '<p>WordPress <a href="' . $user_url . '">user ' . $user->ID . '</a> found with new roles: <em>' . implode( ', ', $new_roles ) . '</em></p>';


		// Did we run a WooCommerce test?
		if ( isset( $bounce_test_data['wc_order'] ) && class_exists( \WooCommerce::class ) ) {

			$order = wc_get_order( intval( $bounce_test_data['wc_order'] ) );

			if ( $order instanceof \WC_Order ) {

				$order_bounced_meta_value = $order->get_meta( WooCommerce::BOUNCED_META_KEY );

				if ( ! empty( $order_bounced_meta_value ) ) {

					$order_url = admin_url( 'post.php?post=' . $order->get_id() . '&action=edit' );

					$html .= '<p>WooCommerce <a href="' . $order_url . '">order ' . $order->get_id() . '</a> found with meta key <em>' . WooCommerce::BOUNCED_META_KEY . '</em> value: <em>' . $order_bounced_meta_value . '</em></p>';

				}
				// } else {
				// Something weird going on.
			}
		}

		// Get Newsletter subscriber status
		if ( isset( $bounce_test_data['tnp_user'] ) && class_exists( TNP::class ) ) {

			$newsletter = \Newsletter::instance();

			$tnp_user = $newsletter->get_user( $bounce_test_data['tnp_user'] );

			$tnp_user_status = $tnp_user->status;

			$tnp_user_url = admin_url( 'admin.php?page=newsletter_users_edit&id=' . $tnp_user->id );

			if ( $tnp_user_status !== $bounce_test_data['tnp_user_status'] ) {
				$html .= '<p>Newsletter <a href="' . $tnp_user_url . '">subscriber ' . $tnp_user->id . '</a> found with new status ' . $tnp_user_status . '</p>';
			} else {
				$html .= '<p>Newsletter user status not changed</p>';
			}
		}

		$data['html'] = $html;


		if( time() - intval( $bounce_test_id ) > MINUTE_IN_SECONDS ) {
			$data['testSuccess'] = false;
			$data['testComplete'] = true;
		}

		$data['newNonce'] = wp_create_nonce('run-ses-bounce-test-form');

		wp_send_json( $data );
	}

	public function delete_test_data() {

	}
}
