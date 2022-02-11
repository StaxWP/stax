<?php

namespace Stax\Panel;

if ( ! defined( 'ABSPATH' ) ) {
	return;
}

use function Stax\stax;

/**
 * Class Addons_Manager
 *
 * @package Stax\Panel
 */
class Addons_Manager {

	/**
	 * @var Addons_Manager The single instance of the class
	 * @since 1.0.0
	 */
	protected static $_instance = null;

	/**
	 * @var array
	 */
	public $plugins = [];

	/**
	 * @var \TGM_Plugin_Activation Instance
	 */
	public $tgmpa;

	/**
	 * Main Addons_Manager Instance
	 *
	 * Ensures only one instance of Addons_Manager is loaded or can be loaded.
	 *
	 * @return Addons_Manager - Main instance
	 * @see Addons_Manager()
	 * @since 1.0.0
	 * @static
	 */
	public static function instance() {
		if ( self::$_instance === null ) {
			self::$_instance = new self();
		}

		return self::$_instance;
	}

	/**
	 * Main class constructor
	 */
	public function __construct() {
		add_action( 'init', [ $this, 'populate_plugins' ] );
		add_action( 'admin_init', [ $this, 'activate_one_plugin' ] );

		// Register Ajax actions
		add_action( 'wp_ajax_sq_do_plugin_action', [ $this, 'do_plugin_action' ] );

		do_action( 'stax_addons_manager_init' );

		add_filter( 'tgmpa_load', [ $this, 'tgmpa_load_hook' ] );
	}

	public function populate_plugins() {

		$this->tgmpa = \TGM_Plugin_Activation::get_instance();

		$this->tgmpa->populate_file_path();

		$this->plugins = $this->tgmpa->plugins;
	}

	public function tgmpa_load_hook() {
		return is_admin();
	}

	public function do_plugin_action() {
		check_ajax_referer( 'sq_plugins_nonce', 'security' );

		$action = ! empty( $_POST['plugin_action'] ) ? $_POST['plugin_action'] : false;
		$slug   = ! empty( $_POST['slug'] ) ? $_POST['slug'] : false;

		// Perform plugin actions here
		switch ( $action ) {
			case 'enable_plugin':
				$this->do_plugin_activate( $slug );
				break;
			case 'install_plugin':
				$this->do_plugin_install( $slug );
				break;
			case 'disable_plugin':
				$this->do_plugin_deactivate( $slug );
				break;
			case 'update_plugin':
				$this->do_plugin_update( $slug );
				break;
			case 'enable_child_theme':
				$this->enable_child_theme( $slug );
				break;
			case 'install_theme':
				$this->install_child_theme( $slug );
				break;
			case 'activate_license':
				wp_send_json_success(
					[
						'redirect' => admin_url( '/themes.php?page=stax' ),
					]
				);
				break;
			default:
				// code...
				break;
		}

	}

	/**
	 * Performs the plugin update
	 *
	 * @param string $slug [description]
	 */
	public function do_plugin_update( $slug ) {
		$status = $this->get_plugin_status( $slug );

		$active = false;
		if ( $this->check_plugin_active( $slug ) ) {
			$active = true;
		}

		if ( empty( $this->plugins[ $slug ] ) ) {
			$status['error'] = 'We have no data about this plugin.';
			wp_send_json_error( $status );
		}

		if ( $this->does_plugin_have_update( $slug ) ) {

			if ( ! class_exists( '\Plugin_Upgrader', false ) ) {
				require_once ABSPATH . 'wp-admin/includes/class-wp-upgrader.php';
			}

			$upgrader = new \Plugin_Upgrader( new \Automatic_Upgrader_Skin() );
			// Inject our info into the update transient.
			$source                       = $this->get_download_url( $slug );
			$to_inject                    = [ $slug => $this->plugins[ $slug ] ];
			$to_inject[ $slug ]['source'] = $source;
			$this->inject_update_info( $to_inject );
			$result = $upgrader->upgrade( $this->plugins[ $slug ]['file_path'] );

			if ( is_wp_error( $result ) ) {
				$status['error'] = $result->get_error_message();
				wp_send_json_error( $status );
			}

			if ( $active === true ) {
				$this->tgmpa->populate_file_path( $slug );
				$result = activate_plugin( $this->plugins[ $slug ]['file_path'] );
				if ( is_wp_error( $result ) ) {
					$status['error'] = wp_kses_post( $result->get_error_message() );
				}
			}

			// Return the status of the plugin
			$status = $this->get_plugin_status( $slug );
			wp_send_json_success( $status );
		}

		$status['error'] = 'The plugin does not have an update.';
		wp_send_json_error( $status );
	}

