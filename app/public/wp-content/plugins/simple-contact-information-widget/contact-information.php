<?php

/**
 * The plugin for show contact information 
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              https://jaydeepchauhanblog.wordpress.com
 * @since             1.0.0
 * @package           contact-information
 *
 * @wordpress-plugin
 * Plugin Name:       Contact Information Widget
 * Plugin URI:        https://wordpress.org/plugins/simple-contact-information-widget/
 * Description:       Wordpress widget for display simple contact information like company,description,address,phone,mobile,email,fax,map.
 * Version:           1.0.2
 * Author:            Jaydeep Chauhan <jd.dev777@gmail.com>
 * Author URI:        https://jaydeepchauhanblog.wordpress.com/
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       contact-information
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Currently pligin version.
 */
define( 'CONTACT_INFORMATION_VERSION', '1.0.2' );

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/contact-information-activator.php
 */
function activate_contact_information() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/contact-information-activator.php';
	Contact_Information_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/contact-information-deactivator.php
 */
function deactivate_contact_information() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/contact-information-deactivator.php';
	Contact_Information_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_contact_information' );
register_deactivation_hook( __FILE__, 'deactivate_contact_information' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/contact-information.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_contact_information() {

	$plugin = new Contact_Information();
	$plugin->run();

}

run_contact_information();