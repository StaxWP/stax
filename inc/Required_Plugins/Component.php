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
		$recommended_plugins = [
			[
				'name'               => 'Elementor Page Builder',
				'slug'               => 'elementor',
				'version'            => '3.5.4',
				'required'           => false,
				'force_activation'   => false,
				'force_deactivation' => false,
				'external_url'       => '',
				'description'        => 'Build your pages with an advanced Drag & Drop interface.',
			],
			[
				'name'               => 'Visibility Logic for Elementor',
				'slug'               => 'visibility-logic-elementor',
				'version'            => '2.2.3',
				'required'           => false,
				'force_activation'   => false,
				'force_deactivation' => false,
				'external_url'       => '',
				'description'        => 'Hide or show an Elementor widget based on whether a user is logged in, logged out (guest), has specific role, based on user meta, at specific dates or by the browser used.',
			],
			[
				'name'               => 'Customizer Export/Import',
				'slug'               => 'customizer-export-import',
				'required'           => false,
				'version'            => '0.9.2',
				'force_activation'   => false,
				'force_deactivation' => false,
				'external_url'       => '',
				'description'        => 'The Customizer Export/Import plugin allows you to export or import your WordPress customizer settings from directly within the customizer interface.',
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
				'installing'                      => esc_html__( 'Installing Plugin: %s', 'stax' ),
				// %1$s = plugin name
				'oops'                            => esc_html__( 'Something went wrong with the plugin API.', 'stax' ),
				'notice_can_install_required'     => _n_noop( 'This theme requires the following plugin: %1$s.', 'This theme requires the following plugins: %1$s.', 'stax' ),
				// %1$s = plugin name(s)
				'notice_can_install_recommended'  => _n_noop( 'This theme recommends the following plugin: %1$s.', 'This theme recommends the following plugins: %1$s.', 'stax' ),
				// %1$s = plugin name(s)
				'notice_cannot_install'           => _n_noop( 'Sorry, but you do not have the correct permissions to install the %s plugin. Contact the administrator of this site for help on getting the plugin installed.', 'Sorry, but you do not have the correct permissions to install the %s plugins. Contact the administrator of this site for help on getting the plugins installed.', 'stax' ),
				// %1$s = plugin name(s)
				'notice_can_activate_required'    => _n_noop( 'The following required plugin is currently inactive: %1$s.', 'The following required plugins are currently inactive: %1$s.', 'stax' ),
				// %1$s = plugin name(s)
				'notice_can_activate_recommended' => _n_noop( 'The following recommended plugin is currently inactive: %1$s.', 'The following recommended plugins are currently inactive: %1$s.', 'stax' ),
				// %1$s = plugin name(s)
				'notice_cannot_activate'          => _n_noop( 'Sorry, but you do not have the correct permissions to activate the %s plugin. Contact the administrator of this site for help on getting the plugin activated.', 'Sorry, but you do not have the correct permissions to activate the %s plugins. Contact the administrator of this site for help on getting the plugins activated.', 'stax' ),
				// %1$s = plugin name(s)
				'notice_ask_to_update'            => _n_noop( 'The following plugin needs to be updated to its latest version to ensure maximum compatibility with this theme: %1$s.', 'The following plugins need to be updated to their latest version to ensure maximum compatibility with this theme: %1$s.', 'stax' ),
				// %1$s = plugin name(s)
				'notice_cannot_update'            => _n_noop( 'Sorry, but you do not have the correct permissions to update the %s plugin. Contact the administrator of this site for help on getting the plugin updated.', 'Sorry, but you do not have the correct permissions to update the %s plugins. Contact the administrator of this site for help on getting the plugins updated.', 'stax' ),
				// %1$s = plugin name(s)
				'install_link'                    => _n_noop( 'Begin installing plugin', 'Begin installing plugins', 'stax' ),
				'activate_link'                   => _n_noop( 'Activate installed plugin', 'Activate installed plugins', 'stax' ),
				'return'                          => esc_html__( 'Return to Required Plugins Installer', 'stax' ),
				'plugin_activated'                => esc_html__( 'Plugin activated successfully.', 'stax' ),
				'complete'                        => esc_html__( 'All plugins installed and activated successfully. %s', 'stax' ),
				// %1$s = dashboard link
			],
		];

		tgmpa( $this->tgm_plugins, $config );
	}

}
