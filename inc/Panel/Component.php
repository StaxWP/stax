<?php
/**
 * Stax\Base_Support\Component class
 *
 * @package stax
 */

namespace Stax\Panel;

use Stax\Component_Interface;
use function Stax\stax;


/**
 * Class for adding basic theme support, most of which is mandatory to be implemented by all themes.
 */
class Component implements Component_Interface {

	/**
	 * Gets the unique identifier for the theme component.
	 *
	 * @return string Component slug.
	 */
	public function get_slug() {
		return 'panel';
	}

	private $current_slug = '';

	/**
	 * Adds the action and filter hooks to integrate with WordPress.
	 */
	public function initialize() {
		$this->set_constants();
		$this->set_hooks();
		Addons_Manager::instance();

		$theme_slug = stax()->get_config( 'slug' ) . '-';
		$current_slug = 'welcome';

		if ( isset( $_GET['page'] ) && $_GET['page'] === 'stax' ) {
			if ( isset( $_GET['sub-page'] ) ) {
				$current_slug = sanitize_text_field( $_GET['sub-page'] );
			}
		}

		$this->current_slug = str_replace( '-', '_', $current_slug );
	}

	/**
	 * Defining constants
	 */
	private function set_constants() {
		if ( ! defined( 'SVQ_LIB_DIR' ) ) {
			define( 'SVQ_LIB_DIR', get_template_directory() . '/inc/Config' );
		}

		if ( ! defined( 'SVQ_PANEL_DIR' ) ) {
			define( 'SVQ_PANEL_DIR', __dir__ );
		}
		if ( ! defined( 'SVQ_PANEL_URI' ) ) {
			define( 'SVQ_PANEL_URI', get_template_directory_uri() . '/inc/Panel' );
		}
	}

	public function set_hooks() {
		add_action( 'admin_menu', array( $this, 'register_panel_page' ) );

		add_action( 'stax_menu_panel_action', array( $this, 'tpl_page' ) );
		add_action( 'stax_header_right_section', array( $this, 'tpl_top_header_right_area' ) );

		add_action( 'stax_welcome_page_content', array( $this, 'tpl_main_quick_settings_content' ) );

		add_action( 'stax_welcome_page_right_sidebar_content', array( $this, 'tpl_side_addons_section' ), 11 );
		add_action( 'stax_welcome_page_right_sidebar_content', array( $this, 'tpl_side_docs_section' ), 12 );
		add_action( 'stax_welcome_page_right_sidebar_content', array( $this, 'tpl_side_community_section' ), 14 );

		// Add-ons.
		add_filter( 'stax_addons_columns', '__return_zero' );
		add_action( 'stax_addons_page_content', array( $this, 'tpl_main_addons_page_content' ) );

		add_filter( 'http_request_args', array( $this, 'stop_update_theme' ), 5, 2 );

		if ( ( isset( $_REQUEST['action'] ) && $_REQUEST['action'] === 'sq_do_plugin_action' ) || ( isset( $_GET['page'] ) && strpos( $_GET['page'], stax()->get_config( 'slug' ) ) === 0 ) ) {
			add_action( 'admin_init', array( $this, 'config_addons' ), 12 );
			add_action( 'admin_enqueue_scripts', array( $this, 'panel_scripts' ) );
		}
	}

	/**
	 * Register CSS & JS Files
	 */
	public function panel_scripts() {

		// CSS.
		wp_register_style( stax()->get_config( 'slug' ) . '-panel', get_theme_file_uri( '/assets/admin/css/theme-panel.css' ), array(), stax()->get_version(), 'all' );
		wp_enqueue_style( stax()->get_config( 'slug' ) . '-panel' );

		wp_register_style( stax()->get_config( 'slug' ) . '-tailwind', get_theme_file_uri( '/assets/admin/css/panel.css' ), array(), stax()->get_version(), 'all' );
		wp_enqueue_style( stax()->get_config( 'slug' ) . '-tailwind' );

		// JS.
		wp_enqueue_script( 'jquery-ui-tooltip' );
		wp_register_script( stax()->get_config( 'slug' ) . '-panel', get_theme_file_uri( '/assets/admin/js/theme-panel.js' ), array( 'jquery' ), stax()->get_version(), true );
		wp_enqueue_script( stax()->get_config( 'slug' ) . '-panel' );
	}

