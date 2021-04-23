<?php

/**
 * Enables the automatic deletion of auto-draft posts
 * upon activation and once per week thereafter.
 */
class SplendidCleanAutoDrafts extends SplendidSpeed 
{
	/**
	 * A unique key used to store the setting in database.
	 */
	public $key = 'clean_auto_drafts';

	/**
	 * Title of the module.
	 */
	public $title = 'Clean auto-drafts';

	/**
	 * Label of the module.
	 */
	public $label = 'Delete all auto-drafts periodically every week.';

	/**
	 * Description of the module.
	 */
	public $description = 'This helps keep your database size small. Auto-drafts are created automatically while you are editing a post or page, and over time this can clutter your database.';

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
		wp_schedule_event(time(), 'weekly', 'splendid_speed_weekly_clean_auto_drafts');
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
		wp_clear_scheduled_hook('splendid_speed_weekly_clean_auto_drafts');
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
			add_action('splendid_speed_weekly_clean_auto_drafts', [$this, 'clean']);
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
		$wpdb->query($wpdb->prepare($sql, 'auto-draft'));
	}
}