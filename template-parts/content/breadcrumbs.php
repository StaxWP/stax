<?php

namespace Stax;

$stax_breadcrumb = stax()->get_breadcrumb();

if ( ! $stax_breadcrumb ) {
	return;
}
?>

<nav aria-label="<?php esc_attr_e( 'Breadcrumb', 'stax' ); ?>" class="svq-breadcrumb">
	<?php echo $stax_breadcrumb; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Markup built by the theme trail or an SEO plugin. ?>
</nav>
