<?php

/*
  Plugin Name: Our Test Plugin
  Description: A truly amazing plugin.
  Version: 1.0
  Author: Brad
  Author URI: https://wwww.udemy.com/user/bradschiff/
*/

class WordCountAndTimePlugin {
	function __construct() {
		add_action('admin_menu', [$this, 'adminPage']);
	}

	function adminPage() {
		add_options_page('Word Count Setting', 'Word Count', 'manage_options', 'word-count-settings-page', [$this, 'ourHTML']);
	}
	
	function ourHTML() { ?>
		<div class="wrap">
			<h1>Word Count Settings</h1>
		</div>
		
	<?php }
}

$wordCountAndTimePlugin = new WordCountAndTimePlugin();



