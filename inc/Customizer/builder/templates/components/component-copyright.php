<?php

namespace Stax\Builder;

use Stax\Builder\Core\Components\Copyright;

$content = component_setting( Copyright::CONTENT_ID );
?>
<div class="component-wrap">
	<?php echo wp_kses_post( balanceTags( parse_dynamic_tags( $content ), true ) ); ?>
</div>
