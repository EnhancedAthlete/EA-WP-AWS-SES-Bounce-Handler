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

?>

<div class="wrap ea-wp-aws-ses-bounce-handler">

	<h1>AWS SES Bounce Handler</h1>

	<h3>Marks user accounts and WooCommerce orders for bounced emails; unsubscribes users who mark email as spam.</h3>

	<p>Follow Amazon's <a href="https://docs.aws.amazon.com/ses/latest/DeveloperGuide/configure-sns-notifications.html">Configuring Amazon SNS Notifications for Amazon SES</a> document to set up.</p>

	<p>Use endpoint: <em><?php echo esc_url( get_rest_url( null, 'ea/v1/aws-ses/' ) ); ?></em></p>

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

	<form method="POST" action="options.php">
		<?php
		settings_fields( 'ea-wp-aws-ses-bounce-handler' );
		do_settings_sections( 'ea-wp-aws-ses-bounce-handler' );
		submit_button();
		?>
	</form>

	<p><a target="_blank" href="https://github.com/EnhancedAthlete/ea-wp-aws-ses-bounce-handler">View code on GitHub</a> &#x2022; <a target="_blank" href="https://BrianHenry.ie">Plugin by BrianHenryIE</a> &#x2022; <a target="_blank" href="https://EnhancedAthlete.com">Plugin for Enhanced Athlete</a></p>

</div>
