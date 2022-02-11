<?php

namespace Stax\Customizer;

class Generator {

	/**
	 * Subscriber list used for CSS generation.
	 *
	 * @var array Subscriber list.
	 */
	protected $_subscribers   = [];
	const SUBSCRIBER_TYPE     = 'type';
	const SUBSCRIBER_MAP      = 'map';
	const SUBSCRIBER_KEY      = 'key';
	const SUBSCRIBER_DEFAULTS = 'defaults';

	/**
	 * Generate the dynamic CSS.
	 *
	 * @param bool $echo Should we write it or return it.
	 *
	 * @return string Css output.
	 */
	public function generate( $echo = false ) {
		$css         = '';
		$tablet_css  = '';
		$desktop_css = '';

		$dynamic_selectors = new Dynamic_Selector( $this->_subscribers );

		$css         .= $dynamic_selectors->for_mobile();
		$tablet_css  .= $dynamic_selectors->for_tablet();
		$desktop_css .= $dynamic_selectors->for_desktop();

		if ( ! empty( $tablet_css ) ) {
			$css .= sprintf( '@media(min-width: 576px){ %s }', $tablet_css );
		}

		if ( ! empty( $desktop_css ) ) {
			$css .= sprintf( '@media(min-width: 960px){ %s }', $desktop_css );
		}

		if ( ! $echo ) {
			return $css;
		}

		echo $css; //phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	}

	/**
	 * Set new subscribers.
	 *
	 * @param array $subscribers New generator list.
	 */
	public function set( $subscribers ) {
		$this->_subscribers = $subscribers;
	}

	/**
	 * Return current subscribers.
	 *
	 * @return array
	 */
	public function get() {
		return $this->_subscribers;
	}
}
