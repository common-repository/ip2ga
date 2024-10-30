<?php
/*
Plugin Name: IP2GA
Description: Track all user activities on the site, including page views, button clicks, and form submissions, and send them to Google Analytics 4.
Version: 1.6.3
Author: Wiredminds GmbH
Author URI: https://wiredminds.de
License: GPL2
*/

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}


// Function to generate Google Analytics client ID based on the generate combined data string from visitor metadata
function ip2ga_generate_ga_client_id() {
    // Generate combined data string from visitor metadata
    $combined_data = '';

    if (isset($_SERVER['HTTP_USER_AGENT'])) {
        $combined_data .= sanitize_text_field(wp_unslash($_SERVER['HTTP_USER_AGENT'])) . '|';   
    } else {
        $combined_data .= 'unknown|';
    }

    // Add Accept-Language information
    $combined_data .= key(ip2ga_parse_accept_language()) . '|';

    // Add Visitor IP information
    $combined_data .= ip2ga_get_visitor_ip();

    $hash = md5($combined_data );

    $first_part = hexdec(substr($hash, 0, 8));
    $second_part = hexdec(substr($hash, 8, 8));

    return $first_part . '.' . $second_part;
}

// Function to get client_id
function ip2ga_get_client_id() {
    if (isset($_COOKIE['_ga'])) {
        // Cookie format: GA1.2.XXXXXXXXX.YYYYYYYYYY
        $ga_cookie = sanitize_text_field(wp_unslash($_COOKIE['_ga']));
        $parts = explode('.', $ga_cookie);
        if (count($parts) >= 4) {
            $client_id = $parts[2] . '.' . $parts[3];
            return [$client_id, true]; // Cookie exists true
        }
    }
    // If there is no cookie or the format is incorrect, we use the generated client_id
    return [ip2ga_generate_ga_client_id(), false];  // Cookie not exists false
}

// Function to parse the Accept-Language header and return languages sorted by priority
function ip2ga_parse_accept_language($accept_language = null) {
    $accept_language = isset($_SERVER['HTTP_ACCEPT_LANGUAGE']) ? sanitize_text_field(wp_unslash($_SERVER['HTTP_ACCEPT_LANGUAGE'])) : '';
    $languages = array_filter(array_map(function ($lang) {
        $parts = explode(';q=', $lang);
        $lang_code = trim($parts[0]);
        $priority = isset($parts[1]) ? max(0, min(1, (float)$parts[1])) : 1.0;

        return [$lang_code => $priority];
    }, explode(',', $accept_language)));

    $languages = array_merge(...$languages);
    arsort($languages);

    return $languages;
}

// Function to get the visitor's IP address
function ip2ga_get_visitor_ip() {
    $ip = '';

    if (!empty($_SERVER['HTTP_CF_CONNECTING_IP'])) {
        $ip = isset($_SERVER['HTTP_CF_CONNECTING_IP']) ? sanitize_text_field(wp_unslash($_SERVER['HTTP_CF_CONNECTING_IP'])) : '';
    }

    if (empty($ip) && !empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
        $ip_array = isset($_SERVER['HTTP_X_FORWARDED_FOR']) ? explode(',', sanitize_text_field(wp_unslash($_SERVER['HTTP_X_FORWARDED_FOR']))) : [];
        foreach ($ip_array as $ip_candidate) {
            $ip_candidate = trim($ip_candidate);
            if (filter_var($ip_candidate, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE)) {
                $ip = $ip_candidate;
                break;
            }
        }
    }

    if (empty($ip) && !empty($_SERVER['HTTP_CLIENT_IP'])) {
        $ip = isset($_SERVER['HTTP_CLIENT_IP']) ? sanitize_text_field(wp_unslash($_SERVER['HTTP_CLIENT_IP'])) : '';
        if (!filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE)) {
            $ip = '';
        }
    }

    if (empty($ip)) {
        $ip = isset($_SERVER['REMOTE_ADDR']) ? sanitize_text_field(wp_unslash($_SERVER['REMOTE_ADDR'])) : '';
        if (!filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE)) {
            $ip = '';
        }
    }

    return sanitize_text_field($ip);
}

