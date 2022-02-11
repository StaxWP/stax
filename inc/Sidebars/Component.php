<?php
/**
 * Stax\Sidebars\Component class
 *
 * @package stax
 */

namespace Stax\Sidebars;

use Stax\Customizer\Config;
use Stax\Component_Interface;
use Stax\Templating_Component_Interface;
use function add_action;
use function add_filter;
use function register_sidebar;
use function is_active_sidebar;
use function dynamic_sidebar;
use function Stax\stax;

/**
 * Class for managing sidebars.
 *
 * Exposes template tags:
 * * `stax()->is_primary_sidebar_active()`
 * * `stax()->display_primary_sidebar()`
 *
 * @link https://developer.wordpress.org/themes/functionality/sidebars/
 */
class Component implements Component_Interface, Templating_Component_Interface {

	const PRIMARY_SIDEBAR_SLUG   = 'sidebar-1';
	const SECONDARY_SIDEBAR_SLUG = 'sidebar-2';
	const FOOTER_COLUMN_1_SLUG   = 'footer-one-widgets';
	const FOOTER_COLUMN_2_SLUG   = 'footer-two-widgets';
	const FOOTER_COLUMN_3_SLUG   = 'footer-three-widgets';
	const FOOTER_COLUMN_4_SLUG   = 'footer-four-widgets';

	/**
	 * Gets the unique identifier for the theme component.
	 *
	 * @return string Component slug.
	 */
	public function get_slug() {
		return 'sidebars';
	}

	/**
	 * Adds the action and filter hooks to integrate with WordPress.
	 */
	public function initialize() {
		add_action( 'widgets_init', [ $this, 'action_register_sidebars' ] );
		add_action( 'widgets_init', [ $this, 'action_register_footer_columns' ] );
		add_filter( 'body_class', [ $this, 'filter_body_classes' ] );
		add_filter( 'theme_post_templates', [ $this, 'add_post_templates' ], 10 );
	}

	/**
	 * Gets template tags to expose as methods on the Template_Tags class instance, accessible through `stax()`.
	 *
	 * @return array Associative array of $method_name => $callback_info pairs. Each $callback_info must either be
	 *               a callable or an array with key 'callable'. This approach is used to reserve the possibility of
	 *               adding support for further arguments in the future.
	 */
	public function template_tags() {
		return [
			'is_primary_sidebar_active'   => [ $this, 'is_primary_sidebar_active' ],
			'is_secondary_sidebar_active' => [ $this, 'is_secondary_sidebar_active' ],
			'is_footer_column_1_active'   => [ $this, 'is_footer_column_1_active' ],
			'is_footer_column_2_active'   => [ $this, 'is_footer_column_2_active' ],
			'is_footer_column_3_active'   => [ $this, 'is_footer_column_3_active' ],
			'is_footer_column_4_active'   => [ $this, 'is_footer_column_4_active' ],
			'is_any_footer_column_active' => [ $this, 'is_any_footer_column_active' ],
			'display_primary_sidebar'     => [ $this, 'display_primary_sidebar' ],
			'display_secondary_sidebar'   => [ $this, 'display_secondary_sidebar' ],
			'display_footer_column_1'     => [ $this, 'display_footer_column_1' ],
			'display_footer_column_2'     => [ $this, 'display_footer_column_2' ],
			'display_footer_column_3'     => [ $this, 'display_footer_column_3' ],
			'display_footer_column_4'     => [ $this, 'display_footer_column_4' ],
			'main_section_class'          => [ $this, 'main_section_class' ],
			'force_main_layout'           => [ $this, 'force_main_layout' ],
			'force_main_container_size'   => [ $this, 'force_main_container_size' ],
		];
	}

