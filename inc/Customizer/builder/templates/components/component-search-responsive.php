<?php

namespace Stax\Builder;

use Stax\Builder\Core\Components\SearchResponsive;

$component_styles_array = [];
$open                   = component_setting( SearchResponsive::OPEN_TYPE );

if ( current_row() === 'sidebar' ) {
	$open = 'floating';
}

$component_styles = '';
if ( ! empty( $component_styles_array ) ) {
	$component_styles = ' style="';
	foreach ( $component_styles_array as $key => $value ) {
		$component_styles .= sprintf( '%1$s: %2$s', $key, $value );
	}
	$component_styles .= '" ';
}

?>

<div class="stx-search-icon-component" <?php echo wp_kses_post( $component_styles ); ?>>
	<div class="menu-item-nav-search <?php echo esc_attr( $open ); ?>">
		<a aria-label="<?php esc_attr_e( 'Search', 'stax' ); ?>" href="#" role="button" class="stx-icon stx-search">
			<svg width="15" height="15" viewBox="0 0 1792 1792" xmlns="http://www.w3.org/2000/svg"><path d="M1216 832q0-185-131.5-316.5t-316.5-131.5-316.5 131.5-131.5 316.5 131.5 316.5 316.5 131.5 316.5-131.5 131.5-316.5zm512 832q0 52-38 90t-90 38q-54 0-90-38l-343-342q-179 124-399 124-143 0-273.5-55.5t-225-150-150-225-55.5-273.5 55.5-273.5 150-225 225-150 273.5-55.5 273.5 55.5 225 150 150 225 55.5 273.5q0 220-124 399l343 343q37 37 37 90z"></path></svg>
		</a>
		<div class="stx-nav-search" aria-label="search">
			<div class="form-wrap <?php echo $open === 'canvas' ? 'container responsive-search' : ''; ?>">
				<?php get_search_form(); ?>
			</div>
			<?php if ( $open !== 'minimal' ) { ?>
				<div class="close-container <?php echo $open === 'canvas' ? 'container responsive-search' : ''; ?>">
					<button  class="close-responsive-search">
						<svg width="50" height="50" viewBox="0 0 20 20" fill="#555555"><path d="M14.95 6.46L11.41 10l3.54 3.54l-1.41 1.41L10 11.42l-3.53 3.53l-1.42-1.42L8.58 10L5.05 6.47l1.42-1.42L10 8.58l3.54-3.53z"/><rect/></svg>
					</button>
				</div>
			<?php } ?>
		</div>
	</div>
</div>
