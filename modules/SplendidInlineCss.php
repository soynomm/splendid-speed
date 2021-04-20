<?php

/**
 * Inlines and minifies all CSS used on the site,
 * thus saving the browser from having to do trips
 * to individual stylesheet files.
 */
use MatthiasMullie\Minify;

class SplendidInlineCss extends SplendidSpeed 
{
	/**
	 * A unique key used to store the setting in database.
	 */
	public $key = 'inline_css';

	/**
	 * Title of the module.
	 */
	public $title = 'Inline CSS';

	/**
	 * Label of the module.
	 */
	public $label = 'Inline all CSS stylesheets.';

	/**
	 * Description of the module.
	 */
	public $description = 'This will save the browser from making trips to each of the individual stylesheet files, thus making the page load faster.<div class="sp-option-heading-description-warning"><strong>Warning:</strong> may not work on every site due to a combination of some plugins.</div>';

	/**
	 * Cache directory.
	 */
	private $cache_dir = '';

	/**
	 * Upon initiation, set some things.
	 */
	function __construct() {
		if($this->setting($this->key)) {
			$this->cache_dir = wp_upload_dir()['basedir'] . '/' . 'splendid_speed';
		}
	}

	/**
	 * Activates any module related things.
	 * 
	 * @since 1.2
	 */
	public function activate(): void {
		$settings = $this->settings();
		$settings[$this->key] = true;
		update_option('splendid_speed_settings', $settings);
	}

	/**
	 * Disables any module related things.
	 * 
	 * @since 1.2
	 */
	public function disable(): void {
		$settings = $this->settings();
		unset($settings[$this->key]);

	 	// If cache file exists, delete it.	
		$this->deleteCache();

		update_option('splendid_speed_settings', $settings);
	}

	/**
	 * Registers any module related things on page load.
	 * 
	 * @since 1.2
	 */
	public function register(): void {
		if($this->setting($this->key)) {
			// If not admin.
			if(!is_admin()) {
				// Create inline styles
				add_action('wp_print_styles', function() {
					global $wp_styles;

					// If cache dir does not exist, create it.
					if(!file_exists($this->cache_dir)) {
						wp_mkdir_p($this->cache_dir);
					}

					// Get cache file.
					$cache = $this->getCache();

					// If cache file does not exist, get data and 
					// create a cache file with the data.
					if(!$cache) {
						// Compose cache
						$cache = $this->composeCache($wp_styles);

						// Create cache.
						$this->putCache($cache);
					}

					// Add inline CSS to head.
					if($cache) {
						foreach($wp_styles->queue as $style) {
							// check if we have this in cache, if yes, dequeue.
							// If we don't, we want it still queued because
							// some themes load CSS only on certain pages conditionally
							// and otherwise we would break that.
							if(preg_match('/splendid-speed:' . $style .'/', $cache)) {
								$wp_styles->dequeue($style);
							}
						}

						add_action('wp_head', function() use($cache) {
							echo '<style id="splendid-speed-inline-css">' . $cache . '</style>';
						});
					}
				}, 99999);
			}

			// Add hook to run when plugin is being updated.
			add_action('upgrader_process_complete', [$this, 'onUpgrade'], 10, 2);
		}
	}

	/**
	 * Runs when the plugin is being upgraded.
	 * 
	 * Will delete the cache for Inline CSS.
	 * 
	 * @param $upgradeObj
	 * @param $options
	 * @return void
	 * 
	 * @since 1.2.4
	 */
	public function onUpgrade($upgraderObj, $options): void {
		$this->deleteCache();
	}

	/**
	 * Retrieves the cache, if it exists.
	 * 
	 * @return string|bool
	 * 
	 * @since 1.2.4
	 */
	public function getCache() {
		if(file_exists($this->cache_dir . '/css.cache')) {
			return file_get_contents($this->cache_dir . '/css.cache');
		}

		return false;
	}

	/**
	 * Creates a cache file or updates it when
	 * it already exists.
	 * 
	 * @param $cache
	 * @return void
	 * 
	 * @since 1.2.4
	 */
	public function putCache(string $cache): void {
		if(file_exists($this->cache_dir)) {
			file_put_contents($this->cache_dir . '/css.cache', $cache);
		}
	}

