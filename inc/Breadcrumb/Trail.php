<?php
/**
 * Stax\Breadcrumb\Trail class
 *
 * @package stax
 */

namespace Stax\Breadcrumb;

use function Stax\stax;
use Stax\Customizer\Config;

/**
 * Class Trail
 *
 * @package Stax\Breadcrumb
 */
class Trail {

	/**
	 * Array of items belonging to the current breadcrumb trail.
	 *
	 * @since  0.1.0
	 * @access public
	 * @var    array
	 */
	public $items = [];

	/**
	 * Arguments used to build the breadcrumb trail.
	 *
	 * @since  0.1.0
	 * @access public
	 * @var    array
	 */
	public $args = [];

	/**
	 * Sets up the breadcrumb trail.
	 *
	 * @param array $args The arguments for how to build the breadcrumb trail.
	 *
	 * @since 0.6.0
	 * @access public
	 */
	public function __construct( $args = [] ) {

		/* Remove the bbPress breadcrumbs. */
		add_filter( 'bbp_get_breadcrumb', '__return_false' );

		$container_class  = 'breadcrumb';
		$container_class .= stax()->get_option( Config::OPTION_SINGLE_POST_CATEORY_BREADCRUMB_ANIMATION ) && stax()->has_media() ? ' will-animate' : '';

		$defaults = [
			'container'       => 'ol',
			'container_class' => $container_class,
			'item_tag'        => 'li',
			'separator'       => '&#47;',
			'before'          => '',
			'after'           => '',
			'show_on_front'   => true,
			'network'         => false,
			// 'show_edit_link'  => false,
			'show_title'      => false,
			'show_browse'     => false,
			'rich_snippet'    => true,

			/* Post taxonomy (examples follow). */
			'post_taxonomy'   => [
				// 'post'  => 'post_tag',
				// 'book'  => 'genre',
			],

			/* Labels for text used (see Breadcrumb_Trail::default_labels). */
			'labels'          => [],
		];

		$this->args = apply_filters( 'breadcrumb_trail_args', wp_parse_args( $args, $defaults ) );

		/* Merge the user-added labels with the defaults. */
		$this->args['labels'] = wp_parse_args( $this->args['labels'], $this->default_labels() );

		$this->do_trail_items();
	}

	/**
	 * Formats and outputs the breadcrumb trail.
	 *
	 * @return string
	 * @since  0.6.0
	 * @access public
	 */
	public function trail() {

		$breadcrumb = '';

		/* Connect the breadcrumb trail if there are items in the trail. */
		if ( ! empty( $this->items ) && is_array( $this->items ) ) {

			/* Make sure we have a unique array of items. */
			$this->items = array_unique( $this->items );

			// google rich snippets
			$vocabulary = '';
			if ( true === $this->args['rich_snippet'] && count( $this->items ) > 1 ) {
				$vocabulary = ' itemscope itemtype="http://schema.org/BreadcrumbList"';
			}

			/* Open the breadcrumb trail containers. */
			$breadcrumb = "\n\t\t" . '<' . tag_escape( $this->args['container'] ) . ' class="' .
						  esc_attr( $this->args['container_class'] ) . '"' . $vocabulary . '  data-cssanimate="' .
						  esc_attr( stax()->get_option( Config::OPTION_SINGLE_POST_CATEORY_BREADCRUMB_ANIMATION ) ) . '">';

			/* If $before was set, wrap it in a container. */
			$breadcrumb .= ( ! empty( $this->args['before'] ) ? "\n\t\t\t" . '<span class="trail-before">' .
																$this->args['before'] . '</span> ' . "\n\t\t\t" : '' );

			/* Add 'browse' label if it should be shown. */
			if ( true === $this->args['show_browse'] ) {
				$breadcrumb .= "\n\t\t\t" . '<span class="trail-browse">' . $this->args['labels']['browse'] . '</span> ';
			}

			/* Format the separator. */
			$separator = ( ! empty( $this->args['separator'] ) ? '<span class="sep">' . $this->args['separator'] . '</span>' : '' );

			$count = 0;
			foreach ( $this->items as &$link ) {
				$count ++;
				$class = ' class="breadcrumb__item"';
				if ( count( $this->items ) == $count ) {
					$class = ' class="breadcrumb__item active"';
				}
				if ( true === $this->args['rich_snippet'] && count( $this->items ) > 1 ) {
					$link = preg_replace( '!rel=".+?"|rel=\'.+?\'|!', '', $link );
					$link = preg_replace(
						'!(<a.*?>)(.*?)(</a>)!',
						'$1<span itemprop="name">$2</span>$3' .
																   '<meta itemprop="position" content="' . $count . '" />',
						$link
					);

					$link       = str_replace( '<a ', '<a itemprop="item" class="breadcrumb__link" ', $link, $count_replace );
					$micro_data = '';
					if ( $count_replace > 0 ) {
						$micro_data = ' itemprop="itemListElement" itemscope itemtype="http://schema.org/ListItem"';
					}
					$link = '<' . $this->args['item_tag'] . $micro_data . $class . '>' . $link . '</' . $this->args['item_tag'] . '>';
				} else {
					$link = '<' . $this->args['item_tag'] . $class . '>' . $link . '</' . $this->args['item_tag'] . '>';
				}
			}

			/* Join the individual trail items into a single string. */
			$breadcrumb .= join( "\n\t\t\t {$separator} ", $this->items );

			/* If $after was set, wrap it in a container. */
			$breadcrumb .= ( ! empty( $this->args['after'] ) ? "\n\t\t\t" . ' <span class="trail-after">' . $this->args['after'] . '</span>' : '' );

			/* Close the breadcrumb trail containers. */
			$breadcrumb .= "\n\t\t" . '</' . tag_escape( $this->args['container'] ) . '>';
		}

		/* Allow developers to filter the breadcrumb trail HTML. */
		$breadcrumb = apply_filters( 'breadcrumb_trail', $breadcrumb, $this->args );

		return $breadcrumb;
	}

