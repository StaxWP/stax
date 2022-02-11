<div class="sqp-container-small sqp-mx-auto sqp-px-4">
	<div class="sqp-flex sqp-flex-wrap sqp-overflow-hidden">
		<div class="sqp-flex-grow sqp-w-full lg:sqp-w-2/3 sqp-overflow-hidden">
			<h1 class="screen-reader-text">
				<?php esc_html_e( 'Stax', 'stax' ); ?>
			</h1>
			<?php do_action( 'stax_' . $current_slug . '_page_content_before' ); ?>

			<?php do_action( 'stax_' . $current_slug . '_page_content' ); ?>

			<?php do_action( 'stax_' . $current_slug . '_page_content_after' ); ?>
		</div>
		<div class="sqp-flex-shrink sqp-max-w-full lg:sqp-max-w-1/3 sqp-overflow-hidden">
			<?php do_action( 'stax_' . $current_slug . '_page_right_sidebar_before' ); ?>

			<?php do_action( 'stax_' . $current_slug . '_page_right_sidebar_content' ); ?>

			<?php do_action( 'stax_' . $current_slug . '_page_right_sidebar_after' ); ?>
		</div>
	</div>
</div>
