<?php

namespace Stax\Builder\Core\Components;

class FooterWidgetFour extends Abstract_FooterWidget {

	const COMPONENT_ID = 'footer-four-widgets';

	/**
	 * FooterWidgetFour constructor.
	 *
	 * @since   1.0.0
	 * @access  public
	 */
	public function init() {
		$this->set_property( 'label', __( 'Footer Four', 'stax' ) );
		$this->set_property( 'id', self::COMPONENT_ID );
		$this->set_property( 'width', 3 );
		$this->set_property( 'section', 'sidebar-widgets-footer-four-widgets' );

		if ( strpos( $this->section, 'widgets-footer' ) !== false ) {
			$this->set_property( 'section', 'stax_' . $this->section );
		}

		add_filter( 'customize_section_active', [ $this, 'footer_widgets_show' ], 15, 2 );
	}

}
