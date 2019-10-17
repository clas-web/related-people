<?php
/**
 *
 * @param  WP_Post  $person
 * @param  int  $count
 * @param  String  $related_taxonomy
 */
if ( ! function_exists( 'relppl_get_related_people' ) ) :
	function relppl_get_related_people( $person, $count, $restrict_taxonomy, $related_taxonomy = '' ) {
		$restricted_terms = wp_get_post_terms( $person->ID, $restrict_taxonomy );
		if ( is_wp_error( $restricted_terms ) ) {
			return array();
		}

		$restricted_term_slugs = array();
		foreach ( $restricted_terms as $term ) {
			$restricted_term_slugs[] = $term->slug;
		}

		$args = array(
			'posts_per_page' => $count,
			'post_type'      => $person->post_type,
			'orderby'        => 'rand',
			'tax_query'      => array(
				'relation' => 'AND',
				array(
					'taxonomy' => $restrict_taxonomy,
					'field'    => 'slug',
					'terms'    => $restricted_term_slugs,
				),
			),
			'exclude'        => array( $person->ID ),
		);

		if ( $related_taxonomy != '' ) {
			$related_terms = wp_get_post_terms( $person->ID, $related_taxonomy );
			$people        = array();

			if ( is_wp_error( $related_terms ) ) {
				return array();
			}

			$related_term_slugs = array();
			foreach ( $related_terms as $term ) {
				$related_term_slugs[] = $term->slug;
			}

			$args['tax_query'][] = array(
				'taxonomy' => $related_taxonomy,
				'field'    => 'slug',
				'terms'    => $related_term_slugs,
			);
		}

		// apl_print( $args );
		return get_posts( $args );
	}
endif;


/**
 *
 * @param  String  $title
 * @param  WP_Post  $person
 * @param  int  $count
 * @param  String  $related_taxonomy
 */
if ( ! function_exists( 'relppl_print_related_people' ) ) :
	function relppl_print_related_people( $title, $person, $count, $restrict_taxonomy, $related_taxonomy = '' ) {
		$people = relppl_get_related_people( $person, $count, $restrict_taxonomy, $related_taxonomy );

		if ( is_array( $people ) && count( $people ) > 0 ) {
			echo '<div class="related-people-title">' . $title . '</div>';
			echo '<div class="related-people">';
			foreach ( $people as $person ) {
				$terms            = wp_get_post_terms( $person->ID, $restrict_taxonomy );
				$person_interests = relppl_get_related_interests( $terms );
				echo '<div class="person">' .
				'<a href="' . get_permalink( $person->ID ) . '" title="' . $person_interests . '">' .
				$person->post_title .
				'</a></div>';
			}
			echo '</div>';
		}
	}
endif;

/**
 * Return the Academic Interests for a related person.
 *
 * @param Array $terms All Academic Interest terms returned by the connection.
 * @return String The related academic terms.
 */
if ( ! function_exists( 'relppl_get_related_interests' ) ) :
	function relppl_get_related_interests( $terms ) {
		$relppl_interests = '';
		foreach ( $terms as $term ) {
			if ( $relppl_interests != '' ) {
				$relppl_interests = $relppl_interests . ', ' . $term->name;
			} else {
				$relppl_interests = $term->name;
			}
		}
			return $relppl_interests;
	}
endif;

/**
 * Return the number of members found from a Connection Group query.
 *
 * @param String $connection_group The Connection Group that will be queried. If blank, returns current query.
 * @return Integer The number of Connection Group members.
 */
if ( ! function_exists( 'relppl_get_connection_group_members' ) ) :
	function relppl_get_connection_group_members( $connection_group ) {

		global $wp_the_query;

		// Retrieve term for the Connection Group if one has been passed
		if ( $connection_group ) {
			$connection_group_terms = get_terms(
				array(
					'name' => $connection_group,
				)
			);

			// Return term count
			if ( $connection_group_terms ) {
				return $connection_group_terms[0]->count;
			} else {
				return 0;
			}
		} else {
			// Otherwise, do not create a new query, return the found posts from the current query
			return $wp_the_query->found_posts;
		}
	}
endif;

/**
 * Build the URL for Connection Groups.
 *
 * @param Object $group Filled with data on a particular Connection Group.
 */
if ( ! function_exists( 'relppl_print_connection_group_link' ) ) :
	function relppl_print_connection_group_link( $group ) {
		$num_of_matches = relppl_get_connection_group_members( $group['name'] );
		$group_classes  = ( $group['slug'] ? $group['slug'] : null );
		echo vtt_get_anchor( $group['link'], sprintf( _n( '%s person', '%s people', $num_of_matches ), $num_of_matches ), $group_classes, $group['name'] );
	}
endif;
