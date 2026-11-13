<?php
/**
 * Stax\Builder_Integration\Managed_Control class
 *
 * @package stax
 */

namespace Stax\Builder_Integration;

/**
 * Customizer note shown instead of the header/footer builder when a plugin manages that area.
 */
class Managed_Control extends \WP_Customize_Control {

	/**
	 * Control type.
	 *
	 * @var string
	 */
	public $type = 'stax_managed_area';

	/**
	 * Area ("header" or "footer").
	 *
	 * @var string
	 */
	public $area = 'header';

	/**
	 * Name of the plugin that manages the area.
	 *
	 * @var string
	 */
	public $name = '';

	/**
	 * Where the area is edited.
	 *
	 * @var string
	 */
	public $url = '';

	/**
	 * Prints the note.
	 */
	protected function render_content() {
		$message = 'footer' === $this->area
			/* translators: %s: plugin name, for example Stax Builder. */
			? __( 'The footer is managed by %s.', 'stax' )
			/* translators: %s: plugin name, for example Stax Builder. */
			: __( 'The header is managed by %s.', 'stax' );
		?>
		<div class="notice notice-info inline">
			<p><?php echo esc_html( sprintf( $message, $this->name ) ); ?></p>
			<p>
				<a class="button button-primary" href="<?php echo esc_url( $this->url ); ?>">
					<?php
					/* translators: %s: plugin name, for example Stax Builder. */
					echo esc_html( sprintf( __( 'Edit in %s', 'stax' ), $this->name ) );
					?>
				</a>
			</p>
			<p class="description"><?php esc_html_e( 'Your theme settings for this area are kept and come back if you deactivate the plugin.', 'stax' ); ?></p>
		</div>
		<?php
	}
}
