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

// TODO: remove span titles within styled title divs
// TODO: fix line break in title once using styled css
// TODO: convert titles to aria-labels to prevent titles from appearing twice

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
				$terms  = wp_get_post_terms( $person->ID, $restrict_taxonomy );
				$groups = wp_get_post_terms( $person->ID, 'connection-group' );

				$person_interests = relppl_get_related_values( $terms );
				$person_groups    = relppl_get_related_values( $groups );

				echo '<div class="person">' .
				'<a href="' . get_permalink( $person->ID ) .
				'" title="Groups: ' . $person_groups . "\n" . 'Academic Interests: ' . $person_interests . '">' .
				'<span title="">' . $person->post_title . '</span>' .
				'</a></div>';
			}
			echo '</div>';
		}
	}
endif;

/**
 * Return the Academic Interests or Groups for a related person as a string.
 *
 * @param Array $terms All Academic terms returned by the connection.
 * @return String The related academic terms.
 */
if ( ! function_exists( 'relppl_get_related_values' ) ) :
	function relppl_get_related_values( $terms ) {
		$relppl_academic_terms = '';
		foreach ( $terms as $term ) {
			if ( '' !== $relppl_academic_terms ) {
				$relppl_academic_terms = $relppl_academic_terms . ', ' . $term->name;
			} else {
				$relppl_academic_terms = $term->name;
			}
		}
		return $relppl_academic_terms;
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

//TODO: convert titles to aria-labels, use get_tax_link or something like that instead of vtt_get_anchor

/**
 * Build the URL for Connection Groups and Links.
 *
 * @param Object $taxonomy A particular Connection Group or Link.
 */
if ( ! function_exists( 'relppl_print_connection_url' ) ) :
	function relppl_print_connection_url( $taxonomy ) {
		$num_of_matches   = relppl_get_connection_group_members( $taxonomy['name'] );
		$taxonomy_classes = ( $taxonomy['slug'] ? $taxonomy['slug'] : '' );
		if ( '' !== $taxonomy_classes ) {
			$taxonomy_classes .= ' ';
		};
		$taxonomy_classes .= 'connection-group';
		echo vtt_get_anchor(
			$taxonomy['link'],
			sprintf( _n( '%s person', '%s people', $num_of_matches ), $num_of_matches ),
			$taxonomy_classes,
			'<span title="">' . $taxonomy['name'] . '</span>'
		);
	}
endif;
