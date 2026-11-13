<?php
/**
 * Title: Community introduction
 * Slug: stax/community
 * Categories: stax, call-to-action
 * Keywords: community, members, buddypress, join, forum
 * Viewport Width: 1280
 * Description: Invite visitors to join a community: three reasons and a join button. Works with BuddyPress, BuddyBoss and bbPress sites.
 *
 * @package stax
 */

$stax_points = [
	esc_html_x( 'Meet people who share your interests', 'Pattern: community point', 'stax' ),
	esc_html_x( 'Ask questions and get answers in the forums', 'Pattern: community point', 'stax' ),
	esc_html_x( 'Follow updates from the members you like', 'Pattern: community point', 'stax' ),
];
?>
<!-- wp:group {"align":"wide","style":{"spacing":{"padding":{"top":"var:preset|spacing|60","bottom":"var:preset|spacing|60"}}},"layout":{"type":"constrained","contentSize":"1140px"}} -->
<div class="wp-block-group alignwide" style="padding-top:var(--wp--preset--spacing--60);padding-bottom:var(--wp--preset--spacing--60)">
<!-- wp:columns {"verticalAlignment":"center"} -->
<div class="wp-block-columns are-vertically-aligned-center">
<!-- wp:column {"verticalAlignment":"center"} -->
<div class="wp-block-column is-vertically-aligned-center">
<!-- wp:heading -->
<h2 class="wp-block-heading"><?php echo esc_html_x( 'Join the community', 'Pattern: community heading', 'stax' ); ?></h2>
<!-- /wp:heading -->
<!-- wp:list {"className":"is-style-stax-checklist"} -->
<ul class="wp-block-list is-style-stax-checklist">
<?php foreach ( $stax_points as $stax_point ) : ?>
<!-- wp:list-item -->
<li><?php echo $stax_point; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Escaped above. ?></li>
<!-- /wp:list-item -->
<?php endforeach; ?>
</ul>
<!-- /wp:list -->
<!-- wp:buttons -->
<div class="wp-block-buttons">
<!-- wp:button {"backgroundColor":"primary","textColor":"dark","className":"is-style-stax-pill"} -->
<div class="wp-block-button is-style-stax-pill"><a class="wp-block-button__link has-dark-color has-primary-background-color has-text-color has-background wp-element-button" href="<?php echo esc_url( wp_registration_url() ); ?>"><?php echo esc_html_x( 'Create your account', 'Pattern: community button', 'stax' ); ?></a></div>
<!-- /wp:button -->
</div>
<!-- /wp:buttons -->
</div>
<!-- /wp:column -->
<!-- wp:column {"verticalAlignment":"center"} -->
<div class="wp-block-column is-vertically-aligned-center">
<!-- wp:group {"className":"is-style-stax-card","layout":{"type":"constrained"}} -->
<div class="wp-block-group is-style-stax-card">
<!-- wp:paragraph {"fontSize":"large"} -->
<p class="has-large-font-size"><?php echo esc_html_x( '"I found my people here within a week."', 'Pattern: community quote', 'stax' ); ?></p>
<!-- /wp:paragraph -->
<!-- wp:paragraph {"textColor":"meta","fontSize":"small"} -->
<p class="has-meta-color has-text-color has-small-font-size"><?php echo esc_html_x( 'A happy member', 'Pattern: community quote author', 'stax' ); ?></p>
<!-- /wp:paragraph -->
</div>
<!-- /wp:group -->
</div>
<!-- /wp:column -->
</div>
<!-- /wp:columns -->
</div>
<!-- /wp:group -->