	public function activate_one_plugin() {
		if ( isset( $_GET['svq_action'] ) && $_GET['svq_action'] == 'activate_plugin' ) {
			if ( ! current_user_can( 'manage_options' ) ) {
				return;
			}

			if ( ! isset( $_GET['svq_nonce'] ) || ! wp_verify_nonce( $_GET['svq_nonce'], 'activate_plugin' ) ) {
				return;
			}

			$plugin = $_GET['plugin'];

			if ( ! $this->is_plugin_installed( $plugin ) ) {
				$install = $this->do_plugin_install( $plugin, false );
			}

			if ( ! $this->check_plugin_active( $plugin ) ) {
				$activate = $this->do_plugin_activate( $plugin, false );
			}

			if ( isset( $_GET['redirect'] ) ) {
				wp_safe_redirect( $_GET['redirect'] );
			} else {
				wp_safe_redirect( $this->get_full_url() );
			}
		}
	}

	/**
	 * Get the current page url
	 *
	 * @return string
	 */
	public function get_full_url() {
		global $wp;

		return home_url( $wp->request );
	}

	/**
	 * Enable a child theme
	 *
	 * @param string $slug The slug used in the addons config file for the child theme
	 *
	 * @return string A json formatted value
	 */
	public function enable_child_theme( $slug ) {
		$status = $this->get_plugin_status( $slug );

		// Get all installed themes
		$current_installed_themes = wp_get_themes();
		// Get the themes currently installed
		$active_theme      = wp_get_theme();
		$theme_folder_name = $active_theme->get_template();

		$child_theme = false;

		if ( is_array( $current_installed_themes ) ) {
			foreach ( $current_installed_themes as $key => $theme_obj ) {
				if ( $theme_obj->get( 'Template' ) === $theme_folder_name ) {
					$child_theme = $theme_obj;
				}
			}
		}

		if ( $child_theme !== false ) {
			switch_theme( $child_theme->get_stylesheet() );
			$status = $this->get_plugin_status( $slug );
		}

		wp_send_json_success( $status );
	}

	public function install_child_theme( $slug ) {
		if ( empty( $this->plugins[ $slug ] ) ) {
			wp_send_json_error( [ 'error' => 'We don\'t know anything about this theme' ] );
		}

		$url    = $this->plugins[ $slug ]['source'];
		$status = $this->get_plugin_status( $slug );

		if ( ! current_user_can( 'install_themes' ) ) {
			$status['error'] = 'You don\'t have permissions to install install_themes';
			wp_send_json_error( $status );
		}

		if ( ! class_exists( '\Theme_Upgrader', false ) ) {
			require_once ABSPATH . 'wp-admin/includes/class-wp-upgrader.php';
		}

		$skin     = new \Automatic_Upgrader_Skin();
		$upgrader = new \Theme_Upgrader( $skin );
		$result   = $upgrader->install( $url );

		// There is a bug in WP where the install method can return null in case the folder already exists
		// see https://core.trac.wordpress.org/ticket/27365
		if ( $result === null && ! empty( $skin->result ) ) {
			$result = $skin->result;
		}

		if ( is_wp_error( $skin->result ) ) {
			$status['error'] = $result->get_error_message();
			wp_send_json_error( $status );
		}

		$status = $this->get_plugin_status( $slug );
		wp_send_json_success( $status );
	}

	/**
	 * Will check if a child theme is installed for the current theme
	 *
	 * @return boolean true/false if a child theme is installed or not
	 */
	public function is_child_theme_installed() {
		// Get all installed themes
		$current_installed_themes = wp_get_themes();
		// Get the themes currently installed
		$active_theme      = wp_get_theme();
		$theme_folder_name = $active_theme->get_template();

		if ( is_array( $current_installed_themes ) ) {
			foreach ( $current_installed_themes as $key => $theme_obj ) {
				if ( $theme_obj->get( 'Template' ) === $theme_folder_name ) {
					return true;
				}
			}
		}

		return false;
	}

	/**
	 * Checks if a child theme is active or not
	 *
	 * @return boolean If the child theme is in use
	 */
	public function is_child_theme_active() {
		$active_theme = wp_get_theme();
		$template     = $active_theme->get( 'Template' );

		return ! empty( $template );
	}

