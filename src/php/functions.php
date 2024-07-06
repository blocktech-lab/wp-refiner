<?php if (!defined('ABSPATH')) { exit; }

function wprfnr_getDefaultOptions() {
    $wprfnrDefaults = array(

        // general tab
        'prioritise' => 'no',
        'adminbar_logo' => 'yes',
        'thank_you' => 'yes',
        'thank_you_string' => '',
        'footer_version' => 'yes',
        'footer_version_string' => '',
        'login_logo' => 'wp_logo',
        'wordpress-tab-suffix' => 'yes',
        'dashboard_news' => 'yes',
        'elementor_overview' => 'yes',
        'smileys' => 'yes',
        'rss' => 'yes',
        'comments' => 'yes',

        // email tab
        'email_from' => '',
        'email_username' => '',

        // advanced tab
        'css' => 'yes',
        'head' => 'yes',
        'wp_embed' => 'yes',
        'block_library' => 'yes',

        // bonus tab
        'svg' => 'no',
        'centerLogin' => 'no',
        'restAPI' => 'yes',
        'jquery' => 'yes',

        // state of banners
        'installDate' => false,
        'installBanner' => false,
        'usedNotice' => false,
    );

    // add wprfnr_ prefix to all keys
    $wprfnrDefaults = array_combine(
        array_map(function($k) { return 'wprfnr_' . $k; },
        array_keys($wprfnrDefaults)), $wprfnrDefaults
    );

    return $wprfnrDefaults;
}

function wprfnr_nw() { return is_network_admin(); }

function wprfnr_updateOption($key, $value) {
    if (wprfnr_nw()) { return update_site_option($key, $value);
    } else { return update_option($key, $value); }
}

function wprfnr_getOption($key) {
    if (wprfnr_nw()) { return get_site_option($key);
    } else { return get_option($key); }
}

function wprfnr_is_login_form() {
    $ABSPATH_MY = str_replace(array('\\','/'), DIRECTORY_SEPARATOR, ABSPATH);
    return ((in_array($ABSPATH_MY.'wp-login.php', get_included_files()) || in_array($ABSPATH_MY.'wp-register.php', get_included_files())) || (isset($_GLOBALS['pagenow']) && $GLOBALS['pagenow'] === 'wp-login.php') || $_SERVER['PHP_SELF']== '/wp-login.php');
}

function wprfnr_tabsUrl() {
    $wprfnr_tabsUrl = '.php?page=wp-refiner';
    if (wprfnr_nw()) { return network_admin_url('settings' . $wprfnr_tabsUrl);
    } else { return admin_url('admin' . $wprfnr_tabsUrl); }
}
function wprfnr_getCurrentTab() {
    $action = isset($_GET['action']) ? sanitize_text_field($_GET['action']) : 'general';

    // validation
    if (!in_array($action, array('general', 'email', 'advanced', 'bonus'))) {
       $action = 'general';
    }

    return esc_html($action);
}

function wprfnr_checkOption($key, $string = false) {
    $key = 'wprfnr_' . $key;

    // checks if per network option has priority
    if (is_multisite() && get_option('wprfnr_prioritise') == 'no') {
        if (!$string) return get_site_option($key) == 'yes' ? true : false;
        return get_site_option($key); // when string output (select)
    } 
    
    if (!$string) return get_option($key) == 'yes' ? true : false; // else just returns normal option
    return get_option($key); // when string output
}