	/**
	 * Register panel page
	 */
	public function register_panel_page() {
		add_theme_page(
			esc_html__( 'Stax Panel', 'stax' ),
			esc_html__( 'Stax Panel', 'stax' ),
			'manage_options',
			stax()->get_config( 'slug' ),
			array( $this, 'panel_menu_callback' )
		);

	}

	public function panel_menu_callback() {
		$theme_url     = apply_filters( 'stax_admin_theme_url', 'https://staxwp.com/stax-theme/' );
		$wrapper_class = apply_filters( 'stax_welcome_wrapper_class', array( $this->current_slug ) );

		?>
		<div
			class="sqp_stax_options wrap sqp-m-0 <?php echo esc_attr( implode( ' ', $wrapper_class ) ); ?>">
			<div class="sqp-bg-white sqp-py-5 sqp-mb-5 sqp-shadow">
				<div
					class="sqp-container sqp-mx-auto sqp-px-5 sqp-flex sqp-flex-wrap sqp-justify-between sqp-items-center">
					<div class="sqp-text-left">
						<a href="<?php echo esc_url( $theme_url ); ?>" target="_blank" rel="noopener"
						   class="sqp-text-base sqp-flex sqp-items-center sqp-content-center sqp-no-underline">
							<img
								src="<?php echo esc_url( get_parent_theme_file_uri( 'assets/img/staxwp_logo.png' ) ); ?>"
								class="sqp-border-0 sqp-w-32" alt="Stax">
							<span
								class="sqp-bg-gray-300 sqp-text-gray-600 sqp-ml-4 sqp-px-2 sqp-py-1 sqp-text-xs sqp-rounded sqp-no-underline"><?php echo stax()->get_version(); ?></span>

							<?php do_action( 'stax_welcome_page_header_title' ); ?>
						</a>
					</div>

					<?php do_action( 'stax_header_right_section' ); ?>

				</div>
			</div>

			<?php do_action( 'stax_menu_panel_action' ); ?>
		</div>
		<?php
	}

	/**
	 * Require panel page
	 */
	public function tpl_page() {
		$current_slug = $this->current_slug;
		require SVQ_PANEL_DIR . '/templates/panel.php';
	}

	public function tpl_top_header_right_area() {
		$top_links = apply_filters(
			'stax_header_top_links',
			array(
				'stax-theme-info' => array(
					'title' => __( 'Super Fast and Customizable WordPress theme', 'stax' ),
				),
			)
		);

		if ( empty( $top_links ) || ! is_array( $top_links ) ) {
			return;
		}

		?>
		<div class="sqp-text-right">
			<ul>
				<?php
				foreach ( $top_links as $key => $info ) {
					/* translators: %1$s: Top Link URL wrapper, %2$s: Top Link URL, %3$s: Top Link URL target attribute */
					printf(
						'<li class="sqp-m-0"><%1$s %2$s %3$s > %4$s </%1$s>',
						isset( $info['url'] ) ? 'a' : 'span',
						isset( $info['url'] ) ? 'href="' . esc_url( $info['url'] ) . '"' : '',
						isset( $info['url'] ) ? 'target="_blank" rel="noopener"' : '',
						esc_html( $info['title'] )
					);
				}
				?>
			</ul>
		</div>
		<?php
	}

