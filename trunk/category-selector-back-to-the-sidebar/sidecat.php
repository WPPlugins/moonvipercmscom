<?php
/*
Plugin Name: Category Selector Back to the Sidebar (MOD)
Plugin URI: http://beyn.org/sidecat/
Description: Puts the category selector section back to the sidebar of the Post page. Now you can write at a WordPress v2.5 blog without hating v2.5.
Author: Baris Unver
Version: 0.7
Author URI: http://beyn.org/

Changelog
(0.5 > 0.6)
Fixed problems about WP2.6.
(0.6 > 0.7)
"Related" section is back, thanks to scribu (scribu.net)
*/

function sidecat_list_category_checkboxes() {
	global $post_ID; ?>

<!-- Category list -->
<div class="inside">
<p><strong><?php echo _e("Categories") ?></strong></p>
<ul id="categorychecklist" class="list:category categorychecklist form-no-clear">
	<?php wp_category_checklist($post_ID) ?>
</ul>
</div>

<!-- Default sidebar content -->
<div class="side-info">
<h5><?php _e('Related') ?></h5>
<ul>
	<?php if ($post_ID): ?>
	<li><a href="edit.php?p=<?php echo $post_ID ?>"><?php _e('See Comments on this Post') ?></a></li>
<?php endif; ?>
	<li><a href="edit-comments.php"><?php _e('Manage All Comments') ?></a></li>
	<li><a href="edit.php"><?php _e('Manage All Posts') ?></a></li>
	<li><a href="categories.php"><?php _e('Manage All Categories') ?></a></li>
	<li><a href="edit-tags.php"><?php _e('Manage All Tags') ?></a></li>
	<li><a href="edit.php?post_status=draft"><?php _e('View Drafts'); ?></a></li>
	<?php do_action('post_relatedlinks_list'); ?>
</ul>

<h5><?php _e('Shortcuts') ?></h5>
<p><?php _e('Drag-and-drop the following link to your bookmarks bar or right click it and add it to your favorites for a posting shortcut.') ?>  <a href="<?php echo get_shortcut_link(); ?>" title="<?php echo attribute_escape(__('Press This')) ?>"><?php _e('Press This') ?></a></p>
</div>
<?php
}

function sidecat_admin_head() { ?>
<script type="text/javascript">
	jQuery(document).ready(function() {
		jQuery('#categorydiv').remove();
		jQuery('.side-info:first').remove();
	});
</script>
<style type="text/css">
#poststuff h2 {clear:none}
.categorychecklist {list-style-type:none; margin:0; overflow:auto; padding-left:0}
.categorychecklist ul {list-style-type:none; margin:0; padding-left:17px}
.inside #categorychecklist {background:#fff; border:2px solid #CEE1EF; max-height:300px; padding:3px 0 5px 5px}
#categories-all .categorychecklist {overflow:hidden !important}
</style>
<?php
}

add_action('admin_head', 'sidecat_admin_head'); // add css styles
add_action('submitpost_box', 'sidecat_list_category_checkboxes'); // add actual sidebar list.

?>
