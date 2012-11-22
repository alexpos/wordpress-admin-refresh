<?php

class SevenDegrees_LoginRefresh {
	public function __construct() {
		// We only want to do the following if we have a custom logo
		if(file_exists(get_template_directory() . '/admin-logo.png')) {
			add_action('login_head',		array($this, 'logoSrc'));
			add_filter('login_headerurl',	array($this, 'logoUrl'));
			add_filter('login_headertitle',	array($this, 'logoTitle'));
		}
	}

	public function logoSrc() {
		?>
		<style>
			#login h1 a {
				background-image: url(<?php echo get_template_directory_uri() . '/admin-logo.png'?>);
			}
		</style>
		<?php
	}

	public function logoUrl() {
		return site_url();
	}

	public function logoTitle() {
		return get_option('blogname');
	}
}

if(!isset($GLOBALS['SevenDegrees_LoginRefresh'])) {
	$GLOBALS['SevenDegrees_LoginRefresh'] = new SevenDegrees_LoginRefresh;
}