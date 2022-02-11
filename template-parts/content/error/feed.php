<?php
/**
 * Template part for displaying no feed error
 *
 * @package stax
 */

namespace Stax;

$user = fup_get_user( get_current_user_id() );

?>

<div class="fup-feed-error alert alert-warning">
	<h6 class="alert-heading"><?php esc_html_e( 'There is no feed available for you right now.', 'stax' ); ?></h6>
	<div>
		<?php
		wp_kses_post(
			sprintf(
				__( 'Try following authors or <a href="%s">customize your interest categories</a> from your profile', 'stax' ),
				esc_url( $user->pages['interests']['url'] )
			)
		);
		?>
	</div>
</div>
