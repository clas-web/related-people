<?php

/**
 * The RelatedPeople_WidgetShortcodeControl class for the "Related People" plugin.
 * Derived from the official WP RSS widget.
 * 
 * Shortcode Example:
 * [related_people title="" items="3" related_taxonomy="post_tag" restrict_taxonomy="" all_pages="no"]
 * 
 * @package    clas-buttons
 * @author     Crystal Barton <atrus1701@gmail.com>
 */
if( !class_exists('RelatedPeople_WidgetShortcodeControl') ):
class RelatedPeople_WidgetShortcodeControl extends WidgetShortcodeControl
{
	/**
	 * The minimum number of RSS items.
	 * @var  int
	 */	
	private static $MIN_ITEMS = 1;

	/**
	 * The maximum number of RSS items.
	 * @var  int
	 */
	private static $MAX_ITEMS = 20;
		
	/**
	 * Constructor.
	 * Setup the properties and actions.
	 */
	public function __construct()
	{
		$widget_ops = array(
			'description'	=> 'Add a Related People widget.',
		);
		
		parent::__construct( 'related-people', 'Related People', $widget_ops );
	}
	

	/**
	 * Enqueues the scripts or styles needed for the control in the site frontend.
	 */
	public function enqueue_scripts()
	{
		wp_enqueue_style( 'related-people', plugins_url( '/style.css' , __FILE__ ) );
	}
	
	
	/**
	 * Output the widget form in the admin.
	 * Use this function instead of form.
	 * @param   array   $options  The current settings for the widget.
	 */
	public function print_widget_form( $options )
	{
		$options = $this->merge_options( $options );
		extract( $options );
		
		$taxonomies = get_taxonomies();
		?>

		<p>
		<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:' ); ?></label> 
		<input id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" class="widefat">
		</p>
		
		<p>
		<label for="<?php echo $this->get_field_id( 'items' ); ?>"><?php _e( 'Number of items:' ); ?></label> 
		<select id="<?php echo $this->get_field_id( 'items' ); ?>" name="<?php echo $this->get_field_name( 'items' ); ?>">
			<?php for( $i = self::$MIN_ITEMS; $i < self::$MAX_ITEMS+1; $i++ ): ?>
			
				<option value="<?php echo $i; ?>" <?php selected($i, $items); ?>><?php echo $i; ?></option>
			
			<?php endfor; ?>
		</select>
		</p>

		<p>
		<label for="<?php echo $this->get_field_id( 'related_taxonomy' ); ?>"><?php _e( 'Related Taxonomy:' ); ?></label> 
		<input type="radio" name="<?php echo $this->get_field_name( 'related_taxonomy' ); ?>" value="" <?php checked( $related_taxonomy, '' ); ?> />None<br/>
		
		<?php foreach( $taxonomies as $taxonomy ): ?>
			<input type="radio" name="<?php echo $this->get_field_name( 'related_taxonomy' ); ?>" value="<?php echo esc_attr( $taxonomy->name ); ?>" <?php checked( $related_taxonomy, $taxonomy->name ); ?> /><?php echo $taxonomy->name; ?><br/>
		<?php endforeach; ?>
		</p>
		
		<p>
		<label for="<?php echo $this->get_field_id( 'restrict_taxonomy' ); ?>"><?php _e( 'Restrict to Taxonomy:' ); ?></label> 
		<input type="radio" name="<?php echo $this->get_field_name( 'restrict_taxonomy' ); ?>" value="" <?php checked( $restrict_taxonomy, '' ); ?> />None<br/>
		
		<?php foreach( $taxonomies as $taxonomy ): ?>
			<input type="radio" name="<?php echo $this->get_field_name( 'restrict_taxonomy' ); ?>" value="<?php echo esc_attr( $taxonomy->name ); ?>" <?php checked( $restrict_taxonomy, $taxonomy->name ); ?> /><?php echo $taxonomy->name; ?><br/>
		<?php endforeach; ?>
		</p>
		
		<p>
		<label for="<?php echo $this->get_field_id( 'all_pages' ); ?>"><?php _e( 'Show On All Pages:' ); ?></label> 
		<input name="<?php echo $this->get_field_name( 'all_pages' ); ?>" type="hidden" value="no">
		<input id="<?php echo $this->get_field_id( 'all_pages' ); ?>" name="<?php echo $this->get_field_name( 'all_pages' ); ?>" type="checkbox" value="yes" <?php checked( $all_pages, 'yes' ); ?> />
		</p>
		<?php
	}
	
	
	/**
	 * Get the default settings for the widget or shortcode.
	 * @return  array  The default settings.
	 */
	public function get_default_options()
	{
		return array(
			'title' => '',
			'items' => 3,
			'related_taxonomy' => '',
			'restrict_taxonomy' => '',
			'all_pages' => 'no',
		);
	}
	
	
	/**
	 * Echo the widget or shortcode contents.
	 * @param   array  $options  The current settings for the control.
	 * @param   array  $args     The display arguments.
	 */
	public function print_control( $options, $args = null )
	{
		global $post;
		
		$options = $this->merge_options( $options );
		extract( $options );
		
		if( 'yes' != $all_pages && ! is_single() ) {
			return;
		}
		
		echo $args['before_widget'];
		echo '<div id="related-people-control-'.self::$index.'" class="wscontrol related-people-control">';
		
		if( !empty( $title ) ) {
			$title = $args['before_title'] . $title . $args['after_title'];
		}
		
		relppl_print_related_people( $title, $post, $items, $related_taxonomy, $same_taxonomy_terms );
		
		echo '</div>';
		echo $args['after_widget'];	
	}
}
endif;