// Function to fetch data from the external API based on the IP address
function ip2ga_fetch_data_from_api($ip, $token) {
    $cache_key = 'ga_ip2c_' . md5($ip);
    $cached_data = get_transient($cache_key);

    if ($cached_data !== false) {
        return $cached_data;
    }

    $api_url = "https://ip2c.wiredminds.com/$token/$ip";
    $args = ['timeout' => 2];
    $response = wp_remote_get($api_url, $args);

    if (is_wp_error($response)) {
        return false;
    }

    $body = wp_remote_retrieve_body($response);
    $data = json_decode($body, true);

    set_transient($cache_key, $data, HOUR_IN_SECONDS);

    return $data;
}

// Function to fetch data from the external RapidAPI based on the IP address
function ip2ga_fetch_data_from_rapid_api($ip, $token) {
    $cache_key = 'ga_ip2c_' . md5($ip);
    $cached_data = get_transient($cache_key);

    if ($cached_data !== false) {
        return $cached_data;
    }

    $api_url = "https://ip2company3.p.rapidapi.com/$ip";
    $headers = [
        'X-Rapidapi-Key' => $token,
        'X-Rapidapi-Host' => 'ip2company3.p.rapidapi.com',
        'Host' => 'ip2company3.p.rapidapi.com'
    ];
    $args = ['timeout' => 2, 'headers' => $headers];
    $response = wp_remote_get($api_url, $args);

    if (is_wp_error($response)) {
        return false;
    }

    $body = wp_remote_retrieve_body($response);
    $data = json_decode($body, true);

    set_transient($cache_key, $data, HOUR_IN_SECONDS);

    return $data;
}

// Function to send data to Google Analytics 4
function ip2ga_send_data_to_ga($data, $event_data) {

    // If company_name is empty, do not send data
    if (empty($data['name'])) {
        return;
    }

    $tracking_id = get_option('ga_ip2c_ga_id');
    $api_secret = get_option('ga_ip2c_api_secret');
    [$client_id, $cookie_exists] = ip2ga_get_client_id();

    $payload = [
        'client_id' => $client_id,
    ];

    $payload['user_properties'] = [
        'company_name' => ['value' => sanitize_text_field($data['name'] ?? '')],
        'company_city' => ['value' => sanitize_text_field($data['city'] ?? '')],
        'company_country_code' => ['value' => sanitize_text_field($data['country_code'] ?? '')],
        'company_industry' => ['value' => sanitize_text_field($data['branch'] ?? '')],
        'company_industry_code' => ['value' => sanitize_text_field($data['branch_code'] ?? '')],
        'company_revenue' => ['value' => sanitize_text_field($data['revenue'] ?? '')],
        'company_revenue_class' => ['value' => sanitize_text_field($data['revenue_class'] ?? '')],
        'company_employee_size' => ['value' => sanitize_text_field($data['size'] ?? '')],
        'company_employee_class' => ['value' => sanitize_text_field($data['size_class'] ?? '')],
        'company_zip' => ['value' => sanitize_text_field($data['zip'] ?? '')],
        'company_region' => ['value' => sanitize_text_field($data['region'] ?? '')],
    ];


    // If GA4 JavaScript code is NOT active, add events to payload
    if (!$cookie_exists) {
        $payload['events'] = [
            [
                'name' => sanitize_text_field($event_data['type']),
                'params' => array_merge([
                    'page_location' => sanitize_text_field($event_data['page']),
                    'page_title' => sanitize_text_field($event_data['title']),
                    'page_host' => wp_parse_url(home_url(), PHP_URL_HOST),
                    'page_referrer' => isset($_SERVER['HTTP_REFERER']) ? sanitize_text_field(wp_unslash($_SERVER['HTTP_REFERER'])) : 'No referrer',
                    'language' => key(ip2ga_parse_accept_language()),
                    'user_agent' => isset($_SERVER['HTTP_USER_AGENT']) ? sanitize_text_field(wp_unslash($_SERVER['HTTP_USER_AGENT'])) : 'Unknown',
                    'visitor_id' => $client_id,
                ], $event_data['additional'] ?? [])
            ]
        ];
    }

    $url = 'https://www.google-analytics.com/mp/collect?measurement_id=' . $tracking_id . '&api_secret=' . $api_secret;

    $args = [
        'headers' => ['Content-Type' => 'application/json'],
        'body' => wp_json_encode($payload),
        'method' => 'POST',
        'timeout' => 2,
    ];

    $response = wp_remote_post($url, $args);

    if (is_wp_error($response)) {
        error_log('Response GA data: ' . print_r($response, true));
    }
}

