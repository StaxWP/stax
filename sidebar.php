<?php
/**
 * The sidebar containing the main widget area
 *
 * @link https://developer.wordpress.org/themes/basics/template-files/#template-partials
 *
 * @package stax
 */

namespace Stax;

use Stax\Customizer\Config;

?>

<?php if ( stax()->is_primary_sidebar_active() ) : ?>

	<?php

	if ( is_single() ) {
		$is_sticky = stax()->get_option( Config::OPTION_LAYOUT_POST_STICKY_PRIMARY_SIDEBAR );
		$gap       = stax()->get_option( Config::OPTION_LAYOUT_POST_SIDEBAR_GAP );
	} elseif ( is_archive() || is_search() || is_home() ) {
		$is_sticky = stax()->get_option( Config::OPTION_LAYOUT_ARCHIVE_STICKY_PRIMARY_SIDEBAR );
		$gap       = stax()->get_option( Config::OPTION_LAYOUT_ARCHIVE_SIDEBAR_GAP );
	} else {
		$is_sticky = stax()->get_option( Config::OPTION_LAYOUT_GENERAL_STICKY_PRIMARY_SIDEBAR );
		$gap       = stax()->get_option( Config::OPTION_LAYOUT_GENERAL_SIDEBAR_GAP );
	}

	$sticky_class = $is_sticky ? 'svq-sticky-el' : '';

	if ( $is_sticky ) {
		\Stax_Assets::instance()->enqueue_hc_sticky();
	}

	?>

	<div class="svq-sidebar-page primary-sidebar" id="primary-sidebar">
		<div class="svq-sidebar-content widget-area <?php echo esc_attr( $sticky_class ); ?>"
			 data-stick-to="primary-sidebar" data-top="<?php echo esc_attr( $gap ); ?>">
			<?php stax()->display_primary_sidebar(); ?>
		</div>
	</div><!-- #primary -->

<?php endif; ?>

<?php if ( stax()->is_secondary_sidebar_active() ) : ?>

	<?php
	if ( is_single() ) {
		$is_sticky  = stax()->get_option( Config::OPTION_LAYOUT_POST_STICKY_SECONDARY_SIDEBAR );
		$sticky_gap = stax()->get_option( Config::OPTION_LAYOUT_POST_SIDEBAR_GAP );
	} elseif ( is_archive() || is_search() || is_home() ) {
		$is_sticky  = stax()->get_option( Config::OPTION_LAYOUT_ARCHIVE_STICKY_SECONDARY_SIDEBAR );
		$sticky_gap = stax()->get_option( Config::OPTION_LAYOUT_ARCHIVE_SIDEBAR_GAP );
	} else {
		$is_sticky  = stax()->get_option( Config::OPTION_LAYOUT_GENERAL_STICKY_SECONDARY_SIDEBAR );
		$sticky_gap = stax()->get_option( Config::OPTION_LAYOUT_GENERAL_SIDEBAR_GAP );
	}

	$sticky_class = $is_sticky ? 'svq-sticky-el' : '';
	$gap          = $sticky_gap;

	if ( $is_sticky ) {
		\Stax_Assets::instance()->enqueue_hc_sticky();
	}
	?>

	<div class="svq-sidebar-page secondary-sidebar" id="secondary-sidebar">
		<div class="svq-sidebar-content widget-area <?php echo esc_attr( $sticky_class ); ?>"
			 data-stick-to="secondary-sidebar" data-top="<?php echo esc_attr( $gap ); ?>">
			<?php stax()->display_secondary_sidebar(); ?>
		</div>
	</div><!-- #secondary -->

	<?php
endif;