	/**
	 * Main welcome content
	 *
	 * @since 1.0.0
	 */
	public function tpl_main_quick_settings_content() {

		// Quick settings.
		$quick_settings = apply_filters(
			'stax_customizer_quick_links',
			array(
				'logo-favicon' => array(
					'title'     => esc_html__( 'Upload Logo', 'stax' ),
					'dashicon'  => 'dashicons-format-image',
					'quick_url' => admin_url( 'customize.php?autofocus[control]=custom_logo' ),
				),
				'colors'       => array(
					'title'     => esc_html__( 'Set Colors', 'stax' ),
					'dashicon'  => 'dashicons-admin-customizer',
					'quick_url' => admin_url( 'customize.php?autofocus[section]=stax_colors' ),
				),
				'typography'   => array(
					'title'     => esc_html__( 'Typography', 'stax' ),
					'dashicon'  => 'dashicons-editor-textcolor',
					'quick_url' => admin_url( 'customize.php?autofocus[panel]=stax_typography' ),
				),
				'layout'       => array(
					'title'     => esc_html__( 'Layout Options', 'stax' ),
					'dashicon'  => 'dashicons-layout',
					'quick_url' => admin_url( 'customize.php?autofocus[panel]=stax_layout' ),
				),
				'header'       => array(
					'title'     => esc_html__( 'Header Builder', 'stax' ),
					'dashicon'  => 'dashicons-hammer',
					'quick_url' => admin_url( 'customize.php?autofocus[panel]=hfg_header' ),
				),
				'footer'       => array(
					'title'     => esc_html__( 'Footer Builder', 'stax' ),
					'dashicon'  => 'dashicons-hammer',
					'quick_url' => admin_url( 'customize.php?autofocus[panel]=hfg_footer' ),
				),
			)
		);

		$extensions = array();

		?>

		<?php if ( ! empty( $quick_settings ) && is_array( $quick_settings ) ) : ?>
			<div class="sqp_box">
				<h2 class="sqp_box_title">
					<?php esc_html_e( 'Customizer Quick Links', 'stax' ); ?>
				</h2>
				<ul class="sqp_box_list">
					<?php
					foreach ( (array) $quick_settings as $key => $link ) {
						echo '<li class="sqp_box_list_item">' .
							 '<span class="dashicons ' . esc_attr( $link['dashicon'] ) . ' sqp-ml-4"></span>' .
							 '<a class="sqp-ml-2 sqp-mr-4 sqp-no-underline focus:sqp-outline-none" href="' . esc_url( $link['quick_url'] ) . '" target="_blank" rel="noopener">' .
							 esc_html( $link['title'] ) . '</a>' .
							 '</li>';
					}
					?>
				</ul>
			</div>
		<?php endif; ?>

		<?php if ( ! empty( $extensions ) && is_array( $extensions ) ) : ?>
			<div class="sqp_box">
				<h2 class="sqp_box_title">
					<?php esc_html_e( 'More Settings from Plugins', 'stax' ); ?>
				</h2>
				<ul class="sqp_box_list">
					<?php
					foreach ( $extensions as $addon => $info ) {
						$title_url     = ( isset( $info['title_url'] ) && ! empty( $info['title_url'] ) ) ? 'href="' . esc_url( $info['title_url'] ) . '"' : '';
						$anchor_target = ( isset( $info['title_url'] ) && ! empty( $info['title_url'] ) ) ? "target='_blank' rel='noopener'" : '';

						echo '<li id="' . esc_attr( $addon ) . '"  class="' . esc_attr( $info['class'] ) . ' sqp_box_list_item">' .
							 '<a class="sqp-mx-4 sqp-no-underline focus:sqp-outline-none"' . $title_url . $anchor_target . ' >' .
							 esc_html( $info['title'] ) . '</a>' .
							 '<div class="stax-addon-link-wrapper">';

						foreach ( $info['links'] as $key => $link ) {
							printf(
								'<a class="%1$s" %2$s %3$s> %4$s </a>',
								esc_attr( $link['link_class'] ),
								isset( $link['link_url'] ) ? 'href="' . esc_url( $link['link_url'] ) . '"' : '',
								( isset( $link['target_blank'] ) && $link['target_blank'] ) ? 'target="_blank" rel="noopener"' : '',
								esc_html( $link['link_text'] )
							);
						}
						echo '</div></li>';
					}
					?>
				</ul>
			</div>

		<?php endif; ?>

		<?php
	}