	/**
	 * Returns an array of the default labels.
	 *
	 * @return array
	 * @since  0.6.0
	 * @access public
	 */
	public function default_labels() {

		$labels = [
			'browse'              => __( 'Browse:', 'stax' ),
			'home'                => __( 'Home', 'stax' ),
			'error_404'           => __( '404 Not Found', 'stax' ),
			'archives'            => __( 'Archives', 'stax' ),
			/* Translators: %s is the search query. The HTML entities are opening and closing curly quotes. */
			'search'              => __( 'Search results for &#8220;%s&#8221;', 'stax' ),
			/* Translators: %s is the page number. */
			'paged'               => __( 'Page %s', 'stax' ),
			/* Translators: Minute archive title. %s is the minute time format. */
			'archive_minute'      => __( 'Minute %s', 'stax' ),
			/* Translators: Weekly archive title. %s is the week date format. */
			'archive_week'        => __( 'Week %s', 'stax' ),

			/* "%s" is replaced with the translated date/time format. */
			'archive_minute_hour' => '%s',
			'archive_hour'        => '%s',
			'archive_day'         => '%s',
			'archive_month'       => '%s',
			'archive_year'        => '%s',
		];

		return $labels;
	}

	/**
	 * Runs through the various WordPress conditional tags to check the current page being viewed.  Once
	 * a condition is met, a specific method is launched to add items to the $items array.
	 *
	 * @return void
	 * @since  0.6.0
	 * @access public
	 */
	public function do_trail_items() {

		/* If viewing the front page. */
		if ( is_front_page() ) {
			$this->do_front_page_items();
		} /* If not viewing the front page. */
		else {

			/* Add the network and site home links. */
			$this->do_network_home_link();
			$this->do_site_home_link();

			/* If viewing the home/blog page. */
			if ( is_home() ) {
				$this->do_posts_page_items();
			} /* If viewing a single post. */
			elseif ( is_singular() ) {
				$this->do_singular_items();
			} /* If viewing an archive page. */
			elseif ( is_archive() ) {

				if ( is_post_type_archive() ) {
					$this->do_post_type_archive_items();
				} elseif ( is_category() || is_tag() || is_tax() ) {
					$this->do_term_archive_items();
				} elseif ( is_author() ) {
					$this->do_user_archive_items();
				} elseif ( get_query_var( 'minute' ) && get_query_var( 'hour' ) ) {
					$this->do_minute_hour_archive_items();
				} elseif ( get_query_var( 'minute' ) ) {
					$this->do_minute_archive_items();
				} elseif ( get_query_var( 'hour' ) ) {
					$this->do_hour_archive_items();
				} elseif ( is_day() ) {
					$this->do_day_archive_items();
				} elseif ( get_query_var( 'w' ) ) {
					$this->do_week_archive_items();
				} elseif ( is_month() ) {
					$this->do_month_archive_items();
				} elseif ( is_year() ) {
					$this->do_year_archive_items();
				} else {
					$this->do_default_archive_items();
				}
			} /* If viewing a search results page. */
			elseif ( is_search() ) {
				$this->do_search_items();
			} /* If viewing the 404 page. */
			elseif ( is_404() ) {
				$this->do_404_items();
			}
		}

		/* Add paged items if they exist. */
		$this->do_paged_items();

		/* Allow developers to overwrite the items for the breadcrumb trail. */
		$this->items = apply_filters( 'breadcrumb_trail_items', $this->items, $this->args );
	}

	/**
	 * Gets front items based on $wp_rewrite->front.
	 *
	 * @return void
	 * @since  0.6.0
	 * @access public
	 */
	public function do_rewrite_front_items() {
		global $wp_rewrite;

		if ( $wp_rewrite->front ) {
			$this->do_path_parents( $wp_rewrite->front );
		}
	}

	/**
	 * Adds the page/paged number to the items array.
	 *
	 * @return void
	 * @since  0.6.0
	 * @access public
	 */
	public function do_paged_items() {

		/* If viewing a paged singular post. */
		if ( is_singular() && 1 < get_query_var( 'page' ) && true === $this->args['show_title'] ) {
			$this->items[] = sprintf( $this->args['labels']['paged'], number_format_i18n( absint( get_query_var( 'page' ) ) ) );
		} /* If viewing a paged archive-type page. */
		elseif ( is_paged() && true === $this->args['show_title'] ) {
			$this->items[] = sprintf( $this->args['labels']['paged'], number_format_i18n( absint( get_query_var( 'paged' ) ) ) );
		}

	}

