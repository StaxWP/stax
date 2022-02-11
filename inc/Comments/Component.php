<?php
/**
 * Stax\Comments\Component class
 *
 * @package stax
 */

namespace Stax\Comments;

use Stax\Component_Interface;
use Stax\Templating_Component_Interface;
use function Stax\stax;
use function add_action;
use function is_singular;
use function comments_open;
use function get_option;
use function the_ID;
use function esc_attr;
use function wp_list_comments;
use function the_comments_navigation;
use function add_filter;
use function remove_filter;
use function wp_get_current_user;
use function get_current_user_id;
use function wp_get_current_commenter;
use function admin_url;
use function wp_logout_url;
use function wp_kses_post;
use function get_avatar;
use function get_comment_author_link;
use function current_user_can;
use function edit_comment_link;
use function comment_text;
use function comment_reply_link;
use function is_wp_error;
use function get_comment;
use function get_comment_meta;

/**
 * Class Component
 *
 * @package Stax\Comments
 */
class Component implements Component_Interface, Templating_Component_Interface {

	/**
	 * Gets the unique identifier for the theme component.
	 *
	 * @return string Component slug.
	 */
	public function get_slug() {
		return 'comments';
	}

	/**
	 * Adds the action and filter hooks to integrate with WordPress.
	 */
	public function initialize() {
		add_action( 'wp_ajax_nopriv_post_comment', [ $this, 'post_comment_handler' ] );
		add_action( 'wp_ajax_post_comment', [ $this, 'post_comment_handler' ] );
		add_action( 'wp_enqueue_scripts', [ $this, 'action_enqueue_comment_reply_script' ] );
		add_action( 'comment_form_before_fields', [ $this, 'comment_form_notifications' ] );
		add_action( 'comment_form_logged_in_after', [ $this, 'comment_form_notifications' ] );

		add_filter( 'comment_reply_link', [ $this, 'comment_reply_class' ] );
		add_filter( 'cancel_comment_reply_link', [ $this, 'cancel_comment_reply_class' ] );
		add_filter( 'edit_comment_link', [ $this, 'comment_edit_link' ] );
		add_filter( 'comment_form_fields', [ $this, 'move_comment_field' ] );

		add_filter( 'previous_comments_link_attributes', [ $this, 'comments_prev_nav_link' ] );
		add_filter( 'next_comments_link_attributes', [ $this, 'comments_next_nav_link' ] );
	}

	/**
	 * Gets template tags to expose as methods on the Template_Tags class instance, accessible through `stax()`.
	 *
	 * @return array Associative array of $method_name => $callback_info pairs. Each $callback_info must either be
	 *               a callable or an array with key 'callable'. This approach is used to reserve the possibility of
	 *               adding support for further arguments in the future.
	 */
	public function template_tags() {
		return [
			'the_comments'         => [ $this, 'the_comments' ],
			'single_comment'       => [ $this, 'single_comment' ],
			'single_comment_after' => [ $this, 'single_comment_after' ],
			'custom_comment_form'  => [ $this, 'custom_comment_form' ],
		];
	}

