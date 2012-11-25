<?php
/**
 * Plugin Name:	7 Degrees: Admin Refresh
 * Plugin URI:	http://7degrees.co.uk
 * Author:		Damian Gostomski
 * Author URI:	http://7degrees.co.uk
 * Description:	Clean up the WordPress admin interface and add some custom branding.
 * Version:		1.0
 */

// Include another class to handle customisations to the login screen
// As they won't get triggered below due to the is_admin() check
include_once 'login.php';

// Only run the following code if we're in the admin area
if(!is_admin()) return;

class SevenDegrees_AdminRefresh {
	public function __construct() {
		add_action('admin_menu',			array($this, 'cleanupMenu'), 999);
		add_action('init',					array($this, 'relabelPosts')); // Admin init runs after the menus are created
		add_action('admin_menu',			array($this, 'relabelPostsMenu'));
		add_action('wp_dashboard_setup',	array($this, 'removeWidgets'));
		add_filter('admin_footer_text',		array($this, 'customFooterText'));
	}

	/**
	 * Cleanup the admin menu by removing items which are never accessed
	 * They can still be acceassed directly via URL should the need exist
	 * This needs to be run very late on (priority 999) as some menu items are added after default priority
	 */
	public function cleanupMenu() {
		// Remove top level menu items
		remove_menu_page('upload.php');
		remove_menu_page('link-manager.php');

		// Remove sub menu items
		remove_submenu_page('themes.php', 'theme-editor.php');
		remove_submenu_page('plugins.php', 'plugin-editor.php');
	}

	/**
	 * Relabel "Posts" to a much more user friendly "Articles"
	 * Handles all the variations as well
	 */
	public function relabelPosts() {
		global $wp_post_types;

		$wp_post_types['post']->labels->name				= 'Articles';
		$wp_post_types['post']->labels->singular_name		= 'Article';
		$wp_post_types['post']->labels->add_new				= 'Add New';
		$wp_post_types['post']->labels->add_new_item		= 'Add New Article';
		$wp_post_types['post']->labels->edit_item			= 'Edit Article';
		$wp_post_types['post']->labels->new_item			= 'New Article';
		$wp_post_types['post']->labels->view_item			= 'View Article';
		$wp_post_types['post']->labels->search_items		= 'Search Articles';
		$wp_post_types['post']->labels->not_found			= 'No articles found';
		$wp_post_types['post']->labels->not_found_in_trash	= 'No articles found in Trash';
		$wp_post_types['post']->labels->all_items			= 'All Articles';
		$wp_post_types['post']->labels->menu_name			= 'Articles';
		$wp_post_types['post']->labels->name_admin_bar		= 'Article';
	}

	/**
	 * Update the admin menu to use Articles instead of News
	 * This will automatically pull in terms updated from $wp_post_types['post']->labels
	 */
	public function relabelPostsMenu() {
		global $menu, $submenu, $wp_post_types;

		$menu[5][0]		= $wp_post_types['post']->labels->name;
		$submenu['edit.php'][5][0]	= $wp_post_types['post']->labels->all_items;
		$submenu['edit.php'][10][0]	= $wp_post_types['post']->labels->add_new;
	}

	/**
	 * Removes most of the dashboard widgets, as they're irrelevant to most sites
	 * Leaves:
	 * - Right Now
	 * - Recent Comments
	 * - Recent Drafts
	 *
	 * Other dashboard widgets added by themes/plugins are unaffected
	 */
	public function removeWidgets() {
		global $wp_meta_boxes;

		unset($wp_meta_boxes['dashboard']['normal']['core']['dashboard_incoming_links']);
		unset($wp_meta_boxes['dashboard']['normal']['core']['dashboard_plugins']);
		unset($wp_meta_boxes['dashboard']['side']['core']['dashboard_quick_press']);
		unset($wp_meta_boxes['dashboard']['side']['core']['dashboard_primary']);
		unset($wp_meta_boxes['dashboard']['side']['core']['dashboard_secondary']);
	}

	/**
	 * Custom footer text
	 * Show the theme name and a link to the author
	 * Information is pulled in from the theme definition
	 */
	public function customFooterText() {
		$theme	= wp_get_theme();

		return	$theme->display('Name') . ' by <a href="' . $theme->display('AuthorURI') . '" target="_blank">' . $theme->display('Author') . '</a>';
	}
}

if(!isset($GLOBALS['SevenDegrees_AdminRefresh'])) {
	$GLOBALS['SevenDegrees_AdminRefresh'] = new SevenDegrees_AdminRefresh;
}