<?php
/**
 * Page-related functions
 *
 * @package WordPress_Plugins
 * @subpackage AdminManagementXtended
 */
 
/*
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

/* ************************************************ */
/* Adding the columns and data						*/
/* ************************************************ */

/**
 * Add a new 'Actions' column to the page management view
 *
 * @since 0.7
 * @author scripts@schloebe.de
 *
 * @param array
 * @return array
 */
function ame_column_page_actions( $defaults ) {
	$wp_version = (!isset($wp_version)) ? get_bloginfo('version') : $wp_version;
	
    $defaults['ame_page_actions'] = '<abbr style="cursor:help;" title="' . __('Enhanced by Admin Management Xtended Plugin', 'admin-management-xtended') . ' ' . get_option("ame_version") . '">' . __('Actions', 'admin-management-xtended') . '</abbr>' . ame_changeImgSet();
    return $defaults;
}

/**
 * Add a new 'Page Order' column to the page management view
 *
 * @since 1.0
 * @author scripts@schloebe.de
 *
 * @param array
 * @return array
 */
function ame_column_page_order( $defaults ) {
	$wp_version = (!isset($wp_version)) ? get_bloginfo('version') : $wp_version;
	
    $defaults['ame_page_order'] = '<abbr style="cursor:help;" title="' . __('Enhanced by Admin Management Xtended Plugin', 'admin-management-xtended') . ' ' . get_option("ame_version") . '">' . __('Page Order') . '</abbr>';
    return $defaults;
}

/**
 * Adds content to the new 'Actions' column on the page management view
 *
 * @since 0.7
 * @author scripts@schloebe.de
 *
 * @param string
 * @param int
 */
function ame_custom_column_page_actions( $ame_column_name, $ame_id ) {
	global $wpdb, $locale;
    if( $ame_column_name == 'ame_page_actions' ) {
    	$post_status = get_post_status( $ame_id );
    	echo '<div style="width:75px;" class="ame_options">';
    	if ( $post_status == 'publish' ) {
    		// Visibility icon
    		echo '<div id="visicon' . $ame_id . '" style="padding:1px;float:left;"><a href="javascript:void(0);" onclick="ame_ajax_set_visibility(' . $ame_id . ', \'draft\', \'page\');return false;"><img src="' . AME_PLUGINFULLURL . 'img/' . AME_IMGSET . 'visible.png" border="0" alt="' . __('Toggle visibility', 'admin-management-xtended') . '" title="' . __('Toggle visibility', 'admin-management-xtended') . '" /></a></div> ';
    	} else {
    		// Visibility icon
    		echo '<div id="visicon' . $ame_id . '" style="padding:1px;float:left;"><a href="javascript:void(0);" onclick="ame_ajax_set_visibility(' . $ame_id . ', \'publish\', \'page\');return false;"><img src="' . AME_PLUGINFULLURL . 'img/' . AME_IMGSET . 'hidden.png" border="0" alt="' . __('Toggle visibility', 'admin-management-xtended') . '" title="' . __('Toggle visibility', 'admin-management-xtended') . '" /></a></div>';
    	}
    	// Date icon
    	echo '<div id="date' . $ame_id . '" style="padding:1px;float:left;"><a href="javascript:void(0);" class="date-pick" id="datepicker' . $ame_id . '"><img src="' . AME_PLUGINFULLURL . 'img/' . AME_IMGSET . 'date.png" border="0" alt="' . __('Change Publication Date', 'admin-management-xtended') . '" title="' . __('Change Publication Date', 'admin-management-xtended') . '" /></a></div> ';
		// Slug edit icon
    	echo '<div id="slug' . $ame_id . '" style="padding:1px;float:left;"><a href="javascript:void(0);" id="slugedit' . $ame_id . '" onclick="ame_slug_edit(' . $ame_id . ', \'page\');"><img src="' . AME_PLUGINFULLURL . 'img/' . AME_IMGSET . 'slug_edit.png" border="0" alt="' . __('Edit Page Slug', 'admin-management-xtended') . '" title="' . __('Edit Page Slug', 'admin-management-xtended') . '" /></a></div>';
    	// Comment open/closed status icon
    	$q_commentstatus = get_post($ame_id);
    	$comment_status = $q_commentstatus->comment_status;
    	if( $comment_status == 'open' ) { $c_status = 0; $c_img = '_open'; } else { $c_status = 1; $c_img = '_closed'; }
    	echo '<div id="commentstatus' . $ame_id . '" style="padding:1px;float:left;"><a tip="' . __('Toggle comment status open/closed', 'admin-management-xtended') . '" href="javascript:void(0);" onclick="ame_ajax_set_commentstatus(' . $ame_id . ', ' . $c_status . ', \'page\');return false;"><img src="' . AME_PLUGINFULLURL . 'img/' . AME_IMGSET . 'comments' . $c_img . '.png" border="0" alt="' . __('Toggle comment status open/closed', 'admin-management-xtended') . '" title="' . __('Toggle comment status open/closed', 'admin-management-xtended') . '" /></a></div> ';
		// Post revisions
		if( function_exists('wp_list_post_revisions') && wp_get_post_revisions( $ame_id ) ) {
			echo '<input type="hidden" name="amehasrev' . $ame_id . '" class="amehasrev" value="1" /><div id="amerevisionwrap' . $ame_id . '" style="width:300px;height:165px;overflow:auto;display:none;">';
			wp_list_post_revisions( $ame_id );
			echo '</div>';
		}
    	echo '</div>';
    }
}

/**
 * Adds content to the new 'Page Order' column on the page management view
 *
 * @since 1.0
 * @author scripts@schloebe.de
 *
 * @param string
 * @param int
 */
function ame_custom_column_page_order( $ame_column_name, $ame_id ) {
	global $wpdb;
    if( $ame_column_name == 'ame_page_order' ) {
    	$q_post_order = get_post( $ame_id );
    	echo '<div style="width:75px;" class="ame_options">';
    	echo '<input type="text" value="' . $q_post_order->menu_order . '" size="3" maxlength="3" style="font-size:1em;" id="ame_pageorder' . $ame_id . '" onchange="ame_ajax_order_save(' . $ame_id . ', \'page\');" /> <span id="ame_order_loader' . $ame_id . '" style="display:none;"><img src="' . AME_PLUGINFULLURL . 'img/' . AME_IMGSET . 'loader.gif" border="0" alt="" /></span>';
    	echo '</div>';
    }
}

add_action('manage_pages_custom_column', 'ame_custom_column_page_actions', 500, 2);
add_filter('manage_pages_columns', 'ame_column_page_actions', 500, 2);
if ( get_option('ame_show_orderoptions') == '1' ) {
	add_action('manage_pages_custom_column', 'ame_custom_column_page_order', 500, 2);
	add_filter('manage_pages_columns', 'ame_column_page_order', 500, 2);
}
?>