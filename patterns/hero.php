<?php
/**
 * Title: Hero with buttons
 * Slug: stax/hero
 * Categories: stax, banner, featured
 * Keywords: hero, header, intro, cta
 * Viewport Width: 1280
 * Description: A full width introduction with a heading, text and two buttons.
 *
 * @package stax
 */

?>
<!-- wp:group {"align":"full","style":{"spacing":{"padding":{"top":"var:preset|spacing|70","bottom":"var:preset|spacing|70","left":"var:preset|spacing|30","right":"var:preset|spacing|30"}}},"backgroundColor":"light","layout":{"type":"constrained"}} -->
<div class="wp-block-group alignfull has-light-background-color has-background" style="padding-top:var(--wp--preset--spacing--70);padding-right:var(--wp--preset--spacing--30);padding-bottom:var(--wp--preset--spacing--70);padding-left:var(--wp--preset--spacing--30)">
<!-- wp:heading {"level":6,"textAlign":"center","className":"is-style-stax-eyebrow"} -->
<h6 class="wp-block-heading has-text-align-center is-style-stax-eyebrow"><?php echo esc_html_x( 'Welcome', 'Pattern: hero eyebrow', 'stax' ); ?></h6>
<!-- /wp:heading -->
<!-- wp:heading {"textAlign":"center","level":1,"fontSize":"xx-large"} -->
<h1 class="wp-block-heading has-text-align-center has-xx-large-font-size"><?php echo esc_html_x( 'Build something people come back to', 'Pattern: hero heading', 'stax' ); ?></h1>
<!-- /wp:heading -->
<!-- wp:paragraph {"align":"center","fontSize":"medium"} -->
<p class="has-text-align-center has-medium-font-size"><?php echo esc_html_x( 'A short sentence about what you offer and who it is for. Keep it clear and specific.', 'Pattern: hero text', 'stax' ); ?></p>
<!-- /wp:paragraph -->
<!-- wp:buttons {"layout":{"type":"flex","justifyContent":"center"}} -->
<div class="wp-block-buttons">
<!-- wp:button {"backgroundColor":"primary","textColor":"dark","className":"is-style-stax-pill"} -->
<div class="wp-block-button is-style-stax-pill"><a class="wp-block-button__link has-dark-color has-primary-background-color has-text-color has-background wp-element-button"><?php echo esc_html_x( 'Get started', 'Pattern: hero button', 'stax' ); ?></a></div>
<!-- /wp:button -->
<!-- wp:button {"className":"is-style-outline"} -->
<div class="wp-block-button is-style-outline"><a class="wp-block-button__link wp-element-button"><?php echo esc_html_x( 'Learn more', 'Pattern: hero button', 'stax' ); ?></a></div>
<!-- /wp:button -->
</div>
<!-- /wp:buttons -->
</div>
<!-- /wp:group -->
