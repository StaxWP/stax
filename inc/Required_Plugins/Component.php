<?php
/**
 * Stax\Base_Support\Component class
 *
 * @package stax
 */

namespace Stax\Required_Plugins;

use Stax\Component_Interface;
use function Stax\stax;


/**
 * Class for adding basic theme support, most of which is mandatory to be implemented by all themes.
 *
 * Exposes template tags:
 * * `stax()->get_version()`
 * * `stax()->get_asset_version( string $filepath )`
 */
class Component implements Component_Interface {

	private $tgm_plugins;

	/**
	 * Gets the unique identifier for the theme component.
	 *
	 * @return string Component slug.
	 */
	public function get_slug() {
		return 'required-plugins';
	}

	/**
	 * Adds the action and filter hooks to integrate with WordPress.
	 */
	public function initialize() {

		$this->tgm_plugins = $this->get_recommended_plugins();
		/* Load required plugins library */
		require_once __dir__ . '/lib/class-tgm-plugin-activation.php';
		add_action( 'tgmpa_register', [ $this, 'required_plugins' ] );
	}

	/**
	 * @return array
	 */
	private function get_recommended_plugins() {
		// Optional, free plugins from WordPress.org. Nothing is installed without the admin clicking.
		$recommended_plugins = [
			[
				'name'        => 'Elementor',
				'slug'        => 'elementor',
				'required'    => false,
				'description' => __( 'Build pages with a drag and drop editor. Stax registers the Elementor Pro Theme Builder locations.', 'stax' ),
			],
			[
				'name'        => 'Visibility Logic for Elementor',
				'slug'        => 'visibility-logic-elementor',
				'required'    => false,
				'description' => __( 'Show or hide Elementor widgets and sections by login status, user role, date and more.', 'stax' ),
			],
			[
				'name'        => 'BuddyBuilder',
				'slug'        => 'stax-buddy-builder',
				'required'    => false,
				'description' => __( 'Design BuddyPress and BuddyBoss community pages with Elementor.', 'stax' ),
			],
			[
				'name'        => 'Customizer Export/Import',
				'slug'        => 'customizer-export-import',
				'required'    => false,
				'description' => __( 'Export or import your Customizer settings.', 'stax' ),
			],
		];

		return apply_filters( 'stax-recommended-plugins', $recommended_plugins );
	}

	/**
	 * @param $name
	 *
	 * @return string
	 */
	private function get_plugin_url( $name ) {

		$api_url = add_query_arg( 'action', 'download', $this->api_url );
		$api_url = add_query_arg( 'slug', $name . '.zip', $api_url );

		return $api_url;
	}


	/**
	 * Method to register TGMPA plugins
	 */
	public function required_plugins() {
		/**
		 * Array of configuration settings. Amend each line as needed.
		 * If you want the default strings to be available under your own theme domain,
		 * leave the strings uncommented.
		 * Some of the strings are added into a sprintf, so see the comments at the
		 * end of each line for what each argument will be.
		 */
		$config = [
			'id'           => 'tgmpa-stax-' . stax()->get_version(),
			'default_path' => '',                           // Default absolute path to pre-packaged plugins
			// 'parent_menu_slug'  => 'themes.php',         // Default parent menu slug
			// 'parent_url_slug'   => 'themes.php',         // Default parent URL slug
			'menu'         => 'install-required-plugins',   // Menu slug
			'has_notices'  => true,                         // Show admin notices or not
			'message'      => '',               // Message to output right before the plugins table
			'strings'      => [
				'page_title'                      => esc_html__( 'Install Required Plugins', 'stax' ),
				'menu_title'                      => esc_html__( 'Install Plugins', 'stax' ),
				/* translators: %s: plugin name. */
				'installing'                      => esc_html__( 'Installing Plugin: %s', 'stax' ),
				'oops'                            => esc_html__( 'Something went wrong with the plugin API.', 'stax' ),
				/* translators: %1$s: plugin name(s). */
				'notice_can_install_required'     => _n_noop( 'This theme requires the following plugin: %1$s.', 'This theme requires the following plugins: %1$s.', 'stax' ),
				/* translators: %1$s: plugin name(s). */
				'notice_can_install_recommended'  => _n_noop( 'This theme recommends the following plugin: %1$s.', 'This theme recommends the following plugins: %1$s.', 'stax' ),
				/* translators: %s: plugin name(s). */
				'notice_cannot_install'           => _n_noop( 'Sorry, but you do not have the correct permissions to install the %s plugin. Contact the administrator of this site for help on getting the plugin installed.', 'Sorry, but you do not have the correct permissions to install the %s plugins. Contact the administrator of this site for help on getting the plugins installed.', 'stax' ),
				/* translators: %1$s: plugin name(s). */
				'notice_can_activate_required'    => _n_noop( 'The following required plugin is currently inactive: %1$s.', 'The following required plugins are currently inactive: %1$s.', 'stax' ),
				/* translators: %1$s: plugin name(s). */
				'notice_can_activate_recommended' => _n_noop( 'The following recommended plugin is currently inactive: %1$s.', 'The following recommended plugins are currently inactive: %1$s.', 'stax' ),
				/* translators: %s: plugin name(s). */
				'notice_cannot_activate'          => _n_noop( 'Sorry, but you do not have the correct permissions to activate the %s plugin. Contact the administrator of this site for help on getting the plugin activated.', 'Sorry, but you do not have the correct permissions to activate the %s plugins. Contact the administrator of this site for help on getting the plugins activated.', 'stax' ),
				/* translators: %1$s: plugin name(s). */
				'notice_ask_to_update'            => _n_noop( 'The following plugin needs to be updated to its latest version to ensure maximum compatibility with this theme: %1$s.', 'The following plugins need to be updated to their latest version to ensure maximum compatibility with this theme: %1$s.', 'stax' ),
				/* translators: %s: plugin name(s). */
				'notice_cannot_update'            => _n_noop( 'Sorry, but you do not have the correct permissions to update the %s plugin. Contact the administrator of this site for help on getting the plugin updated.', 'Sorry, but you do not have the correct permissions to update the %s plugins. Contact the administrator of this site for help on getting the plugins updated.', 'stax' ),
				'install_link'                    => _n_noop( 'Begin installing plugin', 'Begin installing plugins', 'stax' ),
				'activate_link'                   => _n_noop( 'Activate installed plugin', 'Activate installed plugins', 'stax' ),
				'return'                          => esc_html__( 'Return to Required Plugins Installer', 'stax' ),
				'plugin_activated'                => esc_html__( 'Plugin activated successfully.', 'stax' ),
				/* translators: %s: dashboard link. */
				'complete'                        => esc_html__( 'All plugins installed and activated successfully. %s', 'stax' ),
			],
		];

		tgmpa( $this->tgm_plugins, $config );
	}

}
