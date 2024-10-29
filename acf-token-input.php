<?php
/*
Plugin Name: ACF Token Input
Plugin URI: http://www.navz.me/plugins/acf-token-input
Description: Bringing the functionality into ACF similar to Facebook whereby you can search and select multiple items
Version: 1.1.0
Author: Navneil Naicker
Author URI: http://www.navz.me/
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html
*/

// exit if accessed directly
if( ! defined( 'ABSPATH' ) ) exit;
	// check if class already exists
	if( !class_exists('acf_plugin_acf_token_input') ) :
		class acf_plugin_acf_token_input {
			
			/*
			*  __construct
			*
			*  This function will setup the class functionality
			*
			*  @type	function
			*  @date	17/02/2016
			*  @since	1.0.0
			*
			*  @param	n/a
			*  @return	n/a
			*/
			
			function __construct() {
				// vars
				$this->settings = array(
					'version'	=> '1.1.0',
					'url'		=> plugin_dir_url( __FILE__ ),
					'path'		=> plugin_dir_path( __FILE__ )
				);
				// include field
				add_action('acf/include_field_types', 	array($this, 'include_field_types')); // v5
				add_action('acf/register_fields', 		array($this, 'include_field_types')); // v4
			}
			
			
			/*
			*  include_field_types
			*
			*  This function will include the field type class
			*
			*  @type	function
			*  @date	17/02/2016
			*  @since	1.0.0
			*
			*  @param	$version (int) major ACF version. Defaults to false
			*  @return	n/a
			*/
			function include_field_types( $version = false ) {
				// support empty $version
				if( !$version ) $version = 4;
				// include
				include_once('fields/acf-token-input-v' . $version . '.php');
			}
		}
		// initialize
		new acf_plugin_acf_token_input();
		// class_exists check
		endif;
?>