	/**
	 * Enqueues the WordPress core 'comment-reply' script as necessary.
	 */
	public function action_enqueue_comment_reply_script() {

		// If the AMP plugin is active, return early.
		if ( stax()->is_amp() ) {
			return;
		}

		// Enqueue comment script on singular post/page views only.
		if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ) {
			// wp_enqueue_script( 'comment-reply' );
		}
	}

	/**
	 * Displays the list of comments for the current post.
	 *
	 * Internally this method calls `wp_list_comments()`. However, in addition to that it will render the wrapping
	 * element for the list, so that must not be added manually. The method will also take care of generating the
	 * necessary markup if amp-live-list should be used for comments.
	 *
	 * @param array $args Optional. Array of arguments. See `wp_list_comments()` documentation for a list of supported
	 *                    arguments.
	 */
	public function the_comments( array $args = [] ) {
		$args = array_merge(
			$args,
			[
				'style'      => 'ol',
				'short_ping' => true,
			]
		);

		$amp_live_list = stax()->using_amp_live_list_comments();

		if ( $amp_live_list ) {
			$comment_order     = get_option( 'comment_order' );
			$comments_per_page = get_option( 'page_comments' ) ? (int) get_option( 'comments_per_page' ) : 10000;
			$poll_interval     = MINUTE_IN_SECONDS * 1000;

			?>
			<amp-live-list
			id="amp-live-comments-list-<?php the_ID(); ?>"
			<?php echo ( 'asc' === $comment_order ) ? ' sort="ascending" ' : ''; ?>
			data-poll-interval="<?php echo esc_attr( $poll_interval ); ?>"
			data-max-items-per-page="<?php echo esc_attr( $comments_per_page ); ?>"
			>
			<?php

			add_filter( 'navigation_markup_template', [ $this, 'filter_add_amp_live_list_pagination_attribute' ] );
		}

		?>
		<ol class="comment-list"<?php echo esc_html( $amp_live_list ? ' items' : '' ); ?>>


			<?php wp_list_comments( $args ); ?>
		</ol>
		<?php

		the_comments_navigation();

		if ( $amp_live_list ) {
			remove_filter(
				'navigation_markup_template',
				[
					$this,
					'filter_add_amp_live_list_pagination_attribute',
				]
			);

			?>
			<div update>
				<button class="button"
						on="tap:amp-live-comments-list-<?php the_ID(); ?>.update"><?php esc_html_e( 'New comment(s)', 'stax' ); ?></button>
			</div>
			</amp-live-list>
			<?php
		}
	}

	/**
	 * Rewrite comment form
	 *
	 * @param array $args
	 * @param null  $post_id
	 */
	public function custom_comment_form( $args = [], $post_id = null ) {
		global $id;

		$user          = wp_get_current_user();
		$user_identity = $user->exists() ? $user->display_name : '';

		if ( null === $post_id ) {
			$post_id = $id;
		} else {
			$id = $post_id;
		}

		if ( comments_open( $post_id ) ) :
			?>
			<div class="comments-listing svq-content--small for-post-id-<?php echo esc_attr( get_the_ID() ); ?>">
				<?php

				$commenter = wp_get_current_commenter();
				$req       = get_option( 'require_name_email' );
				$aria_req  = ( $req ? " aria-required='true'" : '' );
				$fields    = [
					'author' => '<div class="row">' .
								'<div class="form-group col-md-6">' .
								'<input id="author" name="author" type="text" class="form-control" ' .
								'placeholder="' . esc_attr__( 'Name', 'stax' ) . ( $req ? ' *' : '' ) . '" ' .
								'value="' . esc_attr( $commenter['comment_author'] ) . '" size="30"' . $aria_req . ' />' .
								'</div>',
					'email'  => '<div class="form-group col-md-6">' .
								'<input id="email" name="email" type="text" class="form-control" ' .
								'placeholder="' . esc_attr__( 'Email', 'stax' ) . ( $req ? ' *' : '' ) . '" ' .
								'value="' . esc_attr( $commenter['comment_author_email'] ) . '" ' .
								'size="30"' . $aria_req . ' />' .
								'</div></div>',
					'url'    => '<div class="row">' .
								'<div class="form-group col-md-6">' .
								'<input id="url" name="url" type="text" class="form-control" ' .
								'placeholder="' . esc_attr__( 'Website', 'stax' ) . '" ' .
								'value="' . esc_attr( $commenter['comment_author_url'] ) . '" size="30" />' .
								'</div></div>',
				];

				$profile_link = admin_url( 'profile.php' );

				$comments_args = [
					'fields'              => apply_filters( 'comment_form_default_fields', $fields ),
					'comment_field'       => '<p class="comment-form-comment"><textarea class="form-control" id="comment" name="comment" cols="45" rows="8" aria-required="true" placeholder="' . esc_attr__( 'Comment', 'stax' ) . '"></textarea></p>',
					'logged_in_as'        => '<p class="logged-in-as">' .
											wp_kses_post(
												sprintf(
													__( 'Logged in as <a href="%1$s" class="user-link">%2$s</a>. <a href="%3$s" class="logout-link" title="Log out of this account">Log out?</a>', 'stax' ),
													$profile_link,
													$user_identity,
													wp_logout_url( apply_filters( 'the_permalink', get_permalink() ) )
												)
											) .
											 '</p>',
					'cancel_reply_link'   => esc_html__( 'Cancel reply', 'stax' ),
					'cancel_reply_before' => '',
					'cancel_reply_after'  => '',
					'label_submit'        => esc_html__( 'Post comment', 'stax' ),
					'class_submit'        => 'btn btn-sm btn-dark btn-glow btn-ripple btn-toggle-state btn-icon--left',
					'submit_button'       => '<button type="submit" class="%3$s" id="%2$s" name="%1$s">
												<span class="btn--default-state">
												   %4$s
												</span>
												<span class="btn--loading-state">' . stax()->load_icon( 'loading icon-is-loading', 18 ) . esc_html__( 'Loading', 'stax' ) . '</span>
								              </button>',
					'submit_field'        => '<p class="form-submit text-center mb-0"><input type="hidden" name="action" value="post_comment">%1$s %2$s</p>',
					'must_log_in'         => '<p class="must-log-in">' . sprintf( wp_kses_post( 'You must be <a href="%s">logged in</a> to post a comment.', 'stax' ), wp_login_url( apply_filters( 'the_permalink', get_permalink() ) ) ) . '</p>',
					'id_form'             => 'sqv-comment-form',
					'class_form'          => 'comment-form svq-form',
					'action'              => admin_url( 'admin-ajax.php' ),
				];

				if ( have_comments() ) {
					$comments_args = array_merge(
						$comments_args,
						[
							'title_reply'    => esc_html__( 'Write a response', 'stax' ),
							'title_reply_to' => esc_html__( 'Write a response to %s', 'stax' ),
						]
					);
				} else {
					$comments_args = array_merge(
						$comments_args,
						[
							'title_reply'        => '',
							'title_reply_to'     => '',
							'title_reply_before' => '',
							'title_reply_after'  => '',
						]
					);
				}

				comment_form( $comments_args );
				?>
			</div>
			<?php
		endif;
	}

	/**
	 * Add notification are before the form
	 */
	public function comment_form_notifications() {
		if ( is_user_logged_in() ) {
			echo '<div class="comment-form-notifications svq-notice request-response"></div>';
		} else {
			echo '<div class="comment-form-notifications svq-notice request-response not-logged"></div>';
		}
	}

	/**
	 * Move comment textarea at the bottom of the form
	 *
	 * @param $fields
	 *
	 * @return mixed
	 */
	public function move_comment_field( $fields ) {
		$comment_field = $fields['comment'];
		unset( $fields['comment'] );
		$fields['comment'] = $comment_field;

		if ( isset( $fields['cookies'] ) ) {
			$cookies_field = $fields['cookies'];
			unset( $fields['cookies'] );
			$fields['cookies'] = $cookies_field;
		}

		return $fields;
	}

	/**
	 * Rewrite default single comment item
	 *
	 * @param $comment
	 * @param $args
	 * @param $depth
	 */
	public function single_comment( $comment, $args, $depth ) {
		$GLOBALS['comment'] = $comment;

		global $comment_depth;
		$old_comment_depth = $comment_depth;
		$comment_depth     = $depth;

		$comment_class = '';

		if ( wp_doing_ajax() ) {
			$post = get_post( $comment->comment_post_ID );

			if ( $comment->user_id === $post->post_author ) {
				$comment_class = 'bypostauthor';
			}
		}

		?>

	<li id="comment-<?php comment_ID(); ?>"
		<?php comment_class( $comment_class ); ?> data-target="<?php comment_ID(); ?>">
		<div class="entry-comment" id="div-comment-<?php comment_ID(); ?>">
			<div class="comment-header">
				<div class="author-avatar">
					<?php echo get_avatar( (int) $comment->user_id, $depth > 1 ? '50' : '56', '', '', [ 'class' => 'avatar-img' ] ); ?>
				</div>

				<div class="comment-author">
					<span class="by-line ">
						<span class="author">
							<?php echo get_comment_author_link(); ?>
						</span>
					</span>
					<span class="posted-on">
						<?php echo esc_html( get_comment_date() ); ?>
					</span>
				</div>

				<?php if ( is_user_logged_in() && current_user_can( 'edit_comment', $comment->comment_ID ) && (int) $comment->user_id === get_current_user_id() && '0' !== $comment->comment_approved ) : ?>
					<div class="comment-action">
						<div class="dropdown">
							<button class="btn btn-link btn-toggle-state" data-toggle="dropdown"
									aria-haspopup="true" aria-expanded="false" data-action="btn-toggle-state">
								<span class="btn--default-state">
									<?php echo stax()->load_icon( 'ellipsis-v', 24 ); ?>
								</span>
								<span class="btn--active-state">
									<?php echo stax()->load_icon( 'close', 24 ); ?>
								</span>
							</button>

							<div class="dropdown-menu dropdown-menu-right">
								<?php edit_comment_link( esc_html__( 'Edit', 'stax' ) ); ?>
							</div>
						</div>
					</div>
				<?php endif; ?>
			</div>

			<?php
			if ( '0' === $comment->comment_approved ) {
				echo '<span class="unapproved">';
				esc_html_e( 'Your comment is awaiting moderation.', 'stax' );
				echo '</span>';
			}
			?>

			<div class="comment-body">
				<div class="comment-content">
					<?php comment_text(); ?>
				</div>
				<?php
				if ( '0' !== $comment->comment_approved ) {
					$args = array_merge(
						$args,
						[
							'reply_text' => esc_html__( 'Reply', 'stax' ),
							'login_text' => esc_html__( 'Log in to reply.', 'stax' ),
							'depth'      => $depth,
							'max_depth'  => get_option( 'thread_comments_depth' ),
							'before'     => '<span class="comment-reply-link-wrap"><span class="comment-reply-link">',
							'after'      => '</span></span>',
							'add_below'  => 'div-comment',
						]
					);

					comment_reply_link(
						$args,
						$comment
					);
				}
				?>
			</div>
		</div>

		<?php

		$comment_depth = $old_comment_depth;
	}

	/**
	 * After single comment
	 */
	public function single_comment_after() {
		echo '</li>';
	}

	/**
	 * Replace default comment reply class
	 *
	 * @param $link
	 *
	 * @return mixed
	 */
	public function comment_reply_class( $link ) {
		$link = str_replace( "class='comment-reply-link", "class='comment-reply-link btn btn-link btn-sm", $link );

		return $link;
	}

	/**
	 * Add custom class to cancel reply link
	 *
	 * @param $link
	 *
	 * @return mixed
	 */
	public function cancel_comment_reply_class( $link ) {
		$link = str_replace( 'id="cancel-comment-reply-link"', 'id="cancel-comment-reply-link" class="btn btn-link btn-sm cancel-comment-reply-link"', $link );

		return $link;
	}

	/**
	 * Change edit comment class
	 *
	 * @param $link
	 *
	 * @return mixed
	 */
	public function comment_edit_link( $link ) {
		$link = str_replace( 'comment-edit-link', 'comment-edit-link dropdown-item', $link );

		return $link;
	}

	/**
	 * Handle post comment request
	 */
	public function post_comment_handler() {
		if ( ! isset( $_POST['action'] ) || $_POST['action'] !== 'post_comment' ) {
			return wp_send_json(
				[
					'status' => 'error',
					'data'   => null,
				]
			);
		}

		$comment = wp_handle_comment_submission( $_POST );

		if ( ! is_wp_error( $comment ) ) {
			ob_start();
			$this->single_comment( $comment, [], $this->get_comment_depth( $comment ) );
			$this->single_comment_after();
			$comment_html = ob_get_clean();

			return wp_send_json(
				[
					'status'         => 'success',
					'data'           => $comment_html,
					'reply_to'       => $_POST['comment_parent'],
					'comment_id'     => $comment->comment_ID,
					'comment_status' => wp_get_comment_status( $comment ),
				]
			);
		}

		$error_list = '<ul>';
		foreach ( $comment->errors as $pack ) {
			foreach ( $pack as $string ) {
				$error_list .= '<li>' . $string . '</li>';
			}
		}
		$error_list .= '</ul>';

		return wp_send_json(
			[
				'status' => 'error',
				'data'   => $error_list,
			]
		);
	}

	/**
	 * Get depth of a specific comment
	 *
	 * @param \WP_Comment $comment
	 * @param int         $last_depth
	 *
	 * @return int
	 */
	public function get_comment_depth( \WP_Comment $comment, $last_depth = 1 ) {
		$parent_id = $comment->comment_parent;

		if ( $parent_id ) {
			$comment = get_comment( $parent_id );
			$last_depth ++;
			$last_depth = $this->get_comment_depth( $comment, $last_depth );
		}

		return $last_depth;
	}

	/**
	 * @return string
	 */
	public function comments_prev_nav_link() {
		return 'class="btn btn-light btn-sm button-ripple btn-icon--left svq-get-prev-comments"';
	}

	/**
	 * @return string
	 */
	public function comments_next_nav_link() {
		return 'class="btn btn-light btn-sm button-ripple btn-icon--right svq-get-next-comments"';
	}

	/**
	 * Adds a pagination reference point attribute for amp-live-list when theme supports AMP.
	 *
	 * This is used by the navigation_markup_template filter in the comments template.
	 *
	 * @link https://www.ampproject.org/docs/reference/components/amp-live-list#pagination
	 *
	 * @param string $markup Navigation markup.
	 *
	 * @return string Filtered markup.
	 */
	public function filter_add_amp_live_list_pagination_attribute( string $markup ) {
		return preg_replace( '/(\s*<[a-z0-9_-]+)/i', '$1 pagination ', $markup, 1 );
	}
}
