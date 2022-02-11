<?php

namespace Stax\Builder;

use function Stax\stax;
use Stax\Customizer\Config;
use Stax\Builder\Core\Builder\Header as HeaderBuilder;

$classes = apply_filters( 'hfg_header_wrapper_class', '' );
?>

<header class="header" role="banner">
	<a class="skip-link screen-reader-text" href="#primary"><?php esc_html_e( 'Skip to content', 'stax' ); ?></a>

	<?php if ( is_single() && stax()->get_option( Config::OPTION_SINGLE_POST_TOP_READING_BAR ) ) : ?>
		<div class="svq-progress-bar">
			<progress value="0" id="progressBar"></progress>
		</div>
	<?php endif; ?>

	<div id="header-grid"  class="<?php echo esc_attr( get_builder( HeaderBuilder::BUILDER_NAME )->get_property( 'panel' ) ) . esc_attr( $classes ); ?> site-header">
		<?php render_builder( HeaderBuilder::BUILDER_NAME ); ?>
	</div>
</header>
