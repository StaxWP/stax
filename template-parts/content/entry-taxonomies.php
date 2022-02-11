<?php
/**
 * Template part for displaying a post's taxonomy terms
 *
 * @package stax
 */

namespace Stax;

?>

<?php if ( has_tag() ) : ?>
	<div class="svq-article-tags">
		<span class="tags-category">
		<?php the_tags( '<span class="tag-links">', '</span> <span class="tag-links">', '</span>' ); ?>
		</span>
	</div>
<?php endif; ?>
