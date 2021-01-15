<?php

/**
 * Enables the automatic deletion of transients
 * upon activation and once per week thereafter.
 */
class SplendidCleanTransients extends SplendidSpeed 
{
	/**
	 * A unique key used to store the setting in database.
	 */
	public $key = 'clean_transients';

	/**
	 * Title of the module.
	 */
	public $title = 'Clean transients';

	/**
	 * Label of the module.
	 */
	public $label = 'Delete all transient information periodically every week.';

	/**
	 * Description of the module.
	 */
	public $description = 'Transients are used to temporarily store cached information by plugin developers and cleaning them helps keep your database small.';

	/**
	 * Activates any module related things.
	 * 
	 * @since 1.1
	 */
	public function activate() {
		$settings = $this->settings();
		$settings[$this->key] = true;
		$this->clean();
		wp_schedule_event(time(), 'weekly', 'splendid_speed_weekly_clean_transients');
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
		wp_clear_scheduled_hook('splendid_speed_weekly_clean_transients');
		update_option('splendid_speed_settings', $settings);
	}

	/**
	 * Registers any module related things on page load.
	 * 
	 * @since 1.1
	 */
	public function register() {
		if($this->setting($this->key)) {
			add_action('splendid_speed_weekly_clean_transients', [$this, 'clean']);
		}
	}

	/**
	 * Cleans all transients, clearing up database  space.
	 *
	 * @since  1.0
	 */
	public function clean() {
		global $wpdb;

		$sql = "DELETE FROM " .  $wpdb->prefix . "options WHERE option_name LIKE ('_transient_%')";
		$wpdb->query($sql);

		$sql = "DELETE FROM " .  $wpdb->prefix . "options WHERE option_name LIKE ('_site_transient_%')";
		$wpdb->query($sql);
	}
}