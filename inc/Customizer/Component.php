<?php
/**
 * Stax\Customizer\Component class
 *
 * @package stax
 */

namespace Stax\Customizer;

use Stax\Component_Interface;
use Stax\Customizer\Core\Loader;

/**
 * Class for managing Customizer integration.
 */
class Component implements Component_Interface {

	/**
	 * Gets the unique identifier for the theme component.
	 *
	 * @return string Component slug.
	 */
	public function get_slug() {
		return 'customizer';
	}

	/**
	 * Adds the action and filter hooks to integrate with WordPress.
	 */
	public function initialize() {
		add_action( 'after_switch_theme', [ $this, 'set_mods' ] );

		add_action(
			'after_setup_theme',
			function() {
				$nav_menus_to_register = apply_filters(
					'stax_register_nav_menus',
					[
						'primary' => esc_html__( 'Primary Menu', 'stax' ),
						'footer'  => esc_html__( 'Footer Menu', 'stax' ),
						'top-bar' => esc_html__( 'Secondary Menu', 'stax' ),
					]
				);
				register_nav_menus( $nav_menus_to_register );
				add_filter( 'wp_nav_menu_args', [ $this, 'nav_walker' ], 1001 );
			}
		);

		require_once get_template_directory() . '/inc/Customizer/Nav_Walker.php';
		require_once get_template_directory() . '/inc/Customizer/core/Sanitizer.php';

		require_once get_template_directory() . '/inc/Customizer/Config.php';
		require_once get_template_directory() . '/inc/Customizer/Mods.php';
		require_once get_template_directory() . '/inc/Customizer/Font_Manager.php';

		require_once get_template_directory() . '/inc/Customizer/styles/Dynamic_Selector.php';
		require_once get_template_directory() . '/inc/Customizer/styles/Css_Vars.php';
		require_once get_template_directory() . '/inc/Customizer/styles/Css_Prop.php';
		require_once get_template_directory() . '/inc/Customizer/styles/Generator.php';
		require_once get_template_directory() . '/inc/Customizer/styles/Frontend.php';
		require_once get_template_directory() . '/inc/Customizer/styles/Dynamic_Css.php';

		$font_manager = new Font_Manager();
		$font_manager->init();

		$dynamic_css = new Dynamic_Css();
		$dynamic_css->init();

		add_action(
			'customize_register',
			static function() {
				// React controls.
				require_once get_template_directory() . '/inc/Customizer/core/controls/react/Builder_Columns.php';
				require_once get_template_directory() . '/inc/Customizer/core/controls/react/Builder_Section.php';
				require_once get_template_directory() . '/inc/Customizer/core/controls/react/Builder.php';
				require_once get_template_directory() . '/inc/Customizer/core/controls/react/Button_Appearance.php';
				require_once get_template_directory() . '/inc/Customizer/core/controls/react/Color.php';
				require_once get_template_directory() . '/inc/Customizer/core/controls/react/Font_Family.php';
				require_once get_template_directory() . '/inc/Customizer/core/controls/react/Global_Colors.php';
				require_once get_template_directory() . '/inc/Customizer/core/controls/react/Heading.php';
				require_once get_template_directory() . '/inc/Customizer/core/controls/react/Inline_Select.php';
				require_once get_template_directory() . '/inc/Customizer/core/controls/react/Instructions_Control.php';
				require_once get_template_directory() . '/inc/Customizer/core/controls/react/Instructions_Section.php';
				require_once get_template_directory() . '/inc/Customizer/core/controls/react/Multiselect.php';
				require_once get_template_directory() . '/inc/Customizer/core/controls/react/Nr_Spacing.php';
				require_once get_template_directory() . '/inc/Customizer/core/controls/react/Ordering.php';
				require_once get_template_directory() . '/inc/Customizer/core/controls/react/Presets_Selector.php';
				require_once get_template_directory() . '/inc/Customizer/core/controls/react/Radio_Buttons.php';
				require_once get_template_directory() . '/inc/Customizer/core/controls/react/Radio_Image.php';
				require_once get_template_directory() . '/inc/Customizer/core/controls/react/Range.php';
				require_once get_template_directory() . '/inc/Customizer/core/controls/react/Responsive_Radio_Buttons.php';
				require_once get_template_directory() . '/inc/Customizer/core/controls/react/Responsive_Range.php';
				require_once get_template_directory() . '/inc/Customizer/core/controls/react/Responsive_Toggle.php';
				require_once get_template_directory() . '/inc/Customizer/core/controls/react/Rich_Text.php';
				require_once get_template_directory() . '/inc/Customizer/core/controls/react/Spacing.php';
				require_once get_template_directory() . '/inc/Customizer/core/controls/react/Typography.php';

				// Normal controls.
				require_once get_template_directory() . '/inc/Customizer/core/controls/Button_Group.php';
				require_once get_template_directory() . '/inc/Customizer/core/controls/Button.php';
				require_once get_template_directory() . '/inc/Customizer/core/controls/Checkbox.php';
				require_once get_template_directory() . '/inc/Customizer/core/controls/Heading.php';
				require_once get_template_directory() . '/inc/Customizer/core/controls/Ordering.php';
				require_once get_template_directory() . '/inc/Customizer/core/controls/Radio_Image.php';
				require_once get_template_directory() . '/inc/Customizer/core/controls/Range.php';
				require_once get_template_directory() . '/inc/Customizer/core/controls/Responsive_Number.php';
				require_once get_template_directory() . '/inc/Customizer/core/controls/Tabs.php';

				// Types.
				require_once get_template_directory() . '/inc/Customizer/core/types/Control.php';
				require_once get_template_directory() . '/inc/Customizer/core/types/Panel.php';
				require_once get_template_directory() . '/inc/Customizer/core/types/Partial.php';
				require_once get_template_directory() . '/inc/Customizer/core/types/Section.php';
			}
		);

		// Loader.
		require_once get_template_directory() . '/inc/Customizer/core/Base_Customizer.php';
		require_once get_template_directory() . '/inc/Customizer/core/Loader.php';

		$loader = new Loader();
		$loader->init();

		$this->load_builder();
		$this->load_metaboxes();
	}

