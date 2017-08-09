<?php
/*
Plugin Name: MoonViperCMS.com
Plugin URI: http://moonvipercms.com/
Description: <strong>WordPress 2.5+ only.</strong> Everything you need to create and manage your new MoonViper CMS site. Accessable to Firefox, Safari and IE8 users only.</strong>
Author: Moon Viper Web Services
Version: 1.2.1
Author URI: http://moonviper.com/
*/ 

/* Main function call */
function wp_admin_fluency_css() {
	wp_admin_fluency_add_css('wp-admin.css');
	wp_admin_fluency_plugins();
}
add_action('admin_head', 'wp_admin_fluency_css',1000);

/* Echo CSS file link */
function wp_admin_fluency_add_css($file, $version = '1.2.1') {
	$fluency_path = get_settings('siteurl') . '/wp-content/plugins/' . plugin_basename(dirname(__FILE__)) ;
	echo '<link rel="stylesheet" type="text/css" href="' . $fluency_path . '/resources/' . $file . '?version=' . $version .'" />'."\n";
}

/* 
 * Plugin Support
 *
 * I will be progressivly adding additional plugin css support in the future,
 * if you have a plugin that you would ike me to support please contact me.
 * 
 */
function wp_admin_fluency_plugins() {
	
	/* WP-PostRatings - http://www.lesterchan.net/portfolio/programming.php */
	if(preg_match("/postratings/",$_GET['page'])) {
		wp_admin_fluency_add_css('plugins/postratings.css');
	}
	
	/* Ozh's Admin Drop Down Menu - http://planetozh.com/blog/my-projects/wordpress-admin-menu-drop-down-css/ */
	if(function_exists("wp_ozh_adminmenu")) {
		wp_admin_fluency_add_css('plugins/ozh_admindropdownmenu.css');
	}
	
	/* AutoResponder PlugIn Menu - http://freeautoresponder.biz?92*/
	if(function_exists("ar-gwa")) {
		wp_admin_fluency_add_css('plugins/freeautoresponder.css');
	}
	
	/* Yellow Swordfish - Admin Drop Down Menus - http://www.stuff.yellowswordfish.com/admin-drop-down-menus/ */
	if(function_exists("admin_menus_create")) {
		wp_admin_fluency_add_css('plugins/yf_admindropdownmenu.css');
	}
	
	/* WP Movie Ratings - http://paulgoscicki.com/projects/wp-movie-ratings/ */
	if(preg_match("/wp_movie_ratings/",$_GET['page'])) {
		wp_admin_fluency_add_css('plugins/wpmovieratings.css');
	}
	
	/* WP-Quotes - http://www.zombierobot.com/wp-quotes/ */
	if(preg_match("/edit-quotes/",$_SERVER['PHP_SELF'])) {
		wp_admin_fluency_add_css('plugins/wpquotes.css');
	}
	
	/* Wordpress Download Monitor - http://wordpress.org/extend/plugins/download-monitor/ */
	if(preg_match("/Downloads/",$_GET['page'])) {
		wp_admin_fluency_add_css('plugins/downloadmonitor.css');
	}
	
	/* Subscribe to Comments - http://txfx.net/code/wordpress/subscribe-to-comments/ */
	if(preg_match("/stc-/",$_GET['page'])) {
		wp_admin_fluency_add_css('plugins/subscribecomments.css');
	}
	
	/* NextGEN Gallery - http://wordpress.org/extend/plugins/nextgen-gallery/ */
	if(preg_match("/nggallery|nextgen-gallery/",$_GET['page'])) {
		wp_admin_fluency_add_css('plugins/nextgengallery.css');
	}
	

	/* 
	 * Additional Plugin Support coming soon.
	 *
	 * Simple Tags - http://wordpress.org/extend/plugins/simple-tags
	 * Contact Form 7 - http://ideasilo.wordpress.com/2007/04/30/contact-form-7/
	 * Quoter - http://www.damagedgoods.it/wp-plugins/quoter/
	 * Search Provider - http://www.neilang.com/
	 * Google Analytics - http://www.semiologic.com/software/marketing/google-analytics/
	 * Google Sitemaps - http://www.arnebrachhold.de/redir/sitemap-home/
	 * WP-Typogrfiy - http://blog.hamstu.com/
	 *
	 */

}

?>