		/**
	 * Include docs content
	 *
	 * @since 1.0.0
	 */
	public function tpl_side_addons_section() {
		?>

		<div class="sqp_box lg:sqp-ml-4">
			<h2 class="sqp_box_title">
				<span class="dashicons dashicons-admin-plugins sqp-mr-2"></span>
				<?php esc_html_e( 'Add-ons', 'stax' ); ?>
			</h2>
			<div class="sqp-p-4">
				<p class="sqp-m-0 sqp-p-0 sqp-mb-4">
					<?php esc_html_e( 'Check out our recommended plugins!', 'stax' ); ?>
				</p>
				<?php
				$link      = apply_filters( 'stax_knowledge_base_documentation_link', admin_url( 'themes.php?page=stax&sub-page=addons' ) );
				$link_text = apply_filters( 'stax_knowledge_base_documentation_link_text', __( 'Go to Add-ons &raquo;', 'stax' ) );

				printf(
				/* translators: %1$s: Stax Addons link. */
					'%1$s',
					! empty( $link ) ? '<a href=' . esc_url( $link ) . '>' . esc_html( $link_text ) . '</a>' : esc_html( $link_text )
				);
				?>
			</div>
		</div>

		<?php
	}

	/**
	 * Include docs content
	 *
	 * @since 1.0.0
	 */
	public function tpl_side_docs_section() {
		?>

		<div class="sqp_box lg:sqp-ml-4">
			<h2 class="sqp_box_title">
				<span class="dashicons dashicons-book sqp-mr-2"></span>
				<?php esc_html_e( 'Knowledge Base', 'stax' ); ?>
			</h2>
			<div class="sqp-p-4">
				<p class="sqp-m-0 sqp-p-0 sqp-mb-4">
					<?php esc_html_e( 'Have questions? Check out our knowledge base to learn more.', 'stax' ); ?>
				</p>
				<?php
				$link      = apply_filters( 'stax_knowledge_base_documentation_link', 'https://staxwp.com/docs/stax-theme/' );
				$link_text = apply_filters( 'stax_knowledge_base_documentation_link_text', __( 'Visit Knowledge Base &raquo;', 'stax' ) );

				printf(
				/* translators: %1$s: Stax Knowledge doc link. */
					'%1$s',
					! empty( $link ) ? '<a href=' . esc_url( $link ) . ' target="_blank" rel="noopener">' . esc_html( $link_text ) . '</a>' : esc_html( $link_text )
				);
				?>
			</div>
		</div>

		<?php
	}

	/**
	 * Include community content
	 *
	 * @since 1.0.0
	 */
	public function tpl_side_community_section() {
		?>

		<div class="sqp_box lg:sqp-ml-4">
			<h2 class="sqp_box_title">
				<span class="dashicons dashicons-groups sqp-mr-2"></span>
				<span>
						<?php
						printf(
						/* translators: %1$s: Stax Theme name. */
							esc_html__( '%1$s Community', 'stax' ),
							'Stax'
						);
						?>
				</span>
			</h2>
			<div class="sqp-p-4">
				<p class="sqp-m-0 sqp-p-0 sqp-mb-4">
					<?php
					printf(
					/* translators: %1$s: Stax Theme name. */
						esc_html__( 'Join the %1$s users. Share resources, ask questions, give feedback and help each other!', 'stax' ),
						'Stax'
					);
					?>
				</p>
				<?php
				$link      = apply_filters( 'stax_community_group_link', 'https://www.facebook.com/groups/staxwp' );
				$link_text = apply_filters( 'stax_community_group_link_text', __( 'Join Our Facebook Group &raquo;', 'stax' ) );

				printf(
				/* translators: %1$s: Stax Knowledge doc link. */
					'%1$s',
					! empty( $link ) ? '<a href=' . esc_url( $link ) . ' target="_blank" rel="noopener">' . esc_html( $link_text ) . '</a>' :
						esc_html( $link_text )
				);
				?>
			</div>
		</div>

		<?php
	}

