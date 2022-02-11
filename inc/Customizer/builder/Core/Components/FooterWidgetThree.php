<?php

namespace Stax\Builder\Core\Components;

class FooterWidgetThree extends Abstract_FooterWidget {

	const COMPONENT_ID = 'footer-three-widgets';

	/**
	 * FooterWidgetThree constructor.
	 *
	 * @since   1.0.0
	 * @access  public
	 */
	public function init() {
		$this->set_property( 'label', __( 'Footer Three', 'stax' ) );
		$this->set_property( 'id', self::COMPONENT_ID );
		$this->set_property( 'width', 3 );
		$this->set_property( 'section', 'sidebar-widgets-footer-three-widgets' );

		if ( strpos( $this->section, 'widgets-footer' ) !== false ) {
			$this->set_property( 'section', 'stax_' . $this->section );
		}

		add_filter( 'customize_section_active', [ $this, 'footer_widgets_show' ], 15, 2 );
	}

}
