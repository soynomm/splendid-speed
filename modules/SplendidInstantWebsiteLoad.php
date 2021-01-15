<?php

/**
 * Enables the perceived instant website load
 * via the use of instant.page javascript library
 * which preloads all pages before clicking on them.
 */
class SplendidInstantWebsiteLoad extends SplendidSpeed 
{
	/**
	 * A unique key used to store the setting in database.
	 */
	public $key = 'instantWebsiteLoad';

	/**
	 * Title of the module.
	 */
	public $title = 'Preload website pages';

	/**
	 * Label of the module.
	 */
	public $label = 'Preload the pages of your WordPress website.';

	/**
	 * Description of the module.
	 */
	public $description = 'Preloading the pages of your WordPress website right before you click on a link gives you much faster website loading which enhances user experience.';

	/**
	 * Activates any module related things.
	 * 
	 * @since 1.1
	 */
	public function activate() {
		$settings = $this->settings();
		$settings[$this->key] = true;
		update_option('splendid_speed_settings', $settings);
	}

	/**
	 * Disables any module related things.
	 * 
	 * @since 1.1
	 */
	public function disable() {
		$settings = $this->settings();
		unset($settings[$this->key]);
		update_option('splendid_speed_settings', $settings);
	}

	/**
	 * Registers any module related things on page load.
	 * 
	 * @since 1.1
	 */
	public function register() {
		if (!is_admin() && $this->setting($this->key)) {
			add_action('wp_enqueue_scripts', function() {
				wp_enqueue_script('splendid-speed-instant-page', SPLENDID_SPEED_DIR_URL . 'assets/js/instant.page.js', [], '5.1.0', true);
			});
		}
	}
}