	public function tpl_main_addons_page_content() {
		?>
		<div class="sqp-w-full">
			<h3 class="sqp-text-2xl sqp-font-medium"><?php esc_html_e( 'Install Add-ons', 'stax' ); ?></h3>
			<p>
				<?php esc_html_e( 'Enhance your site with our recommended extensions. They are not mandatory and you can use any plugins you like.', 'stax' ); ?>
			</p>

			<div class="sqp-flex sqp-flex-wrap sqp--mx-3 sqp-overflow-hidden">
				<?php
				foreach ( Addons_Manager::instance()->plugins as $plugin ) :
					?>

					<?php
					$plugin_status = Addons_Manager::instance()->get_plugin_status( $plugin['slug'] );

					?>
					<div
						class="sqp-py-3 sqp-w-full md:sqp-w-1/2 lg:sqp-w-1/3 xl:sqp-w-1/3 sqp-overflow-hidden svq-extension <?php echo esc_attr( $plugin_status['status'] ); ?>"
						id="ext-<?php echo esc_attr( $plugin['slug'] ); ?>">
						<div
							class="sqp-bg-white sqp-border sqp-border-solid sqp-border-gray-300 sqp-text-center sqp-mx-3 sqp-h-full sqp-relative">
							<div class="sqp-px-6 sqp-py-10 sqp-pb-24">
								<h4 class="sqp-mt-0 sqp-mb-4 sqp-text-xl sqp-font-bold sqp-leading-tight"><?php echo esc_html( $plugin['name'] ); ?></h4>
								<div class="sqp-my-5">
									<span
										class="sqp_plugin_status svq-extension-status">
										<?php echo esc_html( $plugin_status['status_text'] ); ?>
									</span>
								</div>
								<p class="sqp-text-gray-700"><?php echo wp_kses_data( isset( $plugin['description'] ) ? $plugin['description'] : '' ); ?></p>
								<p class="sqp-italic sqp-text-gray-500">
									<cite><?php echo esc_html( ( isset( $plugin['required'] ) && $plugin['required'] === true ) ? __( 'REQUIRED', 'stax' ) : __( 'Recommended', 'stax' ) ); ?></cite>
								</p>
								<p class="svq-extension-ajax-text"></p>
							</div>
							<div class="sqp-w-full sqp-absolute sqp-bottom-0 sqp-left-0 svq-extension-actions">
								<?php
								echo '<a class="sqp_plugin_btn svq-extension-button"' .
									 ' data-action="' . esc_attr( $plugin_status['action'] ) . '"' .
									 ' data-status="' . esc_attr( $plugin_status['status'] ) . '"' .
									 ' data-nonce="' . esc_attr( wp_create_nonce( 'sq_plugins_nonce' ) ) . '"' .
									 ' href="#"' .
									 ' data-slug="' . esc_attr( $plugin['slug'] ) . '">' .
									 esc_html( $plugin_status['action_text'] ) .
									 '</a>';
								?>
							</div>
						</div>

					</div>
				<?php endforeach; ?>
			</div>
		</div>
		<?php
	}

	/**
	 * @param $r
	 * @param $url
	 *
	 * @return mixed
	 */
	public function stop_update_theme(
		$r, $url
	) {
		if ( 0 !== strpos( $url, 'https://api.wordpress.org/themes/update-check/1.1/' ) ) {
			return $r; // Not a theme update request. Bail immediately.
		}

		$themes = json_decode( $r['body']['themes'] );
		$theme  = get_template();
		unset( $themes->themes->$theme );
		$r['body']['themes'] = json_encode( $themes );

		return $r;
	}

	/**
	 * Addons config
	 */
	public function config_addons() {
		/* Move elements first */
		if ( empty( stax()->get_config( 'priority_addons' ) ) ) {
			return;
		}

		$priority_list = array_reverse( stax()->get_config( 'priority_addons' ) );
		foreach ( $priority_list as $item ) {
			if ( isset( Addons_Manager::instance()->plugins[ $item ] ) ) {
				Addons_Manager::instance()->plugins = array( $item => Addons_Manager::instance()->plugins[ $item ] ) + Addons_Manager::instance()->plugins;
			}
		}
	}

}