	/**
	 * Registers the sidebars.
	 */
	public function action_register_sidebars() {
		register_sidebar(
			[
				'name'          => esc_html__( 'Primary Sidebar', 'stax' ),
				'id'            => static::PRIMARY_SIDEBAR_SLUG,
				'description'   => esc_html__( 'Add widgets here.', 'stax' ),
				'before_widget' => '<section id="%1$s" class="widget %2$s">',
				'after_widget'  => '</section>',
				'before_title'  => '<h3 class="widget-title"><span>',
				'after_title'   => '</span></h3>',
			]
		);
		register_sidebar(
			[
				'name'          => esc_html__( 'Secondary Sidebar', 'stax' ),
				'id'            => static::SECONDARY_SIDEBAR_SLUG,
				'description'   => esc_html__( 'Add widgets here.', 'stax' ),
				'before_widget' => '<section id="%1$s" class="widget %2$s">',
				'after_widget'  => '</section>',
				'before_title'  => '<h3 class="widget-title"><span>',
				'after_title'   => '</span></h3>',
			]
		);
	}

	/**
	 * Register footer columns
	 */
	public function action_register_footer_columns() {
		register_sidebar(
			[
				'name'          => esc_html__( 'Footer Column 1', 'stax' ),
				'id'            => self::FOOTER_COLUMN_1_SLUG,
				'description'   => esc_html__( 'Add widgets here.', 'stax' ),
				'before_widget' => '<div id="%1$s" class="widget %2$s">',
				'after_widget'  => '</div>',
				'before_title'  => '<h3 class="widget-title"><span>',
				'after_title'   => '</span></h3>',
			]
		);

		register_sidebar(
			[
				'name'          => esc_html__( 'Footer Column 2', 'stax' ),
				'id'            => self::FOOTER_COLUMN_2_SLUG,
				'description'   => esc_html__( 'Add widgets here.', 'stax' ),
				'before_widget' => '<div id="%1$s" class="widget %2$s">',
				'after_widget'  => '</div>',
				'before_title'  => '<h3 class="widget-title"><span>',
				'after_title'   => '</span></h3>',
			]
		);

		register_sidebar(
			[
				'name'          => esc_html__( 'Footer Column 3', 'stax' ),
				'id'            => self::FOOTER_COLUMN_3_SLUG,
				'description'   => esc_html__( 'Add widgets here.', 'stax' ),
				'before_widget' => '<div id="%1$s" class="widget %2$s">',
				'after_widget'  => '</div>',
				'before_title'  => '<h3 class="widget-title"><span>',
				'after_title'   => '</span></h3>',
			]
		);

		register_sidebar(
			[
				'name'          => esc_html__( 'Footer Column 4', 'stax' ),
				'id'            => self::FOOTER_COLUMN_4_SLUG,
				'description'   => esc_html__( 'Add widgets here.', 'stax' ),
				'before_widget' => '<div id="%1$s" class="widget %2$s">',
				'after_widget'  => '</div>',
				'before_title'  => '<h3 class="widget-title"><span>',
				'after_title'   => '</span></h3>',
			]
		);
	}

	/**
	 * Adds custom classes to indicate whether a sidebar is present to the array of body classes.
	 *
	 * @param array $classes Classes for the body element.
	 *
	 * @return array Filtered body classes.
	 */
	public function filter_body_classes( array $classes ) {
		if ( $this->is_primary_sidebar_active() || $this->is_secondary_sidebar_active() ) {
			global $template;

			if ( 'front-page.php' !== basename( $template ) ) {
				$classes[] = 'has-sidebar';
			}
		}

		return $classes;
	}

	/**
	 * Checks whether the primary sidebar is active.
	 *
	 * @return bool True if the primary sidebar is active, false otherwise.
	 */
	public function is_primary_sidebar_active() {

		if ( ! (bool) is_active_sidebar( static::PRIMARY_SIDEBAR_SLUG ) ) {
			return false;
		}

		if ( $this->get_layout() === 'no-side' ) {
			return false;
		}

		return true;
	}

	/**
	 * Checks whether the secondary sidebar is active.
	 *
	 * @return bool True if the primary sidebar is active, false otherwise.
	 */
	public function is_secondary_sidebar_active() {

		if ( ! (bool) is_active_sidebar( static::SECONDARY_SIDEBAR_SLUG ) ) {
			return false;
		}

		if ( in_array( $this->get_layout(), [ 'srr', 'sll', 'slr' ] ) ) {
			return true;
		}

		return false;
	}

