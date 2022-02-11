<?php
/**
 * Stax\Breadcrumb\Bbpress_Trail class
 *
 * @package stax
 */

namespace Stax\Breadcrumb;

/**
 * Class Bbpress_Trail
 *
 * @package Stax\Breadcrumb
 */
class Bbpress_Trail extends Trail {

	/**
	 * Runs through the various bbPress conditional tags to check the current page being viewed.  Once
	 * a condition is met, add items to the $items array.
	 *
	 * @since  0.6.0
	 * @access public
	 * @return void
	 */
	public function do_trail_items() {

		/* Add the network and site home links. */
		$this->do_network_home_link();
		$this->do_site_home_link();

		/* Get the forum post type object. */
		$post_type_object = get_post_type_object( bbp_get_forum_post_type() );

		/* If not viewing the forum root/archive page and a forum archive exists, add it. */
		if ( ! empty( $post_type_object->has_archive ) && ! bbp_is_forum_archive() ) {
			$this->items[] = '<a href="' . get_post_type_archive_link( bbp_get_forum_post_type() ) . '">' . bbp_get_forum_archive_title() . '</a>';
		}

		/* If viewing the forum root/archive. */
		if ( bbp_is_forum_archive() ) {

			if ( true === $this->args['show_title'] ) {
				$this->items[] = bbp_get_forum_archive_title();
			}
		} /* If viewing the topics archive. */
		elseif ( bbp_is_topic_archive() ) {

			if ( true === $this->args['show_title'] ) {
				$this->items[] = bbp_get_topic_archive_title();
			}
		} /* If viewing a topic tag archive. */
		elseif ( bbp_is_topic_tag() ) {

			if ( true === $this->args['show_title'] ) {
				$this->items[] = bbp_get_topic_tag_name();
			}
		} /* If viewing a topic tag edit page. */
		elseif ( bbp_is_topic_tag_edit() ) {
			$this->items[] = '<a href="' . bbp_get_topic_tag_link() . '">' . bbp_get_topic_tag_name() . '</a>';

			if ( true === $this->args['show_title'] ) {
				$this->items[] = __( 'Edit', 'stax' );
			}
		} /* If viewing a "view" page. */
		elseif ( bbp_is_single_view() ) {

			if ( true === $this->args['show_title'] ) {
				$this->items[] = bbp_get_view_title();
			}
		} /* If viewing a single topic page. */
		elseif ( bbp_is_single_topic() ) {

			/* Get the queried topic. */
			$topic_id = get_queried_object_id();

			/* Get the parent items for the topic, which would be its forum (and possibly forum grandparents). */
			$this->do_post_parents( bbp_get_topic_forum_id( $topic_id ) );

			/* If viewing a split, merge, or edit topic page, show the link back to the topic.  Else, display topic title. */
			if ( bbp_is_topic_split() || bbp_is_topic_merge() || bbp_is_topic_edit() ) {
				$this->items[] = '<a href="' . bbp_get_topic_permalink( $topic_id ) . '">' . bbp_get_topic_title( $topic_id ) . '</a>';
			} elseif ( true === $this->args['show_title'] ) {
				$this->items[] = bbp_get_topic_title( $topic_id );
			}

			/* If viewing a topic split page. */
			if ( bbp_is_topic_split() && true === $this->args['show_title'] ) {
				$this->items[] = __( 'Split', 'stax' );
			} /* If viewing a topic merge page. */
			elseif ( bbp_is_topic_merge() && true === $this->args['show_title'] ) {
				$this->items[] = __( 'Merge', 'stax' );
			} /* If viewing a topic edit page. */
			elseif ( bbp_is_topic_edit() && true === $this->args['show_title'] ) {
				$this->items[] = __( 'Edit', 'stax' );
			}
		} /* If viewing a single reply page. */
		elseif ( bbp_is_single_reply() ) {

			/* Get the queried reply object ID. */
			$reply_id = get_queried_object_id();

			/* Get the parent items for the reply, which should be its topic. */
			$this->do_post_parents( bbp_get_reply_topic_id( $reply_id ) );

			/* If viewing a reply edit page, link back to the reply. Else, display the reply title. */
			if ( bbp_is_reply_edit() ) {
				$this->items[] = '<a href="' . bbp_get_reply_url( $reply_id ) . '">' . bbp_get_reply_title( $reply_id ) . '</a>';

				if ( true === $this->args['show_title'] ) {
					$this->items[] = __( 'Edit', 'stax' );
				}
			} elseif ( true === $this->args['show_title'] ) {
				$this->items[] = bbp_get_reply_title( $reply_id );
			}
		} /* If viewing a single forum. */
		elseif ( bbp_is_single_forum() ) {

			/* Get the queried forum ID and its parent forum ID. */
			$forum_id        = get_queried_object_id();
			$forum_parent_id = bbp_get_forum_parent_id( $forum_id );

			/* If the forum has a parent forum, get its parent(s). */
			if ( 0 !== $forum_parent_id ) {
				$this->do_post_parents( $forum_parent_id );
			}

			/* Add the forum title to the end of the trail. */
			if ( true === $this->args['show_title'] ) {
				$this->items[] = bbp_get_forum_title( $forum_id );
			}
		} /* If viewing a user page or user edit page. */
		elseif ( bbp_is_single_user() || bbp_is_single_user_edit() ) {

			if ( bbp_is_single_user_edit() ) {
				$this->items[] = '<a href="' . bbp_get_user_profile_url() . '">' . bbp_get_displayed_user_field( 'display_name' ) . '</a>';

				if ( true === $this->args['show_title'] ) {
					$this->items[] = __( 'Edit', 'stax' );
				}
			} elseif ( true === $this->args['show_title'] ) {
				$this->items[] = bbp_get_displayed_user_field( 'display_name' );
			}
		}

		/* Return the bbPress breadcrumb trail items. */
		$this->items = apply_filters( 'breadcrumb_trail_get_bbpress_items', $this->items, $this->args );
	}
}
