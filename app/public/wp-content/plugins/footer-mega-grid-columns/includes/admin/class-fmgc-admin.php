<?php
/**
 * Admin Class
 *
 * Handles the Admin side functionality of plugin
 *
 * @package Footer Mega Grid Columns
 * @since 1.1.2
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

class Fmgc_Admin {

	function __construct() {

		// Action to register admin menu
		add_action( 'admin_menu', array($this, 'fmgc_register_menu'), 12 );
	}

	/**
	 * Function to register admin menus
	 * 
	 * @package Footer Mega Grid Columns
	 * @since 1.1.2
	 */
	function fmgc_register_menu() {

		// Register plugin premium page
		add_submenu_page( 'fmgc-about', __('Upgrade to PRO - Footer Mega Grid Columns', 'footer-mega-grid-columns'), '<span style="color:#2ECC71">'.__('Upgrade to PRO', 'footer-mega-grid-columns').'</span>', 'manage_options', 'fmgc-premium', array($this, 'fmgc_premium_page') );
		
		// Register plugin hire us page
		add_submenu_page( 'fmgc-about', __('Hire Us', 'footer-mega-grid-columns'), '<span style="color:#2ECC71">'.__('Hire Us', 'footer-mega-grid-columns').'</span>', 'manage_options', 'fmgc-hireus', array($this, 'fmgc_hireus_page') );
	}

	/**
	 * Getting Started Page Html
	 * 
	 * @package Footer Mega Grid Columns
	 * @since 1.1.2
	 */
	function fmgc_premium_page() {
		include_once( FMGC_DIR . '/includes/admin/settings/premium.php' );
	}

	/**
	 * Getting Started Page Html
	 * 
	 * @package Footer Mega Grid Columns
	 * @since 1.1.2
	 */
	function fmgc_hireus_page() {
		include_once( FMGC_DIR . '/includes/admin/settings/hire-us.php' );
	}
}

$fmgc_admin = new Fmgc_Admin();