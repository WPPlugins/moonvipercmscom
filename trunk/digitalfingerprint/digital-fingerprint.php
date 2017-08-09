<?php
/*
Plugin Name: Digital Fingerprint 
Plugin URI: http://www.maxpower.ca
Description: This plugin enables you to easily and quickly search the web for potential cases of plagiarism and content theft.  It works by adding a customizable "digital fingerprint" to your RSS feed in posts that you select (or all posts if you wish).  You can then easily monitor the blogosphere for your digital fingerprint in the hopes of finding potential content thieves and scrapers abusing your content.
Version: Beta 0.2
Author: Kirk Montgomery
Author URI: http://www.maxpower.ca
*/ 

// License Info
/*
Attribution-ShareAlike 2.5
You are free:

    * to copy, distribute, display, and perform the work
    * to make derivative works
    * to make commercial use of the work, but not selling this code outright

Under the following conditions:
By Attribution: 

Creative Commons Attribution-ShareAlike 2.5 Copyright 2006 MaxPower www.maxpower.ca


Share Alike: 
If you alter, transform, or build upon this work, you may distribute the 
resulting work only under a licence identical to this one.

    * For any reuse or distribution, you must make clear to others the licence 
      terms and freely and openly provide the link to the original source code.
    * Any of these conditions can be waived if you get permission from the 
        copyright holder.
    * You may not sell this WordPress plugin or any derivation of it
    
For more details please see: http://www.maxpower.ca/plugins/

This program is distributed in the hope that it will be useful, but 
WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY 
or FITNESS FOR A PARTICULAR PURPOSE.  
*/

/* Set the error reporting to make sure that if one of the rss feeds breaks the user doesn't see any complaints -- maybe there is a better way */
error_reporting(0);

$DF_the_print = get_option("DF_the_print");
$DF_the_checkbox = get_option("DF_the_checkbox");
$DF_the_auto_checkbox = get_option("DF_the_auto_checkbox");
$DF_placement_selection = get_option("DF_placement_selection");
// Do not adjust this value
$DF_current_version = 'Beta 0.2';
// Do not adjust this value

/* insert the fingerprint button 
into the editor using ButtonSnap (WOW so easy, thanks Owen Winkler!)*/

        include('buttonsnap.php');
        add_action('init', 'DF_button_init_max_class');
        function DF_button_init_max_class() {
        	// Set up some unique button image URLs for the new buttons from the plugin directory
        	$DF_But_insert_image_url3 = buttonsnap_dirname(__FILE__) . '/transmit_go.png';
        	// Create a vertical separator in the WYSI toolbar (does nothing in the Quicktags):
        	buttonsnap_separator();
        	// Create a button that uses Ajax to fetch replacement text from a WordPress plugin hook sink:
        	buttonsnap_ajaxbutton($DF_But_insert_image_url3, 'FingerPrint', 'DF_But_insert_hook');
        	add_filter('DF_But_insert_hook', 'DF_But_insert_hook_sink');
        }
          // return function needs to have somthing before and after selectedtext to work correctly
        function DF_But_insert_hook_sink($selectedtext) {
        	return '' . $selectedtext . ' <!--fingerprint--> ';
        }

/*---- add management menu ----- */

function insert_finger_add_manage() {
	if (function_exists('add_management_page')) {
		add_management_page('Digital Fingerprint', 'Digital Fingerprint', 8, basename(__FILE__), 'insert_finger_options');
	}
	add_action('admin_menu', 'insert_finger_add_manage');
}

