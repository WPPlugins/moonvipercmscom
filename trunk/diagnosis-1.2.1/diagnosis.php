<?php
/*
Plugin Name: Diagnosis
Plugin URI: http://nlindblad.org/index.php/projects/wordpress-plugins/diagnosis
Description: Add a debugging page at <a href="index.php">Dashboard</a> >> Diagnosis
Version: 1.2.1
Author: Niklas Lindblad
Author URI: http://nlindblad.org/
*/

/*
License: GPL
Compatibility: Requires WordPress 2 or newer for full functionality.
*/

/*  Copyright (C) Niklas Lindblad http://nlindblad.org

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
Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
*/

function show_diagnosis() {	
	/* If function add_submenu_page exists (one of the functions that add an entry to the administration panel)
	   we kindly ask it to make a page for this plugin */
	if (function_exists('add_submenu_page')) {
		add_submenu_page("index.php", "Diagnosis", "Diagnosis", 10, __FILE__, 'diagnosis');    }
 }

function present_make_css ()
{

/* CSS */
_e('
<style type="text/css">
table.info td {
	text-align: center;
	padding: 1em;
}
table.info tr td { 
background: #f1f1f1;
}
table.info tr td.value {
background: #E7E7E7;
}
table.info tr td.explanation {
	text-align: left;
}
table.info tr.activated td.extension {
	background: #88bb88;
}
table.info tr.activated td {
	background: #aaddaa;
}
</style>
');
/* CSS */

}

function make_table_row ($description, $help_url, $help, $value) {
/* Make a row for our table */
if ( $help_url != "" ) {
print("<tr><td>$description (<a href=\"$help_url\">?</a>)</td>"."<td class=\"value\">".$value."<td class=\"explanation\">$help</td></tr>");
}
else {
print("<tr><td>$description</td>"."<td class=\"value\">".$value."</td>"."<td class=\"explanation\">$help</td></tr>");
}
}

function make_headline ($text) {
print("<h2>".$text."</h2>");
}

function make_table ($piece) {
	if ( $piece == "start" )
		{
		_e('<table class="info" width="100%" cellpadding="3" cellspacing="3">');
		}
	elseif ( $piece == "end" ) {
		_e('</table>');
		}
}

function make_table_header($header_columns) {
		_e('<tr>');
		foreach ($header_columns as $h )
			{
			_e('<th>'.$h.'</th>');
		}
		_e('</tr>');
}

function get_php_loaded_extensions () {
	$loaded_extensions = get_loaded_extensions();

	$imploded_array = implode(", ", $loaded_extensions);		
	
	return($imploded_array);

}

function get_php_configuration_boolean ($value) {
	$setting = ini_get($value);

	$boolean =  (bool) $setting;

	if ( $boolean == true ) 
		{
			return("Yes");
		}

	elseif ( $boolean == false ) {
			return("No");
		}
	else {
			return("Not set");
		}
}

function get_mysql_variable ($variable) {

	/* Call the Wordpress database object */
        global $wpdb;
	 
	/* In order to get the information we want we must ask the database object
	   to make a query for us and return the result as an associative array (ARRAY_A)
	   after that we simply return the value of it, using the key 'Value'.
        */

        $result = $wpdb->get_row("SHOW VARIABLES LIKE '$variable';", ARRAY_A); /* Result is saved in the associative array called $result */
	
	return $result['Value'];
}

function get_mysql_status ($variable) {

	/* Call the Wordpress database object */
        global $wpdb;
 
	/* In order to get the information we want we must ask the database object
	   to make a query for us and return the result as an associative array (ARRAY_A)
	   after that we simply return the value of it, using the key 'Value'.
        */

        $result = $wpdb->get_row("SHOW STATUS LIKE '$variable';", ARRAY_A); /* Result is saved in the associative array called $result */
	
	return $result['Value'];

}

function get_mysql_statistics ($variable, $timeunit) {

	$amount = get_mysql_status($variable);

	$uptime_seconds = get_mysql_status("Uptime");

	switch ($timeunit) {
		case "seconds":
			
			$result = $amount / $uptime_seconds;
   		break;

		case "minutes":
			$uptime = $uptime_seconds / 60;
		
			$result = $amount / $uptime; 		
		break;
	
		case "hours":
			$uptime = $uptime_seconds / 3600;
		
   			$result = $amount / $uptime;
		break;
	}

	/* We round it down to 8 decimals */
	
	return round($result, 8);

}


function present_general_information() {
/* XHTML */
/* Make a table header with descriptive information about each field we are outputting */
make_headline("Server Information");
$columns = array("Variable", "Value", "Explanation");
make_table("start");
make_table_header($columns);

/* Try to display the current Wordpress version */
global $wp_version;
	
make_table_row("Current WordPress version", "http://wordpress.org", "The version of the current WordPress installation", $wp_version);



make_table_row("Server operating system", "http://en.wikipedia.org/wiki/Operating_system", "The operating system currently running on the server", php_uname('s'));

make_table_row("Current version of PHP", "http://www.php.net", "The current version of PHP used on this server", "PHP ".phpversion());

make_table_row("Current version of MySQL", "http://dev.mysql.com/", "The current version of MySQL used by Wordpress", "MySQL ".get_mysql_variable("version"));

make_table_row("Webserver software", "http://en.wikipedia.org/wiki/Webserver", "The name of the <i>webserver</i>, the computer program that serves the pages to the users", $_SERVER['SERVER_SOFTWARE']);

make_table_row("Webserver IP address", "http://en.wikipedia.org/wiki/IP_address", "A unique number that identifies the webserver to other computers on the internet and the local network", $_SERVER['SERVER_ADDR']);

make_table_row("Webserver port number", "http://en.wikipedia.org/wiki/Port_number", "Which port that is used by the webserver to send pages and receive requests (usually <i>80</i>)", $_SERVER['SERVER_PORT']);

make_table_row("MySQL server IP address", "http://en.wikipedia.org/wiki/IP_address", "The IP address or hostname for the MySQL server Wordpress is using ", DB_HOST);

make_table_row("MySQL server port number", "http://en.wikipedia.org/wiki/Port_number", "Which port that is used by Wordpress to send queries to the MySQL server (usually <i>3306</i>)", get_mysql_variable("port"));

make_table_row("MySQL database user", "", "The username Wordpress uses to authorize itself against the MySQL server", DB_USER);

make_table_row("MySQL database name", "", "The name of the database where all data from Wordpress is stored", DB_NAME); 

make_table_row("Domain name", "http://en.wikipedia.org/wiki/Domain_name", "A part of the address the visitors enter in order to access your page", $_SERVER['SERVER_NAME']);

make_table_row("Webserver document root", "", "Where on the webserver your webpages and Wordpress are placed", $_SERVER['DOCUMENT_ROOT']);

/* Code to assure that the ISO 8601 format is made regardless of PHP version */
$unix_timestamp = time();
$iso8601 = gmdate('Y-m-d\TH:i:sO',$unix_timestamp);
$iso8601 = str_replace('+0000','+00:00',$iso8601);

make_table_row("Current time (ISO 8601)", "http://en.wikipedia.org/wiki/ISO_8601", "The current time and date on the server expressed in the standard format called ISO 8601", $iso8601);

make_table_row("Current time (RFC 2822)", "http://tools.ietf.org/html/3339", "The current time and date on the server expressed in the standard format called RFC 2822 ", date("r"));

make_table("end");
}

function present_mysql_information() {
make_headline("MySQL Information"." (MySQL ".get_mysql_variable("version").")");
$columns = array("Variable", "Value", "Explanation");
make_table("start");
make_table_header($columns);

if ( get_mysql_variable("have_bdb") == "YES" )

{

make_table_row("Berkeley DB version", "http://dev.mysql.com/doc/refman/4.1/en/bdb-storage-engine.html", "The current version of Berkeley DB (<i>BDB</i>) in use by MySQL", get_mysql_variable("version_bdb"));

}

make_table_row("Storage engine used", "http://dev.mysql.com/doc/refman/4.1/en/storage-engines.html", "The storage engine currently in use by MySQL", get_mysql_variable("table_type"));

make_table_row("Large File Support", "http://mirrors.dotsrc.org/mysql/doc/refman/5.0/en/table-size.html", "Whether MySQL has the option for <i>large file support</i> on or off", get_mysql_variable("large_files_support"));

make_table("end");
}

function present_mysql_load_status() {
make_headline("MySQL Database Load");
$columns = array("Measurement", "Result", "Explanation");
make_table("start");
make_table_header($columns);

make_table_row("Queries per second", "", "The number of queries the database server has received per second on average since it started. The higher the number, the higher the load.", get_mysql_statistics("Questions", "seconds"));

make_table_row("Connections per minute", "", "The number of connections that clients have made to the database server per minute on average since it started.", get_mysql_statistics("Connections", "minutes"));

$ratio = get_mysql_status("Aborted_connects") / get_mysql_status("Connections");

$per = 1 - $ratio;

$per = $per * 100;

$percent = round($per, 1);

make_table_row("Connection success rate", "", "The amount of connections to the database server that actually worked flawless, in percent.", $percent . "%");
make_table("end");

}

function present_mysql_storage_engines () {
make_headline("MySQL Storage Engines");
$columns = array("Storage Engine", "Available?", "Explanation");
make_table("start");
make_table_header($columns);

make_table_row("<i>BDB</i>", "http://dev.mysql.com/doc/refman/4.1/en/bdb-storage-engine.html", "Whether the <i>BDB</i> storage engine is available", get_mysql_variable("have_bdb"));

make_table_row("<i>INNODB</i>", "http://dev.mysql.com/doc/refman/4.1/en/innodb.html", "Whether the <i>INNODB</i> storage engine is available", get_mysql_variable("have_innodb"));

make_table_row("<i>ARCHIVE</i>", "http://dev.mysql.com/doc/refman/4.1/en/archive-storage-engine.htmll", "Whether the <i>ARCHIVE</i> storage engine is available", get_mysql_variable("have_archive"));

make_table_row("<i>BLACKHOLE</i>", "http://dev.mysql.com/doc/refman/5.0/en/blackhole-storage-engine.html", "Whether the <i>BLACKHOLE</i> storage engine is available", get_mysql_variable("have_blackhole_engine"));

make_table_row("<i>ISAM</i>", "http://dev.mysql.com/doc/refman/4.1/en/isam-storage-engine.html", "Whether the <i>ISAM</i> storage engine is available", get_mysql_variable("have_isam"));

make_table_row("<i>CSV</i>", "http://dev.mysql.com/doc/refman/4.1/en/csv-storage-engine.html", "Whether the <i>CSV</i> storage engine is available", get_mysql_variable("have_csv"));

make_table_row("<i>EXAMPLE</i>", "http://dev.mysql.com/doc/refman/4.1/en/example-storage-engine.html", "Whether the <i>EXAMPLE</i> storage engine is available", get_mysql_variable("have_csv"));

make_table("end");
}

function present_mysql_encoding() {
make_headline("MySQL Encodings");
$columns = array("Value", "Encoding", "Explanation");
make_table("start");
make_table_header($columns);

make_table_row("Character set <i>client</i>", "http://mirrors.dotsrc.org/mysql/doc/refman/5.0/en/charset-syntax.html", "The character set in which statements are sent by the client", get_mysql_variable("character_set_client"));

make_table_row("Character set <i>connection</i>", "http://mirrors.dotsrc.org/mysql/doc/refman/5.0/en/charset-syntax.html", "What character set the server translates a statement to after receiving it", get_mysql_variable("character_set_connection"));

make_table_row("Character set <i>database</i>", "http://mirrors.dotsrc.org/mysql/doc/refman/5.0/en/charset-syntax.html", "The character set used for databases", get_mysql_variable("character_set_database"));

make_table_row("Character set <i>results</i>", "http://mirrors.dotsrc.org/mysql/doc/refman/5.0/en/charset-syntax.html", "What character set the server translates to before shipping result sets or error messages back to the client", get_mysql_variable("character_set_results"));

make_table_row("Character set <i>server</i>", "http://mirrors.dotsrc.org/mysql/doc/refman/5.0/en/charset-syntax.html", "The character set used by the MySQL server itself", get_mysql_variable("character_set_server"));

make_table_row("Character set <i>system</i>", "http://mirrors.dotsrc.org/mysql/doc/refman/5.0/en/charset-syntax.html", "The character set used by the system", get_mysql_variable("character_set_system"));


make_table("end");
}

function present_php_information() {
make_headline("PHP Information"." (PHP ".phpversion().")");
$columns = array("Variable", "Value", "Explanation");
make_table("start");
make_table_header($columns);

make_table_row("Loaded Extensions", "", "An extension adds extra features or functions to PHP", get_php_loaded_extensions());

make_table_row("Display errors", "", "Whether PHP is configured to display errors or not", get_php_configuration_boolean("display_errors"));

make_table_row("Register globals", "", "Whether PHP is configured to accept register globals. This is known to possibly cause security problems for scripts. It should <b>not</b> be activated.", get_php_configuration_boolean("register_globals"));

make_table_row("Allow <i>url_fopen</i>", "", "Whether to allow the treatment of URLs (like http:// or ftp://) as files", get_php_configuration_boolean("allow_url_fopen"));

make_table_row("Expose PHP", "", "Whether the webserver should expose to the world that it is running PHP. If turned off (<i>No</i>) the webserver hides some typical PHP exposures.", get_php_configuration_boolean("expose_php"));

make_table("end");
}


function present_all() {
	present_general_information();
	present_mysql_information();
	present_mysql_load_status();
	present_mysql_storage_engines();
	present_mysql_encoding();
	present_php_information();
}
function diagnosis () {
/* XHTML */
/* This is the skeleton of the actual page that the end user will see */
_e('
<div class="wrap">
    <h2>Useful Information About Your Setup</h2>
	<p>Here you can find information about the backend that is currently powering your Wordpress installation.</p>');
					
		present_all(); /* Call the function to get a table with all the PHP information */
	
		
_e('</div>');
/* XHTML */
}

/* Add pages to the administration panel */
add_action('admin_head', 'present_make_css'); /* Make sure our CSS is included */
add_action('admin_menu', 'show_diagnosis'); /* Make sure the rest of the page is outputted */

?>