	/**
	 * Adds the network (all sites) home page link to the items array.
	 *
	 * @return void
	 * @since  0.6.0
	 * @access public
	 */
	public function do_network_home_link() {
		if ( is_multisite() && ! is_main_site() && true === $this->args['network'] ) {
			$this->items[] = '<a href="' . esc_url( network_home_url( '/' ) ) . '" title="' . esc_attr( $this->args['labels']['home'] ) . '" rel="home">' . $this->args['labels']['home'] . '</a>';
		}
	}

	/**
	 * Adds the current site's home page link to the items array.
	 *
	 * @return void
	 * @since  0.6.0
	 * @access public
	 */
	public function do_site_home_link() {
		$label         = ( is_multisite() && ! is_main_site() && true === $this->args['network'] ) ? get_bloginfo( 'name' ) : $this->args['labels']['home'];
		$rel           = ( is_multisite() && ! is_main_site() && true === $this->args['network'] ) ? '' : ' rel="home"';
		$this->items[] = '<a href="' . esc_url( home_url( '/' ) ) . '" title="' . esc_attr( get_bloginfo( 'name' ) ) . '"' . $rel . '>' . $label . '</a>';
	}

	/**
	 * Adds items for the front page to the items array.
	 *
	 * @return void
	 * @since  0.6.0
	 * @access public
	 */
	public function do_front_page_items() {
		/* Only show front items if the 'show_on_front' argument is set to 'true'. */
		if ( true === $this->args['show_on_front'] || is_paged() || ( is_singular() && 1 < get_query_var( 'page' ) ) ) {

			/* If on a paged view, add the home link items. */
			if ( is_paged() ) {
				$this->do_network_home_link();
				$this->do_site_home_link();
			} /* If on the main front page, add the network home link item and the home item. */
			else {
				$this->do_network_home_link();

				if ( true === $this->args['show_title'] ) {
					$this->items[] = ( is_multisite() && true === $this->args['network'] ) ? get_bloginfo( 'name' ) : $this->args['labels']['home'];
				}
			}
		}
	}

	/**
	 * Adds items for the posts page (i.e., is_home()) to the items array.
	 *
	 * @return void
	 * @since  0.6.0
	 * @access public
	 */
	public function do_posts_page_items() {
		if ( is_main_query() ) {
			return;
		}
		/* Get the post ID and post. */
		$post_id = get_queried_object_id();
		$post    = get_post( $post_id );

		/* If the post has parents, add them to the trail. */
		if ( isset( $post->post_parent ) && 0 < $post->post_parent ) {
			$this->do_post_parents( $post->post_parent );
		}

		/* Get the page title. */
		$title = get_the_title( $post_id );
		/*
			  if ( get_cfield( 'custom_title', $post_id ) && get_cfield( 'custom_title', $post_id ) != '' ) {
					$title = get_cfield( 'custom_title', $post_id );
				}*/

		/* Add the posts page item. */
		if ( is_paged() ) {
			$this->items[] = '<a href="' . get_permalink( $post_id ) . '" title="' . esc_attr( $title ) . '">' . $title . '</a>';
		} elseif ( $title && true === $this->args['show_title'] ) {
			$this->items[] = $title;
		}
	}

	/**
	 * Adds singular post items to the items array.
	 *
	 * @return void
	 * @since  0.6.0
	 * @access public
	 */
	public function do_singular_items() {

		/* Get the queried post. */
		$post    = get_queried_object();
		$post_id = get_queried_object_id();

		/* If the post has a parent, follow the parent trail. */
		if ( 0 < $post->post_parent ) {
			$this->do_post_parents( $post->post_parent );
		} /* If the post doesn't have a parent, get its hierarchy based off the post type. */
		else {
			$this->do_post_hierarchy( $post_id );
		}

		/* Display terms for specific post type taxonomy if requested. */
		$this->do_post_terms( $post_id );

		/* End with the post title. */
		if ( $post_title = single_post_title( '', false ) ) {

			/*
				  if ( get_cfield( 'custom_title', $post_id ) && get_cfield( 'custom_title', $post_id ) != '' ) {
						$post_title = get_cfield( 'custom_title', $post_id );
					}*/

			if ( 1 < get_query_var( 'page' ) ) {
				$this->items[] = '<a href="' . get_permalink( $post_id ) . '" title="' . esc_attr( $post_title ) . '">' . $post_title . '</a>';
			} elseif ( true === $this->args['show_title'] ) {
				$this->items[] = $post_title;
			}
		}
	}

