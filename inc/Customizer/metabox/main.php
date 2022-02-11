<?php

namespace Stax\Metabox;

class Main extends Controls_Base {

	/**
	 * Add controls.
	 */
	public function add_controls() {
		$this->add_layout_controls();
		$this->add_control( new Controls\Separator( 'stax_meta_separator', [ 'priority' => 20 ] ) );
		$this->add_content_toggles();
		$this->add_control( new Controls\Separator( 'stax_meta_separator', [ 'priority' => 45 ] ) );
		$this->add_content_width();
	}

	/**
	 * Add layout controls.
	 */
	private function add_layout_controls() {
		$this->add_control(
			new Controls\Radio(
				'stax_meta_container',
				[
					'default' => 'default',
					'choices' => [
						'default'    => __( 'Customizer Setting', 'stax' ),
						'contained'  => __( 'Contained', 'stax' ),
						'full-width' => __( 'Full Width', 'stax' ),
					],
					'label'   => __( 'Container', 'stax' ),
				]
			)
		);

		$this->add_control(
			new Controls\Radio(
				'stax_meta_sidebar',
				[
					'default'  => ( self::is_new_page() || self::is_checkout() ) ? 'full-width' : 'default',
					'choices'  => [
						'default'    => __( 'Customizer Setting', 'stax' ),
						'left'       => __( 'Left Sidebar', 'stax' ),
						'right'      => __( 'Right Sidebar', 'stax' ),
						'full-width' => __( 'No Sidebar', 'stax' ),
					],
					'label'    => __( 'Sidebar', 'stax' ),
					'priority' => 15,
				]
			)
		);
	}

	/**
	 * Add content toggles.
	 */
	private function add_content_toggles() {
		$content_controls = [
			'stax_meta_disable_header'         => [
				'default'     => 'off',
				'label'       => __( 'Components', 'stax' ),
				'input_label' => __( 'Disable Header', 'stax' ),
				'priority'    => 25,
			],
			'stax_meta_disable_title'          => [
				'default'         => 'off',
				'input_label'     => __( 'Disable Title', 'stax' ),
				'active_callback' => [ $this, 'hide_on_single_product' ],
				'priority'        => 30,
			],
			'stax_meta_disable_featured_image' => [
				'default'         => 'off',
				'input_label'     => __( 'Disable Featured Image', 'stax' ),
				'active_callback' => [ $this, 'hide_on_single_page_and_product' ],
				'priority'        => 35,
			],
			'stax_meta_disable_footer'         => [
				'default'     => 'off',
				'input_label' => __( 'Disable Footer', 'stax' ),
				'priority'    => 40,
			],
		];

		$default_control_args = [
			'default'         => 'off',
			'label'           => '',
			'input_label'     => '',
			'active_callback' => '__return_true',
			'priority'        => 10,
		];

		foreach ( $content_controls as $control_id => $args ) {
			$args = wp_parse_args( $args, $default_control_args );

			$this->add_control(
				new Controls\Checkbox(
					$control_id,
					[
						'default'         => $args['default'],
						'label'           => $args['label'],
						'input_label'     => $args['input_label'],
						'active_callback' => $args['active_callback'],
						'priority'        => $args['priority'],
					]
				)
			);
		}
	}

	/**
	 * Add content width control.
	 */
	private function add_content_width() {
		$this->add_control(
			new Controls\Checkbox(
				'stax_meta_enable_content_width',
				[
					'default'     => ( self::is_new_page() || self::is_checkout() ) ? 'on' : 'off',
					'label'       => __( 'Content Width', 'stax' ) . ' (%)',
					'input_label' => __( 'Enable Individual Content Width', 'stax' ),
					'priority'    => 50,
				]
			)
		);

		$this->add_control(
			new Controls\Range(
				'stax_meta_content_width',
				[
					'default'    => ( self::is_new_page() || self::is_checkout() ) ? 100 : 70,
					'min'        => 50,
					'max'        => 100,
					'hidden'     => self::hide_content_width(),
					'depends_on' => 'stax_meta_enable_content_width',
					'priority'   => 55,
				]
			)
		);
	}

	/**
	 * Hide content width.
	 *
	 * @return bool
	 */
	public static function hide_content_width() {
		if ( self::is_new_page() ) {
			return false;
		}

		if ( ! isset( $_GET['post'] ) ) {
			return true;
		}

		$meta = get_post_meta( (int) $_GET['post'], 'stax_meta_enable_content_width', true );

		if ( empty( $meta ) && self::is_checkout() ) {
			return false;
		}

		if ( empty( $meta ) || $meta === 'off' ) {
			return true;
		}

		return false;
	}

	/**
	 * Callback to hide on single product edit page.
	 *
	 * @return bool
	 */
	public function hide_on_single_product() {
		if ( isset( $_GET['post_type'] ) && $_GET['post_type'] === 'product' ) {
			return false;
		}

		if ( ! isset( $_GET['post'] ) ) {
			return true;
		}

		$post_type = get_post_type( (int) $_GET['post'] );

		if ( $post_type !== 'product' ) {
			return true;
		}

		return false;
	}

	/**
	 * Callback to hide on single product/page edit page
	 *
	 * @return bool
	 */
	public function hide_on_single_page_and_product() {
		if ( isset( $_GET['post_type'] ) && ( $_GET['post_type'] === 'page' || $_GET['post_type'] === 'product' ) ) {
			return false;
		}

		if ( ! isset( $_GET['post'] ) ) {
			return true;
		}

		$post_type = get_post_type( (int) $_GET['post'] );

		if ( $post_type !== 'page' && $post_type !== 'product' ) {
			return true;
		}

		return false;
	}

	/**
	 * Check if we're adding a new post of type `page`.
	 *
	 * @return bool
	 */
	public static function is_new_page() {
		global $pagenow;

		if ( $pagenow !== 'post-new.php' ) {
			return false;
		}

		if ( ! isset( $_GET['post_type'] ) ) {
			return false;
		}
		if ( ( $_GET['post_type'] !== 'page' ) ) {
			return false;
		}

		return true;
	}

	/**
	 * Check if is checkout.
	 */
	public static function is_checkout() {
		if ( ! class_exists( 'WooCommerce', false ) ) {
			return false;
		}

		if ( ! isset( $_GET['post'] ) ) {
			return false;
		}

		if ( $_GET['post'] === get_option( 'woocommerce_checkout_page_id' ) ) {
			return true;
		}

		return false;
	}

}
