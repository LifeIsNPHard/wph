<?php

require_once dirname( __DIR__ ) . '/vendor/autoload.php';

use Symfony\Component\Yaml\Yaml;

$env = getenv('WP_ENV') ?: 'local';
$config_location = __DIR__ . "/config/{$env}.yaml";

if (!file_exists($config_location)) {
	die("Config file for environment '$env' not found.");
}

$config = Yaml::parseFile($config_location);

/**
 * Cache configuration
 */
define('WP_CACHE', $config['cache']['enabled']);
if ($config['cache']['enabled'] && isset($config['redis'])) {
	$redis = $config['redis'];
	define('WP_REDIS_HOST', $redis['host']);
	define('WP_REDIS_PORT', $redis['port']);
	define('WP_REDIS_CLIENT', $redis['client']);
	define('WP_REDIS_PREFIX', $redis['prefix']);
	define('WP_REDIS_DATABASE', $redis['database']);
	define('WP_REDIS_TIMEOUT', $redis['timeout']);
	define('WP_REDIS_READ_TIMEOUT', $redis['read_timeout']);
}

/** Database settings **/
define('DB_NAME', $config['database']['name']);
define('DB_USER', $config['database']['user']);
define('DB_PASSWORD', $config['database']['password']);
define('DB_HOST', $config['database']['host']);
define('DB_CHARSET', $config['database']['charset']);
define('DB_COLLATE', $config['database']['collate']);
$table_prefix = $config['database']['table_prefix'];

/** Authentication unique keys and salts **/
define('AUTH_KEY', $config['keys']['auth_key']);
define('SECURE_AUTH_KEY', $config['keys']['secure_auth_key']);
define('LOGGED_IN_KEY', $config['keys']['logged_in_key']);
define('NONCE_KEY', $config['keys']['nonce_key']);
define('AUTH_SALT', $config['keys']['auth_salt']);
define('SECURE_AUTH_SALT', $config['keys']['secure_auth_salt']);
define('LOGGED_IN_SALT', $config['keys']['logged_in_salt']);
define('NONCE_SALT', $config['keys']['nonce_salt']);

/** Debug configuration **/
define('WP_DEBUG', $config['debug']['wp_debug']);
define('WP_DEBUG_LOG', $config['debug']['wp_debug_log']);
define('WP_DEBUG_DISPLAY', $config['debug']['wp_debug_display']);
@ini_set('display_errors', $config['debug']['display_errors'] ? '1' : '0');
define('SCRIPT_DEBUG', $config['debug']['script_debug']);

/**
 * Allow WordPress to detect HTTPS when used behind a reverse proxy or a load balancer
 * See https://codex.wordpress.org/Function_Reference/is_ssl#Notes
 */
if ($config['site_data']['https'] && isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https') {
	$_SERVER['HTTPS'] = 'on';
}

/** WP URL configuration **/
define('WP_HOME', $config['site_data']['home_url']);
define('WP_SITEURL', $config['site_data']['wp_url']);

/** Auto updates and cron **/
define('WP_AUTO_UPDATE_CORE', $config['site_data']['auto_update']);
define('DISABLE_WP_CRON', $config['site_data']['disable_cron']);

/** Absolute path to the WordPress directory **/
if (!defined('ABSPATH')) {
	define('ABSPATH', __DIR__ . '/wp/');
}

/** Sets up WordPress vars and included files **/
require_once ABSPATH . 'wp-settings.php';