	/**
	 * Adds a specific post's parents to the items array.
	 *
	 * @param int $post_id The ID of the post to get the parents of.
	 *
	 * @return void
	 * @since  0.6.0
	 * @access public
	 */
	public function do_post_parents( $post_id ) {
		$parents = [];

		while ( $post_id ) {

			/* Get the post by ID. */
			$post = get_post( $post_id );

			$page_title = get_the_title( $post_id );

			/*
					  if ( get_cfield( 'custom_title', $post_id ) && get_cfield( 'custom_title', $post_id ) != '' ) {
							$page_title = get_cfield( 'custom_title', $post_id );
						}*/

			/* Add the formatted post link to the array of parents. */
			$parents[] = '<a href="' . get_permalink( $post_id ) . '" title="' . $page_title . '">' . $page_title . '</a>';

			/* If there's no longer a post parent, brea out of the loop. */
			if ( 0 >= $post->post_parent ) {
				break;
			}

			/* Change the post ID to the parent post to continue looping. */
			$post_id = $post->post_parent;
		}

		/* Get the post hierarchy based off the final parent post. */
		$this->do_post_hierarchy( $post_id );

		/* Merge the parent items into the items array. */
		$this->items = array_merge( $this->items, array_reverse( $parents ) );
	}

	/**
	 * Adds a post's terms from a specific taxonomy to the items array.
	 *
	 * @param int $post_id The ID of the post to get the terms for.
	 *
	 * @return void
	 * @since  0.6.0
	 * @access public
	 */
	public function do_post_terms( $post_id ) {

		/* Get the post type. */
		$post_type = get_post_type( $post_id );

		/* Add the terms of the taxonomy for this post. */
		if ( ! empty( $this->args['post_taxonomy'][ $post_type ] ) ) {
			$this->add_post_terms( $post_id, $this->args['post_taxonomy'][ $post_type ] );
		}
	}

	/**
	 * Adds a post's terms from a specific taxonomy to the items array.
	 *
	 * @param int    $post_id The ID of the post to get the terms for.
	 * @param string $taxonomy The taxonomy to get the terms from.
	 *
	 * @return void
	 * @since  1.0.0
	 * @access protected
	 */
	protected function add_post_terms( $post_id, $taxonomy ) {

		// Get the post categories.
		$terms = get_the_terms( $post_id, $taxonomy );
		$term  = null;

		// Check that categories were returned.
		if ( $terms && ! is_wp_error( $terms ) ) {

			if ( $terms ) {
				$primary = get_post_meta( $post_id, '_yoast_wpseo_primary_product', true );
				foreach ( $terms as $the_term ) {
					if ( $primary == $the_term->term_id ) {
						$term = get_term( $the_term, $taxonomy );
						break;
					}
				}
			} else {
				// Sort the terms by ID and get the first category.
				usort( $terms, '_usort_terms_by_ID' );
				$term = get_term( $terms[0], $taxonomy );
			}

			$term = apply_filters( 'svq_breadcrumb_term', $term, $taxonomy );
			// If the category has a parent, add the hierarchy to the trail.
			if ( 0 < $term->parent ) {
				$this->do_term_parents( $term->parent, $taxonomy );
			}

			// Add the category archive link to the trail.
			$this->items[] = sprintf( '<a href="%s">%s</a>', esc_url( get_term_link( $term, $taxonomy ) ), $term->name );
		}
	}

	/**
	 * Adds a specific post's hierarchy to the items array.  The hierarchy is determined by post type's
	 * rewrite arguments and whether it has an archive page.
	 *
	 * @param int $post_id The ID of the post to get the hierarchy for.
	 *
	 * @return void
	 * @since  0.6.0
	 * @access public
	 */
	public function do_post_hierarchy( $post_id ) {

		/* Get the post type. */
		$post_type        = get_post_type( $post_id );
		$post_type_object = get_post_type_object( $post_type );

		/* If this is the 'post' post type, get the rewrite front items and map the rewrite tags. */
		if ( 'post' === $post_type ) {

			/* Add $wp_rewrite->front to the trail. */
			$this->do_rewrite_front_items();

			/* Map the rewrite tags. */
			$this->map_rewrite_tags( $post_id, get_option( 'permalink_structure' ) );
		} /* If the post type has rewrite rules. */
		elseif ( false !== $post_type_object->rewrite ) {

			/* If 'with_front' is true, add $wp_rewrite->front to the trail. */
			if ( $post_type_object->rewrite['with_front'] ) {
				$this->do_rewrite_front_items();
			}

			/* If there's a path, check for parents. */
			if ( ! empty( $post_type_object->rewrite['slug'] ) && ! $post_type_object->has_archive ) {
				$this->do_path_parents( $post_type_object->rewrite['slug'] );
			}
		}

		/* If there's an archive page, add it to the trail. */
		if ( ! empty( $post_type_object->has_archive ) ) {

			$label = ! empty( $post_type_object->labels->archive_title ) ? $post_type_object->labels->archive_title : $post_type_object->labels->name;

			$this->items[] = '<a href="' . get_post_type_archive_link( $post_type ) . '">' . $label . '</a>';
		}
	}