	/**
	 * Load Header/Footer builder modules
	 *
	 * @return void
	 */
	public function load_builder() {
		require_once get_template_directory() . '/inc/Customizer/builder/Traits/Core.php';

		require_once get_template_directory() . '/inc/Customizer/builder/Core/Interfaces/Builder.php';
		require_once get_template_directory() . '/inc/Customizer/builder/Core/Interfaces/Component.php';

		require_once get_template_directory() . '/inc/Customizer/builder/Core/Builder/Abstract_Builder.php';
		require_once get_template_directory() . '/inc/Customizer/builder/Core/Builder/Footer.php';
		require_once get_template_directory() . '/inc/Customizer/builder/Core/Builder/Header.php';

		require_once get_template_directory() . '/inc/Customizer/builder/Core/Components/Abstract_Component.php';
		require_once get_template_directory() . '/inc/Customizer/builder/Core/Components/Abstract_FooterWidget.php';
		require_once get_template_directory() . '/inc/Customizer/builder/Core/Components/Button.php';
		require_once get_template_directory() . '/inc/Customizer/builder/Core/Components/CartIcon.php';
		require_once get_template_directory() . '/inc/Customizer/builder/Core/Components/Copyright.php';
		require_once get_template_directory() . '/inc/Customizer/builder/Core/Components/CustomHtml.php';
		require_once get_template_directory() . '/inc/Customizer/builder/Core/Components/FooterWidgetFour.php';
		require_once get_template_directory() . '/inc/Customizer/builder/Core/Components/FooterWidgetOne.php';
		require_once get_template_directory() . '/inc/Customizer/builder/Core/Components/FooterWidgetThree.php';
		require_once get_template_directory() . '/inc/Customizer/builder/Core/Components/FooterWidgetTwo.php';
		require_once get_template_directory() . '/inc/Customizer/builder/Core/Components/Logo.php';
		require_once get_template_directory() . '/inc/Customizer/builder/Core/Components/MenuIcon.php';
		require_once get_template_directory() . '/inc/Customizer/builder/Core/Components/Nav.php';
		require_once get_template_directory() . '/inc/Customizer/builder/Core/Components/NavFooter.php';
		require_once get_template_directory() . '/inc/Customizer/builder/Core/Components/Search.php';
		require_once get_template_directory() . '/inc/Customizer/builder/Core/Components/SearchResponsive.php';
		require_once get_template_directory() . '/inc/Customizer/builder/Core/Components/SecondNav.php';
		require_once get_template_directory() . '/inc/Customizer/builder/Core/Css_Generator.php';
		require_once get_template_directory() . '/inc/Customizer/builder/Core/Customizer.php';
		require_once get_template_directory() . '/inc/Customizer/builder/Core/Magic_Tags.php';
		require_once get_template_directory() . '/inc/Customizer/builder/Core/Settings/Config.php';
		require_once get_template_directory() . '/inc/Customizer/builder/Core/Settings/Defaults.php';
		require_once get_template_directory() . '/inc/Customizer/builder/Core/Settings/Manager.php';
		require_once get_template_directory() . '/inc/Customizer/builder/Main.php';

		require_once get_template_directory() . '/inc/Customizer/builder/loader.php';
	}

