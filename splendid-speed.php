<?php

/**
 * Plugin Name: Splendid Speed
 * Plugin URI: https://wordpress.org/plugins/splendid-speed
 * Description: Splendid Speed improves your website performance and Google Pagespeed score with converting images to WebP, preloading pages, using Gzip and more.
 * Author: Nomm
 * Author URI: https://nomm.xyz
 * Version: 1.3.5
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