	/**
	 * Gets post types by slug.  This is needed because the get_post_types() function doesn't exactly
	 * match the 'has_archive' argument when it's set as a string instead of a boolean.
	 *
	 * @param int $slug The post type archive slug to search for.
	 *
	 * @return void
	 * @since  0.6.0
	 * @access public
	 */
	public function get_post_types_by_slug( $slug ) {

		$return = [];

		$post_types = get_post_types( [], 'objects' );

		foreach ( $post_types as $type ) {

			if ( $slug === $type->has_archive || ( true === $type->has_archive && $slug === $type->rewrite['slug'] ) ) {
				$return[] = $type;
			}
		}

		return $return;
	}

	/**
	 * Adds the items to the trail items array for taxonomy term archives.
	 *
	 * @return void
	 * @global object $wp_rewrite
	 * @since  0.6.0
	 * @access public
	 */
	public function do_term_archive_items() {
		global $wp_rewrite;

		/* Get some taxonomy and term variables. */
		$term     = get_queried_object();
		$taxonomy = get_taxonomy( $term->taxonomy );

		/* If there are rewrite rules for the taxonomy. */
		if ( false !== $taxonomy->rewrite ) {

			/* If 'with_front' is true, dd $wp_rewrite->front to the trail. */
			if ( isset( $taxonomy->rewrite['with_front'] ) && $taxonomy->rewrite['with_front'] && $wp_rewrite->front ) {
				$this->do_rewrite_front_items();
			}

			/* Get parent pages by path if they exist. */
			$this->do_path_parents( $taxonomy->rewrite['slug'] );

			/* Add post type archive if its 'has_archive' matches the taxonomy rewrite 'slug'. */
			if ( $taxonomy->rewrite['slug'] ) {

				$slug = trim( $taxonomy->rewrite['slug'], '/' );

				/**
				 * Deals with the situation if the slug has a '/' between multiple strings. For
				 * example, "movies/genres" where "movies" is the post type archive.
				 */
				$matches = explode( '/', $slug );

				/* If matches are found for the path. */
				if ( isset( $matches ) ) {

					/* Reverse the array of matches to search for posts in the proper order. */
					$matches = array_reverse( $matches );

					/* Loop through each of the path matches. */
					foreach ( $matches as $match ) {

						/* If a match is found. */
						$slug = $match;

						/* Get public post types that match the rewrite slug. */
						$post_types = $this->get_post_types_by_slug( $match );

						if ( ! empty( $post_types ) ) {

							$post_type_object = $post_types[0];

							/* Add support for a non-standard label of 'archive_title' (special use case). */
							$label = ! empty( $post_type_object->labels->archive_title ) ? $post_type_object->labels->archive_title : $post_type_object->labels->name;

							/* Add the post type archive link to the trail. */
							$this->items[] = '<a href="' . get_post_type_archive_link( $post_type_object->name ) . '" title="' . esc_attr( $label ) . '">' . wp_kses_post( $label ) . '</a>';

							/* Break out of the loop. */
							break;
						}
					}
				}
			}
		}

		/* If the taxonomy is hierarchical, list its parent terms. */
		if ( is_taxonomy_hierarchical( $term->taxonomy ) && $term->parent ) {
			$this->do_term_parents( $term->parent, $term->taxonomy );
		}

		/* Add the term name to the trail end. */
		if ( is_paged() ) {
			$this->items[] = '<a href="' . esc_url( get_term_link( $term, $term->taxonomy ) ) . '" title="' . esc_attr( single_term_title( '', false ) ) . '">' . single_term_title( '', false ) . '</a>';
		} elseif ( true === $this->args['show_title'] ) {
			$this->items[] = single_term_title( '', false );
		}
	}

	/**
	 * Adds the items to the trail items array for post type archives.
	 *
	 * @return void
	 * @since  0.6.0
	 * @access public
	 */
	public function do_post_type_archive_items() {

		/* Get the post type object. */
		$post_type_object = get_post_type_object( get_query_var( 'post_type' ) );

		if ( false !== $post_type_object->rewrite ) {

			/* If 'with_front' is true, add $wp_rewrite->front to the trail. */
			if ( $post_type_object->rewrite['with_front'] ) {
				$this->do_rewrite_front_items();
			}

			/*
			 If there's a rewrite slug, check for parents. */
			/*
			if (!empty($post_type_object->rewrite['slug']))
				$this->do_path_parents($post_type_object->rewrite['slug']);*/
		}

		/* Add the post type [plural] name to the trail end. */
		if ( is_paged() ) {
			$this->items[] = '<a href="' . esc_url( get_post_type_archive_link( $post_type_object->name ) ) . '" title="' . esc_attr( post_type_archive_title( '', false ) ) . '">' . post_type_archive_title( '', false ) . '</a>';
		} elseif ( true === $this->args['show_title'] ) {
			$this->items[] = post_type_archive_title( '', false );
		}
	}

