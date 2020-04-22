<?php
/**
 * Functionality for WooCommerce to highlight in orders when the user's email address is incorrect.
 *
 * @see https://wordpress.org/plugins/woocommerce
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
use WC_Order;

/**
 * Hook onto `handle_ses_bounce` to add order note and meta key, hook onto `admin_notices` to display notice on orders.
 *
 * Class WooCommerce
 *
 * @package EA_WP_AWS_SES_Bounce_Handler\integrations
 */
class WooCommerce extends WPPB_Object implements SES_Bounce_Handler_Integration_Interface {

	const BOUNCED_META_KEY = 'ea_wp_aws_ses_bounce_hander_bounced';


	public function get_description(): string {
		return 'Adds a note and notice on orders whose email address bounced';
	}

	/**
	 * Add the hook for displaying the order notice.
	 */
	public function init(): void {
		add_action( 'admin_notices', array( $this, 'display_bounce_notification' ) );
	}

	/**
	 * Check is WooCommerce installed.
	 *
	 * @return bool
	 */
	public function is_enabled(): bool {
		return class_exists( \WooCommerce::class );
	}

	/**
	 * Add a note to orders whose email addresses are invalid.
	 *
	 * @hooked handle_ses_bounce
	 *
	 * @param string $email_address     The email address that has bounced.
	 * @param object $bounced_recipient Parent object with emailAddress, status, action, diagnosticCode.
	 * @param object $message           Parent object of complete notification.
	 */
	public function handle_ses_bounce( $email_address, $bounced_recipient, $message ): void {

		if ( ! $this->is_enabled() ) {
			return;
		}

		/**
		 * Find any orders made with this email address.
		 *
		 * @var WC_Order[] $customer_orders
		 */
		$customer_orders = wc_get_orders( array( 'customer' => $email_address ) );

		foreach ( $customer_orders as $order ) {

			$order->add_order_note( 'Email address bounced' );

			$order->add_meta_data( self::BOUNCED_META_KEY, $email_address, true );

			$order->save();
		}

	}

	public function handle_ses_complaint( $email_address, $complained_recipient, $message ): void {
		// Do nothing.
	}

	/**
	 * Create an order with the bounce simulator email address.
	 *
	 * @param Bounce_Handler_Test $test
	 *
	 * @return array|void
	 */
	public function setup_test( $test ): ?array {

		if ( ! $this->is_enabled() ) {
			return null;
		}

		// Created dummy order
		$order   = wc_create_order();
		$address = array(
			'email' => $test->get_email(),
		);
		$order->set_address( $address, 'billing' );
		$order_bounced_meta_value = $order->get_meta( self::BOUNCED_META_KEY );
		$order_bounced_meta_value = empty( $order_bounced_meta_value ) ? 'empty' : $order_bounced_meta_value;

		$order_url = admin_url( 'post.php?post=' . $order->get_id() . '&action=edit' );

		$data['wc_order']                    = $order->get_id();
		$data['wc_order_bounced_meta_value'] = $order_bounced_meta_value;

		$html = '<p>WooCommerce <a href="' . $order_url . '">order ' . $order->get_id() . '</a> created with meta key <em>' . self::BOUNCED_META_KEY . '</em> value: <em>' . $order_bounced_meta_value . '</em></p>';

		return array(
			'data' => $data,
			'html' => $html,
		);
	}

	/**
	 * Verify the order has metadata added.
	 *
	 * @param array $test_data
	 *
	 * @return array
	 */
	public function verify_test( $test_data ): ?array {

		$order = wc_get_order( intval( $test_data['wc_order'] ) );

		$success = false;

		if ( $order instanceof \WC_Order ) {

			$order_bounced_meta_value = $order->get_meta( self::BOUNCED_META_KEY );

			if ( ! empty( $order_bounced_meta_value ) ) {

				$success = true;

				$order_url = admin_url( 'post.php?post=' . $order->get_id() . '&action=edit' );

				$html = '<p>WooCommerce <a href="' . $order_url . '">order ' . $order->get_id() . '</a> found with meta key <em>' . self::BOUNCED_META_KEY . '</em> value: <em>' . $order_bounced_meta_value . '</em></p>';

			}
			// } else {
			// Something weird going on.
		}

		return array(
			'success' => $success,
			'html'    => $html,
		);

	}

	/**
	 * Delete the order created for the test.
	 *
	 * @param array $test_data
	 */
	public function delete_test_data( $test_data ): bool {

		if ( isset( $bounce_test_datum['wc_order_id'] ) && class_exists( \WooCommerce::class ) ) {
			wp_delete_post( intval( $bounce_test_datum['wc_order_id'] ) );
		}

		return true;
	}


	/**
	 * Display an admin notice if this order's customer email has bounced.
	 *
	 * @hooked admin_notices
	 *
	 * @see https://stackoverflow.com/questions/56971501/how-to-add-admin-notices-on-woocommerce-order-edit-page
	 */
	public function display_bounce_notification() {

		if ( ! $this->is_enabled() ) {
			return;
		}

		$id = get_the_ID();

		if ( false === $id ) {
			return;
		}

		$order = wc_get_order( $id );

		if ( ! $order instanceof WC_Order ) {
			return;
		}

		$bounced_email = $order->get_meta( self::BOUNCED_META_KEY );

		if ( ! empty( $bounced_email ) ) {

			$notice = sprintf(
				"<div class='notice notice-warning'><p>%s <em>%s</em> %s.</p></div>",
				__( "The customer's email address", 'ea-wp-aws-ses-bounce-handler' ),
				$bounced_email,
				__( 'is invalid', 'ea-wp-aws-ses-bounce-handler' )
			);

			$allowed_html = array(
				'div' => array(
					'class' => array(),
				),
				'p'   => array(),
				'em'  => array(),
			);

			echo wp_kses( $notice, $allowed_html );
		}
	}

}
