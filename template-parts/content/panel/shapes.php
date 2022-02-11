<?php

\Stax_Assets::instance()->enquque_jquery_parallax();
$template_uri = get_template_directory_uri();

?>

<div class="shapes-box">
	<span data-parallax='{"x": 150, "y": -20, "rotateZ":500}'>
		<img src="<?php echo esc_url( $template_uri ); ?>/assets/img/parallax-bg/fl-shape-1.png">
	</span>
	<span data-parallax='{"x": 250, "y": 150, "rotateZ":500}'>
		<img src="<?php echo esc_url( $template_uri ); ?>/assets/img/parallax-bg/fl-shape-2.png">
	</span>
	<span data-parallax='{"x": -180, "y": 80, "rotateY":2000}'>
		<img src="<?php echo esc_url( $template_uri ); ?>/assets/img/parallax-bg/fl-shape-3.png">
	</span>
	<span data-parallax='{"x": -20, "y": 180}'>
		<img src="<?php echo esc_url( $template_uri ); ?>/assets/img/parallax-bg/fl-shape-4.png">
	</span>
	<span data-parallax='{"x": 300, "y": 70}'>
		<img src="<?php echo esc_url( $template_uri ); ?>/assets/img/parallax-bg/fl-shape-5.png">
	</span>
	<span data-parallax='{"x": 250, "y": 180, "rotateZ":1500}'>
		<img src="<?php echo esc_url( $template_uri ); ?>/assets/img/parallax-bg/fl-shape-6.png">
	</span>
	<span data-parallax='{"x": 180, "y": 10, "rotateZ":2000}'>
		<img src="<?php echo esc_url( $template_uri ); ?>/assets/img/parallax-bg/fl-shape-7.png">
	</span>
	<span data-parallax='{"x": 60, "y": -100}'>
		<img src="<?php echo esc_url( $template_uri ); ?>/assets/img/parallax-bg/fl-shape-9.png">
	</span>
	<span data-parallax='{"x": -30, "y": 150, "rotateZ":1500}'>
		<img src="<?php echo esc_url( $template_uri ); ?>/assets/img/parallax-bg/fl-shape-10.png">
	</span>
</div>
