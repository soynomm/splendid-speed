<?php

/**
 * Plugin Name: Splendid Speed
 * Plugin URI: https://flame.sh/products/splendid-speed
 * Description: Splendid Speed improves your website performance and Google Pagespeed score with converting images to WebP, preloading pages, using Gzip and more.
 * Author: Flame
 * Author URI: https://flame.sh
 * Version: 1.3.1
 * Text Domain: splendid-speed
 * Domain Path: /languages
 */

// Exit if accessed directly.
if(!defined('ABSPATH')) {
	exit;
}

// Define directory.
define('SPLENDID_SPEED_DIR', __DIR__);

// Define directory URL.
define('SPLENDID_SPEED_DIR_URL', plugin_dir_url(__FILE__));

// Define plugin basename.
define('SPLENDID_SPEED_BASENAME', plugin_basename(__FILE__));

// Define plugin file.
define('SPLENDID_SPEED_FILE', __FILE__);

// Composer deps
require __DIR__ . '/vendor/autoload.php';

// Get an instance of SplendidSpeed.
require_once __DIR__ . '/SplendidSpeed.php';

$SplendidSpeed = new SplendidSpeed();
$SplendidSpeed->init();