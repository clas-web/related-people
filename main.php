<?php
/*
Plugin Name: Related People
Plugin URI: https://github.com/clas-web/related-people
Description: 
Version: 0.2.1
Author: Crystal Barton
Author URI: https://www.linkedin.com/in/crystalbarton
GitHub Plugin URI: https://github.com/clas-web/related-people
*/


if( !defined('RELATED_PEOPLE') ):

/**
 * The full title of the Related People plugin.
 * @var  string
 */
define( 'RELATED_PEOPLE', 'Related People' );

/**
 * True if debug is active, otherwise False.
 * @var  bool
 */
define( 'RELATED_PEOPLE_DEBUG', false );

/**
 * The path to the plugin.
 * @var  string
 */
define( 'RELATED_PEOPLE_PLUGIN_PATH', __DIR__ );

/**
 * The url to the plugin.
 * @var  string
 */
define( 'RELATED_PEOPLE_PLUGIN_URL', plugins_url('', __FILE__) );

/**
 * The version of the plugin.
 * @var  string
 */
define( 'RELATED_PEOPLE_VERSION', '1.0.1' );

/**
 * The database version of the plugin.
 * @var  string
 */
define( 'RELATED_PEOPLE_DB_VERSION', '1.1' );

/**
 * The database options key for the Related People version.
 * @var  string
 */
define( 'RELATED_PEOPLE_VERSION_OPTION', 'related-people-version' );

/**
 * The database options key for the Related People database version.
 * @var  string
 */
define( 'RELATED_PEOPLE_DB_VERSION_OPTION', 'related-people-db-version' );

/**
 * The full path to the log file used for debugging.
 * @var  string
 */
define( 'RELATED_PEOPLE_LOG_FILE', __DIR__.'/log.txt' );

endif;

require_once( __DIR__ . '/functions.php' );

/* Add widget and shortcode */
require_once( __DIR__ . '/control.php' );
RelatedPeople_WidgetShortcodeControl::register_widget();
RelatedPeople_WidgetShortcodeControl::register_shortcode();

