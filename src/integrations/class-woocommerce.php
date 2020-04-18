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

use EA_WP_AWS_SES_Bounce_Handler\WPPB\WPPB_Object;
use WC_Admin_Notices;
use WC_Order;

/**
 * Hook onto `handle_ses_bounce` to add order note and meta key, hook onto `admin_notices` to display notice on orders.
 *
 * Class WooCommerce
 *
 * @package EA_WP_AWS_SES_Bounce_Handler\integrations
 */
class WooCommerce extends WPPB_Object {

	const BOUNCED_META_KEY = 'ea_wp_aws_ses_bounce_hander_bounced';

	/**
	 * Add a note to orders whose email addresses are invalid.
	 *
	 * @hooked handle_ses_bounce
	 *
	 * @param string $email_address     The email address that has bounced.
	 * @param object $bounced_recipient Parent object with emailAddress, status, action, diagnosticCode.
	 * @param object $message           Parent object of complete notification.
	 */
	public function mark_order_email_bounced( $email_address, $bounced_recipient, $message ) {

		if ( ! class_exists( \WooCommerce::class ) ) {
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

	/**
	 * Display an admin notice if this order's customer email has bounced.
	 *
	 * @hooked admin_notices
	 *
	 * @see https://stackoverflow.com/questions/56971501/how-to-add-admin-notices-on-woocommerce-order-edit-page
	 */
	public function display_bounce_notification() {

		if ( ! class_exists( \WooCommerce::class ) ) {
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
