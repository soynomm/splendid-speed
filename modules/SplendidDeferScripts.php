<?php

/**
 * Allows the website to become visible before
 * all of the scripts have loaded, thus allowing
 * users to start interacting with the website faster.
 */
class SplendidDeferScripts extends SplendidSpeed 
{
	/**
	 * A unique key used to store the setting in database.
	 */
	public $key = 'defer_scripts';

	/**
	 * Title of the module.
	 */
	public $title = 'Defer Scripts';

	/**
	 * Label of the module.
	 */
	public $label = 'Defer interactive scripts.';

	/**
	 * Description of the module.
	 */
	public $description = 'This will allow your website to become visible even before all of the scripts on your website have loaded, allowing the visitors to start interacting with your website faster.';

	/**
	 * List of JS libraries that we won't defer, 
	 * as it can cause breakages.
	 */
	private $excluded = [
		'jquery'
	];

	/**
	 * Activates any module related things.
	 * 
	 * @since 1.2
	 */
	public function activate() {
		$settings = $this->settings();
		$settings[$this->key] = true;
		update_option('splendid_speed_settings', $settings);
	}

	/**
	 * Disables any module related things.
	 * 
	 * @since 1.2
	 */
	public function disable() {
		$settings = $this->settings();
		unset($settings[$this->key]);
		update_option('splendid_speed_settings', $settings);
	}

	/**
	 * Registers any module related things on page load.
	 * 
	 * @since 1.2
	 */
	public function register() {
		if($this->setting($this->key) && !is_admin()) {
			add_filter('script_loader_tag', function($script) {
				// If it's not JS, don't do anything.
				if(strpos($script, '.js') === false) return $script;
				
				// If it already has deferring, don't do anything.
				if(preg_match('/defer/', $script)) return $script;

				// If it has async, don't do anything.
				if(preg_match('/async/', $script)) return $script;

				// If it's in $excluded, don't do anything.
				foreach($this->excluded as $excluded) {
					if(preg_match('/' . $excluded .'/', $script)) {
						return $script;
					}
				}

				// If we reached this point, defer it.
				return str_replace(' src', ' defer src', $script);
			}, 99999);
		}
	}
}