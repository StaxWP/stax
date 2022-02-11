<?php

$form_classes = [ 'search-form' ];

$placeholder = array_key_exists( 'placeholder', $args ) ? $args['placeholder'] : __( 'Search for...', 'stax' );

if ( array_key_exists( 'additional_form_classes', $args ) && is_array( $args['additional_form_classes'] ) ) {
	$form_classes = array_merge( $form_classes, $args['additional_form_classes'] );
}

$value       = array_key_exists( 'value', $args ) ? $args['value'] : '';
$placeholder = apply_filters( 'stx_search_placeholder', $placeholder );
$aria_label  = __( 'Search', 'stax' );
$home_url    = home_url( '/' )

?>

<form role="search"
	method="get"
	class="<?php echo esc_html( implode( ' ', $form_classes ) ); ?>"
	action="<?php echo esc_url( $home_url ); ?>">
	<label>
		<span class="screen-reader-text"><?php echo esc_html__( 'Search for...', 'stax' ); ?></span>
	</label>
	<input type="search"
		class="search-field"
		placeholder="<?php echo esc_attr( $placeholder ); ?>"
		value="<?php echo esc_html( $value ); ?>"
		name="s"/>
	<button type="submit"
			class="search-submit"
			aria-label="<?php echo esc_attr( $aria_label ); ?>">
		<span class="stx-search-icon-wrap">
			<svg width="15" height="15" viewBox="0 0 1792 1792" xmlns="http://www.w3.org/2000/svg"><path d="M1216 832q0-185-131.5-316.5t-316.5-131.5-316.5 131.5-131.5 316.5 131.5 316.5 316.5 131.5 316.5-131.5 131.5-316.5zm512 832q0 52-38 90t-90 38q-54 0-90-38l-343-342q-179 124-399 124-143 0-273.5-55.5t-225-150-150-225-55.5-273.5 55.5-273.5 150-225 225-150 273.5-55.5 273.5 55.5 225 150 150 225 55.5 273.5q0 220-124 399l343 343q37 37 37 90z"></path></svg>
		</span>
	</button>
	<?php if ( array_key_exists( 'post_type', $args ) ) : ?>
		<input type="hidden" name="post_type" value="<?php echo esc_attr( $args['post_type'] ); ?>"/>
	<?php endif; ?>
</form>
