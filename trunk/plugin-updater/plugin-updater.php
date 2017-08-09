<?php
/*
Plugin Name: Plugin Updater
Plugin URI: http://tgardner.net/
Description: Allows for updating your wordpress plugins with the click of a button.
Version: 1.0.3
Author: Trent Gardner
Author URI: http://tgardner.net/

Copyright 2007  Trent Gardner  (email : trent.gardner@gmail.com)

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

class plugin_updater {
	var $plugin_directory = 'plugin-updater';
	
	function plugin_updater() {
		add_action('after_plugin_row', array(&$this, 'add_update_button'));
		add_action('admin_head', array(&$this,'add_admin_head'));
	}
	
	function add_admin_head() {
		if (stristr($_SERVER['REQUEST_URI'], 'plugins.php')===false) return;
		
		echo '<link rel="stylesheet" href="';
		echo $this->get_absolute_path().'css/admin_styles.css';
		echo '" type="text/css" />';
		?>
		<script type="text/javascript" src="<?php echo get_option('siteurl'); ?>/wp-includes/js/prototype.js"></script>
		<script type="text/javascript" src="<?php echo $this->get_absolute_path().'js/plug-updater-js.php'; ?>"></script>
				
		<?php
	}
	
	function add_update_button($file) {
		global $plugin_data;
		$current = get_option( 'update_plugins' );
		$r = $current->response[$file];
		if (empty($r)) return false;
		
		$file_url = $r->package;
		
		echo "<tr><td colspan='5' class='update-plugin-bar'>";
		
		?>

		<div class="update-placeholder" id="update-<?php echo $r->id; ?>">&nbsp;</div>
		<div class="update-now"><input type="button" name="update-now" value="Update Now" onclick="init_plugin_update(<?php echo "'$r->slug', '$r->package', 'update-$r->id'"; ?>);" /></div>
		
		<?php
		
		printf('There is a new version of %s available. <a href="%s">Download version %s here</a>', $plugin_data['Name'], $r->url, $r->new_version);
		
		echo "</td></tr>";
		
	}
	
	function get_absolute_path() {
		return get_option('siteurl').'/wp-content/plugins/'.$this->plugin_directory . "/";
	}
	
}

global $plugin_updater;
$plugin_updater = new plugin_updater();

?>