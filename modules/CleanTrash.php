<?php

namespace SplendidSpeed\Modules;

use SplendidSpeed\SplendidSpeed;

/**
 * Enables the automatic deletion of trashed posts
 * upon activation and once per week thereafter.
 */
class CleanTrash extends SplendidSpeed
{
	/**
	 * A unique key used to store the setting in database.
	 */
	public $key = 'clean_trash';

	/**
	 * Title of the module.
	 */
	public $title = 'Clean trash';

	/**
	 * Label of the module.
	 */
	public $label = 'Delete all trash periodically every week.';

	/**
	 * Description of the module.
	 */
	public $description = 'This helps keep your database size small. Note however that by doing so all deleted content will disappear for good.';

	/**
	 * Activates any module related things.
	 * 
	 * @since 1.1
	 */
	public function activate(): void
	{
		$settings = $this->settings();
		$settings[$this->key] = true;
		$this->clean();
		wp_schedule_event(time(), 'weekly', 'splendid_speed_weekly_clean_trash');
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
		wp_clear_scheduled_hook('splendid_speed_weekly_clean_trash');
		update_option('splendid_speed_settings', $settings);
	}

	/**
	 * Registers any module related things on page load.
	 * 
	 * @since 1.1
	 */
	public function register(): void
	{
		if($this->setting($this->key)) {
			add_action('splendid_speed_weekly_clean_trash', [$this, 'clean']);
		}
	}

	/**
	 * Cleans all trash, clearing up database space.
	 *
	 * @since 1.1
	 */
	public function clean(): void
	{
		global $wpdb;
		
		$sql = "DELETE FROM " . $wpdb->prefix . "posts WHERE post_status = %s;";
		$wpdb->query($wpdb->prepare($sql, 'trash'));
	}
}