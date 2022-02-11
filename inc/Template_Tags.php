<?php
/**
 * Stax\Template_Tags class
 *
 * @package stax
 */

namespace Stax;

use InvalidArgumentException;
use BadMethodCallException;
use RuntimeException;

/**
 * Template tags entry point.
 *
 * This class provides access to all available template tag methods.
 *
 * Its instance can be accessed through `stax()`. For example, if there is a template tag called `posted_on`, it can
 * be accessed via `stax()->posted_on()`.
 *
 * @method string|Base_Support\Component get_version()
 * @method string|Base_Support\Component get_asset_version( string $filepath )
 * @method void|Base_Support\Component get_breadcrumbs()
 * @method void|Base_Support\Component get_categories_nav( $animation = true )
 *
 * @method void|Comments\Component the_comments( array $args = [] )
 *
 * @method string|array|Config\Component    get_option( $option = false, $default = false, $filters = true )
 * @method bool|Config\Component            has_media( $post_id = null )
 * @method array|Config\Component           get_settings()
 * @method array|Load_Template\Component    get_template_part( $slug, $name = '', $vars = [] )
 * @method array|Config\Component           get_setting( $id )
 * @method void|Config\Component            add_setting( $id, $data )
 * @method string|array|Config\Component    get_config( $name )
 * @method void|Config\Component            set_config( $name, $value )
 * @method bool|Config\Component            is_active( $code = '', $force = true, $with_error = false )
 * @method void|Custom_Logo\Component       the_logo()
 * @method string|Custom_Logo\Component     get_logo()
 * @method void|Sidebars\Component          main_section_class()
 * @method void|Sidebars\Component          force_main_layout( $layout = '' )
 * @method void|Sidebars\Component          force_main_container_size( $size = '' )
 */
class Template_Tags {

	/**
	 * Associative array of all available template tags.
	 *
	 * Method names are the keys, their callback information the values.
	 *
	 * @var array
	 */
	protected $template_tags = [];

	/**
	 * Constructor.
	 *
	 * Sets the theme components.
	 *
	 * @param array $components Optional. List of theme templating components. Each of these must implement the
	 *                          Templating_Component_Interface interface.
	 *
	 * @throws InvalidArgumentException Thrown if one of the $components does not implement
	 *                                  Templating_Component_Interface.
	 */
	public function __construct( array $components = [] ) {

		// Set the template tags for the components.
		foreach ( $components as $component ) {

			// Bail if a templating component is invalid.
			if ( ! $component instanceof Templating_Component_Interface ) {
				throw new InvalidArgumentException(
					sprintf(
					/* translators: 1: classname/type of the variable, 2: interface name */
						__( 'The theme templating component %1$s does not implement the %2$s interface.', 'stax' ),
						gettype( $component ),
						Templating_Component_Interface::class
					)
				);
			}

			$this->set_template_tags( $component );
		}
	}

	/**
	 * Magic call method.
	 *
	 * Will proxy to the template tag $method, unless it is not available, in which case an exception will be thrown.
	 *
	 * @param string $method Template tag name.
	 * @param array  $args Template tag arguments.
	 *
	 * @return mixed Template tag result, or null if template tag only outputs markup.
	 *
	 * @throws BadMethodCallException Thrown if the template tag does not exist.
	 */
	public function __call( $method, array $args ) {
		if ( ! isset( $this->template_tags[ $method ] ) ) {
			throw new BadMethodCallException(
				sprintf(
				/* translators: %s: template tag name */
					__( 'The template tag %s does not exist.', 'stax' ),
					'stax()->' . $method . '()'
				)
			);
		}

		return call_user_func_array( $this->template_tags[ $method ]['callback'], $args );
	}

	/**
	 * Sets template tags for a given theme templating component.
	 *
	 * @param Templating_Component_Interface $component Theme templating component.
	 *
	 * @throws InvalidArgumentException Thrown when one of the template tags is invalid.
	 * @throws RuntimeException         Thrown when one of the template tags conflicts with an existing one.
	 */
	protected function set_template_tags( Templating_Component_Interface $component ) {
		$tags = $component->template_tags();

		foreach ( $tags as $method_name => $callback ) {
			if ( is_callable( $callback ) ) {
				$callback = [ 'callback' => $callback ];
			}

			if ( ! is_array( $callback ) || ! isset( $callback['callback'] ) ) {
				throw new InvalidArgumentException(
					sprintf(
					/* translators: 1: template tag method name, 2: component class name */
						__( 'The template tag method %1$s registered by theme component %2$s must either be a callable or an array.', 'stax' ),
						$method_name,
						get_class( $component )
					)
				);
			}

			if ( isset( $this->template_tags[ $method_name ] ) ) {
				throw new RuntimeException(
					sprintf(
					/* translators: 1: template tag method name, 2: component class name */
						__( 'The template tag method %1$s registered by theme component %2$s conflicts with an already registered template tag of the same name.', 'stax' ),
						$method_name,
						get_class( $component )
					)
				);
			}

			$this->template_tags[ $method_name ] = $callback;
		}
	}
}