// Function to process data on each page load and send to GA
function ip2ga_process_and_send_data() {
    $token = get_option('ga_ip2c_token');
    $rapid_token = get_option('ga_ip2c_rapid_token');
    $visitor_ip = ip2ga_get_visitor_ip();

    if ($token != "" && $rapid_token == "") {
        $data = ip2ga_fetch_data_from_api($visitor_ip, $token);
    } elseif ($rapid_token != "" && $token == "") {
        $data = ip2ga_fetch_data_from_rapid_api($visitor_ip, $rapid_token);
    } else {
        $data = [];
    }

    if ($data) {
        ip2ga_send_data_to_ga($data, [
            'type' => 'page_view',
            'page' => isset($_SERVER['REQUEST_URI']) ? sanitize_text_field(wp_unslash($_SERVER['REQUEST_URI'])) : '',
            'title' => wp_title('', false),
            'category' => 'Page View',
            'action' => 'view',
            'label' => wp_title('', false),
            'additional' => []
        ]);
    }
}

// Enqueue the script for event tracking with versioning
function ip2ga_enqueue_event_tracking_script() {
    $enable_ajax_tracking = get_option('ga_ip2c_enable_ajax_tracking');

    if ($enable_ajax_tracking) {
        wp_enqueue_script('ga-ip2c-event-tracking', plugins_url('/ip2ga-event-tracking.js', __FILE__), array('jquery'), '1.6.3', true);
        wp_localize_script('ga-ip2c-event-tracking', 'ga_ip2c_ajax', [
            'ajax_url' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('ga_ip2c_nonce')
        ]);
    }
}
add_action('wp_enqueue_scripts', 'ip2ga_enqueue_event_tracking_script');

// AJAX handler for event tracking
function ip2ga_handle_event_tracking_ajax() {
    $enable_ajax_tracking = get_option('ga_ip2c_enable_ajax_tracking');

    if (!$enable_ajax_tracking) {
        wp_send_json_error(['message' => 'AJAX tracking is disabled'], 403);
    }

    check_ajax_referer('ga_ip2c_nonce', 'security');

    $event_type = isset($_POST['type']) ? sanitize_text_field(wp_unslash($_POST['type'])) : '';
    $page = isset($_POST['page']) ? sanitize_text_field(wp_unslash($_POST['page'])) : '';
    $title = isset($_POST['title']) ? sanitize_text_field(wp_unslash($_POST['title'])) : '';
    $category = isset($_POST['category']) ? sanitize_text_field(wp_unslash($_POST['category'])) : '';
    $action = isset($_POST['action']) ? sanitize_text_field(wp_unslash($_POST['action'])) : '';
    $label = isset($_POST['label']) ? sanitize_text_field(wp_unslash($_POST['label'])) : '';
    $additional = isset($_POST['additional']) ? array_map('sanitize_text_field', wp_unslash($_POST['additional'])) : [];

    if (!$event_type || !$page || !$title || !$category || !$action || !$label) {
        wp_send_json_error(['message' => 'Missing required parameters'], 404);
    }

    $token = get_option('ga_ip2c_token');
    $rapid_token = get_option('ga_ip2c_rapid_token');
    $visitor_ip = ip2ga_get_visitor_ip();

    if ($token != "" && $rapid_token == "" ) {
        $data = ip2ga_fetch_data_from_api($visitor_ip, $token);
    } elseif ($rapid_token != "" && $token == "") {
        $data = ip2ga_fetch_data_from_rapid_api($visitor_ip, $rapid_token);
    } else {
        $data = [];
    }

    if ($data) {
        ip2ga_send_data_to_ga($data, [
            'type' => $event_type,
            'page' => $page,
            'title' => $title,
            'category' => $category,
            'action' => $action,
            'label' => $label,
            'additional' => $additional
        ]);
        wp_send_json_success();
    } else {
        wp_send_json_error(['message' => 'Failed to fetch company data'], 500);
    }

    wp_die();
}

