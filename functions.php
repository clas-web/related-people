<?php
/**
 * 
 * @param  WP_Post  $person  
 * @param  int  $count
 * @param  String  $related_taxonomy  
 */
if( ! function_exists( 'relppl_get_related_people' ) ):
function relppl_get_related_people( $person, $count, $restrict_taxonomy, $related_taxonomy = '' )
{
	$restricted_terms = wp_get_post_terms( $person->ID, $restrict_taxonomy );
	if( is_wp_error( $restricted_terms ) ) {
		return array();
	}
	
	$restricted_term_slugs = array();
	foreach( $restricted_terms as $term ) {
		$restricted_term_slugs[] = $term->slug;
	}
	
	
	$args = array(
		'posts_per_page' => $count,
		'post_type' => $person->post_type,
		'orderby' => 'rand',
		'tax_query' => array(
			'relation' => 'AND',
			array(
				'taxonomy' => $restrict_taxonomy,
				'field'    => 'slug',
				'terms'    => $restricted_term_slugs,
			),
		),
		'exclude' => array( $person->ID ),
	);

	
	if( $related_taxonomy != '' )
	{
		$related_terms = wp_get_post_terms( $person->ID, $related_taxonomy );
		$people = array();
		
		if( is_wp_error( $related_terms ) ) {
			return array();
		}
		
		$related_term_slugs = array();
		foreach( $related_terms as $term ) {
			$related_term_slugs[] = $term->slug;
		}
		
		$args['tax_query'][] = array(
			'taxonomy' => $related_taxonomy,
			'field'    => 'slug',
			'terms'    => $related_term_slugs,
		);
	}
	
	
// 	apl_print( $args );
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
if( ! function_exists('relppl_print_related_people') ):
function relppl_print_related_people( $title, $person, $count, $restrict_taxonomy, $related_taxonomy = '' )
{
	$people = relppl_get_related_people( $person, $count, $restrict_taxonomy, $related_taxonomy );
	
	echo $title;
	
	echo '<div class="related-people">';
	
	if( ! is_array( $people ) || empty( $people ) ) {
		echo '<div class="no-related">No related people found.</div>';
	} else {
		foreach( $people as $person ) {
			echo '<div class="person">' . 
				'<a href="' . get_permalink( $person->ID ) . '" title="' . $person->post_title . '">' .
				$person->post_title . 
				'</a></div>';
		}
	}
	
	echo '</div>';
}
endif;