	/**
	 * Adds the items to the trail items array for user (author) archives.
	 *
	 * @return void
	 * @global object $wp_rewrite
	 * @since  0.6.0
	 * @access public
	 */
	public function do_user_archive_items() {
		global $wp_rewrite;

		/* Add $wp_rewrite->front to the trail. */
		$this->do_rewrite_front_items();

		/* Get the user ID. */
		$user_id = get_query_var( 'author' );

		/* If $author_base exists, check for parent pages. */
		if ( ! empty( $wp_rewrite->author_base ) ) {
			$this->do_path_parents( $wp_rewrite->author_base );
		}

		/* Add the author's display name to the trail end. */
		if ( is_paged() ) {
			$this->items[] = '<a href="' . esc_url( get_author_posts_url( $user_id ) ) . '" title="' . esc_attr( get_the_author_meta( 'display_name', $user_id ) ) . '">' . get_the_author_meta( 'display_name', $user_id ) . '</a>';
		} elseif ( true === $this->args['show_title'] ) {
			$this->items[] = get_the_author_meta( 'display_name', $user_id );
		}
	}

	/**
	 * Adds the items to the trail items array for minute + hour archives.
	 *
	 * @return void
	 * @since  0.6.0
	 * @access public
	 */
	public function do_minute_hour_archive_items() {

		/* Add $wp_rewrite->front to the trail. */
		$this->do_rewrite_front_items();

		/* Add the minute + hour item. */
		if ( true === $this->args['show_title'] ) {
			$this->items[] = sprintf( $this->args['labels']['archive_minute_hour'], get_the_time( _x( 'g:i a', 'minute and hour archives time format', 'stax' ) ) );
		}
	}

	/**
	 * Adds the items to the trail items array for minute archives.
	 *
	 * @return void
	 * @since  0.6.0
	 * @access public
	 */
	public function do_minute_archive_items() {

		/* Add $wp_rewrite->front to the trail. */
		$this->do_rewrite_front_items();

		/* Add the minute item. */
		if ( true === $this->args['show_title'] ) {
			$this->items[] = sprintf( $this->args['labels']['archive_minute'], get_the_time( _x( 'i', 'minute archives time format', 'stax' ) ) );
		}
	}

	/**
	 * Adds the items to the trail items array for hour archives.
	 *
	 * @return void
	 * @since  0.6.0
	 * @access public
	 */
	public function do_hour_archive_items() {

		/* Add $wp_rewrite->front to the trail. */
		$this->do_rewrite_front_items();

		/* Add the hour item. */
		if ( true === $this->args['show_title'] ) {
			$this->items[] = sprintf( $this->args['labels']['archive_hour'], get_the_time( _x( 'g a', 'hour archives time format', 'stax' ) ) );
		}
	}

	/**
	 * Adds the items to the trail items array for day archives.
	 *
	 * @return void
	 * @since  0.6.0
	 * @access public
	 */
	public function do_day_archive_items() {

		/* Add $wp_rewrite->front to the trail. */
		$this->do_rewrite_front_items();

		/* Get year, month, and day. */
		$year  = sprintf( $this->args['labels']['archive_year'], get_the_time( _x( 'Y', 'yearly archives date format', 'stax' ) ) );
		$month = sprintf( $this->args['labels']['archive_month'], get_the_time( _x( 'F', 'monthly archives date format', 'stax' ) ) );
		$day   = sprintf( $this->args['labels']['archive_day'], get_the_time( _x( 'j', 'daily archives date format', 'stax' ) ) );

		/* Add the year and month items. */
		$this->items[] = '<a href="' . get_year_link( get_the_time( 'Y' ) ) . '" title="' . esc_attr( $year ) . '">' . $year . '</a>';
		$this->items[] = '<a href="' . get_month_link( get_the_time( 'Y' ), get_the_time( 'm' ) ) . '" title="' . esc_attr( $month ) . '">' . $month . '</a>';

		/* Add the day item. */
		if ( is_paged() ) {
			$this->items[] = '<a href="' . get_day_link( get_the_time( 'Y' ), get_the_time( 'm' ), get_the_time( 'd' ) ) . '" title="' . esc_attr( $day ) . '">' . $day . '</a>';
		} elseif ( true === $this->args['show_title'] ) {
			$this->items[] = $day;
		}
	}

	/**
	 * Adds the items to the trail items array for week archives.
	 *
	 * @return void
	 * @since  0.6.0
	 * @access public
	 */
	public function do_week_archive_items() {

		/* Add $wp_rewrite->front to the trail. */
		$this->do_rewrite_front_items();

		/* Get the year and week. */
		$year = sprintf( $this->args['labels']['archive_year'], get_the_time( _x( 'Y', 'yearly archives date format', 'stax' ) ) );
		$week = sprintf( $this->args['labels']['archive_week'], get_the_time( _x( 'W', 'weekly archives date format', 'stax' ) ) );

		/* Add the year item. */
		$this->items[] = '<a href="' . get_year_link( get_the_time( 'Y' ) ) . '" title="' . esc_attr( $year ) . '">' . $year . '</a>';

		/* Add the week item. */
		if ( is_paged() ) {
			$this->items[] = get_archives_link(
				add_query_arg(
					[
						'm' => get_the_time( 'Y' ),
						'w' => get_the_time( 'W' ),
					],
					esc_url( home_url( '/' ) )
				),
				$week,
				false
			);
		} elseif ( true === $this->args['show_title'] ) {
			$this->items[] = $week;
		}
	}