function ip2ga_activate() {
    add_option('ga_ip2c_enable_ajax_tracking', 0);
    add_option('ga_ip2c_enable_ga_tracking', 0);
}

register_activation_hook(__FILE__, 'ip2ga_activate');

function ip2ga_insert_ga_tracking_code() {
    $enable_ga_tracking = get_option('ga_ip2c_enable_ga_tracking');
    $ga_measurement_id = get_option('ga_ip2c_ga_id');

    if ($enable_ga_tracking && !empty($ga_measurement_id) && isset($_COOKIE['ip2ga_consent']) && $_COOKIE['ip2ga_consent'] === 'accepted') {
        echo "<!-- Google tag (gtag.js) -->
        <script async src=\"https://www.googletagmanager.com/gtag/js?id={$ga_measurement_id}\"></script>
        <script>
          window.dataLayer = window.dataLayer || [];
          function gtag(){dataLayer.push(arguments);}
          gtag('js', new Date());
          gtag('config', '{$ga_measurement_id}');
        </script>";
    }
}

// Function to display the simple consent banner
function ip2ga_simple_consent_banner() {
    $enable_ga_tracking = get_option('ga_ip2c_enable_ga_tracking');
    if ($enable_ga_tracking && !isset($_COOKIE['ip2ga_consent'])) {
        // Get the message text and privacy policy link from settings
        $consent_message = get_option('ga_ip2c_consent_message', 'We use cookies to enhance your experience. By continuing to use the site, you agree to our {privacy_policy_link}.');
        $privacy_policy_url = get_option('ga_ip2c_privacy_policy_url', '/privacy-policy');

        // Sanitize values before output
        $consent_message = wp_kses_post($consent_message);
        $privacy_policy_url = esc_url($privacy_policy_url);

        // Replace {privacy_policy_link} with the actual link
        $consent_message_formatted = str_replace(
            '{privacy_policy_link}',
            '<a href="' . $privacy_policy_url . '" target="_blank">privacy policy</a>',
            $consent_message
        );
        ?>
        <div id="ip2ga-consent-banner" style="position: fixed; bottom: 0; left: 0; right: 0; background-color: #f5f5f5; padding: 10px; text-align: center; z-index: 1000;">
            <p style="display: inline-block; margin: 0;">
                <?php echo $consent_message_formatted; ?>
            </p>
            <button id="ip2ga-accept-btn" style="margin-left: 10px; padding: 5px 10px; background-color: #0073aa; color: #fff; border: none; cursor: pointer;">Accept</button>
            <button id="ip2ga-decline-btn" style="margin-left: 5px; padding: 5px 10px; background-color: #aaa; color: #fff; border: none; cursor: pointer;">Decline</button>
        </div>
        <script>
            document.getElementById('ip2ga-accept-btn').addEventListener('click', function() {
                // Set the consent cookie to 'accepted' for 180 days
                var expiryDate = new Date();
                expiryDate.setTime(expiryDate.getTime() + (180 * 24 * 60 * 60 * 1000)); // 180 days
                document.cookie = "ip2ga_consent=accepted; expires=" + expiryDate.toUTCString() + "; path=/";
                document.getElementById('ip2ga-consent-banner').style.display = 'none';
                window.location.reload(); // Reload the page to activate GA4
            });

            document.getElementById('ip2ga-decline-btn').addEventListener('click', function() {
                // Set the consent cookie to 'declined' with a long expiration date
                var expiryDate = new Date();
                expiryDate.setTime(expiryDate.getTime() + (10 * 365 * 24 * 60 * 60 * 1000)); // 10 years
                document.cookie = "ip2ga_consent=declined; expires=" + expiryDate.toUTCString() + "; path=/";
                document.getElementById('ip2ga-consent-banner').style.display = 'none';
            });
        </script>
        <?php
    }
}
add_action('wp_footer', 'ip2ga_simple_consent_banner');

