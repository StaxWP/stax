<?php
/**
 * Title: Call to action
 * Slug: stax/call-to-action
 * Categories: stax, call-to-action
 * Keywords: cta, newsletter, signup
 * Viewport Width: 1280
 * Description: A dark banner with a heading, a sentence and a button.
 *
 * @package stax
 */

?>
<!-- wp:group {"align":"full","style":{"spacing":{"padding":{"top":"var:preset|spacing|60","bottom":"var:preset|spacing|60","left":"var:preset|spacing|30","right":"var:preset|spacing|30"}},"elements":{"heading":{"color":{"text":"var:preset|color|background"}}}},"backgroundColor":"dark","textColor":"background","layout":{"type":"constrained"}} -->
<div class="wp-block-group alignfull has-background-color has-dark-background-color has-text-color has-background" style="padding-top:var(--wp--preset--spacing--60);padding-right:var(--wp--preset--spacing--30);padding-bottom:var(--wp--preset--spacing--60);padding-left:var(--wp--preset--spacing--30)">
<!-- wp:heading {"textAlign":"center"} -->
<h2 class="wp-block-heading has-text-align-center"><?php echo esc_html_x( 'Ready to start?', 'Pattern: call to action heading', 'stax' ); ?></h2>
<!-- /wp:heading -->
<!-- wp:paragraph {"align":"center"} -->
<p class="has-text-align-center"><?php echo esc_html_x( 'Tell visitors what happens next and why it is worth it.', 'Pattern: call to action text', 'stax' ); ?></p>
<!-- /wp:paragraph -->
<!-- wp:buttons {"layout":{"type":"flex","justifyContent":"center"}} -->
<div class="wp-block-buttons">
<!-- wp:button {"backgroundColor":"primary","textColor":"dark","className":"is-style-stax-pill"} -->
<div class="wp-block-button is-style-stax-pill"><a class="wp-block-button__link has-dark-color has-primary-background-color has-text-color has-background wp-element-button"><?php echo esc_html_x( 'Contact us', 'Pattern: call to action button', 'stax' ); ?></a></div>
<!-- /wp:button -->
</div>
<!-- /wp:buttons -->
</div>
<!-- /wp:group -->
