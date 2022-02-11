<?php

namespace Stax\Builder;

use Stax\Builder\Core\Builder\Footer as FooterBuilder;

?>
<footer class="<?php echo esc_attr( apply_filters( 'stax_footer_wrap_classes', 'site-footer' ) ); ?>" id="site-footer" next-page-hide>
	<div class="<?php echo esc_attr( get_builder( FooterBuilder::BUILDER_NAME )->get_property( 'panel' ) ); ?>">
		<?php render_builder( FooterBuilder::BUILDER_NAME ); ?>
	</div>
</footer>