	/**
	 * Get site layout
	 *
	 * @return array|string|\Stax\Config\Component
	 */
	private function get_layout() {
		if ( is_single() ) {
			$layout = stax()->get_option( Config::OPTION_LAYOUT_POST );
		} elseif ( is_archive() || is_search() || is_home() ) {
			$layout = stax()->get_option( Config::OPTION_LAYOUT_ARCHIVE );
		} else {
			$layout = stax()->get_option( Config::OPTION_LAYOUT_GENERAL );
		}

		return $layout;
	}

	/**
	 * Get content layout
	 *
	 * @return array|string|\Stax\Config\Component
	 */
	private function get_content_size() {
		if ( is_single() ) {
			$size = stax()->get_option( Config::OPTION_LAYOUT_POST_CONTAINER );
		} elseif ( is_archive() || is_search() || is_home() ) {
			$size = stax()->get_option( Config::OPTION_LAYOUT_ARCHIVE_CONTAINER );
		} else {
			$size = stax()->get_option( Config::OPTION_LAYOUT_GENERAL_CONTAINER );
		}

		return $size;
	}

	/**
	 * Checks whether the footer column 1 is active.
	 *
	 * @return bool True if the footer column 1 is active, false otherwise.
	 */
	public function is_footer_column_1_active() {

		if ( ! (bool) is_active_sidebar( static::FOOTER_COLUMN_1_SLUG ) ) {
			return false;
		}

		return true;
	}

	/**
	 * Checks whether the footer column 2 is active.
	 *
	 * @return bool True if the footer column 2 is active, false otherwise.
	 */
	public function is_footer_column_2_active() {

		if ( ! (bool) is_active_sidebar( static::FOOTER_COLUMN_2_SLUG ) ) {
			return false;
		}

		return true;
	}

	/**
	 * Checks whether the footer column 3 is active.
	 *
	 * @return bool True if the footer column 3 is active, false otherwise.
	 */
	public function is_footer_column_3_active() {

		if ( ! (bool) is_active_sidebar( static::FOOTER_COLUMN_3_SLUG ) ) {
			return false;
		}

		return true;
	}

	/**
	 * Checks whether the footer column 4 is active.
	 *
	 * @return bool True if the footer column 4 is active, false otherwise.
	 */
	public function is_footer_column_4_active() {

		if ( ! (bool) is_active_sidebar( static::FOOTER_COLUMN_4_SLUG ) ) {
			return false;
		}

		return true;
	}

	/**
	 * Check if there's at any footer column active
	 *
	 * @return bool
	 */
	public function is_any_footer_column_active() {
		return $this->is_footer_column_1_active() ||
			   $this->is_footer_column_2_active() ||
			   $this->is_footer_column_3_active() ||
			   $this->is_footer_column_4_active();
	}

	/**
	 * Displays the primary sidebar.
	 */
	public function display_primary_sidebar() {

		// Add custom sidebar plugin support.
		if ( function_exists( 'generated_dynamic_sidebar' ) ) {
			generated_dynamic_sidebar( static::PRIMARY_SIDEBAR_SLUG );
		} else {
			dynamic_sidebar( static::PRIMARY_SIDEBAR_SLUG );
		}
	}

	/**
	 * Displays the primary sidebar.
	 */
	public function display_secondary_sidebar() {
		dynamic_sidebar( static::SECONDARY_SIDEBAR_SLUG );
	}

	/**
	 * Displays footer column 1.
	 */
	public function display_footer_column_1() {
		dynamic_sidebar( static::FOOTER_COLUMN_1_SLUG );
	}

	/**
	 * Displays footer column 2.
	 */
	public function display_footer_column_2() {
		dynamic_sidebar( static::FOOTER_COLUMN_2_SLUG );
	}

	/**
	 * Displays footer column 3.
	 */
	public function display_footer_column_3() {
		dynamic_sidebar( static::FOOTER_COLUMN_3_SLUG );
	}

