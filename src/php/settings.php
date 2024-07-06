<?php if (!defined('ABSPATH')) { exit; }

class wprfnrOptions {

	// for each checkbox
	public function printCheckbox($key, $title, $text = false) {
		$isChecked = checked('yes', wprfnr_getOption('wprfnr_' . $key), false); ?>

		<tr>
			<th scope="row">
				<?php echo esc_html($title);
					if ($key === 'prioritise') {
						echo "<p class='desc'>" . esc_html(__('Your site uses WordPress Multisite, which means WP Refiner options are set up ', 'wp-refiner')) . "<a href='" . esc_url(network_admin_url('settings.php?page=wp-refiner')) . "'>" . esc_html(__('on the network level', 'wp-refiner')) . "</a>" . esc_html(__('. You can prioritise this specific site\'s settings by toggling this option.', 'wp-refiner')) . "</p>";
					}
				?>
			</th>
			
			<td>
				<label class='switch'>
					<!-- hidden checkbox to return no when unchecked -->
					<input 
						name="wprfnr_<?php echo esc_html($key) ?>"
						<?php echo esc_attr($isChecked) ?>
						type="hidden" value="no"
					>
					<input id="<?php echo esc_attr($key) ?>" name="wprfnr_<?php echo esc_attr($key) ?>" <?php echo esc_attr($isChecked) ?> type="checkbox" value="yes">
					<span class='slider round span'></span>
				</label>

				<?php if ($text) { 
					$stringKey = $key . '_string';
					$value = esc_attr(wprfnr_getOption('wprfnr_' . $stringKey));
					$placeholder = esc_attr($text);

					echo "<input type='text' id='" . esc_attr($stringKey) . "' class='greyedOut' name='wprfnr_" . esc_attr($stringKey) . "' value='" . esc_attr($value) . "' placeholder='" . esc_attr($placeholder) . "' />";

				} ?>
			</td>
		</tr>
	<?php }

	public function printTextField($key, $title, $placeholder) { 
		$value = wprfnr_getOption('wprfnr_' . $key) ? wprfnr_getOption('wprfnr_' . $key) : ''; ?>

		<tr>
			<th scope="row"><?php echo esc_html($title) ?></th>
			<td>
				<input type="text" id="<?php echo esc_attr($key) ?>" name="wprfnr_<?php echo esc_attr($key) ?>" value="<?php echo esc_attr($value) ?>" placeholder="<?php echo esc_attr($placeholder) ?>" />
				<?php
					if (isset($key) && is_string($key) && $key === 'email_username') {
						echo ' @' . esc_html(sanitize_text_field($_SERVER['SERVER_NAME']));
					}
				?>
			</td>
		</tr>
	<?php }

	public function settings_dom() {
		$settingsUrl = wprfnr_nw() ? 'edit' : 'admin-post'; ?>
		
		<div class="wrap">
			<h1>
				<?php if (wprfnr_nw()) { _e('WP Refiner multisite settings', 'wp-refiner'); }
				else { _e('WP Refiner Settings', 'wp-refiner'); } ?>
			</h1>

			<?php if (wprfnr_nw()) {
				echo "<span>" . esc_html(__('These will impact your whole network of sites. If you wish to set things up specifically for a site, head to the WP Refiner settings of its dashboard.', 'wp-refiner')) . "</span>";
			} ?>
				
			<h2 class="nav-tab-wrapper">
				<a href="<?php echo esc_url(wprfnr_tabsUrl()); ?>" class="nav-tab <?php if (wprfnr_getCurrentTab() == 'general') echo 'nav-tab-active'; ?>">
					<?php esc_html_e('General', 'wp-refiner') ?>
				</a>

				<a href="<?php echo esc_url(add_query_arg(array('action' => 'email'), wprfnr_tabsUrl())); ?>" class="nav-tab <?php if (wprfnr_getCurrentTab() == 'email') echo 'nav-tab-active'; ?>">
					<?php esc_html_e('Email', 'wp-refiner') ?>
				</a> 

				<a href="<?php echo esc_url(add_query_arg(array('action' => 'advanced'), wprfnr_tabsUrl())); ?>" class="nav-tab <?php if (wprfnr_getCurrentTab() == 'advanced') echo 'nav-tab-active'; ?>">
					<?php esc_html_e('Advanced', 'wp-refiner') ?>
				</a>
			</h2>

			<form method="post" action="<?php echo wprfnr_nw() ? 'edit' : 'admin-post' ?>.php?action=wprfnrAction&tab=<?php echo esc_attr(wprfnr_getCurrentTab())?>">
				<?php wp_nonce_field('wprfnr-validate'); ?>

				<table class="form-table">
					<?php 
						if (is_multisite() && !wprfnr_nw()) {
							$this->printCheckbox('prioritise', __('Prioritise these settings', 'wp-refiner'));
						} ?>

						<?php switch(wprfnr_getCurrentTab()) {
							case 'general':
								$this->printCheckbox('adminbar_logo', __('WordPress admin bar logo', 'wp-refiner'));
								$this->printCheckbox('thank_you', __('Thank you sentence in admin footer', 'wp-refiner'), __('Your own text', 'wp-refiner'));
								$this->printCheckbox('footer_version', __('WordPress version in admin footer', 'wp-refiner'), __('Your own text', 'wp-refiner'));

								$options = wprfnr_getOption('wprfnr_login_logo') ? wprfnr_getOption('wprfnr_login_logo') : 'wp_logo' ?>
								<tr><th scope="row"><?php _e('Login logo image', 'wp-refiner'); ?></th>
									<td>
										<select name="wprfnr_login_logo">
											<option value="wp_logo" <?php selected($options, "wp_logo"); ?>>
												<?php _e('Default WordPress logo', 'wp-refiner') ?>
											</option>
								
											<option value="site_logo" <?php selected($options, "site_logo"); ?>>
												<?php _e('Site logo (if there is one)', 'wp-refiner') ?>
											</option>
								
											<option value="site_title" <?php selected($options, "site_title"); ?>>
												<?php _e('Site title', 'wp-refiner') ?>
											</option>
								
											<option value="none" <?php selected($options, "none"); ?>>
												<?php _e('Hide', 'wp-refiner') ?>
											</option>
										</select>
									</td>
								</tr>

                                <?php 
								$this->printCheckbox('wordpress-tab-suffix', __('"— WordPress" suffix in tab titles of dashboard pages', 'wp-refiner'));

								$this->printCheckbox('dashboard_news', __('"News and events" widget on dashboard', 'wp-refiner'));

								if (is_plugin_active('elementor/elementor.php')) {
									$this->printCheckbox('elementor_overview', __('"Elementor overview" widget on dashboard', 'wp-refiner'));
								}

								$this->printCheckbox('smileys', __('Integrated smileys', 'wp-refiner'));
								$this->printCheckbox('rss', __('Integrated RSS feed', 'wp-refiner'));
								$this->printCheckbox('comments', __('Comments', 'wp-refiner'));
								break;
							case 'email':
								$this->printTextField('email_from', __('"From" text of emails sent by your site', 'wp-refiner'), __('Your site\'s name', 'wp-refiner'));
								$this->printTextField('email_username', __('Username of the email adress that sends from your site', 'wp-refiner'), __('First part of email', 'wp-refiner'));
								break;
							case 'advanced':
								$this->printCheckbox('css', __('Global inline styles', 'wp-refiner'));
								$this->printCheckbox('head', __('Unnecessary code in head tag', 'wp-refiner'));
                                $this->printCheckbox('wp_embed', __('Embeds', 'wp-refiner'));
                                $this->printCheckbox('block_library', __('Block library', 'wp-refiner'));
                                $this->printCheckbox('svg', __('SVG upload', 'wp-refiner'));
                                $this->printCheckbox('centerLogin', __('Center login form vertically', 'wp-refiner'));
                                $this->printCheckbox('restAPI', __('REST API', 'wp-refiner'));
                                $this->printCheckbox('jquery', __('jQuery (if possible)', 'wp-refiner'));
								break;
						}
					?>
				</table>
				<?php submit_button(); ?>
			</form>
		</div>
	<?php }


