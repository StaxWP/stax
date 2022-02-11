<?php

namespace Stax\Builder\Core\Components;

use Stax\Builder\Core\Settings\Manager as SettingsManager;
use Stax\Builder\Main;
use Stax\Customizer\Dynamic_Selector;
use Stax\Customizer\Core\Sanitizer;

class CustomHtml extends Abstract_Component {

	const CONTENT_ID   = 'content';
	const COMPONENT_ID = 'custom_html';
	const COLOR_ID     = 'color';

	/**
	 * CustomHtml constructor.
	 *
	 * @since   1.0.0
	 * @access  public
	 */
	public function init() {
		$this->set_property( 'label', __( 'HTML', 'stax' ) );
		$this->set_property( 'id', $this->get_class_const( 'COMPONENT_ID' ) );
		$this->set_property( 'width', 3 );
		$this->set_property( 'component_slug', 'hfg-html' );
		$this->set_property( 'icon', 'welcome-write-blog' );
		$this->set_property( 'has_typeface_control', true );
		$this->set_property( 'default_typography_selector', $this->default_typography_selector . '.builder-item--' . $this->get_id() . ' .stx-html-content' ); //phpcs:ignore WordPressVIPMinimum.Security.Vuejs.RawHTMLDirectiveFound
		$this->set_property( 'has_horizontal_alignment', true );

		add_filter( 'wp_kses_allowed_html', [ $this, 'allow_input_form_tags' ], 10, 2 );
	}

	/**
	 * Add form and input tag to allowed tags in header_footer_grid context.
	 *
	 * @param array        $tags    HTML Tags.
	 * @param string|array $context The context for which to retrieve tags.
	 *
	 * @return array
	 */
	public function allow_input_form_tags( $tags, $context ) {
		if ( 'header_footer_grid' !== $context ) {
			return $tags;
		}

		$tags = wp_kses_allowed_html( 'post' );

		$global_attributes = [
			'accesskey'       => true,
			'class'           => true,
			'contenteditable' => true,
			'data-*'          => true,
			'dir'             => true,
			'draggable'       => true,
			'dropzone'        => true,
			'hidden'          => true,
			'id'              => true,
			'lang'            => true,
			'spellcheck'      => true,
			'style'           => true,
			'tabindex'        => true,
			'title'           => true,
			'translate'       => true,
		];

		$input_attributes = [
			'accept'         => true,
			'align'          => true,
			'alt'            => true,
			'autocomplete'   => true,
			'autofocus'      => true,
			'checked'        => true,
			'dirname'        => true,
			'disabled'       => true,
			'form'           => true,
			'formaction'     => true,
			'formenctype'    => true,
			'formmethod'     => true,
			'formnovalidate' => true,
			'formtarget'     => true,
			'height'         => true,
			'list'           => true,
			'max'            => true,
			'maxlength'      => true,
			'min'            => true,
			'multiple'       => true,
			'name'           => true,
			'pattern'        => true,
			'placeholder'    => true,
			'readonly'       => true,
			'required'       => true,
			'size'           => true,
			'src'            => true,
			'step'           => true,
			'type'           => true,
			'value'          => true,
			'width'          => true,
		];

		$form_attributes = [
			'accept'         => true,
			'accept-charset' => true,
			'action'         => true,
			'autocomplete'   => true,
			'enctype'        => true,
			'method'         => true,
			'name'           => true,
			'nonvalidate'    => true,
			'target'         => true,
		];

		$tags['input'] = array_merge( $input_attributes, $global_attributes );
		$tags['form']  = array_merge( $form_attributes, $global_attributes );
		$tags['span']  = array_merge( [], $global_attributes );
		$tags['time']  = [
			'datetime' => true,
			'class'    => true,
		];

		return $tags;
	}

	/**
	 * Called to register component controls.
	 *
	 * @since   1.0.0
	 * @access  public
	 */
	public function add_settings() {
		SettingsManager::get_instance()->add(
			[
				'id'                 => self::CONTENT_ID,
				'group'              => $this->get_class_const( 'COMPONENT_ID' ),
				'tab'                => SettingsManager::TAB_GENERAL,
				'transport'          => 'post' . $this->get_class_const( 'COMPONENT_ID' ),
				'sanitize_callback'  => 'wp_kses_post',
				'default'            => get_theme_mod( 'stax_top_bar_content', '' ),
				'label'              => __( 'HTML', 'stax' ),
				'description'        => __( 'Arbitrary HTML code. It supports also shortcodes.', 'stax' ),
				'type'               => '\Stax\Customizer\Core\Controls\React\Rich_Text',
				'section'            => $this->section,
				'options'            => [
					'input_attrs' => [
						'toolbars' => [
							'toolbar1' => 'formatselect,styleselect,bold,italic,bullist,numlist,link,alignleft,aligncenter,alignright,wp_adv',
							'toolbar2' => 'strikethrough,hr,forecolor,pastetext,removeformat,charmap,outdent,indent',
						],
					],
				],
				'use_dynamic_fields' => [ 'string', 'url' ],
				'conditional_header' => $this->get_builder_id() === 'header',
			]
		);

		SettingsManager::get_instance()->add(
			[
				'id'                    => self::COLOR_ID,
				'group'                 => $this->get_class_const( 'COMPONENT_ID' ),
				'tab'                   => SettingsManager::TAB_STYLE,
				'transport'             => 'postMessage',
				'sanitize_callback'     => [ Sanitizer::instance(), 'sanitize_colors' ],
				'label'                 => __( 'Text Color', 'stax' ),
				'type'                  => 'stax_color_control',
				'section'               => $this->section,
				'live_refresh_selector' => true,
				'live_refresh_css_prop' => [
					'cssVar' => [
						'vars'     => '--color',
						'selector' => '.builder-item--' . $this->get_id(),
					],
					[
						'selector' => $this->default_typography_selector . ', ' . $this->default_typography_selector . ' *:not(a)',
						'prop'     => 'color',
						'fallback' => 'inherit',
					],
				],
				'conditional_header'    => $this->get_builder_id() === 'header',
			]
		);
	}

	/**
	 * Method to add Component css styles.
	 *
	 * @param array $css_array An array containing css rules.
	 *
	 * @return array
	 * @since   1.0.0
	 * @access  public
	 */
	public function add_style( array $css_array = [] ) {
		$css_array[] = [
			Dynamic_Selector::KEY_SELECTOR => '.builder-item--' . $this->get_id(),
			Dynamic_Selector::KEY_RULES    => [
				'--color' => [
					Dynamic_Selector::META_KEY     => $this->get_id() . '_' . self::COLOR_ID,
					Dynamic_Selector::META_DEFAULT => SettingsManager::get_instance()->get_default( $this->get_id() . '_' . self::COLOR_ID ),
				],
			],
		];

		return parent::add_style( $css_array );
	}

	/**
	 * The render method for the component.
	 *
	 * @since   1.0.0
	 * @access  public
	 */
	public function render_component() {
		Main::get_instance()->load( 'components/component-html' );
	}
}