add_action('wp_footer', 'ip2ga_insert_ga_tracking_code');

add_action('wp_ajax_ga_ip2c_event', 'ip2ga_handle_event_tracking_ajax');
add_action('wp_ajax_nopriv_ga_ip2c_event', 'ip2ga_handle_event_tracking_ajax');
add_action('wp_head', 'ip2ga_process_and_send_data');

// Add admin settings page

// Enqueue admin scripts
function ip2ga_enqueue_admin_scripts($hook_suffix) {
    // Ensure the script is only loaded on the plugin's settings page
    if ($hook_suffix == 'settings_page_ga_ip2c_settings') {
        wp_enqueue_script('ga-ip2c-admin-script', plugins_url('/admin-script.js', __FILE__), array('jquery'), '1.6.3', true);
    }
}
add_action('admin_enqueue_scripts', 'ip2ga_enqueue_admin_scripts');

// Add admin menu for the settings page
function ip2ga_add_admin_menu() {
    add_options_page(
        'IP2GA Settings',
        'IP2GA Settings',
        'manage_options',
        'ga_ip2c_settings',
        'ip2ga_settings_page'
    );
}
add_action('admin_menu', 'ip2ga_add_admin_menu');

// Register plugin settings
function ip2ga_register_settings() {
    register_setting('ga_ip2c_settings_group', 'ga_ip2c_token');
    register_setting('ga_ip2c_settings_group', 'ga_ip2c_rapid_token');
    register_setting('ga_ip2c_settings_group', 'ga_ip2c_ga_id');
    register_setting('ga_ip2c_settings_group', 'ga_ip2c_api_secret');
    register_setting('ga_ip2c_settings_group', 'ga_ip2c_enable_ajax_tracking');
    register_setting('ga_ip2c_settings_group', 'ga_ip2c_enable_ga_tracking');
    register_setting('ga_ip2c_settings_group', 'ga_ip2c_consent_message');
    register_setting('ga_ip2c_settings_group', 'ga_ip2c_privacy_policy_url');
}
add_action('admin_init', 'ip2ga_register_settings');

