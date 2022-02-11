<?php

namespace Stax\Builder\Core\Builder;

use Stax\Builder\Main;

class Footer extends Abstract_Builder {
	/**
	 * Builder name.
	 */
	const BUILDER_NAME = 'footer';

	/**
	 * Footer constructor.
	 *
	 * @since   1.0.0
	 * @access  public
	 */
	public function init() {
		$this->set_property( 'title', __( 'Footer', 'stax' ) );
		$this->set_property( 'columns_layout', true );

		$this->set_property(
			'instructions_array',
			[
				'description' => sprintf(
				/* translators: 1: builder, 2: builder symbol */
					esc_attr__( 'Welcome to the %1$s builder! Click the %2$s button to add a new component or follow the Quick Links.', 'stax' ),
					$this->get_property( 'title' ),
					'+'
				),
				'quickLinks'  => [
					'footer_copyright_content'            => [
						'label' => esc_html__( 'Change Copyright', 'stax' ),
						'icon'  => 'dashicons-nametag',
					],
					'hfg_footer_layout_bottom_background' => [
						'label' => esc_html__( 'Change Footer Color', 'stax' ),
						'icon'  => 'dashicons-admin-appearance',
					],
				],
			]
		);

		$this->devices = [
			'desktop' => __( 'Footer', 'stax' ),
		];
	}

	/**
	 * Method called via hook.
	 *
	 * @since   1.0.0
	 * @access  public
	 */
	public function load_template() {
		Main::get_instance()->load( 'footer-wrapper' );
	}

	/**
	 * Render builder row.
	 *
	 * @param string $device_id The device id.
	 * @param string $row_id The row id.
	 * @param array  $row_details Row data.
	 */
	public function render_row( $device_id, $row_id, $row_details ) {
		Main::get_instance()->load( 'footer-row-wrapper' );
	}

	/**
	 * Get builder id.
	 *
	 * @return string Builder id.
	 */
	public function get_id() {
		return self::BUILDER_NAME;
	}

	/**
	 * Overrides parent method to limit rows.
	 *
	 * @return array
	 * @since   1.0.0
	 * @access  protected
	 */
	protected function get_rows() {
		return [
			'top'    => [
				'title'       => __( 'Footer Top', 'stax' ),
				'description' => $this->get_property( 'description' ),
			],
			'main'   => [
				'title'       => __( 'Footer Main', 'stax' ),
				'description' => $this->get_property( 'description' ),
			],
			'bottom' => [
				'title'       => __( 'Footer Bottom', 'stax' ),
				'description' => $this->get_property( 'description' ),
			],
		];
	}
}