	public function get_addon_config( $plugin_slug ) {
		if ( ! empty( $this->plugins[ $plugin_slug ] ) ) {
			return $this->plugins[ $plugin_slug ];
		}
	}

	/**
	 * Returns the status and actions for a plugin
	 *
	 * @param string $plugin_slug The plugin slug
	 *
	 * @return array  The status and actions for the requested plugin
	 */
	public function get_plugin_status( $plugin_slug ) {
		$status        = [];
		$plugin_config = $this->get_addon_config( $plugin_slug );

		if ( isset( $plugin_config['addon_type'] ) && $plugin_config['addon_type'] === 'child_theme' ) {
			// We have a theme
			if ( $this->is_child_theme_installed() ) {
				// Check if the theme is active or not
				if ( $this->is_child_theme_active() ) {
					$status['status']      = 'svq-active svq-addons-disabled';
					$status['status_text'] = __( 'Active', 'stax' );
					$status['action_text'] = __( 'Child theme installed and active', 'stax' );
					$status['action']      = 'no_action';
				} else {
					$status['status']      = 'svq-inactive';
					$status['status_text'] = __( 'Inactive', 'stax' );
					$status['action_text'] = __( 'Activate child theme', 'stax' );
					$status['action']      = 'enable_child_theme';
				}
			} else {
				$status['status']      = 'svq-needs-install';
				$status['status_text'] = __( 'Not installed', 'stax' );
				$status['action_text'] = __( 'Install child theme', 'stax' );
				$status['action']      = 'install_theme';

				if ( ! current_user_can( 'install_themes' ) ) {
					$status['status']      = 'svq-not-installed v-addons-disabled';
					$status['action_text'] = __( 'Permissions needed to install child themes. Contact site administrator.', 'stax' );
					$status['action']      = 'contact_network_admin';
				}
			}
		} elseif ( $this->is_plugin_installed( $plugin_slug ) ) {
			if ( $this->does_plugin_have_update( $plugin_slug ) ) {
				$status['status']      = 'svq-has-update';
				$status['status_text'] = __( 'Needs update', 'stax' );
				$status['action_text'] = __( 'Update plugin', 'stax' );
				$status['action']      = 'update_plugin';
			} elseif ( $this->check_plugin_active( $plugin_slug ) ) {
				$status['status']      = 'svq-active';
				$status['status_text'] = __( 'Active', 'stax' );
				$status['action_text'] = __( 'Deactivate plugin', 'stax' );
				$status['action']      = 'disable_plugin';
			} else {
				$status['status']      = 'svq-inactive';
				$status['status_text'] = __( 'Inactive', 'stax' );
				$status['action_text'] = __( 'Activate plugin', 'stax' );
				$status['action']      = 'enable_plugin';
			}
		} else {
			$status['status']      = 'svq-not-installed';
			$status['status_text'] = __( 'Not Installed', 'stax' );
			$status['action_text'] = __( 'Install plugin', 'stax' );
			$status['action']      = 'install_plugin';

			if ( ! current_user_can( 'install_plugins' ) ) {
				$status['status']      = 'svq-not-installed svq-addons-disabled';
				$status['action_text'] = __( 'You don\'t have permission to install plugins. Contact site administrator.', 'stax' );
				$status['action']      = 'contact_network_admin';
			}
		}

		return $status;
	}

	/**
	 * Inject information into the 'update_plugins' site transient as WP checks that before running an update.
	 *
	 * @param array $plugins The plugin information for the plugins which are to be updated.
	 *
	 * @since 1.0.0
	 */
	public function inject_update_info( $plugins ) {
		$this->tgmpa->inject_update_info( $plugins );
	}

	/**
	 * Performs plugin update
	 *
	 * @return bool
	 */
	public function plugin_has_update( $slug ) {
		if ( empty( $this->plugins[ $slug ] ) ) {
			return false;
		}

		$installed_version = $this->get_installed_version( $slug );
		$minimum_version   = $this->plugins[ $slug ]['version'];

		return version_compare( $minimum_version, $installed_version, '>' );

	}