// Display the settings page
function ip2ga_settings_page() {
    ?>
<div class="wrap">
    <h1>IP2GA Integration Settings</h1>
    <h2 class="nav-tab-wrapper">
        <a href="#general-settings" class="nav-tab nav-tab-active">General Settings</a>
        <a href="#tracking-settings" class="nav-tab">Tracking Settings</a>
        <a href="#consent-settings" class="nav-tab" style="display: none;">Consent Settings</a>
    </h2>
    <form method="post" action="options.php">
        <?php settings_fields('ga_ip2c_settings_group'); ?>
        <?php do_settings_sections('ga_ip2c_settings_group'); ?>
        
        <!-- General Settings Tab -->
        <div id="general-settings" class="tab-content" style="display: block;">
            <table class="form-table">
                <tr valign="top">
                    <th scope="row"><span class="dashicons dashicons-rest-api"> </span> RapidAPI Token</th>
                    <td>
                      <input id="rapidapi_token" type="text" name="ga_ip2c_rapid_token" value="<?php echo esc_attr(get_option('ga_ip2c_rapid_token')); ?>" />
                      <p>Enter your <a href="https://rapidapi.com/wiredminds-gmbh-wiredminds-gmbh-default/api/ip2company3" target="_blank"><b>RapidAPI Token</b></a>. If you fill this field, leave the <b>WiredMinds API Token</b> field empty.</p>
                    </td>
                </tr>
                <tr valign="top">
                    <th scope="row"><span class="dashicons dashicons-rest-api"></span> WiredMinds API Token</th>
                    <td>
                      <input id="ip2c_token" type="text" name="ga_ip2c_token" value="<?php echo esc_attr(get_option('ga_ip2c_token')); ?>" />
                      <p>Enter the API Token provided by WiredMinds GmbH. If you fill this field, leave the <b>RapidAPI Token</b> field empty.</p>
                    </td>
                </tr>
                <tr valign="top">
                    <th scope="row"><span class="dashicons dashicons-google"></span> Google Analytics ID</th>
                    <td>
                      <input type="text" name="ga_ip2c_ga_id" value="<?php echo esc_attr(get_option('ga_ip2c_ga_id')); ?>" />
                      <p>Enter your Google Analytics Measurement ID (e.g., <b>G-XXXXXXXXXX</b>) used for tracking events.</p>
                    </td>
                </tr>
                <tr valign="top">
                    <th scope="row"><span class="dashicons dashicons-shield"></span> Google Analytics API Secret</th>
                    <td>
                      <input type="text" name="ga_ip2c_api_secret" value="<?php echo esc_attr(get_option('ga_ip2c_api_secret')); ?>" />
                      <p>Enter the API Secret you created in the <b>"Measurement Protocol"</b> section of Google Analytics 4.</p>
                    </td>
                </tr>
            </table>
        </div>
        
        <!-- Tracking Settings Tab -->
        <div id="tracking-settings" class="tab-content" style="display: none;">
            <table class="form-table">
                <tr valign="top">
                    <th scope="row"><span class="dashicons dashicons-welcome-view-site"></span> Enable AJAX Event Tracking</th>
                    <td>
                      <input type="checkbox" name="ga_ip2c_enable_ajax_tracking" value="1" <?php checked(1, get_option('ga_ip2c_enable_ajax_tracking'), true); ?> />
                      <p>Enable JavaScript to track GA4 events if a visitor rejects cookies.</p>
                    </td>
                </tr>
                <tr valign="top">
                    <th scope="row"><span class="dashicons dashicons-visibility"></span> Enable GA4 Tracking Code</th>
                    <td>
                      <input id="enable_ga_tracking" type="checkbox" name="ga_ip2c_enable_ga_tracking" value="1" <?php checked(1, get_option('ga_ip2c_enable_ga_tracking'), true); ?> />
                      <p>Enable GA4 tracking only if you're not using other methods to add Google Analytics or Tag Manager code.</p>
                    </td>
                </tr>
            </table>
        </div>

        <!-- Consent Settings Tab -->
        <div id="consent-settings" class="tab-content" style="display: none;">
            <table class="form-table">
                <tr valign="top">
                    <th scope="row"><span class="dashicons dashicons-format-aside"></span> Consent Banner Text</th>
                    <td>
                        <textarea name="ga_ip2c_consent_message" rows="3" cols="50"><?php echo esc_textarea(get_option('ga_ip2c_consent_message', 'We use cookies to enhance your experience. By continuing to use the site, you agree to our {privacy_policy_link}.')); ?></textarea>
                        <p>Enter the text that will be displayed in the consent banner. Use <code>{privacy_policy_link}</code> where you want the privacy policy link to appear.</p>
                    </td>
                </tr>
                <tr valign="top">
                    <th scope="row"><span class="dashicons dashicons-admin-links"></span> Privacy Policy URL</th>
                    <td>
                        <input type="text" name="ga_ip2c_privacy_policy_url" value="<?php echo esc_attr(get_option('ga_ip2c_privacy_policy_url', '/privacy-policy')); ?>" size="50" />
                        <p>Enter the URL of your privacy policy page (e.g., <code>/privacy-policy</code>).</p>
                    </td>
                </tr>
            </table>
        </div>

            <?php submit_button(); ?>
        </form>
        <?php
        // Loading the help file
        $file_path = plugin_dir_path(__FILE__) . 'help/info.php';
        if (file_exists($file_path)) {
            include $file_path;
        } else {
            echo '<p>Help file not found.</p>';
        }
        ?>
    </div>
    <?php
}
?>