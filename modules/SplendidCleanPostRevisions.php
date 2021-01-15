<?php

/**
 * Enables the automatic deletion of post revisions
 * upon activation and once per week thereafter.
 */
class SplendidCleanPostRevisions extends SplendidSpeed 
{
	/**
	 * A unique key used to store the setting in database.
	 */
	public $key = 'clean_post_revisions';

	/**
	 * Title of the module.
	 */
	public $title = 'Clean post revisions';

	/**
	 * Label of the module.
	 */
	public $label = 'Delete all post revisions periodically every week.';

	/**
	 * Description of the module.
	 */
	public $description = 'This helps keep your database size small. Note however that by doing so all old versions of content will disappear for good.';

	/**
	 * Activates any module related things.
	 * 
	 * @since 1.1
	 */
	public function activate() {
		$settings = $this->settings();
		$settings[$this->key] = true;
		$this->clean();
		wp_schedule_event(time(), 'weekly', 'splendid_speed_weekly_clean_post_revisions');
		update_option('splendid_speed_settings', $settings);
	}

	/**
	 * Disables any module related things.
	 * 
	 * @since 1.1
	 */
	public function disable() {
		$settings = $this->settings();
		unset($settings[$this->key]);
		wp_clear_scheduled_hook('splendid_speed_weekly_clean_post_revisions');
		update_option('splendid_speed_settings', $settings);
	}

	/**
	 * Registers any module related things on page load.
	 * 
	 * @since 1.1
	 */
	public function register() {
		if($this->setting($this->key)) {
			add_action('splendid_speed_weekly_clean_post_revisions', [$this, 'clean']);
		}
	}

	/**
	 * Cleans all post revisions, clearing up database space.
	 *
	 * @since 1.0
	 */
	public function clean() {
		global $wpdb;
		$sql = "DELETE FROM " . $wpdb->prefix . "posts WHERE post_type = %s;";
		$wpdb->query($wpdb->prepare($sql, 'revision'));
	}
}