<?php
ini_set('display_errors', 0);
require_once("../../../../wp-config.php");
header('Content-Type: text/javascript');
header('Cache-Control: no-cache');
header('Pragma: no-cache');
?>

/*global document, Ajax, location */

var plugin_dir = "<?php global $plugin_updater; echo $plugin_updater->get_absolute_path(); ?>";

function returnError(destId) {
	var element = document.getElementById(destId);
	if(!element) {
		return;
	}
	element.innerHTML = "Unexpected error occured while performing an AJAX request";
}

function displayLoading(destId) {
	var element = document.getElementById(destId);
	if(!element) {
		return;
	}
	var imgHTML = 'Downloading: <img src="' + plugin_dir + 'images/loading.gif" alt="loading..." />';
	element.innerHTML = imgHTML;
}

function parseExtractResponse(content, remote_package, slug, destId) {
	content = content.split('SUCCESS:');
	if(content.length > 1) {
		location.reload(true);
	} else {
		var element = document.getElementById(destId);
		element.innerHTML = content[content.length - 1];
	}
}

function init_package_extract(remote_package, slug, destId) {
	var url = plugin_dir + "ajax-updater.php";
	var query = "action=init_package_extract&package=" + remote_package + "&slug=" + slug;
	var updater_ajax = new Ajax.Request(url, {method: 'get',onSuccess: function(transport){
      var response = transport.responseText;
      parseExtractResponse(response, remote_package, slug, destId);
    }, parameters: query, onFailure: function(){ returnError(destId); }});
}

function parseInitResponse(content, remote_package, slug, destId) {
	var element = document.getElementById(destId);
	content = content.split('SUCCESS:');
	element.innerHTML = content[content.length - 1];
	if(content.length > 1) {
		init_package_extract(remote_package, slug, destId);
	}
}

function init_plugin_update(slug, remote_package, destId) {
	var url = plugin_dir + "ajax-updater.php";
	var query = "action=init_package_transfer&package=" + remote_package + "&slug=" + slug;
	displayLoading(destId);
	var updater_ajax = new Ajax.Request(url, {method: 'get',onSuccess: function(transport){
      var response = transport.responseText;
      parseInitResponse(response, remote_package, slug, destId);
    }, parameters: query, onFailure: function(){ returnError(destId); }});
	
	return false;
}
