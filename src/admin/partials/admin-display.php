<?php
/**
 * Provide a admin area view for the plugin
 *
 * This file is used to markup the admin-facing aspects of the plugin.
 *
 * @link       https://BrianHenry.ie
 * @since      1.0.0
 *
 * @package   EA_WP_AWS_SES_Bounce_Handler
 * @subpackage EA_WP_AWS_SES_Bounce_Handler/admin/partials
 */

use EA_WP_AWS_SES_Bounce_Handler\includes\Settings_Interface;

/** @var Settings_Interface $settings */
$settings = $this->settings;

?>

<div class="wrap ea-wp-aws-ses-bounce-handler">

	<h1>AWS SES Bounce Handler</h1>

	<h3>Marks user accounts and WooCommerce orders for bounced emails; unsubscribes users who mark email as spam.</h3>

	<p>Follow Amazon's <a target="_blank" href="https://docs.aws.amazon.com/ses/latest/DeveloperGuide/configure-sns-notifications.html">Configuring Amazon SNS Notifications for Amazon SES</a> document to set up.</p>

	<p>Use endpoint: <input class="bounce-handler-endpoint" value="<?php echo esc_url( get_rest_url( null, 'ea/v1/aws-ses/' ) ); ?>"></p>

	<p>Confirmed ARNs:</p>

	<?php

	if ( empty( $settings->get_confirmed_arns() ) ) {
		echo '<div class="notice inline notice-error"><p>No SNS ARN subscriptions have been added. Visit <a target="_blank" href="https://console.aws.amazon.com/sns/v3/home?#/topics">AWS SNS Console</a>.</p></div>';
	}

	foreach ( $settings->get_confirmed_arns() as $arn ) {

		echo '<div class="notice inline notice-success"><p><a target="_blank" href="https://console.aws.amazon.com/sns/v3/home?#/topic/' . $arn . '">' . $arn . '</a></p></div>';

	}

	?>

	<h2>Testing</h2>

	<p>This plugin can test the configuration by creating a test WordPress user
	<?php
	if ( class_exists( WooCommerce::class ) ) {
		echo ' and WooCommerce order';
	}
	if ( class_exists( TNP::class ) ) {
		echo ' and Newsletter subscriber';
	}
	?>
	 then sending an email to <em>bounce@simulator.amazonses.com</em>.</p>

	<?php echo $this->get_wp_mail_info(); ?>

	<form id="run-ses-bounce-test-form" action="">
		<input type="hidden" name="action" value="run_ses_bounce_test" />
		<?php wp_nonce_field( 'run-ses-bounce-test-form' ); ?>
		<input class="button" id="run-ses-bounce-test-button" type="submit" value="Run Test" />
		<img class="bounce-test-running-spinner" alt="Is bounce test running spinner" src="<?php echo esc_url( admin_url( '/images/spinner-2x.gif' ) ); ?>" />
	</form>

	<div id="run-ses-bounce-test-response"></div>


	<h2>Integrations:</h2>

	<ul >
		<li>WordPress: adds <a href="<?php echo esc_url( admin_url( 'users.php?role=bounced_email' ) ); ?>">Bounced Email</a> role to users</li>
		<li>WooCommerce: adds a note and notice on orders whose email address bounced</li>
		<li>
			<?php
			if ( class_exists( TNP::class ) ) {
				echo '<a href="' . esc_url( admin_url( 'admin.php?page=newsletter_users_index' ) ) . '">The Newsletter plugin</a>';
			} else {
				echo '<a target="_blank" href="https://wordpress.org/plugins/newsletter">The Newsletter plugin</a>';
			}
			?>
			: Marks users as bounced and unsubscribes complaints</li>
		<li>Fires actions <code>handle_ses_bounce</code> and <code>handle_ses_complaint</code></li>
	</ul>

	<p><a target="_blank" href="https://github.com/EnhancedAthlete/ea-wp-aws-ses-bounce-handler">View code on GitHub</a> &#x2022; <a target="_blank" href="https://BrianHenry.ie">Plugin by BrianHenryIE</a> &#x2022; <a target="_blank" href="https://EnhancedAthlete.com">Plugin for Enhanced Athlete</a></p>

</div>
