<?php
/*
Plugin Name: Branded Admin
Plugin URI: http://www.MoonViperCMS.com
Description: Display logo and custom header &amp; footer in the WordPress Admin area.
Author: Moon Viper Web Services
Version: 2.1
Author URI: http://www.MoonViperCMS.com/
*/



function branded_header() {
	global $user_identity;
	get_currentuserinfo();
	$siteurl = get_bloginfo('siteurl');
	$blogname = get_bloginfo('name');
	echo '<link rel="stylesheet" type="text/css" href="' . get_settings('siteurl') . '/wp-content/plugins/branded-admin/branded-admin.css" />';

	echo '<div id="wphead_branded">';
	echo '<img height=35 src="' . get_settings('siteurl') . '/wp-content/plugins/branded-admin/images/branded-header.png" /> </div>';

	echo '<div id="user_info2" class="top_strip">';
	echo '<div id="dashmenu_dashboard" class="top_strip"><a href="index.php" class="top_strip">Dashboard </a>    </div>';

	echo '<div id="dashmenu_login" class="top_strip"> . <a href="'.$siteurl.'/wp-login.php?action=logout" class="top_strip">Log Out</a></div>';

	echo '</div>';
}

add_action('admin_head', 'branded_header', 11);



function branded_footer() {
	echo '<div id="footer_branded">

		<p class="foot_txt">'  . get_bloginfo('name') . ' Admin  - WordPress Version '. get_bloginfo('version') . '</p></div>';
}

add_action('admin_footer', 'branded_footer');

?>
