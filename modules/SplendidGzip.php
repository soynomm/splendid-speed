<?php

/**
 * Enables GZipping of static assets feature via the use of
 * Apache web server and mod_deflate module.
 */
class SplendidGzip extends SplendidSpeed 
{
	/**
	 * A unique key used to store the setting in database.
	 */
	public $key = 'gzip';

	/**
	 * Title of the module.
	 */
	public $title = 'ZIP static assets';

	/**
	 * Label of the module.
	 */
	public $label = 'Zip static assets with Gzip technology.';

	/**
	 * Description of the module.
	 */
	public $description = 'This will reduce the size of your static assets, such as images, stylesheets and scripts. Smaller sizes of these assets allow the browser to load your website faster.';

	/**
	 * Activates any module related things.
	 * 
	 * @since 1.1
	 */
	public function activate(): void
	{
		$settings = $this->settings();
		$settings[$this->key] = true;
		$this->zip();
		update_option('splendid_speed_settings', $settings);
	}

	/**
	 * Disables any module related things.
	 * 
	 * @since 1.1
	 */
	public function disable(): void
	{
		$settings = $this->settings();
		unset($settings[$this->key]);
		$this->unzip();
		update_option('splendid_speed_settings', $settings);
	}

	/**
	 * Registers any module related things on page load.
	 * 
	 * @since 1.1
	 */
	public function register(): void
	{
		// Silence is golden.
	}

	/**
	 * Adds the needed rules to .htaccess to enable
	 * gzipping of static assets.
	 *
	 * @return void
	 *
	 * @since 1.0
	 */
	private function zip(): void
	{
		$lines = [
			'<IfModule mod_deflate.c>',
			'AddOutputFilterByType DEFLATE image/svg+xml',
			'AddOutputFilterByType DEFLATE application/javascript',
			'AddOutputFilterByType DEFLATE application/rss+xml',
			'AddOutputFilterByType DEFLATE application/vnd.ms-fontobject',
			'AddOutputFilterByType DEFLATE application/x-font',
			'AddOutputFilterByType DEFLATE application/x-font-opentype',
			'AddOutputFilterByType DEFLATE application/x-font-otf',
			'AddOutputFilterByType DEFLATE application/x-font-truetype',
			'AddOutputFilterByType DEFLATE application/x-font-ttf',
			'AddOutputFilterByType DEFLATE application/x-javascript',
			'AddOutputFilterByType DEFLATE application/xhtml+xml',
			'AddOutputFilterByType DEFLATE application/xml',
			'AddOutputFilterByType DEFLATE font/opentype',
			'AddOutputFilterByType DEFLATE font/otf',
			'AddOutputFilterByType DEFLATE font/ttf',
			'AddOutputFilterByType DEFLATE image/x-icon',
			'AddOutputFilterByType DEFLATE text/css',
			'AddOutputFilterByType DEFLATE text/html',
			'AddOutputFilterByType DEFLATE text/javascript',
			'AddOutputFilterByType DEFLATE text/plain',
			'AddOutputFilterByType DEFLATE text/xml',
			'</IfModule>'
		];

		insert_with_markers( get_home_path() . '.htaccess', 'Splendid Speed GZIP', $lines );
	}

	/**
	 * Removes the gzipping rules from .htaccess.
	 *
	 * @return void
	 *
	 * @since 1.0
	 */
	private function unzip(): void
	{
		insert_with_markers( get_home_path() . '.htaccess', 'Splendid Speed GZIP', [] );
	}
}