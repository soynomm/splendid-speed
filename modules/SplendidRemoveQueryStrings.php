<?php

/**
 * Allows the website to become visible before
 * all of the scripts have loaded, thus allowing
 * users to start interacting with the website faster.
 */
class SplendidRemoveQueryStrings extends SplendidSpeed 
{
	/**
	 * A unique key used to store the setting in database.
	 */
	public $key = 'remove_query_strings';

	/**
	 * Title of the module.
	 */
	public $title = 'Remove query strings';

	/**
	 * Label of the module.
	 */
	public $label = 'Remove query strings from static assets.';

	/**
	 * Description of the module.
	 */
	public $description = 'Removing query strings from your static assets helps the caching of your website to be more efficient, thus making the website faster.';

	/**
	 * Activates any module related things.
	 * 
	 * @since 1.2
	 */
	public function activate(): void
    {
		$settings = $this->settings();
		$settings[$this->key] = true;
		update_option('splendid_speed_settings', $settings);
	}

	/**
	 * Disables any module related things.
	 * 
	 * @since 1.2
	 */
	public function disable(): void
    {
		$settings = $this->settings();
		unset($settings[$this->key]);
		update_option('splendid_speed_settings', $settings);
	}

	/**
	 * Registers any module related things on page load.
	 * 
	 * @since 1.2
	 */
	public function register(): void
    {
		if($this->setting($this->key) && !is_admin()) {
			add_filter('script_loader_tag', [$this, 'remove'], 15);
			add_filter('style_loader_tag', [$this, 'remove'], 15);
		}
	}

    /**
     * Remove the query string  from provided param.
     *
     * @param string $src
     *
     * @return string
     *
     * @since 1.2
     */
	public function remove(string $src): string
    {
		$src = preg_replace('/\?[^\']*/', '', $src);
		$src = preg_replace('/\?[^\"]*/', '', $src);

		return $src;
	}
}