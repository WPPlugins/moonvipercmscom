<?php

	ini_set('display_errors', 0);
	
	require_once("../../../wp-config.php");
	if(!current_user_can('edit_plugins')) {
		die('Oops, sorry, you are not authorized to fiddle with plugins!');
	}

	switch ($_REQUEST['action']) {
		case 'init_package_transfer':
			if(!isset($_REQUEST['package'])) return;
			init_package_transfer($_REQUEST['package']);
			break;
		case 'init_package_extract':
			if(!isset($_REQUEST['package'],$_REQUEST['slug'])) return;
			init_package_extract($_REQUEST['package'], $_REQUEST['slug']);
			break;
	}
	
	function output_error($error) {
		echo "ERROR:$error";
	}
	
	function init_package_extract($package_url, $slug) {
		
		$filename = explode("/", $package_url);
		$filename = dirname(__FILE__) . "/packages/" . $filename[count($filename) - 1];
		
		if(!is_writable(dirname(__FILE__) . "/..")) {
			output_error("Plugins directory not writable.");
		} else {
			$mydir = dirname(__FILE__) . "/../" . $slug; 
			delete_directory($mydir);
			if(class_exists('ZipArchive')) {
				$zip = new ZipArchive;
				if ($zip->open($filename) === TRUE) {
					$zip->extractTo(dirname(__FILE__) . "/../");
					$zip->close();
					echo "SUCCESS:Plugin updated successfully!";
				} else {
					output_error("Failed to extract archive");
				}
			} else {
				require(dirname(__FILE__) . '/pclzip.lib.php');
				$archive = new PclZip($filename);
				if ($archive->extract(PCLZIP_OPT_PATH, realpath(dirname(__FILE__) . "/../") . "/") == 0) {
					output_error($archive->errorInfo(true));
				} else {
					echo "SUCCESS:Plugin updated successfully!";
				}
			}
			
		}
	}
	
	function init_package_transfer($package_url) {
		if(is_writable(dirname(__FILE__) . "/packages")) {
			$filename = explode("/", $package_url);
			$filename = $filename[count($filename) - 1];
			$local_path = dirname(__FILE__) . "/packages/$filename";
			get_file($package_url, $local_path);
		} else {
			output_error("Package directory not writable.");
		}
	}
	
	function get_file($remote_path, $local_path) {
		$out = fopen($local_path, 'wb'); 
		if(!$out) return false;
		
		if(function_exists('curl_init')) {
			$ch = curl_init(); 
			
			curl_setopt($ch, CURLOPT_FILE, $out); 
			curl_setopt($ch, CURLOPT_HEADER, 0); 
			curl_setopt($ch, CURLOPT_URL, $remote_path); 
			curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/4.0 (compatible; MSIE 5.01; Windows NT 5.0)');
			@curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
			curl_setopt($ch, CURLOPT_MAXREDIRS, 10);
			curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 15);
			curl_setopt($ch, CURLOPT_FAILONERROR, true);
			
			curl_exec($ch);
			
			if(curl_errno($ch) != 0) {
				output_error(trim(strip_tags(curl_error($ch))));
				return false;
			}
			
			$info = curl_getinfo($ch); 
			echo "SUCCESS:Downloaded {$info['size_download']} bytes @ ";
			echo number_format($info['speed_download'] / 1024,2) . "KBps";
			
			curl_close($ch); 
		} else if (ini_get('allow_url_fopen') && (($rh = fopen($remote_path, 'rb')) !== false)) {
			$segment_size = 4096;
			while (!feof($rh)) {
				fwrite($out,fread($rh, $segment_size));
			}
			fclose($rh);
		} else {
			output_error("You must install <a href='http://www.php.net/curl'>cURL</a>");
		}
		fclose($out);
		
		return true;
	}

	function delete_directory($dirname) {
		if(is_dir($dirname))
			$dir_handle = opendir($dirname);
		if (!$dir_handle)
			return false;
		while($file = readdir($dir_handle)) {
			if ($file != "." && $file != "..") {
				if (!is_dir($dirname."/".$file))
					unlink($dirname."/".$file);
				else {
					if($file != 'packages') delete_directory($dirname.'/'.$file);
				}
			}
		}
		closedir($dir_handle);
		// rmdir($dirname);
		return true;
	} 
	
?>
