<?php
/**
 * Title: Three features
 * Slug: stax/features
 * Categories: stax, text, columns
 * Keywords: features, services, columns
 * Viewport Width: 1280
 * Description: Three columns with a heading and a short text each.
 *
 * @package stax
 */

$stax_features = [
	[ esc_html_x( 'Fast by default', 'Pattern: feature title', 'stax' ), esc_html_x( 'Lean pages load quickly on every device, with nothing you did not ask for.', 'Pattern: feature text', 'stax' ) ],
	[ esc_html_x( 'Works with your tools', 'Pattern: feature title', 'stax' ), esc_html_x( 'Use the block editor or your favourite page builder, shop and community plugins.', 'Pattern: feature text', 'stax' ) ],
	[ esc_html_x( 'Made to be changed', 'Pattern: feature title', 'stax' ), esc_html_x( 'Colors, fonts, header and footer are all set in the Customizer.', 'Pattern: feature text', 'stax' ) ],
];
?>
<!-- wp:group {"align":"wide","style":{"spacing":{"padding":{"top":"var:preset|spacing|60","bottom":"var:preset|spacing|60"}}},"layout":{"type":"constrained","contentSize":"1140px"}} -->
<div class="wp-block-group alignwide" style="padding-top:var(--wp--preset--spacing--60);padding-bottom:var(--wp--preset--spacing--60)">
<!-- wp:heading {"textAlign":"center"} -->
<h2 class="wp-block-heading has-text-align-center"><?php echo esc_html_x( 'Why choose us', 'Pattern: features heading', 'stax' ); ?></h2>
<!-- /wp:heading -->
<!-- wp:columns {"style":{"spacing":{"blockGap":{"left":"var:preset|spacing|50"}}}} -->
<div class="wp-block-columns">
<?php foreach ( $stax_features as $stax_feature ) : ?>
<!-- wp:column -->
<div class="wp-block-column">
<!-- wp:group {"className":"is-style-stax-card","layout":{"type":"constrained"}} -->
<div class="wp-block-group is-style-stax-card">
<!-- wp:heading {"level":3,"fontSize":"large"} -->
<h3 class="wp-block-heading has-large-font-size"><?php echo $stax_feature[0]; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Escaped above. ?></h3>
<!-- /wp:heading -->
<!-- wp:paragraph -->
<p><?php echo $stax_feature[1]; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Escaped above. ?></p>
<!-- /wp:paragraph -->
</div>
<!-- /wp:group -->
</div>
<!-- /wp:column -->
<?php endforeach; ?>
</div>
<!-- /wp:columns -->
</div>
<!-- /wp:group -->
