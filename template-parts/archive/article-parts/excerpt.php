<?php

use function Stax\stax;

?>

<div class="post-excerpt">
	<?php if ( $quote ) : ?>
		<a href="<?php the_permalink(); ?>" class="post-quote-link"></a>
		<?php
		stax()->get_template_part(
			'template-parts/archive/article-format/quote',
			null,
			compact( 'quote' )
		);
		?>
	<?php endif; ?>

	<?php if ( get_post_format() === 'audio' ) : ?>
		<?php stax()->get_template_part( 'template-parts/content/panel/audio' ); ?>
	<?php endif; ?>

	<?php if ( ! $quote ) : ?>
		<div class="screen-reader-link">
			<?php echo stax()->get_excerpt(); ?>
		</div>
	<?php endif; ?>
</div>