	/**
	 * Load metaboxes
	 *
	 * @return void
	 */
	public function load_metaboxes() {
		require_once get_template_directory() . '/inc/Customizer/metabox/controls/control_base.php';
		require_once get_template_directory() . '/inc/Customizer/metabox/controls/checkbox.php';
		require_once get_template_directory() . '/inc/Customizer/metabox/controls/radio.php';
		require_once get_template_directory() . '/inc/Customizer/metabox/controls/range.php';
		require_once get_template_directory() . '/inc/Customizer/metabox/controls/separator.php';

		require_once get_template_directory() . '/inc/Customizer/metabox/controls_base.php';
		require_once get_template_directory() . '/inc/Customizer/metabox/main.php';
		require_once get_template_directory() . '/inc/Customizer/metabox/manager.php';

		$manager = new \Stax\Metabox\Manager();
		$manager->init();
	}

	public function set_mods() {
		$defaults = [
			'header_search_responsive_component_align'    => [
				'desktop' => 'right',
				'tablet'  => 'left',
				'mobile'  => 'left',
			],
			'header_search_field_height'                  => '{"mobile":40,"tablet":40,"desktop":40}',
			'header_search_field_text_size'               => '{"mobile":14,"tablet":14,"desktop":14}',
			'header_search_field_border_width'            => [
				'mobile'       => [
					'top'    => '',
					'right'  => '',
					'bottom' => '',
					'left'   => '',
				],
				'tablet'       => [
					'top'    => '',
					'right'  => '',
					'bottom' => '',
					'left'   => '',
				],
				'desktop'      => [
					'top'    => '',
					'right'  => '',
					'bottom' => '',
					'left'   => '',
				],
				'mobile-unit'  => 'px',
				'tablet-unit'  => 'px',
				'desktop-unit' => 'px',
			],
			'header_search_field_border_radius'           => [
				'mobile'       => [
					'top'    => '',
					'right'  => '',
					'bottom' => '',
					'left'   => '',
				],
				'tablet'       => [
					'top'    => '',
					'right'  => '',
					'bottom' => '',
					'left'   => '',
				],
				'desktop'      => [
					'top'    => '',
					'right'  => '',
					'bottom' => '',
					'left'   => '',
				],
				'mobile-unit'  => 'px',
				'tablet-unit'  => 'px',
				'desktop-unit' => 'px',
			],
			'header_search_field_background'              => '#ffffff',
			'header_search_field_text_color'              => '#d1d1d1',
			'header_search_component_padding'             => [
				'mobile'       => [
					'top'         => '',
					'right'       => '',
					'bottsssssom' => '',
					'left'        => '',
				],
				'tablet'       => [
					'top'    => '',
					'right'  => '',
					'bottom' => '',
					'left'   => '',
				],
				'desktop'      => [
					'top'    => '',
					'right'  => '',
					'bottom' => '',
					'left'   => '',
				],
				'mobile-unit'  => 'px',
				'tablet-unit'  => 'px',
				'desktop-unit' => 'px',
			],
			'header_search_component_margin'              => [
				'mobile'       => [
					'top'    => '',
					'right'  => '',
					'bottom' => '',
					'left'   => '',
				],
				'tablet'       => [
					'top'    => '',
					'right'  => '',
					'bottom' => '',
					'left'   => '',
				],
				'desktop'      => [
					'top'    => '',
					'right'  => '',
					'bottom' => '',
					'left'   => '',
				],
				'mobile-unit'  => 'px',
				'tablet-unit'  => 'px',
				'desktop-unit' => 'px',
			],
			'header_search_responsive_field_height'       => [
				'mobile'  => 40,
				'tablet'  => 40,
				'desktop' => 40,
			],
			'header_search_responsive_field_text_size'    => [
				'mobile'  => 14,
				'tablet'  => 14,
				'desktop' => 14,
			],
			'header_search_responsive_field_border_width' => [
				'mobile'       => [
					'top'    => '',
					'right'  => '',
					'bottom' => '',
					'left'   => '',
				],
				'tablet'       => [
					'top'    => '',
					'right'  => '',
					'bottom' => '',
					'left'   => '',
				],
				'desktop'      => [
					'top'    => '',
					'right'  => '',
					'bottom' => '',
					'left'   => '',
				],
				'mobile-unit'  => 'px',
				'tablet-unit'  => 'px',
				'desktop-unit' => 'px',
			],
			'header_search_responsive_icon_size'          => 14,
			'header_search_responsive_field_background'   => '',
			'header_search_responsive_field_text_color'   => '#6acfc9',
			'hfg_header_layout_v2'                        => '{"desktop":{"top":{"left":[],"c-left":[],"center":[],"c-right":[],"right":[]},"main":{"left":[{"id":"logo"}],"c-left":[],"center":[],"c-right":[],"right":[{"id":"primary-menu"},{"id":"header_search_responsive"}]},"bottom":{"left":[],"c-left":[],"center":[],"c-right":[],"right":[]}},"mobile":{"top":{"left":[],"c-left":[],"center":[],"c-right":[],"right":[]},"main":{"left":[{"id":"logo"}],"c-left":[],"center":[],"c-right":[],"right":[{"id":"header_search_responsive"},{"id":"nav-icon"}]},"bottom":{"left":[],"c-left":[],"center":[],"c-right":[],"right":[]},"sidebar":[{"id":"primary-menu"}]}}',
			'hfg_header_layout_top_layout'                => 'layout-fullwidth',
			'hfg_header_layout_main_layout'               => 'layout-full-contained',
			'hfg_header_layout_main_background'           => [
				'type'              => 'color',
				'imageUrl'          => '',
				'focusPoint'        => [
					'x' => 0.5,
					'y' => 0.5,
				],
				'colorValue'        => '#ffffff',
				'overlayColorValue' => '',
				'overlayOpacity'    => 50,
				'fixed'             => false,
				'useFeatured'       => false,
			],
			'hfg_header_layout_main_bottom_border'        => '{"mobile":1,"tablet":1,"desktop":1}',
			'hfg_header_layout_main_border_color'         => 'var(--border-color)',
			'primary-menu_component_typeface'             => [
				'fontSize'      => [
					'suffix'  => [
						'mobile'  => 'rem',
						'tablet'  => 'rem',
						'desktop' => 'px',
					],
					'vars'    => [],
					'mobile'  => 1,
					'tablet'  => 1,
					'desktop' => 16,
				],
				'lineHeight'    => [
					'vars'    => [],
					'mobile'  => 1.3,
					'tablet'  => 1.3,
					'desktop' => '',
					'suffix'  => [
						'mobile'  => 'rem',
						'tablet'  => 'rem',
						'desktop' => 'rem',
					],
				],
				'letterSpacing' => [
					'vars'    => [],
					'mobile'  => 0,
					'tablet'  => 0,
					'desktop' => '',
					'suffix'  => [
						'mobile'  => 'px',
						'tablet'  => 'px',
						'desktop' => 'px',
					],
				],
				'fontWeight'    => '300',
				'textTransform' => 'none',
			],
			'logo_component_align'                        => [
				'mobile'  => 'left',
				'tablet'  => 'left',
				'desktop' => 'left',
			],
			'primary-menu_spacing'                        => [
				'mobile'  => 0,
				'tablet'  => 0,
				'desktop' => 20,
			],
			'primary-menu_item_height'                    => [
				'mobile'  => 0,
				'tablet'  => 0,
				'desktop' => 25,
			],
			'header_search_responsive_open_type'          => 'canvas',
			'primary-menu_component_padding'              => [
				'mobile'       => [
					'top'    => '',
					'right'  => '',
					'bottom' => '',
					'left'   => '',
				],
				'tablet'       => [
					'top'    => '',
					'right'  => '',
					'bottom' => '',
					'left'   => '',
				],
				'desktop'      => [
					'top'    => '',
					'right'  => '',
					'bottom' => '',
					'left'   => '',
				],
				'mobile-unit'  => 'px',
				'tablet-unit'  => 'px',
				'desktop-unit' => 'px',
			],
			'primary-menu_component_margin'               => [
				'mobile'       => [
					'top'    => '',
					'right'  => '',
					'bottom' => '',
					'left'   => '',
				],
				'tablet'       => [
					'top'    => '',
					'right'  => '',
					'bottom' => '',
					'left'   => '',
				],
				'desktop'      => [
					'top'    => '',
					'right'  => '',
					'bottom' => '',
					'left'   => '',
				],
				'mobile-unit'  => 'px',
				'tablet-unit'  => 'px',
				'desktop-unit' => 'px',
			],
			'primary-menu_color'                          => '#2a2a68',
			'primary-menu_active_color'                   => '#1aaaa0',
			'primary-menu_hover_color'                    => '#1aaaa0',
			'primary-menu_component_font_family'          => '',
			'header_search_responsive_component_padding'  => [
				'mobile'       => [
					'top'    => '',
					'right'  => '',
					'bottom' => '',
					'left'   => '',
				],
				'tablet'       => [
					'top'    => '',
					'right'  => '',
					'bottom' => '',
					'left'   => '',
				],
				'desktop'      => [
					'top'    => '',
					'right'  => '',
					'bottom' => '',
					'left'   => '',
				],
				'mobile-unit'  => 'px',
				'tablet-unit'  => 'px',
				'desktop-unit' => 'px',
			],
			'header_search_responsive_component_margin'   => [
				'mobile'       => [
					'top'    => '',
					'right'  => '',
					'bottom' => '',
					'left'   => '',
				],
				'tablet'       => [
					'top'    => '',
					'right'  => '',
					'bottom' => '',
					'left'   => '',
				],
				'desktop'      => [
					'top'    => '',
					'right'  => '',
					'bottom' => '',
					'left'   => '',
				],
				'mobile-unit'  => 'px',
				'tablet-unit'  => 'px',
				'desktop-unit' => 'px',
			],
			'hfg_header_layout_main_height'               => [
				'mobile'  => 0,
				'tablet'  => 0,
				'desktop' => 0,
			],
			'hfg_header_layout_main_new_text_color'       => '',
			'primary-menu_style'                          => 'style-plain',
			'logo_display'                                => 'default',
			'logo_show_tagline'                           => 0,
			'logo_show_title'                             => 1,
			'logo_max_width'                              => [
				'mobile'  => 36,
				'tablet'  => 36,
				'desktop' => 200,
			],
			'logo_color'                                  => '#242424',
			'logo_component_padding'                      => [
				'mobile'       => [
					'top'    => '',
					'right'  => '',
					'bottom' => '',
					'left'   => '',
				],
				'tablet'       => [
					'top'    => '',
					'right'  => '',
					'bottom' => '',
					'left'   => '',
				],
				'desktop'      => [
					'top'    => '',
					'right'  => '',
					'bottom' => '',
					'left'   => '',
				],
				'mobile-unit'  => 'px',
				'tablet-unit'  => 'px',
				'desktop-unit' => 'px',
			],
			'logo_component_margin'                       => [
				'mobile'       => [
					'top'    => '',
					'right'  => '',
					'bottom' => '',
					'left'   => '',
				],
				'tablet'       => [
					'top'    => '',
					'right'  => '',
					'bottom' => '',
					'left'   => '',
				],
				'desktop'      => [
					'top'    => '',
					'right'  => '',
					'bottom' => '',
					'left'   => '',
				],
				'mobile-unit'  => 'px',
				'tablet-unit'  => 'px',
				'desktop-unit' => 'px',
			],
			'header_search_responsive_color'              => '#2a2a68',
			'header_search_responsive_hover_color'        => '#1aaaa0',
			'nav-icon_menu_label'                         => '',
			'nav-icon_component_padding'                  => [
				'mobile'       => [
					'top'    => '8',
					'right'  => '8',
					'bottom' => '8',
					'left'   => '8',
				],
				'tablet'       => [
					'top'    => '8',
					'right'  => '8',
					'bottom' => '8',
					'left'   => '8',
				],
				'desktop'      => [
					'top'    => '',
					'right'  => '',
					'bottom' => '',
					'left'   => '',
				],
				'mobile-unit'  => 'px',
				'tablet-unit'  => 'px',
				'desktop-unit' => 'px',
			],
			'nav-icon_component_margin'                   => [
				'mobile'       => [
					'top'    => '',
					'right'  => '',
					'bottom' => '',
					'left'   => '',
				],
				'tablet'       => [
					'top'    => '',
					'right'  => '',
					'bottom' => '',
					'left'   => '',
				],
				'desktop'      => [
					'top'    => '',
					'right'  => '',
					'bottom' => '',
					'left'   => '',
				],
				'mobile-unit'  => 'px',
				'tablet-unit'  => 'px',
				'desktop-unit' => 'px',
			],
			'nav-icon_button_appearance'                  => [
				'type'         => 'fill',
				'borderRadius' => [
					'top'    => '',
					'right'  => '',
					'bottom' => '',
					'left'   => '',
				],
				'borderWidth'  => [
					'top'    => '',
					'right'  => '',
					'bottom' => '',
					'left'   => '',
				],
				'background'   => '#242424',
				'text'         => '#ffffff',
			],
			'hfg_footer_layout_v2'                        => '{"desktop":{"top":{"left":[],"c-left":[],"center":[],"c-right":[],"right":[]},"bottom":{"left":[],"c-left":[],"center":[],"c-right":[],"right":[]},"main":{"left":[],"c-left":[{"id":"footer_copyright"}],"center":[],"c-right":[],"right":[]}}}',
			'hfg_footer_layout_main_height'               => '{"mobile":0,"tablet":0,"desktop":50}',
			'hfg_footer_layout_main_background'           => [
				'type'              => 'color',
				'imageUrl'          => '',
				'focusPoint'        => [
					'x' => 0.5,
					'y' => 0.5,
				],
				'colorValue'        => '#ffffff',
				'overlayColorValue' => '',
				'overlayOpacity'    => 50,
				'fixed'             => false,
				'useFeatured'       => false,
			],
			'hfg_footer_layout_main_bottom_border'        => '{"mobile":1,"tablet":1,"desktop":1}',
			'hfg_footer_layout_main_border_color'         => 'var(--border-color)',
			'footer_copyright_color'                      => '#000000',
			'footer_copyright_component_align'            => [
				'mobile'  => 'center',
				'tablet'  => 'center',
				'desktop' => 'center',
			],
			'footer_copyright_component_vertical_align'   => 'middle',
			'footer_copyright_component_margin'           => [
				'mobile'       => [
					'top'    => '-12',
					'right'  => '0',
					'bottom' => '20',
					'left'   => '0',
				],
				'tablet'       => [
					'top'    => '',
					'right'  => '',
					'bottom' => '',
					'left'   => '',
				],
				'desktop'      => [
					'top'    => '-10',
					'right'  => '0',
					'bottom' => '20',
					'left'   => '0',
				],
				'mobile-unit'  => 'px',
				'tablet-unit'  => 'px',
				'desktop-unit' => 'px',
			],
			'footer_copyright_component_typeface'         => [
				'fontSize'      => [
					'suffix'  => [
						'mobile'  => 'px',
						'tablet'  => 'px',
						'desktop' => 'px',
					],
					'vars'    => [],
					'mobile'  => '12',
					'tablet'  => '',
					'desktop' => '14',
				],
				'lineHeight'    => [
					'vars'    => [],
					'mobile'  => '',
					'tablet'  => '',
					'desktop' => '',
					'suffix'  => [
						'mobile'  => 'px',
						'tablet'  => 'px',
						'desktop' => 'px',
					],
				],
				'letterSpacing' => [
					'vars'    => [],
					'mobile'  => '',
					'tablet'  => '',
					'desktop' => '',
					'suffix'  => [
						'mobile'  => 'px',
						'tablet'  => 'px',
						'desktop' => 'px',
					],
				],
				'fontWeight'    => '300',
				'textTransform' => 'none',
			],
		];

		$theme_mods = get_option( 'theme_mods_' . get_template() );

		if ( ! empty( $theme_mods ) ) {
			foreach ( $defaults as $key => $data ) {
				if ( ! isset( $theme_mods[ $key ] ) ) {
					set_theme_mod( $key, $data );
				}
			}
		}
	}

	/**
	 * Tweak menu walker to support selective refresh.
	 *
	 * @param array $args List of arguments for navigation.
	 *
	 * @return mixed
	 */
	public function nav_walker( $args ) {
		if ( isset( $args['walker'] ) && is_string( $args['walker'] ) && class_exists( $args['walker'] ) ) {
			$args['walker'] = new $args['walker']();
		}

		return $args;
	}

}