	/**
	 * Performs plugins installation
	 *
	 * @param string  $slug
	 * @param boolean $echo
	 * @param bool    $activate_too
	 *
	 * @return mixed|array|boolean
	 */
	public function do_plugin_install( $slug, $echo = true, $activate_too = true ) {
		if ( empty( $this->plugins[ $slug ] ) ) {
			return false;
		}

		$status = $this->get_plugin_status( $slug );

		if ( ! current_user_can( 'install_plugins' ) ) {
			$status['error'] = 'You don\'t have permissions to install plugins';
			if ( $echo ) {
				wp_send_json_error( $status );
			} else {
				return $status;
			}
		}

		$_GET['plugin']        = $slug;
		$_GET['tgmpa-install'] = 'install-plugin';

		$nonce                   = wp_create_nonce( 'tgmpa-install' );
		$_REQUEST['tgmpa-nonce'] = $nonce;

		if ( $activate_too === true ) {
			$this->tgmpa->is_automatic = true;
		} else {
			$this->tgmpa->is_automatic = false;
		}

		$this->tgmpa->install_plugins_page();

		$status = $this->get_plugin_status( $slug );

		if ( $echo ) {
			wp_send_json_success( $status );
		} else {
			return $status;
		}
	}

	/**
	 * Performs a plugin deactivation
	 *
	 * @return void
	 */
	public function do_plugin_deactivate( $slug ) {
		$status = $this->get_plugin_status( $slug );

		if ( empty( $this->plugins[ $slug ] ) ) {
			$status['error'] = 'We have no data about this plugin.';
			wp_send_json_error( $status );
		}

		deactivate_plugins( $this->plugins[ $slug ]['file_path'] );

		$status = $this->get_plugin_status( $slug );
		wp_send_json_success( $status );
	}

	/**
	 * Performs plugins activation
	 *
	 * @param string $slug
	 * @param bool   $echo
	 *
	 * @return array
	 */
	public function do_plugin_activate( $slug, $echo = true ) {
		$status = $this->get_plugin_status( $slug );

		if ( empty( $this->plugins[ $slug ] ) ) {
			$status['error'] = 'We have no data about this plugin.';
			if ( $echo ) {
				wp_send_json_error( $status );
			} else {
				return $status;
			}
		}

		$result = activate_plugin( $this->plugins[ $slug ]['file_path'] );
		if ( is_wp_error( $result ) ) {
			$status['error'] = $result->get_error_message();
			if ( $echo ) {
				wp_send_json_error( $status );
			} else {
				return $status;
			}
		}

		$status = $this->get_plugin_status( $slug );

		if ( $echo ) {
			wp_send_json_success( $status );
		} else {
			return $status;
		}
	}

	/**
	 * Returns the install url for the current plugin
	 *
	 * @param string $slug
	 *
	 * @return string
	 */
	public function get_download_url( $slug ) {
		return $this->tgmpa->get_download_url( $slug );
	}

	/**
	 * Check if a plugin is installed. Does not take must-use plugins into account.
	 *
	 * @param string $slug Plugin slug.
	 *
	 * @return bool True if installed, false otherwise.
	 * @since 1.0.0
	 */
	public function is_plugin_installed( $slug ) {
		return $this->tgmpa->is_plugin_installed( $slug );
	}

	/**
	 * Check whether there is an update available for a plugin.
	 *
	 * @param string $slug Plugin slug.
	 *
	 * @return false|string Version number string of the available update or false if no update available.
	 * @since 1.0.0
	 */
	public function does_plugin_have_update( $slug ) {
		return $this->tgmpa->does_plugin_have_update( $slug );
	}

	/**
	 * Check if a plugin is active.
	 *
	 * @param string $slug Plugin slug.
	 *
	 * @return bool True if active, false otherwise.
	 * @since 1.0.0
	 */
	public function check_plugin_active( $slug ) {
		return $this->tgmpa->is_plugin_active( $slug );
	}

	/**
	 * Retrieve the version number of an installed plugin.
	 *
	 * @param string $slug Plugin slug.
	 *
	 * @return string Version number as string or an empty string if the plugin is not installed
	 *                or version unknown (plugins which don't comply with the plugin header standard).
	 * @since 1.0.0
	 */
	public function get_installed_version( $slug ) {
		return $this->tgmpa->get_installed_version( $slug );
	}

	/**
	 * Wrapper around the core WP get_plugins function, making sure it's actually available.
	 *
	 * @param string $plugin_folder Optional. Relative path to single plugin folder.
	 *
	 * @return array Array of installed plugins with plugin information.
	 * @since 1.0.0
	 */
	public function get_plugins( $plugin_folder = '' ) {
		return $this->tgmpa->get_plugins( $plugin_folder );
	}

}
