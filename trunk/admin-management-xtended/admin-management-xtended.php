<?php
/**
 * The main plugin file
 *
 * @package WordPress_Plugins
 * @subpackage AdminManagementXtended
 */
 
/*
Plugin Name: Admin Management Xtended
Version: 1.8.4
Plugin URI: http://www.schloebe.de/wordpress/admin-management-xtended-plugin/
Description: <strong>WordPress 2.5+ only.</strong> Extends admin functionalities by introducing: toggling post/page visibility inline, changing page order with drag'n'drop, inline category management, inline tag management, changing publication date inline, changing post slug inline, toggling comment status open/closed, hide draft posts, change media order, change media description inline, toggling link visibility, changing link categories
Author: Oliver Schl&ouml;be
Author URI: http://www.schloebe.de/


Copyright 2008 Oliver Schlöbe (email : webmaster@schloebe.de)

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; either version 2 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/


/**
 * Pre-2.6 compatibility
 */
if ( !defined( 'WP_CONTENT_URL' ) )
	define( 'WP_CONTENT_URL', get_option( 'siteurl' ) . '/wp-content' );
if ( !defined( 'WP_CONTENT_DIR' ) )
	define( 'WP_CONTENT_DIR', ABSPATH . 'wp-content' );
if ( !defined( 'WP_PLUGIN_URL' ) )
	define( 'WP_PLUGIN_URL', WP_CONTENT_URL. '/plugins' );
if ( !defined( 'WP_PLUGIN_DIR' ) )
	define( 'WP_PLUGIN_DIR', WP_CONTENT_DIR . '/plugins' );



/**
 * Checks if a given plugin is active
 *
 * @since 1.4.0
 * @author scripts@schloebe.de
 *
 * @param string
 * @return bool
 */
function ame_is_plugin_active( $plugin_filename ) {
	$plugins = get_option('active_plugins');
	if( !is_array($plugins) ) settype($plugins, 'array');
	return ( in_array($plugin_filename, $plugins) );
}

/**
 * Define the plugin version
 */
define("AME_VERSION", "1.8.4");

/**
 * Define the global var AMEISWP25, returning bool if at least WP 2.5 is running
 */
define('AMEISWP25', version_compare($wp_version, '2.4', '>='));

/**
 * Define the global var ISINSTBTM, returning bool
 * if the 'Better Tags Manager' plugin is installed
 */
define('ISINSTBTM', ame_is_plugin_active('better-tags-manager/better-tags-manager.php') );

/**
 * Define the plugin path slug
 */
define("AME_PLUGINPATH", "/" . plugin_basename( dirname(__FILE__) ) . "/");

/**
 * Define the plugin full url
 */
define("AME_PLUGINFULLURL", WP_PLUGIN_URL . AME_PLUGINPATH );

/**
 * Define the plugin full directory
 */
define("AME_PLUGINFULLDIR", WP_PLUGIN_DIR . AME_PLUGINPATH );

/**
 * Define the plugin image set
 */
define("AME_IMGSET", get_option("ame_imgset") . "/" );

/** 
* The AdminManagementXtended class
*
* @package WordPress_Plugins
* @subpackage AdminManagementXtended
* @since 1.4.0
* @author scripts@schloebe.de
*/
class AdminManagementXtended {

	/**
 	* The AdminManagementXtended class constructor
 	* initializing required stuff for the plugin
 	*
 	* @since 1.4.0
 	* @author scripts@schloebe.de
 	*/
	function adminmanagementxtended() {
		if ( function_exists('load_plugin_textdomain') ) {
			/**
			* Load all the l18n data from languages path
			*/
			if ( !defined('WP_PLUGIN_DIR') ) {
                load_plugin_textdomain('admin-management-xtended', str_replace( ABSPATH, '', dirname(__FILE__) ) . '/languages');
        	} else {
                load_plugin_textdomain('admin-management-xtended', false, dirname(plugin_basename(__FILE__)) . '/languages');
        	}
		}
		
		if( ISINSTBTM ) {
			add_action('admin_notices', array(&$this, 'wpIncompCheck'));
		}
		
		if ( !AMEISWP25 ) {
			add_action('admin_notices', array(&$this, 'wpVersionFailed'));
			return;
		}
		
		set_include_path( dirname(__FILE__) . PATH_SEPARATOR . get_include_path() );
		/** 
 		* This file holds all of the general information and functions
 		*/
		require_once(dirname (__FILE__) . '/' . 'general-functions.php');

		/** 
 		* This file holds all of the post functions
 		*/
		require_once(dirname (__FILE__) . '/' . 'post-functions.php');

		/** 
 		* This file holds all of the page functions
 		*/
		require_once(dirname (__FILE__) . '/' . 'page-functions.php');

		/** 
 		* This file holds all of the media functions
 		*/
		require_once(dirname (__FILE__) . '/' . 'media-functions.php');

		/** 
 		* This file holds all of the link functions
 		*/
		require_once(dirname (__FILE__) . '/' . 'link-functions.php');
		
		restore_include_path();
		
		if( !get_option("ame_show_orderoptions") ) {
			add_option("ame_show_orderoptions", "1");
		}
		if( !get_option("ame_toggle_showinvisposts") ) {
			add_option("ame_toggle_showinvisposts", "1");
		}
		if( !get_option("ame_version") ) {
			add_option("ame_version", AME_VERSION);
		}
		if( !get_option("ame_imgset") ) {
			add_option("ame_imgset", 'set1');
		}
		if( get_option("ame_version") != AME_VERSION ) {
			update_option("ame_version", AME_VERSION);
		}
	}
	
	/**
 	* Checks for the version of WordPress,
 	* and adds a message to inform the user
 	* if required WP version is less than 2.5
 	*
 	* @since 1.4.0
 	* @author scripts@schloebe.de
 	*/
	function wpVersionFailed() {
		echo "<div id='message' class='error fade'><p>" . __('Admin Management Xtended requires at least WordPress 2.5!', 'admin-management-xtended') . "</p></div>";
	}
	
	/**
 	* Checks for the existance of 'Better Tags Manager' plugin,
 	* which is known to cause problems with this plugin
 	* and adds a message to inform the user
 	*
 	* @since 1.4.0
 	* @author scripts@schloebe.de
 	*/
	function wpIncompCheck() {
		echo "<div id='message' class='error fade'><p>" . __('You seem using the <em>Better Tags Manager</em> plugin, which collides with the <em>Admin Management Xtended</em> plugin since both extend the tags column. Please deactivate one of both to make this message disappear.', 'admin-management-xtended') . "</p><p align='right' style='font-weight:200;'><small><em>" . __('(This message was created by Admin Management Xtended plugin)', 'admin-management-xtended') . "</em></small></p></div>";
	}
	
}

if ( class_exists('AdminManagementXtended') ) {
	$adminmanagementxtended = new AdminManagementXtended();
}
?>