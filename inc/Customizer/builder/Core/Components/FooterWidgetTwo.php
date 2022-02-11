<?php

namespace Stax\Builder\Core\Components;

class FooterWidgetTwo extends Abstract_FooterWidget {

	const COMPONENT_ID = 'footer-two-widgets';

	public function init() {
		$this->set_property( 'label', __( 'Footer Two', 'stax' ) );
		$this->set_property( 'id', self::COMPONENT_ID );
		$this->set_property( 'width', 3 );
		$this->set_property( 'section', 'sidebar-widgets-footer-two-widgets' );

		if ( strpos( $this->section, 'widgets-footer' ) !== false ) {
			$this->set_property( 'section', 'stax_' . $this->section );
		}

		add_filter( 'customize_section_active', [ $this, 'footer_widgets_show' ], 15, 2 );
	}

}
