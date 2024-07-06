<?php if (!defined('ABSPATH')) { exit; }

// adds "settings" link in plugins.php page 
add_filter('plugin_action_links_wp-refiner/wp-refiner.php', function($links) {
    // Build and escape the URL.
    $url = esc_url(add_query_arg('page', 'wp-refiner', get_admin_url() . 'admin.php'));

    // Create the link.
    $settings_link = "<a href='$url'>" . __('Settings') . '</a>';

    // Adds the link to the end of the array.
    array_push($links, $settings_link);
    return $links;
});

if (current_user_can('activate_plugins')) {
    // manages banners
    if (get_option('wprfnr_installBanner') == 'toBeTriggered') {
        add_action('admin_notices', function() { ?>
            <div class="notice notice-success is-dismissible" style="display: flex; flex-direction: row; align-items: center;">

                <p><?php _e('Thank you for installing <b>WP Refiner</b>! You can start getting rid of WordPress\' branding right away.', 'wp-refiner')?><br>
            
                <a class="button" href="<?php menu_page_url('wp-refiner')?>" style="margin-top: 8px"><?php _e('Visit settings page', 'wp-refiner') ?></a></p>
                
            </div> 
        <?php });

        update_option('wprfnr_installBanner', 'triggered');

    // change to +30 days to debug notice
    } else if (get_option('wprfnr_installDate') < strtotime('-30 days') && empty(get_option('wprfnr_usedNotice'))) {

        add_action('admin_notices', function() { ?>

            <script type="text/javascript">
                window.addEventListener('DOMContentLoaded', () => {
                    document.querySelector('#used_banner .notice-dismiss').onclick = function() {
                        document.querySelector('#used_banner').remove();

                        fetch('<?php echo esc_url(get_option('siteurl') . "/wp-admin/admin-ajax.php") ?>', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/x-www-form-urlencoded;charset=UTF-8'
                            },
                            body: 'action=used_notice'
                        }).catch(console.error)

                    };
                })
            </script>

            <div class="notice" id="used_banner" style="display: flex; flex-direction: row; align-items: center; position: relative;">
                <p style="margin-right: 10px">
                    <?php _e('You\'ve been using WP Refiner for a while now, I hope you like it!', 'wp-refiner')?><br>
                </p>

                <!-- added button manually instead of with 
                .is-dismissible otherwise onclick doesn't work -->
                <button type="button" class="notice-dismiss">
                    <span class="screen-reader-text">Ignore this notification.</span>
                </button>
            </div> 
        <?php });
    }
}