	/**
	 * Adds the items to the trail items array for month archives.
	 *
	 * @return void
	 * @since  0.6.0
	 * @access public
	 */
	public function do_month_archive_items() {

		/* Add $wp_rewrite->front to the trail. */
		$this->do_rewrite_front_items();

		/* Get the year and month. */
		$year  = sprintf( $this->args['labels']['archive_year'], get_the_time( _x( 'Y', 'yearly archives date format', 'stax' ) ) );
		$month = sprintf( $this->args['labels']['archive_month'], get_the_time( _x( 'F', 'monthly archives date format', 'stax' ) ) );

		/* Add the year item. */
		$this->items[] = '<a href="' . get_year_link( get_the_time( 'Y' ) ) . '" title="' . esc_attr( $year ) . '">' . $year . '</a>';

		/* Add the month item. */
		if ( is_paged() ) {
			$this->items[] = '<a href="' . get_month_link( get_the_time( 'Y' ), get_the_time( 'm' ) ) . '" title="' . esc_attr( $month ) . '">' . $month . '</a>';
		} elseif ( true === $this->args['show_title'] ) {
			$this->items[] = $month;
		}
	}

	/**
	 * Adds the items to the trail items array for year archives.
	 *
	 * @return void
	 * @since  0.6.0
	 * @access public
	 */
	public function do_year_archive_items() {

		/* Add $wp_rewrite->front to the trail. */
		$this->do_rewrite_front_items();

		/* Get the year. */
		$year = sprintf( $this->args['labels']['archive_year'], get_the_time( _x( 'Y', 'yearly archives date format', 'stax' ) ) );

		/* Add the year item. */
		if ( is_paged() ) {
			$this->items[] = '<a href="' . get_year_link( get_the_time( 'Y' ) ) . '" title="' . esc_attr( $year ) . '">' . $year . '</a>';
		} elseif ( true === $this->args['show_title'] ) {
			$this->items[] = $year;
		}
	}

	/**
	 * Adds the items to the trail items array for archives that don't have a more specific method
	 * defined in this class.
	 *
	 * @return void
	 * @since  0.6.0
	 * @access public
	 */
	public function do_default_archive_items() {

		/* If this is a date-/time-based archive, add $wp_rewrite->front to the trail. */
		if ( is_date() || is_time() ) {
			$this->do_rewrite_front_items();
		}

		if ( true === $this->args['show_title'] ) {
			$this->items[] = $this->args['labels']['archives'];
		}
	}

	/**
	 * Adds the items to the trail items array for search results.
	 *
	 * @return void
	 * @since  0.6.0
	 * @access public
	 */
	public function do_search_items() {

		if ( is_paged() ) {
			$this->items[] = '<a href="' . get_search_link() . '" title="' . esc_attr( sprintf( $this->args['labels']['search'], get_search_query() ) ) . '">' . sprintf( $this->args['labels']['search'], get_search_query() ) . '</a>';
		} elseif ( true === $this->args['show_title'] ) {
			$this->items[] = sprintf( $this->args['labels']['search'], get_search_query() );
		}
	}

	/**
	 * Adds the items to the trail items array for 404 pages.
	 *
	 * @return void
	 * @since  0.6.0
	 * @access public
	 */
	public function do_404_items() {

		if ( true === $this->args['show_title'] ) {
			$this->items[] = $this->args['labels']['error_404'];
		}
	}

	/**
	 * Get parent posts by path.  Currently, this method only supports getting parents of the 'page'
	 * post type.  The goal of this function is to create a clear path back to home given what would
	 * normally be a "ghost" directory.  If any page matches the given path, it'll be added.
	 *
	 * @param string $path The path (slug) to search for posts by.
	 *
	 * @return void
	 * @since  0.6.0
	 * @access public
	 */
	function do_path_parents( $path ) {

		/* Trim '/' off $path in case we just got a simple '/' instead of a real path. */
		$path = trim( $path, '/' );

		/* If there's no path, return. */
		if ( empty( $path ) ) {
			return;
		}

		/* Get parent post by the path. */
		$post = get_page_by_path( $path );

		if ( ! empty( $post ) ) {
			$this->do_post_parents( $post->ID );
		} elseif ( is_null( $post ) ) {

			/* Separate post names into separate paths by '/'. */
			$path = trim( $path, '/' );
			preg_match_all( '/\/.*?\z/', $path, $matches );

			/* If matches are found for the path. */
			if ( isset( $matches ) ) {

				/* Reverse the array of matches to search for posts in the proper order. */
				$matches = array_reverse( $matches );

				/* Loop through each of the path matches. */
				foreach ( $matches as $match ) {

					/* If a match is found. */
					if ( isset( $match[0] ) ) {

						/* Get the parent post by the given path. */
						$path = str_replace( $match[0], '', $path );
						$post = get_page_by_path( trim( $path, '/' ) );

						/* If a parent post is found, set the $post_id and break out of the loop. */
						if ( ! empty( $post ) && 0 < $post->ID ) {
							$this->do_post_parents( $post->ID );
							break;
						}
					}
				}
			}
		}
	}

