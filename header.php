<?php
/**
 * The header for our theme
 *
 * This is the template that displays all of the <head> section and everything up until <div id="content">
 *
 * @link https://developer.wordpress.org/themes/basics/template-files/#template-partials
 *
 * @package stax
 */

namespace Stax;

?>
<!DOCTYPE html>
<html <?php language_attributes(); ?> class="<?php echo esc_attr( stax()->get_html_class( 'no-js' ) ); ?>" dir="ltr">
	<head>
		<meta charset="<?php bloginfo( 'charset' ); ?>">
		<meta http-equiv="X-UA-Compatible" content="IE=edge"/>
		<meta name="viewport" content="width=device-width, initial-scale=1, minimum-scale=1">
		<meta name="author" content="StaxWP"/>
		<link rel="profile" href="http://gmpg.org/xfn/11">

		<?php wp_head(); ?>
	</head>

	<body <?php body_class(); ?>>

		<?php wp_body_open(); ?>
		<?php do_action( 'stax_after_body' ); ?>

		<div class="svq-page-wrapper">
			<?php
			/**
			 * Included Header section using actions
			 *
			 * @hooked stax_header_builder_action
			 */
			do_action( 'stax_header' );
