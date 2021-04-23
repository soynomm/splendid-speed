<?php

/**
 * Enables browser caching feature via the use of
 * Apache web server and mod_expires module.
 */
class SplendidBrowserCaching extends SplendidSpeed 
{
	/**
	 * A unique key used to store the setting in database.
	 */
	public $key = 'browser_caching';

	/**
	 * Title of the module.
	 */
	public $title = 'Cache static assets';

	/**
	 * Label of the module.
	 */
	public $label = 'Tell browsers to hold onto all of your static assets.';

	/**
	 * Description of the module.
	 */
	public $description = 'This will tell browsers to  hold onto images, stylesheets and scripts for longer which will make your pages load faster for people who have already visited your website, because all the static assets have already been downloaded.';

	/**
	 * Activates any module related things.
	 * 
	 * @since 1.1
	 */
	public function activate(): void
	{
		$settings = $this->settings();
		$settings[$this->key] = true;
		$this->add();
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
		$this->remove();
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
	 * Adds the needed rules to .htaccess to allow
	 * caching of static assets.
	 *
	 * @return void
	 *
	 * @since 1.0
	 */
	private function add(): void
	{
		$lines = [
			'<IfModule mod_expires.c>',
			// Add correct content-type for fonts
			'AddType application/vnd.ms-fontobject .eot',
			'AddType font/ttf .ttf',
			'AddType font/otf .otf',
			'AddType font/woff .woff',
			'AddType font/woff2 .woff2',
			'AddType image/svg+xml .svg',

			// Compress compressible fonts
			'AddOutputFilterByType DEFLATE font/ttf font/otf image/svg+xml',

			'ExpiresActive on',
			'ExpiresDefault "access plus 10 years"',
			'ExpiresByType image/jpg "access plus 10 years"',
			'ExpiresByType image/svg+xml "access 10 years"',
			'ExpiresByType image/gif "access plus 10 years"',
			'ExpiresByType image/jpeg "access plus 10 years"',
			'ExpiresByType image/png "access plus 10 years"',
			'ExpiresByType image/webp "access plus 10 years"',
			'ExpiresByType text/css "access plus 10 years"',
			'ExpiresByType text/javascript "access plus 10 years"',
			'ExpiresByType application/javascript "access plus 10 years"',
			'ExpiresByType application/x-shockwave-flash "access plus 10 years"',
			'ExpiresByType image/ico "access plus 10 years"',
			'ExpiresByType image/x-icon "access plus 10 years"',
			'ExpiresByType application/vnd.ms-fontobject "access plus 1 year"',
			'ExpiresByType font/ttf "access plus 1 year"',
			'ExpiresByType font/otf "access plus 1 year"',
			'ExpiresByType font/woff "access plus 1 year"',
			'ExpiresByType font/woff2 "access plus 1 year"',
			'ExpiresByType text/html "access plus 600 seconds"',
			'</IfModule>'
		];

		insert_with_markers( get_home_path() . '.htaccess', 'Splendid Speed Cache', $lines );
	}

	/**
	 * Removes the caching rules from .htaccess.
	 *
	 * @return void
	 *
	 * @since 1.0
	 */
	private function remove(): void
	{
		insert_with_markers( get_home_path() . '.htaccess', 'Splendid Speed Cache', [] );
	}
}