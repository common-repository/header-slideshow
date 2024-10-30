<?php
/*
Plugin Name: Header Slideshow
Plugin URI: http://wordpress.org/extend/plugins/header-slideshow/
Description: Adds an image slideshow with fade transitions between images. Add an unlimited number of images by modifying the xml file.
Author: Michael Smale
Version: 1.1
Author URI: http://www.michaelsmale.com/
License: GPL2  Copyright 2012  Michael Smale  (email : mike@michaelsmale.com)
	
    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License, version 2, as 
    published by the Free Software Foundation.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    A copy of the GNU General Public License is available online at
    http://www.gnu.org/licenses/gpl-2.0.html or write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA

*/

/**
 *  Includes the google jquery, if not already included
 *  Includes custom.js used by the slideshow to implement custom settings, 
 *  custom settings set in the plugin admin page, found under Settings.
*/
function includeScriptCSS() {

	if( get_option('header_slideshow_homepage_only', true ) && is_home() || !get_option('header_slideshow_homepage_only', false ) )
	{
		echo "<link rel='stylesheet' type='text/css' media='screen' href='" . WP_PLUGIN_URL . "/header-slideshow/resources/screen.css' />";
		
		// use the latest version of jquery on Google's CDN at the time of development
		if (!is_admin()) {	
			wp_deregister_script( 'jquery' );
			echo "<script src='http://ajax.googleapis.com/ajax/libs/jquery/1.4.2/jquery.min.js' type='text/javascript'></script>";
		}	
	}
}

add_action('wp_head', 'includeScriptCSS');

/**
 *  Writes the HTML tags that will display the slideshow on the site,
 *  and also some hidden tags that store the settings
 *  
 *  All pages, except admin pages, have div tags added immediately below the body tag
 *  to display the slideshow.  Additionally there are two hidden divs that store settings 
 *  for use in the javascript (custom.js) that is responsible for the loading and 
 *  transitioning the images.  Finally a style tag adds a top-margin equal to the height 
 *  of the images, as set by an administrator in the option page. 
*/
function setupDivs() {

	if( get_option('header_slideshow_homepage_only', true ) && is_home() || !get_option('header_slideshow_homepage_only', false )  )
	{
        echo "<div class='header-slideshow' id='hdr-slideshow' style='height:" . attribute_escape(stripslashes(get_option('header_slideshow_height'))) . "px;'>";
		echo "<div class='header current' id='hdr1' ";
		echo "style='top:" . attribute_escape(stripslashes(get_option('header_slideshow_top'))) . "px; ";
		echo "width:" . attribute_escape(stripslashes(get_option('header_slideshow_width'))) . "px; ";
		
        echo "height:" . attribute_escape(stripslashes(get_option('header_slideshow_height'))) . "px; ";
		
		//test if centered - or uses left
		if(!get_option('header_slideshow_centered')){
			echo "left:" . attribute_escape(stripslashes(get_option('header_slideshow_left'))) . "px; ";
		}else{
			echo "margin-left:" . (attribute_escape(stripslashes(get_option('header_slideshow_width')))/-2) . "px; ";
		}
		
		echo "'></div>";
		
		echo "<div class='header' id='hdr2' ";
		echo "style='top:" . attribute_escape(stripslashes(get_option('header_slideshow_top'))) . "px; ";
		echo "width:" . attribute_escape(stripslashes(get_option('header_slideshow_width'))) . "px; ";
		echo "height:" . attribute_escape(stripslashes(get_option('header_slideshow_height'))) . "px; ";
	
		//test if centered - or uses left
		if(!get_option('header_slideshow_centered')){
			echo "left:" . attribute_escape(stripslashes(get_option('header_slideshow_left'))) . "px; ";
		}else{
			echo "margin-left:" . (attribute_escape(stripslashes(get_option('header_slideshow_width')))/-2) . "px; ";
		}
	
		echo "'></div>";      
        echo "</div>";
		
		echo "<div id='installedPath' class='hidden'>" . plugins_url().'/header-slideshow/resources/' . "</div>";
        echo "<div id='xmlPath' class='hidden'>" . attribute_escape(stripslashes(get_option('header_slideshow_xmlPath'))) ."</div>";
		echo "<div id='timeout' class='hidden'>" . attribute_escape(stripslashes(get_option('header_slideshow_timeout'))) . "</div>";

        echo "<script src='" . WP_PLUGIN_URL . "/header-slideshow/resources/custom.js' type='text/javascript'></script>";
	}
}

add_action('wp_footer', 'setupDivs');

/**
 *  Upon installing header slideshow, default example settings are saved to options
 *  and the plug-in's administration page added under settings
 * 
 * */
function header_slideshow_admin_menu() {

	// Install with the default / example settings
	add_option('header_slideshow_timeout',3000);
	add_option('header_slideshow_width',940);
	add_option('header_slideshow_height',338);
	add_option('header_slideshow_centered', true);
	add_option('header_slideshow_homepage_only', false);
	add_option('header_slideshow_left',0);
	add_option('header_slideshow_top',30);
	add_option('header_slideshow_xmlPath', WP_PLUGIN_URL . '/header-slideshow/resources/header-image-links.xml');
		
	add_options_page('Header Slideshow Settings', 'Header Slideshow', 'administrator', 'header_slideshowID', 'header_slideshow_submenu');
		
}

add_action('admin_menu', 'header_slideshow_admin_menu');


