<?php if ( ! $quote && get_the_title() ) : ?>
	<div class="heading-title">
		<div class="heading-title-content">
			<h2 class="heading-title-text">
				<a href="<?php the_permalink() ?>" class="heading-title-link">
					<?php the_title() ?>
				</a>
			</h2>
		</div>
	</div>
<?php endif; ?>
