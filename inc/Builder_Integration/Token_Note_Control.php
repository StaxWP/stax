<?php
/**
 * Stax\Builder_Integration\Token_Note_Control class
 *
 * @package stax
 */

namespace Stax\Builder_Integration;

/**
 * Customizer note shown above a control whose value is overridden by the site's global styles
 * (the brand written by Stax Builder, or styles saved another way).
 */
class Token_Note_Control extends \WP_Customize_Control {

	/**
	 * Control type.
	 *
	 * @var string
	 */
	public $type = 'stax_token_note';

	/**
	 * Names of the overridden values (palette colors), empty when the whole control is overridden.
	 *
	 * @var string[]
	 */
	public $items = [];

	/**
	 * Name of the plugin that manages the brand, empty for plain global styles.
	 *
	 * @var string
	 */
	public $name = '';

	/**
	 * Where the brand is edited.
	 *
	 * @var string
	 */
	public $url = '';

	/**
	 * Prints the note.
	 */
	protected function render_content() {
		$message = $this->name
			/* translators: %s: plugin name, for example Stax Builder. */
			? sprintf( __( 'Set by your brand (%s)', 'stax' ), $this->name )
			: __( 'Set in the site\'s global styles', 'stax' );

		if ( $this->items ) {
			/* translators: 1: "Set by your brand (Stax Builder)" or "Set in the site's global styles", 2: comma-separated color names. */
			$message = sprintf( __( '%1$s: %2$s', 'stax' ), $message, implode( ', ', $this->items ) );
		}
		?>
		<div class="notice notice-info inline">
			<p>
				<?php echo esc_html( $message ); ?>
				<?php if ( $this->name && $this->url ) : ?>
					<a href="<?php echo esc_url( $this->url ); ?>">
						<?php
						/* translators: %s: plugin name, for example Stax Builder. */
						echo esc_html( sprintf( __( 'Edit in %s', 'stax' ), $this->name ) );
						?>
					</a>
				<?php endif; ?>
			</p>
			<p class="description"><?php esc_html_e( 'The value below is kept and applies again if that setting is removed.', 'stax' ); ?></p>
		</div>
		<?php
	}
}
