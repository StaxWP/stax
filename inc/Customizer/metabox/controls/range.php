<?php

namespace Stax\Metabox\Controls;

class Range extends Control_Base {

	/**
	 * Control type.
	 *
	 * @var string
	 */
	public $type = 'range';

	/**
	 * Render control.
	 *
	 * @return void
	 */
	public function render_content( $post_id ) {
		$value      = $this->get_value( $post_id );
		$class      = 'stax-range-input ';
		$dependency = '';
		if ( $this->settings['hidden'] === true ) {
			$class .= ' stax-hidden';
		}
		if ( isset( $this->settings['depends_on'] ) ) {
			$dependency .= ' data-depends=' . esc_attr( $this->settings['depends_on'] );
			$class      .= ' stax-dependent';
		}

		$markup = '
<style>
.stax-range-input{display: flex; align-items: center;}
.stax-range-input .stx-range{flex-grow: 1; margin-right: 5px;}
.stax-range-input .stx-number{min-width: 0; margin-left: auto;}
.stax-range-input.stax-hidden{display: none;}
</style>';

		$markup .= '<p class="' . esc_attr( $class ) . '" ' . esc_attr( $dependency ) . ' >';
		$markup .= '<input type="range"
		value="' . esc_attr( $value ) . '"
		id="' . esc_attr( $this->id ) . '-range"
		class="stx-range"
		name="' . esc_attr( $this->id ) . '"
		min="' . esc_attr( $this->settings['min'] ) . '"
		max="' . esc_attr( $this->settings['max'] ) . '" >';
		$markup .= '<input type="number"
		value="' . esc_attr( $value ) . '"
		id="' . esc_attr( $this->id ) . '"
		class="stx-number"
		name="' . esc_attr( $this->id ) . '"
		min="' . esc_attr( $this->settings['min'] ) . '"
		max="' . esc_attr( $this->settings['max'] ) . '" >';
		$markup .= '</p>';

		echo $markup; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	}

}