	/**
	 * Displays footer column 4.
	 */
	public function display_footer_column_4() {
		dynamic_sidebar( static::FOOTER_COLUMN_4_SLUG );
	}

	/**
	 * Display the classes for body wrap element
	 *
	 * @param string|array $class One or more classes to add to the class list.
	 *
	 * @since 1.0
	 */
	function main_section_class( $class = '' ) {
		// Separates classes with a single space, collates classes for body element
		echo 'class="' . esc_attr( join( ' ', $this->get_main_section_class( $class ) ) ) . '"';
	}

	/**
	 * Retrieve the classes for the body wrap element as an array.
	 *
	 * @param string|array $class One or more classes to add to the class list.
	 *
	 * @return array Array of classes.
	 * @since 1.0
	 */
	function get_main_section_class( $class = '' ) {
		$classes           = [ 'svq-body-section' ];
		$classes['layout'] = 'svq-tpl-' . $this->get_layout();
		$classes['width']  = 'svq-content--' . $this->get_content_size();

		if ( ! empty( $class ) ) {
			if ( ! is_array( $class ) ) {
				$class = preg_split( '#\s+#', $class );
			}
			$classes = array_merge( $classes, $class );
		} else {
			// Ensure that we always coerce class to being an array.
			$class = [];
		}

		$classes = apply_filters( 'stax_main_section_classes', $classes, $class );

		return array_unique( $classes );
	}

	/**
	 * Force main layout
	 *
	 * @param $layout
	 */
	public function force_main_layout( $layout ) {
		/* Allow Human readable settings */
		if ( $layout === 'full' ) {
			$layout = 'no-side';
		} elseif ( $layout === 'left' ) {
			$layout = 'sl';
		} elseif ( $layout === 'right' ) {
			$layout = 'sr';
		} elseif ( $layout === 'two-right' ) {
			$layout = 'srr';
		} elseif ( $layout === 'two-left' ) {
			$layout = 'sll';
		} elseif ( $layout === 'left-right' ) {
			$layout = 'slr';
		}

		add_filter(
			'stax_option',
			static function ( $value, $option ) use ( $layout ) {
				$option_name = Config::OPTION_LAYOUT_GENERAL;
				if ( is_single() ) {
					$option_name = Config::OPTION_LAYOUT_POST;
				} elseif ( is_archive() || is_search() ) {
					$option_name = Config::OPTION_LAYOUT_ARCHIVE;
				}

				if ( $option_name === $option ) {
					return $layout;
				}

				return $value;
			},
			10,
			2
		);
	}

	/**
	 * Force container size
	 *
	 * @param $size
	 */
	public function force_main_container_size( $size ) {
		add_filter(
			'stax_option',
			static function ( $value, $option ) use ( $size ) {
				$option_name = Config::OPTION_LAYOUT_GENERAL_CONTAINER;
				if ( is_single() ) {
					$option_name = Config::OPTION_LAYOUT_POST_CONTAINER;
				} elseif ( is_archive() || is_search() ) {
					$option_name = Config::OPTION_LAYOUT_ARCHIVE_CONTAINER;
				}

				if ( $option_name === $option ) {
					return $size;
				}

				return $value;
			},
			10,
			2
		);
	}

	/**
	 * Add page templates
	 *
	 * @param array $post_templates
	 *
	 * @return array
	 */
	function add_post_templates( $post_templates = [] ) {
		return [
			'page-templates/full-large.php'          => 'Full Width - Large container',
			'page-templates/full-small.php'          => 'Full Width - Small container',
			'page-templates/left-right-sidebars.php' => 'Left & Right Sidebars',
			'page-templates/left-sidebar.php'        => 'Left Sidebar',
			'page-templates/right-sidebar.php'       => 'Right Sidebar',
			'page-templates/two-right-sidebars.php'  => 'Two Right Sidebars',
			'page-templates/two-left-sidebars.php'   => 'Two Left Sidebars',
		] + $post_templates;
	}

}
