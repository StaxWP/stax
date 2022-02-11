<?php
/**
 * Stax\Breadcrumb\Buddypress_Trail class
 *
 * @package stax
 */

namespace Stax\Breadcrumb;

use function apply_filters;
use function get_the_title;

/**
 * Class Buddypress_Trail
 *
 * @package Stax\Breadcrumb
 */
class Buddypress_Trail extends Trail {

	/**
	 * Runs through the various buddyPress conditional tags to check the current page being viewed.  Once
	 * a condition is met, add items to the $items array.
	 *
	 * @since  1.0.0
	 * @access public
	 * @return void
	 */
	public function do_trail_items() {
		global $bp;
		/* Add the network and site home links. */
		$this->do_network_home_link();
		$this->do_site_home_link();

		$trail_end = '';

		if ( ! empty( $bp->displayed_user->fullname ) ) { // looking at a user or self
			$this->items[] = '<a href="' . bp_get_members_directory_permalink() . '">' . get_the_title( $bp->pages->members->id ) . '</a>';
			$this->items[] = '<a href="' . $bp->displayed_user->domain . '" title="' . strip_tags( $bp->displayed_user->userdata->display_name ) . '">' . strip_tags( $bp->displayed_user->userdata->display_name ) . '</a>';

		} elseif ( $bp->is_single_item ) { // we're on a single item page
			$this->items[] = '<a href="' . get_permalink( $bp->pages->{$bp->current_component}->id ) . '" title="' . esc_attr( strip_tags( get_the_title( $bp->pages->{$bp->current_component}->id ) ) ) . '">' . get_the_title( $bp->pages->{$bp->current_component}->id ) . '</a>';
			$this->items[] = '<a href="' . get_permalink() . '" title="' . esc_attr( $bp->bp_options_title ) . '">' . $bp->bp_options_title . '</a>';

		} elseif ( $bp->is_directory ) { // this is a top level directory page
			$trail_end = get_the_title( $bp->pages->{$bp->current_component}->id );
		} elseif ( bp_is_register_page() ) {
			$trail_end = __( 'Create an Account', 'stax' );
		} elseif ( bp_is_activation_page() ) {
			$trail_end = __( 'Activate your Account', 'stax' );
		} elseif ( bp_is_group_create() ) {
			$this->items[] = '<a href="' . get_permalink( $bp->pages->groups->id ) . '" title="' . esc_attr( get_the_title( $bp->pages->groups->id ) ) . '">' . get_the_title( $bp->pages->groups->id ) . '</a>';

			$trail_end = __( 'Create a Group', 'stax' );

		} elseif ( bp_is_create_blog() ) {
			$this->items[] = '<a href="' . esc_url( home_url( '/' ) . $bp->current_component ) . '" title="' . ucwords( $bp->current_component ) . '">' . ucwords( $bp->current_component ) . '</a>';
			$trail_end     = __( 'Create a Blog', 'stax' );
		}
		if ( $trail_end ) {
			$this->items[] = $trail_end;
		}

		/* Return the bbPress breadcrumb trail items. */
		$this->items = apply_filters( 'breadcrumb_trail_get_buddypress_items', $this->items, $this->args );
	}
}