/* 
 * Displays the header_slideshow admin menu
 */
function header_slideshow_submenu() {

	// if save clicked and posting back here - save the changes - if from valid admin page
	if (isset($_REQUEST['save']) && $_REQUEST['save']) {
	
		check_admin_referer('header_slideshow-config');
	
		if (isset($_POST['timeout']) && is_numeric($_POST['timeout'])) {
			update_option('header_slideshow_timeout',$_POST['timeout']);
		}else {
			update_option('header_slideshow_timeout',3000);
		}
	
		if (isset($_POST['width']) && is_numeric($_POST['width'])) {
			update_option('header_slideshow_width',$_POST['width']);
		}else {
			update_option('header_slideshow_width',940);
		}
		
		if (isset($_POST['height']) && is_numeric($_POST['height'])) {
			update_option('header_slideshow_height',$_POST['height']);
		}else {
			update_option('header_slideshow_height',338);
		}
		
		if (isset($_POST['centered']) && $_POST['centered'])
		{
			update_option('header_slideshow_centered', true);				
		}else{
			update_option('header_slideshow_centered', false);
		}
		
		if (isset($_POST['homepage_only']) && $_POST['homepage_only'])
		{
			update_option('header_slideshow_homepage_only', true);				
		}else{
			update_option('header_slideshow_homepage_only', false);
		}
					
		if (isset($_POST['left']) && is_numeric($_POST['left'])) {
			update_option('header_slideshow_left',$_POST['left']);
		}else {
			update_option('header_slideshow_left',0);
		}
		
		if (isset($_POST['top']) && is_numeric($_POST['top'])) {
			update_option('header_slideshow_top',$_POST['top']);
		}else {
			update_option('header_slideshow_top',30);
		}
		
		if (isset($_POST['xmlPath']) && !trim($_POST['xmlPath']) == "") {
			update_option('header_slideshow_xmlPath',$_POST['xmlPath']);
		}else {
			update_option('header_slideshow_xmlPath', WP_PLUGIN_DIR . '/header-slideshow/resources/header-image-links.xml');
		}
				
	}
	
	/**
	 * Display options.
	 */
	?>
	<form action="<?php echo attribute_escape( $_SERVER['REQUEST_URI'] ); ?>" method="post">
	<?php
		if ( function_exists('wp_nonce_field') )
			wp_nonce_field('header_slideshow-config');
	?>
	
	
	<div class="wrap">
		<?php screen_icon(); ?>
		<h2><?php _e("Header Slideshow Settings", 'header_slideshow'); ?></h2>
		<table class="form-table">
		
		<tr>
			<th scope="row" valign="top">
				XML location:
			</th>
			<td>
				<input size="100" type="text" name="xmlPath" 
				value="<?php echo attribute_escape(stripslashes(get_option('header_slideshow_xmlPath', 
								 WP_PLUGIN_URL . '/header-slideshow/resources/header-image-links.xml'))); ?>" /><br />
			</td>
		</tr>
		
		<tr>
			<th scope="row" valign="top">
				Set Timeout:
			</th>
			<td>
				timeout: <input type="text" name="timeout" value="<?php echo attribute_escape(stripslashes(get_option('header_slideshow_timeout',3000))); ?>" />
			</td>		
		</tr>
		
		<tr>
			<th scope="row" valign="top">
				Set Dimensions:
			</th>
			<td>
				width: <input type="text" name="width" value="<?php echo attribute_escape(stripslashes(get_option('header_slideshow_width',940))); ?>" />
				height: <input type="text" name="height" value="<?php echo attribute_escape(stripslashes(get_option('header_slideshow_height',338))); ?>" />
			</td>		
		</tr>
		
		<tr>
			<th scope="row" valign="top">
				Set Position:
			</th>
			<td>
				left: <input type="text" name="left" value="<?php echo attribute_escape(stripslashes(get_option('header_slideshow_left',0))); ?>" />
				top: <input type="text" name="top" value="<?php echo attribute_escape(stripslashes(get_option('header_slideshow_top',30))); ?>" />
			</td>		
		</tr>
		
		<tr>
			<th scope="row" valign="top">
				Centered:
			</th>
			<td>
				<input type="checkbox" name="centered" value="1" <?php checked('1', get_option('header_slideshow_centered', true)); ?>" />
			</td>
		</tr>
		
		<tr>
			<th scope="row" valign="top">
				Homepage Only:
			</th>
			<td>
				<input type="checkbox" name="homepage_only" value="1" <?php checked('1', get_option('header_slideshow_homepage_only', false)); ?> />
			</td>
		</tr>
				
		<tr>
			<td>&nbsp;</td>
			<td>
				<span class="submit"><input name="save" value="Save Changes" type="submit" /></span>
			</td>
		</tr>
	</table>
	</div>
	</form>
<?php
}

/**
 * Delete options used by plug-in on uninstall
 * 
 */
function uninstall_header_slideshow() {
	
	delete_option('header_slideshow_timeout');
	delete_option('header_slideshow_width');
	delete_option('header_slideshow_height');
	delete_option('header_slideshow_centered');
	delete_option('header_slideshow_homepage_only');
	delete_option('header_slideshow_left');
	delete_option('header_slideshow_top');
	delete_option('header_slideshow_xml_path');
				
}

// On deactivation remove options
register_deactivation_hook(__FILE__,'uninstall_header_slideshow');
?>