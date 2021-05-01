<?php

namespace SplendidSpeed;

class SplendidSpeed 
{
	/**
	 * Calls register_services once plugins have been loaded.
	 * 
	 * @since 1.0
	 */
	public function init(): void
    {
		add_action('plugins_loaded', [$this, 'register_services']);
	}

	/**
	 * Registers all actions and filters needed for the plugin to work.
	 * 
	 * @since 1.0
	 */
	public function register_services(): void
    {
		/**
		 * Update settings on POST.
		 * 
		 * @since 1.0
		 */
		add_action('admin_init', function() {
			if($this->post('splendid_form') && $this->get('page') === 'splendid-speed') {
				$this->update_settings();
			}
		});

		/**
		 * Create the admin page.
		 *
		 * @since 1.0
		 */
		add_action('admin_menu', function() {
			add_options_page(
				esc_html__( 'Splendid Speed', 'splendid-speed' ),
				esc_html__( 'Splendid Speed', 'splendid-speed' ),
				'manage_options',
				'splendid-speed',
				[$this, 'admin_page']
			);
		});

		/**
		 * Add our admin css and scripts.
		 *
		 * @since 1.0
		 */
		add_action('admin_enqueue_scripts', function($hook) {
		   if($hook === 'settings_page_splendid-speed') {
			   wp_enqueue_style('splendid-speed', SPLENDID_SPEED_DIR_URL . 'assets/css/admin.css', [], '1.3.5');
			   wp_enqueue_script('splendid-speed-admin-page', SPLENDID_SPEED_DIR_URL . 'assets/js/admin.js', [], '1.3.5', true);
		   }
		});

		/**
		 * Remove the footer text for extra cleanliness.
		 *
		 * @since 1.0
		 */
		add_filter('admin_footer_text', function() {
			$screen = get_current_screen();

			if($screen->id === 'settings_page_splendid-speed') {
				return '<p class="sp-footnote">A very <a class="sp-logo-mark" href="https://nomm.xyz" target="_blank"></a> plugin</p>';
			}
		});

		/**
		 * Register the deactivation hook.
		 * 
		 * @since 1.0
		 */
		register_deactivation_hook(SPLENDID_SPEED_FILE, [$this, 'erase']);

		/**
		 * Add filter for creating a "Settings" link in the plugins page
		 *
		 * @since 1.0
		 */
		add_filter('plugin_action_links_' . SPLENDID_SPEED_BASENAME, [$this, 'plugins_page_link']);

		/**
		 * Register all modules
		 * 
		 * @since 1.1
		 */
		foreach(glob(SPLENDID_SPEED_DIR . '/modules/*.php') as $module) {
			require_once $module;
			$className = '\SplendidSpeed\Modules\\' . str_replace('.php', '', basename($module));
			$classInstance = new $className();
			call_user_func([$classInstance, 'register']);
		}
	}

	/**
	 * Checks for an item in $_POST.
	 *
	 * @param string $key
	 *
	 * @return string|bool
	 *
	 * @since 1.0
	 */
	public function post(string $key)
    {
		if(!empty($_POST[$key])) {
			return $_POST[$key];
		}

		return false;
	}

	/**
	 * checks for an item in $_GET.
	 *
	 * @param string $key
	 *
	 * @return string|bool
	 *
	 * @since 1.0
	 */
	public function get(string $key)
    {
		if(!empty($_GET[$key])) {
			return sanitize_key($_GET[$key]);
		}

		return false;
	}

	/**
	 * Returns all of the settings.
	 *
	 * @return array
	 *
	 * @since 1.0
	 */
	public function settings(): array
    {
		return get_option('splendid_speed_settings', []);
	}

	/**
	 * Returns a specific item from `$this->settings()`.
	 *
	 * @param string $key
	 *
	 * @return false|mixed
	 *
	 * @since 1.0
	 */
	public function setting(string $key)
    {
		$settings = $this->settings();

		if(isset($settings[$key])) {
			return $settings[$key];
		}

		return false;
	}

	/**
	 * Updates the settings by given key => value collection.
	 *
	 * @since 1.0
	 */
	public function update_settings(): void
    {
		$settings = $this->post('splendid');

		foreach(glob(SPLENDID_SPEED_DIR . '/modules/*.php') as $module) {
			$className = '\SplendidSpeed\Modules\\' . str_replace('.php', '', basename($module));
			$classInstance = new $className();

			if(is_array($settings)) {
				if(array_key_exists($classInstance->key, $settings)) {
					call_user_func([$classInstance, 'activate']);
				} else {
					call_user_func([$classInstance, 'disable']);
				}
			} else {
				call_user_func([$classInstance, 'disable']);
			}
		}
	}

	/**
	 * Deactivation cleaning. Because causing more
	 * bloat to your website is the last thing I want.
	 *
	 * @since 1.0
	 */
	public function erase(): void
    {
		foreach(glob(SPLENDID_SPEED_DIR . '/modules/*.php') as $module) {
			$className = '\SplendidSpeed\Modules\\' . str_replace('.php', '', basename($module));
			$classInstance = new $className();
			call_user_func([$classInstance, 'disable']);
		}

		// And finally, delete all options.
		delete_option('splendid_speed_settings');
	}

	/**
	 * Adds a "Settings" link to the plugin screen for Splendid's
	 * plugin, that way the user can more easily find the settings
	 * once the plugin is activated.
	 * 
	 * @since 1.0
	 */
	public function plugins_page_link(array $links): array
    {
		$url = get_admin_url() . "options-general.php?page=splendid-speed";
		$settings_link = '<a href="' . $url . '">' . __('Settings', 'splendid-speed') . '</a>';
		array_unshift($links, $settings_link);

		return $links;
	}

	/**
	 * Renders the admin page.
	 * 
	 * @since 1.0
	 */
	public function admin_page() { ?>
		<div class="sp-header">
			<div class="sp-logo"></div>
		</div>
		<div class="sp-body">	
			<form method="post" id="splendid-speed">
				<input type="hidden" name="splendid_form" value="1">
				<?php
				foreach(glob(SPLENDID_SPEED_DIR . '/modules/*.php') as $module):
				$className = '\SplendidSpeed\Modules\\' . str_replace('.php', '', basename($module));
				$classInstance = new $className();
				?>
				<div class="sp-option <?php if($this->setting($classInstance->key)): ?>sp-option-active<?php endif; ?>">
					<div class="sp-option-heading">
						<h3><?php _e($classInstance->title, 'splendid-speed'); ?>
							<span class="sp-option-heading-description">
								<span class="dashicons dashicons-info-outline"></span>
								<div class="sp-tooltip"><?php _e($classInstance->description, 'splendid-speed'); ?></div>
							</span>

							<?php 
							if(property_exists($classInstance, 'option_heading_html')): 
								echo $classInstance->option_heading_html; 
							endif; 
							?>
						</h3>
					</div>
					<div class="sp-option-label">
						<?php if(!isset($classInstance->supported) || $classInstance->supported === true): ?>
						<label class="switch">
							<input type="checkbox" name="splendid[<?php echo $classInstance->key; ?>]" <?php if($this->setting($classInstance->key)): ?>checked<?php endif; ?>>
							<span class="slider"></span>
							<p><?php _e($classInstance->label, 'splendid-speed'); ?></p>
						</label>
						<?php else: echo $classInstance->not_supported_message; endif; ?>
					</div>
				</div>
				<?php endforeach; ?>
			</form>
		</div>
	<?php
	}
}