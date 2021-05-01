<?php

namespace SplendidSpeed\Modules;

use SplendidSpeed\SplendidSpeed;

/**
 * Enables the conversion of images to the more 
 * performant WebP file format upon activation, while
 * being on the Splendid Speed settings page, and whenever
 * uploading new images.
 */
class ConvertImagesWebp extends SplendidSpeed
{
	/**
	 * A unique key used to store the setting in database.
	 */
	public $key = 'convert_images_webp';

	/**
	 * Title of the module.
	 */
	public $title = 'Convert images to WebP';

	/**
	 * Label of the module.
	 */
	public $label = 'Convert all of your images to WebP file format.';

	/**
	 * Description of the module.
	 */
	public $description = 'WebP is a more compact image file format, which allows browsers to download your images faster.
	<div class="sp-option-heading-description-warning"><strong>Warning:</strong> deactivating this module will delete all of the WebP images that have been converted.</div>';

	/**
	 * Option heading HTML
	 */
	public $option_heading_html = '';

	/**
	 * Is the module supported by the hosting provider?
	 */
	public $supported = true;

	/**
	 * And if it isn't, display a message.
	 */
	public $not_supported_message;

	/**
	 * Upon plugin load, if the plugin is activated,
	 * add the span into the description for our ajax based 
	 * conversion system, which will display the amount of 
	 * images left to convert.
	 * 
	 * @since 1.1
	 */
	function __construct()
	{
		if(!class_exists('\Imagick') && !extension_loaded('gd')) {
			$this->supported = false;
			$this->not_supported_message =  'This webserver does not support Imagick or GD required for the module to work. Please contact your webmaster.';
		}

		if($this->setting($this->key)) {
			$this->option_heading_html = '<span class="sp-convert-images-webp-js">
				<div class="sp-convert-images-webp-js-progress"></div>
				<div class="sp-convert-images-webp-js-label">0%</div>
			</span>';
		}
	}

	/**
	 * Activates any module related things.
	 * 
	 * @since 1.1
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
	 * @since 1.1
	 */
	public function disable(): void
	{
		$settings = $this->settings();
		unset($settings[$this->key]);
		$this->deleteImages();
		update_option('splendid_speed_settings', $settings);
	}

	/**
	 * Registers any module related things on page load.
	 * 
	 * @since 1.1
	 */
	public function register(): void
	{
		// Add JS to admin.
		add_action('admin_enqueue_scripts', function($hook) {
			if($hook === 'settings_page_splendid-speed') {
				$convert_images_js = SPLENDID_SPEED_DIR_URL . 'assets/js/admin-convert-images-webp.js';
				wp_enqueue_script('admin-convert-images-webp', $convert_images_js, ['wp-util'], '1.0', true);
			}
		});

		add_action('wp_ajax_nopriv_splendid_speed_convert_images_webp', [$this, 'ajaxConvert']);
		add_action('wp_ajax_splendid_speed_convert_images_webp', [$this, 'ajaxConvert']);

		// If the plugin is activated...
		if($this->setting($this->key)) {
			// Convert on image uploads
			add_filter('wp_generate_attachment_metadata', [$this, 'convertOnUpload'], 10, 2);

			// Alter HTML
			add_filter('the_content', [$this, 'alterHTML'], 99999);
			add_filter('the_excerpt', [$this, 'alterHTML'], 99999);
			add_filter('post_thumbnail_html', [$this, 'alterHTML'], 99999);

			// Alter image SRC
			add_filter('wp_get_attachment_image_src', [$this, 'alterImageSrc']);
		}
	}

	/**
	 * Deletes all .webp images.
	 * 
	 * @since 1.1
	 */
	public function deleteImages(): void
	{
		$directory = wp_upload_dir()['basedir'];
		$iterator = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($directory));

