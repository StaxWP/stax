<?php
/**
 * Title: Latest posts grid
 * Slug: stax/latest-posts
 * Categories: stax, query, posts
 * Keywords: blog, posts, news, grid
 * Block Types: core/query
 * Viewport Width: 1280
 * Description: The three latest posts with image, title, date and excerpt.
 *
 * @package stax
 */

?>
<!-- wp:group {"align":"wide","layout":{"type":"constrained","contentSize":"1140px"}} -->
<div class="wp-block-group alignwide">
<!-- wp:heading -->
<h2 class="wp-block-heading"><?php echo esc_html_x( 'From the blog', 'Pattern: latest posts heading', 'stax' ); ?></h2>
<!-- /wp:heading -->
<!-- wp:query {"queryId":1,"query":{"perPage":3,"pages":0,"offset":0,"postType":"post","order":"desc","orderBy":"date","inherit":false}} -->
<div class="wp-block-query">
<!-- wp:post-template {"layout":{"type":"grid","columnCount":3}} -->
<!-- wp:post-featured-image {"isLink":true,"aspectRatio":"16/9"} /-->
<!-- wp:post-title {"isLink":true,"fontSize":"large"} /-->
<!-- wp:post-date {"fontSize":"small","textColor":"meta"} /-->
<!-- wp:post-excerpt {"excerptLength":20} /-->
<!-- /wp:post-template -->
<!-- wp:query-no-results -->
<!-- wp:paragraph -->
<p><?php echo esc_html_x( 'No posts yet.', 'Pattern: latest posts empty', 'stax' ); ?></p>
<!-- /wp:paragraph -->
<!-- /wp:query-no-results -->
</div>
<!-- /wp:query -->
</div>
<!-- /wp:group -->
