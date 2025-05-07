<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              https://seofy.dk
 * @since             1.0.18
 * @package           Seofy_Dk_Navigation_Health
 *
 * @wordpress-plugin
 * Plugin Name:       Seofy DK Navigation Health
 * Plugin URI:        https://seofy.dk
 * Description:       Seofy DK Navigation for Health is a specialized WordPress plugin designed to improve website navigation and user experience for health and wellness websites. This powerful tool enables clinic owners, healthcare professionals, and developers to create dynamic, accessible, and customizable navigation menus that help users easily find the health services, practitioners, and information they need.
 * Version:           1.0.18
 * Author:            Jonard Aragon
 * Author URI:        https://seofy.dk/
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       seofy-dk-navigation-health
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Currently plugin version.
 * Start at version 1.0.18 and use SemVer - https://semver.org
 * Rename this for your plugin and update it as you release new versions.
 */
define( 'SEOFY_DK_NAVIGATION_HEALTH_VERSION', '1.0.18' );

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-seofy-dk-navigation-health-activator.php
 */
function activate_seofy_dk_navigation_health() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-seofy-dk-navigation-health-activator.php';
	Seofy_Dk_Navigation_Health_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-seofy-dk-navigation-health-deactivator.php
 */
function deactivate_seofy_dk_navigation_health() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-seofy-dk-navigation-health-deactivator.php';
	Seofy_Dk_Navigation_Health_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_seofy_dk_navigation_health' );
register_deactivation_hook( __FILE__, 'deactivate_seofy_dk_navigation_health' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-seofy-dk-navigation-health.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.18
 */
function run_seofy_dk_navigation_health() {

	$plugin = new Seofy_Dk_Navigation_Health();
	$plugin->run();

}
run_seofy_dk_navigation_health();
add_action( 'init', 'github_plugin_updater_dk_navigation_health_init' );
function github_plugin_updater_dk_navigation_health_init() {
    if ( is_admin() ) { // note the use of is_admin() to double check that this is happening in the admin
        include_once 'seofy-updater.php';
        define( 'SDKNH_PLUGIN_DIRECTORY',  __FILE__ );
        define( 'SDKNH_PROPER_FOLDER_NAME', 'seofy-dk-navigation-health');
        define( 'SDKNH_PLUGIN_SLUG',  'seofy-dk-navigation-health' );
        define( 'SDKNH_GITHUB_URL',  'https://api.github.com/repos/devseofy/seofy-dk-navigation-health/releases');
        define( 'SDKNH_GITHUB_TOKEN',  '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');
        new WP_GitHub_Updater_For_SeofyPlugin_Dk_Navigation_Health();

    }

}