	/**
	 * If the cache file exists, deletes it.
	 * 
	 * @return void
	 * 
	 * @since 1.2.4
	 */
	public function deleteCache(): void {
		if(file_exists($this->cache_dir . '/css.cache')) {
			unlink($this->cache_dir . '/css.cache');
		}
	}

	/**
	 * Composes the information we need for the cache, such as 
	 * CSS file contents and all CSS @import's, separately. 
	 * 
	 * We need them separately because CSS @import's will be ignored
	 * if they do not precede all other CSS rules.
	 * 
	 * @param $styles
	 * @return string
	 * 
	 * @since 1.2.3
	 */
	public function composeCache($styles): string {
		$cache = '';
		$imports = [];

		foreach($styles->registered as $style) {
			if(in_array($style->handle, $styles->queue)) {
				$deps = $style->deps;
				$src = $style->handle;

				// Get each dependency.
				foreach($deps as $dep) {
					// Do not get new dependency if we already have it.
					if(!preg_match('/splendid-speed:' . $dep .'/', $cache)) {
						$fetchResult = $this->fetch($dep);

						// Set imports
						if(!empty($fetchResult['imports'])) {
							$imports = $imports + $fetchResult['imports'];
						}

						// Set cache
						if(!empty($fetchResult['css'])) {
							$cache = $cache . "\n" . '/* splendid-speed:' . $dep . ' */' . "\n" . $fetchResult['css'];
						}
					}
				}

				// Get style itself
				$fetchResult = $this->fetch($src);

				// Set imports
				if(!empty($fetchResult['imports'])) {
					$imports = $imports + $fetchResult['imports'];
				}

				// Set cache
				if(!empty($fetchResult['css'])) {
					$cache = $cache . "\n" . '/* splendid-speed:' . $src .' */' . "\n" . $fetchResult['css'];
				}
			}
		}

		return join(' ', $imports) . $cache;
	}

	/**
	 * Fetches the CSS for a given registered style.
	 *
	 * @param $name
	 * @return string|bool
	 * 
	 * @since 1.2
	 */
	public function fetch(string $name) {
		global $wp_styles;

		$src = $wp_styles->registered[$name]->src;

		if(!$src) return false;

		// Local URLs.
		if($src[0] === '/' && $src[1] !== '/') {
			$src = get_bloginfo('url') . $src;
		}

		// Protocol relative URLs.
		if($src[0] === '/' && $src[1] === '/') {
			$src = 'http:' . $src; // I assume http, because usually even if not, it gets redirected nicely.
		}

		try {
			if(!function_exists('curl_version')) {
				return false;
			}

		    $curl = curl_init();

			curl_setopt($curl, CURLOPT_URL, $src);
			curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($curl, CURLOPT_HEADER, false);
			curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 0);
			curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);

			$contents = curl_exec($curl);
			$content_type = curl_getinfo($curl, CURLINFO_CONTENT_TYPE);

			curl_close($curl);

			// If no content, or content not CSS, return nothing.
			if(!$contents || !preg_match('/text\/css/', $content_type)) {
				return false;
			}

			// Correct URL's
			$contents = preg_replace('/url\((?!http|\/\/|\'|\")/', 'url(' . substr($src, 0, strrpos($src, '/')) . '/', $contents);
			$contents = preg_replace('/url\(\'(?!http|\/\/|data:)/', "url('" . substr($src, 0, strrpos($src, '/')) . '/', $contents);
			$contents = preg_replace('/url\(\"(?!http|\/\/|data:)/', 'url("' . substr($src, 0, strrpos($src, '/')) . '/', $contents);

			// Find all @imports
			preg_match_all('/@import.*;/', $contents, $matches);
			
			// Remove all @imports
			$contents = preg_replace('/@import.*;/', '', $contents);

			$minifier = new Minify\CSS();
			$minifier->add($contents);
			$wp_styles->dequeue($name);

			return [
				'css' => $minifier->minify(),
				'imports' => !empty($matches[0]) ? $matches[0] : []
			];
		} catch(Exception $e) {
			return false;
		}
	}
}