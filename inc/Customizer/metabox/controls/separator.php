<?php

namespace Stax\Metabox\Controls;

class Separator extends Control_Base {

	/**
	 * Control type.
	 *
	 * @var string
	 */
	public $type = 'separator';

	/**
	 * Render control.
	 *
	 * @return void
	 */
	public function render_content( $post_id ) {
		echo '<hr/>';
	}

}
