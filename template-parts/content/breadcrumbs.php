<?php

namespace Stax;

if ( ! stax()->get_breadcrumb() ) {
	return;
}
?>

<nav aria-label="breadcrumb" class="svq-breadcrumb">
	<?php echo stax()->get_breadcrumb(); ?>
</nav>