	/**
	 * Searches for term parents of hierarchical taxonomies.  This function is similar to the WordPress
	 * function get_category_parents() but handles any type of taxonomy.
	 *
	 * @param int    $term_id ID of the term to get the parents of.
	 * @param string $taxonomy Name of the taxonomy for the given term.
	 *
	 * @return void
	 * @since  0.6.0
	 */
	function do_term_parents( $term_id, $taxonomy ) {

		/* Set up some default arrays. */
		$parents = [];

		/* While there is a parent ID, add the parent term link to the $parents array. */
		while ( $term_id ) {

			/* Get the parent term. */
			$term = get_term( $term_id, $taxonomy );

			/* Add the formatted term link to the array of parent terms. */
			$parents[] = '<a href="' . get_term_link( $term, $taxonomy ) . '" title="' . esc_attr( $term->name ) . '">' . $term->name . '</a>';

			/* Set the parent term's parent as the parent ID. */
			$term_id = $term->parent;
		}

		/* If we have parent terms, reverse the array to put them in the proper order for the trail. */
		if ( ! empty( $parents ) ) {
			$parents = array_reverse( $parents );
		}
		$this->items = array_merge( $this->items, $parents );
	}

	/**
	 * Turns %tag% from permalink structures into usable links for the breadcrumb trail.  This feels kind of
	 * hackish for now because we're checking for specific %tag% examples and only doing it for the 'post'
	 * post type.  In the future, maybe it'll handle a wider variety of possibilities, especially for custom post
	 * types.
	 *
	 * @param int    $post_id ID of the post whose parents we want.
	 * @param string $path Path of a potential parent page.
	 * @param array  $args Mixed arguments for the menu.
	 *
	 * @return array
	 * @since  0.6.0
	 * @access public
	 */
	public function map_rewrite_tags( $post_id, $path ) {

		/* Get the post based on the post ID. */
		$post = get_post( $post_id );

		/* If no post is returned, an error is returned, or the post does not have a 'post' post type, return. */
		if ( empty( $post ) || is_wp_error( $post ) || 'post' !== $post->post_type ) {
			return $trail;
		}

		/* Trim '/' from both sides of the $path. */
		$path = trim( $path, '/' );

		/* Split the $path into an array of strings. */
		$matches = explode( '/', $path );

		/* If matches are found for the path. */
		if ( is_array( $matches ) ) {

			/* Loop through each of the matches, adding each to the $trail array. */
			foreach ( $matches as $match ) {

				/* Trim any '/' from the $match. */
				$tag = trim( $match, '/' );

				/* If using the %year% tag, add a link to the yearly archive. */
				if ( '%year%' == $tag ) {
					$this->items[] = '<a href="' . get_year_link( get_the_time( 'Y', $post_id ) ) . '">' . sprintf( $this->args['labels']['archive_year'], get_the_time( _x( 'Y', 'yearly archives date format', 'stax' ) ) ) . '</a>';
				} /* If using the %monthnum% tag, add a link to the monthly archive. */
				elseif ( '%monthnum%' == $tag ) {
					$this->items[] = '<a href="' . get_month_link( get_the_time( 'Y', $post_id ), get_the_time( 'm', $post_id ) ) . '">' . sprintf( $this->args['labels']['archive_month'], get_the_time( _x( 'F', 'monthly archives date format', 'stax' ) ) ) . '</a>';
				} /* If using the %day% tag, add a link to the daily archive. */
				elseif ( '%day%' == $tag ) {
					$this->items[] = '<a href="' . get_day_link( get_the_time( 'Y', $post_id ), get_the_time( 'm', $post_id ), get_the_time( 'd', $post_id ) ) . '">' . sprintf( $this->args['labels']['archive_day'], get_the_time( _x( 'j', 'daily archives date format', 'stax' ) ) ) . '</a>';
				} /* If using the %author% tag, add a link to the post author archive. */
				elseif ( '%author%' == $tag ) {
					$this->items[] = '<a href="' . get_author_posts_url( $post->post_author ) . '" title="' . esc_attr( get_the_author_meta( 'display_name', $post->post_author ) ) . '">' . get_the_author_meta( 'display_name', $post->post_author ) . '</a>';
				} /* If using the %category% tag, add a link to the first category archive to match permalinks. */
				elseif ( '%category%' == $tag ) {

					/* Force override terms in this post type. */
					$this->args['post_taxonomy'][ $post->post_type ] = false;

					/* Get the post categories. */
					$terms = get_the_category( $post_id );

					/* Check that categories were returned. */
					if ( $terms ) {

						/* Sort the terms by ID and get the first category. */
						usort( $terms, '_usort_terms_by_ID' );
						$term = get_term( $terms[0], 'category' );

						/* If the category has a parent, add the hierarchy to the trail. */
						if ( 0 < $term->parent ) {
							$this->do_term_parents( $term->parent, 'category' );
						}

						/* Add the category archive link to the trail. */
						$this->items[] = '<a href="' . get_term_link( $term, 'category' ) . '" title="' . esc_attr( $term->name ) . '">' . $term->name . '</a>';
					}
				}
			}
		}
	}
}
