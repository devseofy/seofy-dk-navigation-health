<?php

/**
 * Fired during plugin activation
 *
 * @link       https://seofy.dk
 * @since      1.0.0
 *
 * @package    Seofy_Dk_Navigation_Health
 * @subpackage Seofy_Dk_Navigation_Health/includes
 */

/**
 * Fired during plugin activation.
 *
 * This class defines all code necessary to run during the plugin's activation.
 *
 * @since      1.0.0
 * @package    Seofy_Dk_Navigation_Health
 * @subpackage Seofy_Dk_Navigation_Health/includes
 * @author     Jonard Aragon <jonardaragon@gmail.com>
 */
class Seofy_Dk_Navigation_Health_Activator {

	/**
	 * Short Description. (use period)
	 *
	 * Long Description.
	 *
	 * @since    1.0.0
	 */
	public static function activate() {

        self::create_directory_index_table();

	}

	   // Function to create a custom table
	public static function create_directory_index_table() {
        global $wpdb;

        $table_name = $wpdb->prefix . 'directory_index_table';

        // Check if table exists
        if ($wpdb->get_var("SHOW TABLES LIKE '$table_name'") != $table_name) {
            // Table does not exist, so create it
            $sql = "CREATE TABLE $table_name (
                id mediumint(9) NOT NULL AUTO_INCREMENT,
                postal_code VARCHAR(20) NOT NULL,
                municipality VARCHAR(255) NOT NULL,
                region VARCHAR(255) NOT NULL,
                PRIMARY KEY  (id),
                UNIQUE KEY unique_row (postal_code, municipality, region)
            );";

            require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
            dbDelta($sql);
        }
    }

}
