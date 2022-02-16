<?php
/**
 * Stax\Customizer\Component class
 *
 * @package stax
 */

namespace Stax\Customizer;

use Stax\Component_Interface;
use Stax\Customizer\Core\Loader;
use function Stax\stax;

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

		add_action( 'after_setup_theme', [ $this, 'add_default_starter_content' ] );

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


		add_filter(
			'hfg_settings_schema',
			function () {
				return stax()->get_theme_default_mods();
			},
			101
		);
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

	/**
	 * Add default starter content
	 *
	 * @return void
	 */
	public function add_default_starter_content() {
		add_theme_support( 'starter-content', $this->set_starter_content() );
	}

	/**
	 * Set starter content
	 *
	 * @return array
	 */
	public function set_starter_content() {
		$content = [
			'theme_mods' => stax()->get_theme_default_mods(),
		];

		return $content;
	}

}
