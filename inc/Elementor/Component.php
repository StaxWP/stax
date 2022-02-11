<?php
/**
 * Stax\Elementor\Component class
 *
 * @package stax
 */

namespace Stax\Elementor;

use Elementor\Controls_Manager;
use Stax\Component_Interface;
use function add_action;

/**
 * Class Component
 *
 * @package Stax\Elementor
 */
class Component implements Component_Interface {

	/**
	 * Gets the unique identifier for the theme component.
	 *
	 * @return string Component slug.
	 */
	public function get_slug() {
		return 'elementor';
	}

	/**
	 * Adds the action and filter hooks to integrate with WordPress.
	 */
	public function initialize() {

		add_action( 'elementor/documents/register_controls', [ $this, 'register_template_control' ] );

		/* Elementor Pro */
		add_action( 'elementor/theme/register_locations', [ $this, 'register_elementor_locations' ] );
	}

	/**
	 * Register elementor locations
	 *
	 * @param $elementor_theme_manager
	 */
	public function register_elementor_locations( $elementor_theme_manager ) {
		$elementor_theme_manager->register_location(
			'header',
			[
				'hook'         => 'stax_header',
				'remove_hooks' => [ 'stax_header_builder_action' ],
			]
		);

		$elementor_theme_manager->register_location(
			'footer',
			[
				'hook'         => 'stax_footer',
				'remove_hooks' => [ 'stax_footer_builder_action' ],
			]
		);

		$elementor_theme_manager->register_location(
			'single',
			[
				'hook'         => 'stax_single',
				'remove_hooks' => [ 'stax_show_single' ],
			]
		);

		$elementor_theme_manager->register_location(
			'archive',
			[
				'hook'         => 'stax_archive',
				'remove_hooks' => [
					'stax_show_featured_posts',
					'stax_show_archive',
				],
			]
		);
	}

	public function register_template_control( $document ) {

		if ( ! $document instanceof \Elementor\Core\DocumentTypes\Page &&
			 ! $document instanceof \Elementor\Core\DocumentTypes\Post &&
			 ! $document instanceof \Elementor\Modules\Library\Documents\Page ) {
			return;
		}

		if ( ! \Elementor\Utils::is_cpt_custom_templates_supported() ) {
			return;
		}

		$document->start_injection(
			[
				'of'       => 'post_status',
				'fallback' => [
					'of' => 'post_title',
				],
			]
		);

		$document->add_control(
			'stax_page_settings_sep',
			[
				'type'  => Controls_Manager::DIVIDER,
				'style' => 'thick',
				'label' => 'Separator',
			]
		);

		$document->add_control(
			'stax_page_settings_title',
			[
				'type' => Controls_Manager::RAW_HTML,
				'raw'  => '<strong>' . esc_html__( 'Stax Settings', 'stax' ) . '</strong>',
			]
		);

		$document->add_control(
			'svq_header',
			[
				'label'        => esc_html__( 'Hide Header', 'stax' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_off'    => esc_html__( 'Off', 'stax' ),
				'label_on'     => esc_html__( 'On', 'stax' ),
				'default'      => '',
				'return_value' => '1',
				'selectors'    => [
					'header.header' => 'display: none',
				],
			]
		);

		$document->add_control(
			'svq_title_breadcrumb',
			[
				'label'        => esc_html__( 'Hide Title + Breadcrumb', 'stax' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_off'    => esc_html__( 'Off', 'stax' ),
				'label_on'     => esc_html__( 'On', 'stax' ),
				'default'      => '',
				'return_value' => '1',
				'selectors'    => [
					'.svq-site-content > .svq-panel' => 'display: none',
				],
			]
		);

		$document->end_injection();
	}

}