	public function save() {
		
		check_admin_referer('wprfnr-validate'); // Nonce security check

		function wprfnr_rediUrl() {
			$tab = isset($_GET['tab']) ? sanitize_text_field($_GET['tab']) : '';

			// validation
			if (!in_array($tab, array('general', 'email', 'advanced', 'bonus'))) {
				$tab = 'general';
			}

			if (wprfnr_nw()) { return network_admin_url('settings.php?action=' . $tab);
			} else { return admin_url('options-general.php?page=wp-refiner&action=' . $tab); }
		}

		// filters post request to only get what starts with wprfnr_
		$values = array_filter($_POST, function($key) {
			return substr($key, 0, 8) === "wprfnr_";
		}, ARRAY_FILTER_USE_KEY);

        foreach ($values as $key => $value) {
			wprfnr_updateOption(
				sanitize_text_field($key),
				sanitize_text_field($value)
			);
        }

		wp_redirect(add_query_arg(array('page' => 'wp-refiner', 'updated' => true),
			wprfnr_rediUrl()
		)); exit;
	}

	// init stuff
	public function __construct() {
		// regular site
		add_action("admin_menu", function() {
			add_options_page(
				'WP Refiner',
				'WP Refiner',
				'manage_options',
				'wp-refiner',
				array($this, 'settings_dom')
			);
		});

		// network
		add_action("network_admin_menu", function() {
			add_submenu_page(
				'settings.php', // Parent element
				'WP Refiner', // Text in browser title bar
				'WP Refiner', // Text to be displayed in the menu.
				'manage_options', // Capability
				'wp-refiner', // Page slug, will be displayed in URL
				array($this, 'settings_dom') // Callback function which displays the page
			);
		});

		add_action('network_admin_edit_wprfnrAction', function() { $this->save(); });
		add_action('admin_post_wprfnrAction', function() { $this->save(); });

		add_action('network_admin_notices', function() {
			if (isset($_GET['page']) && $_GET['page'] == 'wp-refiner' && isset($_GET['updated'])) {
				echo '<div id="message" class="updated notice is-dismissible"><p>' . esc_html__('Settings updated.', 'wp-refiner') . '</p><button type="button" class="notice-dismiss"><span class="screen-reader-text">' . esc_html__('Dismiss this notice.', 'wp-refiner') . '</span></button></div>';
			}
		});
	}
}

add_action('admin_enqueue_scripts', function($hook_suffix) {
    // if not settings page
    if ($hook_suffix != 'settings_page_wp-refiner') return;

    $handle = 'wp-refiner';
    wp_register_script($handle, plugin_dir_url(__DIR__) . 'js/script.js');
    wp_enqueue_script($handle);
    wp_register_style($handle, plugin_dir_url(__DIR__) . 'css/style.css');
    wp_enqueue_style($handle);
});

if (is_admin()) $settings_page = new wprfnrOptions();