		foreach($iterator as $file) {
			if($file->isDir()) continue;

			$path_name = $file->getPathname();
			$ext = $file->getExtension();

			if(str_ends_with($path_name, '_ss.webp') && $ext === 'webp') {
				unlink($path_name);
			}
		}
	}

	/**
	 * Alters the given HTML given by
	 * modifying all `img` tags to use the .webp
	 * counterpart if one is available.
	 *
	 * @param string $content
	 *
	 * @return string
	 *
	 * @since 1.1
	 */
	public function alterHTML(string $content): string
	{
		// Skip the feed.
		if(is_feed()) return $content;

		preg_match_all('/(https?:\/\/\S+\.(?:jpg|jpeg|png))\s+/', $content, $matches);

		if(!empty($matches[0]) && is_array($matches[0])) {
			foreach($matches[0] as $match) {
				$file = substr($match, strpos($match, 'uploads/') + 8);
				$file_webp = str_replace('.jpg', '_ss.webp', $file);
				$file_webp = str_replace('.jpeg', '_ss.webp', $file_webp);
				$file_webp = str_replace('.png', '_ss.webp', $file_webp);
				
				if(file_exists(wp_upload_dir()['basedir'] . '/' . $file_webp)) {
					$content = str_replace($file, $file_webp, $content);
				}
			}
		}


		return $content;
	}

	/**
	 * Alters the images called with 
	 * `wp_get_attachment_image_src` by modifying
	 * it to use the .webp counterpart if
	 * one is available.
	 * 
	 * @param $image
	 * @return array
	 * 
	 * @since 1.1.3
	 */
	public function alterImageSrc($image)
	{
		if(!empty($image) && !empty($image[0])) {
			$src = $image[0];
			$file = substr($src, strpos($src, 'uploads/') + 8);
			$newsrc = str_replace('.jpg', '_ss.webp', $src);
			$newsrc = str_replace('.jpeg', '_ss.webp', $newsrc);
			$newsrc = str_replace('.png', '_ss.webp', $newsrc);
			$webp_file = substr($newsrc, strpos($newsrc, 'uploads/') + 8);
			$webp_file_path = wp_upload_dir()['basedir'] . '/' . $webp_file;

			if(file_exists($webp_file_path)) {
				$image[0] = str_replace($file, $webp_file, $image[0]);
			}
		}

		return $image;
	}

	/**
	 * Converts images via ajax requests
	 * and while doing so returns a string containing
	 * the number of images converted.
	 * 
	 * @since 1.1
	 */
	public function ajaxConvert()
	{
		if(!class_exists('Imagick') && !extension_loaded('gd')) {
			wp_send_json_success(['error' => 'Can\'t convert images. Contact your webmaster about lacking Imagick.']);
		}

		$totalImages = $this->totalImages();
		$convertedImages = $this->convertedImages();

		if($totalImages === $convertedImages) {
			wp_send_json_success(['progress' => 100]);
		}

		$this->convert();

		wp_send_json_success(['progress' => number_format(($convertedImages * 100) / $totalImages, 2)]);

		die();
	}

	/**
	 * Returns the total number of images in the
	 * uploads directory.
	 * 
	 * @return int
	 * 
	 * @since 1.1
	 */
	public function totalImages(): int
	{
		$images = 0;
		$directory = wp_upload_dir()['basedir'];
		$iterator = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($directory));

		foreach($iterator as $file) {
			if($file->isDir()) continue;

			$ext = $file->getExtension();

			if($ext === 'jpg' || $ext === 'jpeg' || $ext === 'png') {
				$images++;
			}
		}

		return $images;
	}

	/**
	 * Returns the total number of images converted
	 * in the uploads directory.
	 * 
	 * @return int
	 * 
	 * @since 1.1
	 */
	public function convertedImages(): int
	{
		$images = 0;
		$directory = wp_upload_dir()['basedir'];
		$iterator = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($directory));

		foreach($iterator as $file) {
			if($file->isDir()) continue;

			$ext = $file->getExtension();
			$path = $file->getPath();

			if($ext === 'jpeg' && file_exists($path . '/' . $file->getBasename('.jpeg') . '.webp')) {
				$images++;
			}

			if($ext === 'jpeg' && file_exists($path . '/' . $file->getBasename('.jpeg') . '_ss.webp')) {
				$images++;
			}

			if($ext === 'jpg' && file_exists($path . '/' . $file->getBasename('.jpg') . '.webp')) {
				$images++;
			}

			if($ext === 'jpg' && file_exists($path . '/' . $file->getBasename('.jpg') . '_ss.webp')) {
				$images++;
			}

			if($ext === 'png' && file_exists($path . '/' . $file->getBasename('.png') . '.webp')) {
				$images++;
			}

			if($ext === 'png' && file_exists($path . '/' . $file->getBasename('.png') . '_ss.webp')) {
				$images++;
			}

		}

		return $images;
	}

	/**
	 * A hook used whenever an image is uploaded and its 
	 * post-processing is done. We then convert up to 100 images,
	 * or try to at least, tho' it is unlikely one uploads more than 100
	 * images at a time. Right? I hope so.
	 * 
	 * Anyway if this fails to do the job one can always head 
	 * to the settings of the plugin and convert there.
	 *
	 * @param $metadata
	 * @param $attachment_id
	 * 
	 * @return array|bool
	 * 
	 * @since 1.1
	 */
	public function convertOnUpload($metadata, $attachment_id)
	{
		$this->convert(100);

		return $metadata;
	}

	/**
	 * Traverses the uploads directory where
	 * it will attempt to convert any JPG, JPEG and
	 * PNG files it finds, that have not already
	 * been converted.
	 *
	 * @param int $limit
	 *
	 * @since 1.1
	 */
	public function convert(int $limit = 5): void
	{
		$directory = wp_upload_dir()['basedir'];
		$iterator = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($directory));
		$converted = 0;

		foreach($iterator as $file) {
			if($file->isDir()) continue;

			$path = $file->getPath();
			$path_name = $file->getPathname();
			$ext = $file->getExtension();

			if($converted < $limit) {
				// Convert JPEGs
				if($ext === 'jpeg' && !file_exists($path . '/' . $file->getBasename('.jpeg') . '_ss.webp')) {
					$this->convertJPEG($path, $path_name, $file->getBasename('.jpeg'));
					$converted++;
				}

				// Convert JPGs
				if($ext === 'jpg' && !file_exists($path . '/' . $file->getBasename('.jpg') . '_ss.webp')) {
					$this->convertJPEG($path, $path_name, $file->getBasename('.jpg'));
					$converted++;
				}

				// Convert PNGs
				if($ext === 'png' && !file_exists($path . '/' . $file->getBasename('.png') . '_ss.webp')) {
					$this->convertPNG($path, $path_name, $file->getBasename('.png'));
					$converted++;
				}
			}
		}
	}

	/**
	 * Converts a JPG or JPEG image into a
	 * WebP image, provided that Imagic is installed.
	 *
	 * @param string $path
	 * @param string $path_name
	 * @param string $base_name
	 *
	 * @since 1.1
	 */
	public function convertJPEG(string $path, string $path_name, string $base_name): void
	{
		if(class_exists('\Imagick')) {
			try {
				$image = new \Imagick();
				$image->readImage( $path_name );
				$image->setImageFormat( 'webp' );
				$image->setImageCompressionQuality( 80 );
				$image->writeImage( $path . '/' . $base_name . '_ss.webp' );
			} catch(\ImagickException $e) {
				// Something went wrong.
			}
		} elseif(extension_loaded('gd')) {
			$image = imagecreatefromjpeg($path_name);
			imagewebp($image, $path . '/' . $base_name . '_ss.webp');
			imagedestroy($image);
		}
	}

	/**
	 * Converts a PNG image into a
	 * WebP image, provided that Imagic is installed.
	 *
	 * @param string $path
	 * @param string $path_name
	 * @param string $base_name
	 *
	 * @since 1.1
	 */
	public function convertPNG(string $path, string $path_name, string $base_name): void
	{
		if(class_exists('\Imagick')) {
			try {
				$image = new \Imagick();
				$image->readImage($path_name);
				$image->setImageFormat('webp');
				$image->setImageCompressionQuality(80);
				$image->setOption('webp:lossless', 'true');
				$image->writeImage($path . '/' . $base_name . '_ss.webp');
			} catch(\ImagickException $e) {
				// Something went wrong.
			}
		} elseif(extension_loaded('gd')) {
			$image = imagecreatefrompng($path_name);
			imagepalettetotruecolor($image);
			imagealphablending($image, true);
			imagesavealpha($image, true);
			imagewebp($image, $path . '/' . $base_name . '_ss.webp');
			imagedestroy($image);
		}
	}
}