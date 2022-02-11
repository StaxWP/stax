<?php

namespace Stax\Builder;

use Stax\Builder\Core\Components\CustomHtml;

$content           = component_setting( CustomHtml::CONTENT_ID );
$content           = apply_filters( 'stax_translate_single_string', $content, CustomHtml::CONTENT_ID );
$content           = apply_filters( 'stax_top_bar_content', $content );
$allowed_post_tags = wp_kses_allowed_html( 'header_footer_grid' );

?>
<div class="stx-html-content"> <?php //phpcs:ignore WordPressVIPMinimum.Security.Vuejs.RawHTMLDirectiveFound ?>
	<?php echo wp_kses( balanceTags( parse_dynamic_tags( $content ), true ), $allowed_post_tags ); ?>
</div>
