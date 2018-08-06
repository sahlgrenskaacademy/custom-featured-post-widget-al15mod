<?php
/**
 * Plugin Name
 *
 * @package           Custom_Featured_Post_Widget_AL15MOD
 * @author            Pontus Sundén/GU
 * @license           GPL-2.0+
 * @link              http://gamajo.com/
 * @copyright         2013 Gary Jones, Gamajo Tech, 2018 Pontus Sundén
 */

/**
 * Custom Featured Post widget class.
 *
 * @package Custom_Featured_Post_Widget_AL15MOD
 * @author  Pontus Sundén
 */
class Custom_Featured_Post extends Genesis_Featured_Post {

	/**
	 * Echo the widget content.
	 *
	 * @since 1.0.0
	 *
	 * @param array $args Display arguments including before_title, after_title, before_widget, and after_widget.
	 * @param array $instance The settings for the particular instance of the widget
	 */
	function widget( $args, $instance ) {

		global $wp_query, $_genesis_displayed_ids;

		extract( $args );

		//* Merge with defaults
		$instance = wp_parse_args( (array) $instance, $this->defaults );

		echo $before_widget;

		//* Set up the author bio
		if ( ! empty( $instance['title'] ) ) {

  		// if more from category-link is clicked, also link heading (psu)
  		if ( ! empty( $instance['more_from_category'] ) && ! empty( $instance['posts_cat'] ) )
  			$before_title = sprintf(
    				'<a href="%1$s" title="%2$s" class="featured-title">',
    				esc_url( get_category_link( $instance['posts_cat'] ) ),
    				esc_attr( get_cat_name( $instance['posts_cat'] ) )
  			  ) . $before_title;
        $after_title =  $after_title . '</a>';

		  $title_html = apply_filters( 'widget_title', $instance['title'], $instance, $this->id_base );
			echo $before_title . $title_html . $after_title;

    }

		$query_args = array(
			'post_type' => 'post',
			'cat'       => $instance['posts_cat'],
			'showposts' => $instance['posts_num'],
			'offset'    => $instance['posts_offset'],
			'orderby'   => $instance['orderby'],
			'order'     => $instance['order'],
		);

		//* Exclude displayed IDs from this loop?
		if ( $instance['exclude_displayed'] )
			$query_args['post__not_in'] = (array) $_genesis_displayed_ids;

		$wp_query = new WP_Query( $query_args );

		if ( have_posts() ) : while ( have_posts() ) : the_post();

			$_genesis_displayed_ids[] = get_the_ID();

			genesis_markup( array(
				'html5'   => '<article %s>',
				'xhtml'   => sprintf( '<div class="%s">', implode( ' ', get_post_class() ) ),
				'context' => 'entry',
			) );

			$image = genesis_get_image( array(
				'format'  => 'html',
				'size'    => $instance['image_size'],
				'context' => 'featured-post-widget',
				'attr'    => genesis_parse_attr( 'entry-image-widget' ),
			) );

			if ( $instance['show_image'] && $image )
				printf( '<a href="%s" title="%s" class="%s"><div class="img-wrapper">%s</div></a>', get_permalink(), the_title_attribute( 'echo=0' ), esc_attr( $instance['image_alignment'] ), $image );

			if ( ! empty( $instance['show_gravatar'] ) ) {
				echo '<span class="' . esc_attr( $instance['gravatar_alignment'] ) . '">';
				echo get_avatar( get_the_author_meta( 'ID' ), $instance['gravatar_size'] );
				echo '</span>';
			}

			if ( $instance['show_title'] )
				echo genesis_html5() ? '<header class="entry-header">' : '';

				if ( ! empty( $instance['show_title'] ) ) {

					$title = get_the_title() ? get_the_title() : __( '(no title)', 'genesis' );

          if ( genesis_html5() ) {
						printf( '<h3 class="entry-title"><a href="%s">%s</a></h3>', get_permalink(), esc_html( $title ) );
					} else {
						printf( '<h3><a href="%s">%s</a></h3>', get_permalink(), esc_html( $title ) );
          }

				}

				if ( ! empty( $instance['show_byline'] ) && ! empty( $instance['post_info'] ) )
					printf( genesis_html5() ? '<p class="entry-meta">%s</p>' : '<p class="byline post-info">%s</p>', do_shortcode( $instance['post_info'] ) );

			if ( $instance['show_title'] )
				echo genesis_html5() ? '</header>' : '';

			if ( ! empty( $instance['show_content'] ) ) {

				printf( '<a href="%s">', get_permalink() ); // psu
				echo genesis_html5() ? '<div class="entry-content">' : '';

				if ( 'excerpt' == $instance['show_content'] ) {
					the_excerpt();
				}
				elseif ( 'content-limit' == $instance['show_content'] ) {
					the_content_limit( (int) $instance['content_limit'], esc_html( $instance['more_text'] ) );
				} else {

					global $more;

					$orig_more = $more;
					$more = 0;

					the_content( esc_html( $instance['more_text'] ) );

					$more = $orig_more;

				}

				echo genesis_html5() ? '</div>' : '';
				printf('</a>'); // psu

			}

			genesis_markup( array(
				'html5' => '</article>',
				'xhtml' => '</div>',
			) );

		endwhile; endif;

		//* Restore original query
		wp_reset_query();

		//* The EXTRA Posts (list)
		if ( ! empty( $instance['extra_num'] ) ) {
			if ( ! empty( $instance['extra_title'] ) )
				echo $before_title . esc_html( $instance['extra_title'] ) . $after_title;

			$offset = intval( $instance['posts_num'] ) + intval( $instance['posts_offset'] );

			$query_args = array(
				'cat'       => $instance['posts_cat'],
				'showposts' => $instance['extra_num'],
				'offset'    => $offset,
			);

			$wp_query = new WP_Query( $query_args );

			$listitems = '';

			if ( have_posts() ) {
				while ( have_posts() ) {
					the_post();
					$_genesis_displayed_ids[] = get_the_ID();
					$listitems .= sprintf( '<li><a href="%s" title="%s">%s</a></li>', get_permalink(), the_title_attribute( 'echo=0' ), get_the_title() );
				}

				if ( mb_strlen( $listitems ) > 0 )
					printf( '<ul>%s</ul>', $listitems );
			}

			//* Restore original query
			wp_reset_query();
		}

		if ( ! empty( $instance['more_from_category'] ) && ! empty( $instance['posts_cat'] ) )
			printf(
				'<p class="more-from-category"><a href="%1$s" title="%2$s">%3$s</a></p>',
				esc_url( get_category_link( $instance['posts_cat'] ) ),
				esc_attr( get_cat_name( $instance['posts_cat'] ) ),
				esc_html( $instance['more_from_category_text'] )
			);

		echo $after_widget;

	}

}
