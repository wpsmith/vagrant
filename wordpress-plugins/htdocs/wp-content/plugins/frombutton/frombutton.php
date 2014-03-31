<?php   
/**
	Plugin Name: From Button
	Description: From Button WP Plugin
	Plugin URI: http://www.frombutton.com/
	Version: 1.0 
	Author: Bassem Rabia
	Author URI: mailto:bassem.rabia@hotmail.co.uk
	License: GPLv2
**/  
  
/* Update on 02.03.2014 */
// delete_option('FromButton_plugin_options');  
 
if(realpath(__FILE__) === realpath($_SERVER["SCRIPT_FILENAME"]))
	exit("Do not access this file directly."); 
else{ 
	$plugin_name 	= 'From Button';
	$plugin_version = '1.0'; 
	require_once(dirname(__FILE__).'/classes/FromButton.class.php');  
	$FromButton = new FromButton($plugin_name, $plugin_version);  
}
?>