/*---- The option menu  ----- */
function insert_finger_options() { 
$site_url_short = substr(get_bloginfo('wpurl'), 7);
global $DF_current_version;

if (isset($_POST["DF_update_pushed"])) {

/* Massage the input words to get rid of bad characters, check if emppty, that kind of thing */

			//update values in db
			$DF_the_print = $_POST["DF_the_print"];
    	$DF_the_checkbox = $_POST["DF_the_checkbox"];
    	$DF_the_auto_checkbox = $_POST["DF_the_auto_checkbox"];
    	$DF_placement_selection = $_POST["DF_placement_selection"];
    	
      // Filter the fingerprint so as to avoid XSS
      $DF_the_print = htmlspecialchars($DF_the_print);
    			update_option("DF_the_checkbox",$DF_the_checkbox);
    			update_option("DF_the_auto_checkbox",$DF_the_auto_checkbox);
          update_option("DF_the_print",$DF_the_print);
    			update_option("DF_placement_selection",$DF_placement_selection);
          
      echo '<div id="message" class="updated fade"><p>Digital FingerPrint options saved.</p></div>';
        } else {
			//get current values
			global $DF_the_print, $DF_the_checkbox, $DF_the_auto_checkbox, $DF_placement_selection;
		}
require_once(ABSPATH . WPINC . '/rss-functions.php');
$rss_df_max_updatefeed = fetch_rss('http://www.maxpower.ca/wp-content/upgrade/pull_my_finger_update_feed.xml');

foreach ( $rss_df_max_updatefeed->items as $item_df ) {
$df_update_check = $item_df['title'];
}
/* Check if Fingerprint is Empty, report message */
if ($DF_the_print == '') {echo '<div id="message" class="error fade"><p><b>WARNING:</b> Digital FingerPrint is blank!</p></div>';}
/* Check if Fingerprint needs to update, if so report it */
if ($df_update_check == $DF_current_version || $df_update_check == 'Beta 2' || $df_update_check == '' ) {
// Do nothing, everything is groovy
//} elseif ($df_update_check == '') {
// Do nothing because we could not connect to maxpower or there was an error or something
} elseif ($df_update_check != $DF_current_version) {
// There is a new version!
echo '<div id="akismet-warning" class="updated fade-ff0000"><p><b>WARNING:</b> There is an update available to this plugin!  Visit <a href="http://www.maxpower.ca/wordpress-plugins/">MaxPower</a>.</p></div>';
}

/*Create the correct blogsearch feed url based on the digital fingerprint input*/
$DF_the_print_search = str_replace(" ", "+", $DF_the_print);
$DF_the_print_search_feedster = str_replace(" ", "%20", $DF_the_print);
?>

<div class="wrap">
<h2>RSS Blog Search</h2>

<?php
$rss_google = fetch_rss('http://blogsearch.google.com/blogsearch_feeds?hl=en&q=%22' . $DF_the_print_search . '%22&scoring=d&ie=utf-8&num=10&output=rss');
$google_result = count($rss_google_bp->items);
$rss_bp = fetch_rss('http://www.blogpulse.com/rss?query=%22' . $DF_the_print_search . '%22&sort=date&operator=phrase');
$bp_result = count($rss_bp->items);
$rss_sphere = fetch_rss('http://rss.sphere.com/rss?q=' . $DF_the_print_search);
$sphere_result = count($rss_sphere->items);
$rss_icerocket = fetch_rss('http://www.icerocket.com/search?tab=blog&q=%22' . $DF_the_print_search . '%22&rss=1');
$icerocket_result = count($rss_icerocket->items);
$rss_feedster = fetch_rss('http://www.feedster.com/search/type/rss/%22' . $DF_the_print_search_feedster . '%22');
$feedster_result = count($rss_feedster->items);
$rss_bloglines = fetch_rss('http://www.bloglines.com/search?q=%22' . $DF_the_print_search . '%22&s=fr&pop=l&news=m&format=rss');
$bloglines_result = count($rss_bloglines->items);


/* Google BlogSearch */
echo '<legend><img src="' . get_bloginfo('wpurl') . '/wp-content/plugins/digitalfingerprint/google_icon.png" alt="Blogsearch at Google" /> Google Blog Search</legend>';
echo '<ul>';
// Determine if there are any matches, if so display them, and if not display a message
    if ($google_result > 0 ) {
        foreach ( $rss_google->items as $item ) {
                echo '<li><a href="' . $item['link'] . '" title="' . $item['title'] . '">' . $item['title'] . '</a> - '. $item['link'] .'</li>';
        }
echo '<li><small>Dig deeper at <a href="http://blogsearch.google.com/blogsearch?hl=en&q=%22' . $DF_the_print_search . '%22&ie=UTF-8&scoring=d">Google Blog Search</a></small></li>';
    } else {
      echo '<li>There are zero matching fingerprints at Google or there is a network error.  Dig deeper at <a href="http://blogsearch.google.com/blogsearch?hl=en&q=%22' . $DF_the_print_search . '%22&ie=UTF-8&scoring=d">Google Blog Search</a>.</li>';
      }
echo '</ul>';


/* IceRocket */
echo '<legend><img src="' . get_bloginfo('wpurl') . '/wp-content/plugins/digitalfingerprint/icerocket_icon.png" alt="Search IceRocket" /> IceRocket Blog Search</legend>';
echo '<ul>';
// Determine if there are any matches, if so display them, and if not display a message
    if ($icerocket_result > 0 ) {
        foreach ( $rss_icerocket->items as $item ) {
                echo '<li><a href="' . $item['link'] . '" title="' . $item['title'] . '">' . $item['title'] . '</a> - '. $item['link'] .'</li>';
        }
echo '<li><small>Dig deeper at <a href="http://blogs.icerocket.com/search?q=%22' . $DF_the_print_search . '%22">Icerocket Blog Search</a></small></li>';
    } else {
      echo '<li>There are zero matching fingerprints at Icerocket or there is a network error.  Dig deeper at <a href="http://blogs.icerocket.com/search?q=%22' . $DF_the_print_search . '%22">Icerocket Blog Search</a>.</li>';
      }
echo '</ul>';

/* Feedster */
echo '<legend><img src="' . get_bloginfo('wpurl') . '/wp-content/plugins/digitalfingerprint/feedster_icon.png" alt="Search feedster" /> Feedster Blog Search</legend>';
echo '<ul>';
// Determine if there are any matches, if so display them, and if not display a message
    if ($feedster_result > 0 ) {
        foreach ( $rss_feedster->items as $item ) {
                echo '<li><a href="' . $item['link'] . '" title="' . $item['title'] . '">' . $item['title'] . '</a> - '. $item['link'] .'</li>';
        }
echo '<li><small>Dig deeper at <a href="http://www.feedster.com/search/extended/archived/%22' . $DF_the_print_search_feedster . '%22">Feedster Blog Search</a></small></li>';
    } else {
      echo '<li>There are zero matching fingerprints at feedster or there is a network error.  Dig deeper at <a href="http://www.feedster.com/search/extended/archived/%22' . $DF_the_print_search_feedster . '%22">Feedster Blog Search</a>.</li>';
      }
echo '</ul>';

/* Bloglines */
echo '<legend><img src="' . get_bloginfo('wpurl') . '/wp-content/plugins/digitalfingerprint/bloglines_icon.png" alt="Search BlogLines" /> BlogLines Blog Search</legend>';
echo '<ul>';
// Determine if there are any matches, if so display them, and if not display a message
    if ($bloglines_result > 0 ) {
        foreach ( $rss_bloglines->items as $item ) {
                echo '<li><a href="' . $item['link'] . '" title="' . $item['title'] . '">' . $item['title'] . '</a> - '. $item['link'] .'</li>';
        }
echo '<li><small>Dig deeper at <a href="http://www.bloglines.com/search?q=%22' . $DF_the_print_search_feedster . '%22">Bloglines Blog Search</a></small></li>';
    } else {
      echo '<li>There are zero matching fingerprints at feedster or there is a network error.  Dig deeper at <a href="http://www.bloglines.com/search?q=%22' . $DF_the_print_search . '%22">BlogLines Blog Search</a>.</li>';
      }
echo '</ul>';


/* BlogPulse */
echo '<legend><img src="' . get_bloginfo('wpurl') . '/wp-content/plugins/digitalfingerprint/blogpulse_icon.png" alt="BlogPulse" /> BlogPulse Blog Search</legend><br />';
echo '<ul>';
// Determine if there are any matches, if so display them, and if not display a message
    if ($bp_result > 0 ) {
      foreach ( $rss_bp->items as $item_bp ) {
              echo '<li><a href="' . $item_bp['link'] . '" title="' . $item_bp['title'] . '">' . $item_bp['title'] . '</a> - '. $item_bp['link'] .'</li>';
              }
      echo '<li><small>Dig deeper at <a href="http://www.blogpulse.com/search?boolean=false&operator=phrase&keywords=' . $DF_the_print_search . '">BlogPulse</a>.</small></li>';
    } else {
      echo '<li>There are zero matching fingerprints at BlogPulse or there is a network error.  Dig deeper at <a href="http://www.blogpulse.com/search?boolean=false&operator=phrase&keywords=' . $DF_the_print_search . '">BlogPulse</a>.</li>';
      }
echo '</ul>';

/* Sphere Blog Search */
echo '<legend><img src="' . get_bloginfo('wpurl') . '/wp-content/plugins/digitalfingerprint/sphere_icon.png" alt="Sphere" /> Sphere Blog Search</legend>';
echo '<ul>';
    if ($sphere_result > 0 ) {
      foreach ( $rss_sphere->items as $item_sphere ) {
      $sphere_counter++;
              if ($sphere_counter < 11) {
              echo '<li><a href="' . $item_sphere['link'] . '" title="' . $item_sphere['title'] . '">' . $item_sphere['title'] . '</a> - '. $item_sphere['link'] .'</li>';
              }
      }
      echo '<li><small>Dig deeper at <a href="http://www.sphere.com/search?q=%22' . $DF_the_print_search . '%22&datedrop=0&sortby=date&lang=all&allfrom=&startdate=&enddate=&histdays=120">Sphere</a>.</small></li>';
      } else {
      echo '<li>There are zero matching fingerprints at BlogPulse or there is a network error.  Dig deeper at <a href="http://www.sphere.com/search?q=%22' . $DF_the_print_search . '%22&datedrop=0&sortby=date&lang=all&allfrom=&startdate=&enddate=&histdays=120">Sphere</a>.</li>';
      }
// Set some default values if the user forgets
if (($DF_the_auto_checkbox == "1") and ($DF_placement_selection == '')) {
update_option("DF_placement_selection","start");
}
?>

</div>
<div class="wrap">
  <h2>Other Detection Methods / Resources</h2>
  <ul>
      <li><h3>Detection<h3></li>
        <ul>
        <?php echo '<li>Perform a quick digital fingerprint search using <a href="http://www.google.com/search?q=+%22' . $DF_the_print_search . '%22+-site%3A' . $site_url_short . '">Google</a>, <a href="http://search.yahoo.com/search?p=%22' . $DF_the_print_search . '%22">Yahoo</a>, and <a href="http://search.msn.com/results.aspx?q=%22' . $DF_the_print_search . '%22">MSN</a></li>'; ?>
        <li>Sign up for <a href="http://www.google.com/alerts">Google Alerts</a> using your Digital Fingerprint</li>
            <ul>
              <li><i>Google Alerts</i> are emails automatically sent to you when there are new Google results for your search term (in this case, your digital fingerprint).</li>
              <li>A good Google Alert search term that incorporates your digital fingerprint (but doesn't alert you when you publish on your own site) is: <br /><strong><em><?php echo '"' . $DF_the_print . '" -site:' . $site_url_short; ?></em></strong></li>
            </ul>
        </ul>
      <li><h3>Resources<h3></li> 
        <ul> 
            <li><a href="http://www.maxpower.ca/what-should-you-do-when-youve-found-a-content-thief/2006/09/24/"/>What should you do when you've found a content thief?</a></li>
            <li>
                One way of stopping content scrapers is to remove their source of income, 
                the advertisements shown on their pages.  Continue to MaxPower.ca to learn 
                more about hitting content scrapers where it hurts -- <a href="http://www.maxpower.ca/what-should-you-do-when-youve-found-a-content-thief/2006/09/24/">the pocketbook</a>.
            </li>
              
                <?php //<li>Report to Adsense bookmarklet</li> ?>
            <li>Plagiarism resources at <a href="http://www.plagiarismtoday.com">Plagiarism Today</a>, a site about plagiarism, content theft and copyright issues</li>
            <li><a href="http://www.templetons.com/brad/copymyths.html" >10 Big Myths about copyright explained</a> by <i>Brad Templeton</i> -- an excellent article on the topic of copyright and publishing</li>
             
        </ul> 
  </ul>
</div>


<div class="wrap">
  	<h2>Digital Fingerprint Options</h2>
  	<form method="post">
  	<fieldset class="options">
      	<legend>Set your FingerPrint</legend>
      	<p>Choose a fingerprint that is unique.  It should be a word or phrase that is not found on any other website anywhere, see <a href="http://www.maxpower.ca/choosing-a-digital-fingerprint-thats-right-for-me/2006/09/21/"><em>Choosing a Digital Fingerprint that is right for me</em></a></p>
      	Set your Digital Fingerprint: <input type="text" name="DF_the_print" size="40" value="<? echo $DF_the_print ?>"/><br /><br />
      	<legend>FingerPrint Placement</legend>
        <p>This plugin can automatically include your fingerprint in every post.</p>
        <input type="checkbox" value="1" name="DF_the_auto_checkbox" <? if ($DF_the_auto_checkbox == "1") {echo "checked";} ?> /> - Would you like this? 
        <p>Regardless of where you place your fingerprint marker (&lt;!--fingerprint--&gt;) within your post, one of the three options below will overide your selection.  This will only happen if you have chosen to automatically include it in every post using the option above: </p>
        <input name="DF_placement_selection" type="radio" value="start" <? if ($DF_placement_selection == "start") {echo 'checked="checked"';} ?> /> - Insert FingerPrint at the start of every post<br />
      	<input name="DF_placement_selection" type="radio" value="end" <? if ($DF_placement_selection == "end") {echo 'checked="checked"';} ?> /> - Insert FingerPrint at the end of every post<br />
      	<input name="DF_placement_selection" type="radio" value="para" <? if ($DF_placement_selection == "para") {echo 'checked="checked"';} ?> /> - Insert FingerPrint at the end of the first paragraph in every post<br />
    </fieldset>
  	<fieldset class="options">
        <legend>DashBoard Fingerprint</legend>
        <p>Would you like to include a quick fingerprint search in the dashboard?  This is what it will look like:</p>
        <p><img src="<?php get_bloginfo('wpurl'); ?>/wp-content/plugins/digitalfingerprint/DashBoardFingerPrint.png" alt="This is what the quick digital fingerprint search looks like." /></p>
      	<input type="checkbox" value="1" name="DF_the_checkbox" <? if ($DF_the_checkbox == "1") {echo "checked";} ?> /> - Would you like this? 
      	</fieldset>
  	<input type="hidden" name="DF_update_pushed" value="1"/>
  	<p class="submit"><input type="submit" name="Submit" value="<?php _e('Update Options') ?> &raquo;" /></p>
  </div>

<div class="wrap">
<?php
echo '<h2>MaxPower\'s WordPress Feed <a href="http://www.maxpower.ca/feed/"><img src="' . get_bloginfo('wpurl') . '/wp-content/plugins/digitalfingerprint/max_feed.png" alt="SubScribe to MaxPower." ></a></p></h2>';

$rss_maxpower_wordpress = fetch_rss('http://www.maxpower.ca/category/design/code/wordpress/feed/');
echo '<legend>Stay informed about WordPress specific news at MaxPower.</legend>';
echo '<ul>';
foreach ( $rss_maxpower_wordpress->items as $item_max ) {
$max_power_counter++;
        if ($max_power_counter < 6) {
        echo '<li><a href="' . $item_max['link'] . '" title="' . $item_max['title'] . '">' . $item_max['title'] . '</a> - <small>'. $item_max['description'] .'</small></li>';
        }
     }
echo '</ul></div>';
}

/*---- Add function to put Quick FingerPrint Search in the DashBoard ----- */

function insert_finger_into_dash() {
$DF_fingerprint_url = get_bloginfo('wpurl');
$DF_short_again = substr($DF_fingerprint_url, 7);
$DF_the_print_search = str_replace(" ", "+", get_option("DF_the_print"));
echo '<h3>Digital Fingerprint Search <a href="/wp-admin/edit.php?page=digital-fingerprint.php" title="Examine RSS Feeds..." /> &raquo;</a></h3>';
echo '<a href="http://www.google.com/search?q=%22' . $DF_the_print_search . '%22+-site%3A' . $DF_short_again . '"><img class="fingerprint_icons" src="' . $DF_fingerprint_url . '/wp-content/plugins/digitalfingerprint/google_icon.png" alt="Quickly search Google for signs of your digital fingerprint" /></a> <a href="http://search.yahoo.com/search?p=%22' . $DF_the_print_search . '%22+-site%3A' . $DF_short_again . '"><img class="fingerprint_icons" src="' . $DF_fingerprint_url . '/wp-content/plugins/digitalfingerprint/yahoo_icon.png" alt="Quickly search Yahoo for signs of your digital fingerprint" /></a> <a href="http://search.msn.com/results.aspx?q=%22' . $DF_the_print_search . '%22+-site%3A' . $DF_short_again . '"><img class="fingerprint_icons" src="' . $DF_fingerprint_url . '/wp-content/plugins/digitalfingerprint/msn_icon.png" alt="Quickly search MSN for signs of your digital fingerprint" /></a> --  Click on the logo to search for your digital fingerprint.';
}

function insert_digital_finger_css() { 
echo '<link rel="stylesheet" type="text/css" media="screen" href="' . get_bloginfo('wpurl') . '/wp-content/plugins/digitalfingerprint/DF-admin.css" />';
}

/* Obtain the fingerprint from the DB */
function pull_my_finger() {
  $DF_the_print = get_option("DF_the_print");
  $DF_the_checkbox = get_option("DF_the_checkbox");
	return $DF_the_print;
}
/* Find the FingerPrint marker in the Content and replace it with the actual fingerprint in the feed only*/
// Do this only if the finger isn't inserted in every post automatically
function insert_my_finger($content) {
    if(is_feed()) {
        if(!preg_match('|<!--fingerprint-->|', $content)) return $content;	
    	    return str_replace('<!--fingerprint-->', pull_my_finger(), $content);
          return $content;
    } else {
        return $content;
    }
}

function auto_insert_finger_end($content) {
    if(is_feed()) {
        $DF_the_print = get_option("DF_the_print");
        return $content . ' ' . $DF_the_print;
    } else {
        return $content;
    }
}

function auto_insert_finger_start($content) {
    if(is_feed()) {
        $DF_the_print = get_option("DF_the_print");
        return $DF_the_print . ' ' . $content;
    } else {
        return $content;
    }
}

function auto_insert_finger_para($content) {
$DF_the_print_para = ' ' . get_option("DF_the_print") . ' </p>';
$DF_search_content = '</p>';
    if(is_feed()) {
         // Looks for the first occurence of $DF_search_content in $content
         // and replaces it with $replace.
         $pos = strpos($content, $DF_search_content);
         if ($pos === false) {
             // Nothing found
             return $content;
         }
         return substr_replace($content, $DF_the_print_para, $pos, strlen($DF_search_content));
    } else {
        return $content;
    }
}

/* check if checkbox has been selected, run actions if true */
if (get_option("DF_the_checkbox") == 1) {
    add_action('activity_box_end','insert_finger_into_dash');
    add_action('admin_head', 'insert_digital_finger_css');
}

// If user wants to automatically place the fingerprint in posts, do so
if (get_option("DF_the_auto_checkbox") == 1) {
    if ($DF_placement_selection == 'start') {
        add_filter('the_content', 'auto_insert_finger_start');
    } elseif ($DF_placement_selection == 'end') {
        add_filter('the_content', 'auto_insert_finger_end');
    } elseif ($DF_placement_selection == 'para') {
        add_filter('the_content', 'auto_insert_finger_para');
    }
}

/* run these actions all the time */
add_action('admin_menu','insert_finger_add_manage');
add_filter('the_content', 'insert_my_finger');
?>
