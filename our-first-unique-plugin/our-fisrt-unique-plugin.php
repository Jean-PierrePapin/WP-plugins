<?php

/*
  Plugin Name: Our Test Plugin
  Description: A truly amazing plugin.
  Version: 1.0
  Author: Brad
  Author URI: https://wwww.udemy.com/user/bradschiff/
*/

class WordCountAndTimePlugin {
	/**
	 * Constructor function of the class WordCountAndTimePlugin
	 */
	function __construct() {
		add_action('admin_menu', [$this, 'adminPage']);
		add_action('admin_init', [$this, 'settings']);
		add_filter('the_content', [$this, 'ifWrap']);
	}

	/**
	 * Function to check if the page is a single post, the query is the main query and the plugin checkboxes are all checked
	 */
	function ifWrap($content) {
		if (is_main_query() AND is_single() AND 
			(
				get_option('wcp_wordcount', '1') OR 
				get_option('wcp_charactercount', '1') OR 
				get_option('wcp_readtime', '1')
			)
		) {
			return $this->createHTML($content);
		}
		return $content;
	}

	/**
	 * Function to add the content at the beginning of a single post
	 */
	function createHTML($content) {
		$html = '<h3>' . esc_html(get_option('wcp_headline', 'Post Statistics')) . '</h3><p>';

		// get word count once because both wordcount and read time will need it
		if (get_option('wcp_wordcount', '1') OR get_option('wcp_readtime', '1')) {
			$wordCount = str_word_count(strip_tags($content));
		}

		if (get_option('wcp_wordcount', '1')) {
			$html .= 'This post has ' . $wordCount . ' words.<br>';
		}

		if (get_option('wcp_charactercount', '1')) {
			$html .= 'This post has ' . strlen(strip_tags($content)) . ' characters.<br>';
		}

		if (get_option('wcp_readtime', '1')) {
			$html .= 'This post will take about ' . count([$wordCount/225]) . ' minute(s) to read.<br>';
		}

		$html .= '</p>';

		if (get_option('wcp_location', '0')  == '0') {
			return $html . $content;
		}
		return $content . $html;
	}

	/**
	 * Function for the plugin settings
	 */
	function settings() {
		add_settings_section('wcp_first_section', null, null, 'word-count-settings-page');
		
		// Location
		add_settings_field('wcp_location', 'Display Location', [$this, 'locationHTML'], 'word-count-settings-page', 'wcp_first_section');
		register_setting('wordcountplugin', 'wcp_location', ['sanitize_callback' => [$this, 'sanitizeLocation'], 'default' => '0']);

		// Headline text
		add_settings_field('wcp_headline', 'Headline Text', [$this, 'headlineHTML'], 'word-count-settings-page', 'wcp_first_section');
		register_setting('wordcountplugin', 'wcp_headline', ['sanitize_callback' => 'sanitize_text_field', 'default' => 'Post Statistics']);

		// Wordcount
		add_settings_field('wcp_wordcount', 'Word Count', [$this, 'wordcountHTML'], 'word-count-settings-page', 'wcp_first_section');
		register_setting('wordcountplugin', 'wcp_wordcount', ['sanitize_callback' => 'sanitize_text_field', 'default' => '1']);

		// Character count
		add_settings_field('wcp_charactercount', 'Character Count', [$this, 'charactercountHTML'], 'word-count-settings-page', 'wcp_first_section');
		register_setting('wordcountplugin', 'wcp_charactercount', ['sanitize_callback' => 'sanitize_text_field', 'default' => '1']);

		// Read time
		add_settings_field('wcp_readtime', 'Read Time', [$this, 'readtimeHTML'], 'word-count-settings-page', 'wcp_first_section');
		register_setting('wordcountplugin', 'wcp_readtime', ['sanitize_callback' => 'sanitize_text_field', 'default' => '1']);
	}

	/**
	 * Function to sanitize the value sent to the database to be equal to 0 or 1
	 */
	function sanitizeLocation($input) {
		if ($input != '0' AND $input != '1') {
			add_settings_error('wcp_location', 'wcp_location_error', 'Display location must be either beginning or end!');
			return get_option('wcp_location');
		} 
		return $input;
	}

	/**
	 * Possible to write a reusable function for the checkboxes
	 */
	function charactercountHTML() { ?>
		<input type="checkbox" name="wcp_charactercount" value="1" <?php checked(get_option('wcp_charactercount', '1')); ?>>
	<?php }

	function readtimeHTML() { ?>
		<input type="checkbox" name="wcp_readtime" value="1" <?php checked(get_option('wcp_readtime', '1')); ?>>
	<?php }

	function wordcountHTML() { ?>
		<input type="checkbox" name="wcp_wordcount" value="1" <?php checked(get_option('wcp_wordcount'), '1'); ?>>
	<?php }

	function headlineHTML() { ?>
		<input type="text" name="wcp_headline" value="<?php echo esc_attr(get_option('wcp_headline')); ?>">
	<?php }

	function locationHTML() { ?>
		<select name="wcp_location">
			<option value="0" <?php selected(get_option('wcp_location'), '0'); ?>>Beginning of post</option>
			<option value="1" <?php selected(get_option('wcp_location'), '1'); ?>>End of post</option>
		</select>
	<?php }

	function adminPage() {
		add_options_page('Word Count Setting', 'Word Count', 'manage_options', 'word-count-settings-page', [$this, 'ourHTML']);
	}
	
	/**
	 * Function to display the HTML codes in the admin section in the WP admin backend
	 */
	function ourHTML() { ?>
		<div class="wrap">
			<h1>Word Count Settings</h1>
			<form action="options.php" method="POST">
			<?php
				settings_fields('wordcountplugin');
				do_settings_sections('word-count-settings-page');
				submit_button();
			?>
			</form>
		</div>
		
	<?php }
}

/**
 * Variable to instantiate the class WordCountAndTimePlugin in order to manipulate it
 */
$wordCountAndTimePlugin = new WordCountAndTimePlugin();



