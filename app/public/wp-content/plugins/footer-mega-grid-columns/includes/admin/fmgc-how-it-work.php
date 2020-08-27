<?php
/**
 * Pro Designs and Plugins Feed
 *
 * @package Footer Mega Grid Columns
 * @since 1.1.2
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

// Action to add menu
add_action('admin_menu', 'fmgc_register_design_page');

/**
 * Register plugin design page in admin menu
 * 
 * @package Footer Mega Grid Columns
 * @since 1.1.2
 */
function fmgc_register_design_page() {
	add_menu_page( __('Footer Mega Grid Columns', 'footer-mega-grid-columns'), __('Footer Mega Grid Columns', 'footer-mega-grid-columns'), 'manage_options', 'fmgc-about',  'fmgc_settings_page', 'dashicons-align-left', 6 );
}

/**
 * Function to display plugin design HTML
 * 
 * @package Footer Mega Grid Columns
 * @since 1.1.2
 */
function fmgc_settings_page() {

	$wpos_feed_tabs = fmgc_help_tabs();
	$active_tab 	= isset($_GET['tab']) ? $_GET['tab'] : 'how-it-work';
?>

	<div class="wrap fmgc-wrap">

		<h2 class="nav-tab-wrapper">
			<?php
			foreach ($wpos_feed_tabs as $tab_key => $tab_val) {
				$tab_name	= $tab_val['name'];
				$active_cls = ($tab_key == $active_tab) ? 'nav-tab-active' : '';
				$tab_link 	= add_query_arg( array('page' => 'fmgc-about', 'tab' => $tab_key), admin_url('admin.php') );
			?>

			<a class="nav-tab <?php echo $active_cls; ?>" href="<?php echo $tab_link; ?>"><?php echo $tab_name; ?></a>

			<?php } ?>
		</h2>

		<div class="fmgc-tab-cnt-wrp">
		<?php
			if( isset($active_tab) && $active_tab == 'how-it-work' ) {
				fmgc_howitwork_page();
			}
			else if( isset($active_tab) && $active_tab == 'plugins-feed' ) {
				echo  fmgc_get_plugin_design( 'plugins-feed' );
			} else {
				echo  fmgc_get_plugin_design( 'offers-feed' );
			}
		?>
		</div><!-- end .fmgc-tab-cnt-wrp -->

	</div><!-- end .fmgc-wrap -->

<?php
}

/**
 * Gets the plugin design part feed
 *
 * @package Footer Mega Grid Columns
 * @since 1.1.2
 */
function fmgc_get_plugin_design( $feed_type = '' ) {

	$active_tab = isset($_GET['tab']) ? $_GET['tab'] : '';

	// If tab is not set then return
	if( empty($active_tab) ) {
		return false;
	}

	// Taking some variables
	$wpos_feed_tabs =  fmgc_help_tabs();
	$transient_key 	= isset($wpos_feed_tabs[$active_tab]['transient_key']) 	? $wpos_feed_tabs[$active_tab]['transient_key'] 	: 'fmgc_' . $active_tab;
	$url 			= isset($wpos_feed_tabs[$active_tab]['url']) 			? $wpos_feed_tabs[$active_tab]['url'] 				: '';
	$transient_time = isset($wpos_feed_tabs[$active_tab]['transient_time']) ? $wpos_feed_tabs[$active_tab]['transient_time'] 	: 172800;
	$cache 			= get_transient( $transient_key );

	if ( false === $cache ) {

		$feed 			= wp_remote_get( esc_url_raw( $url ), array( 'timeout' => 120, 'sslverify' => false ) );
		$response_code 	= wp_remote_retrieve_response_code( $feed );

		if ( ! is_wp_error( $feed ) && $response_code == 200 ) {
			if ( isset( $feed['body'] ) && strlen( $feed['body'] ) > 0 ) {
				$cache = wp_remote_retrieve_body( $feed );
				set_transient( $transient_key, $cache, $transient_time );
			}
		} else {
			$cache = '<div class="error"><p>' . __( 'There was an error retrieving the data from the server. Please try again later.', 'footer-mega-grid-columns' ) . '</div>';
		}
	}
	return $cache;
}

/**
 * Function to get plugin feed tabs
 *
 *@package Footer Mega Grid Columns
 * @since 1.1.2
 */
function fmgc_help_tabs() {
	$wpos_feed_tabs = array(
						'how-it-work' 	=> array(
													'name' => __('How It Works', 'footer-mega-grid-columns'),
												),
						'plugins-feed' 	=> array(
													'name' 				=> __('Our Plugins', 'footer-mega-grid-columns'),
													'url'				=> 'http://wponlinesupport.com/plugin-data-api/plugins-data.php',
													'transient_key'		=> 'wpos_plugins_feed',
													'transient_time'	=> 172800
												)
					);
	return $wpos_feed_tabs;
}

/**
 * Function to get 'How It Works' HTML
 *
 * @package Footer Mega Grid Columns
 * @package Footer Mega Grid Columns
 * @package Footer Mega Grid Columns
 * @since 1.1.2
 */
function fmgc_howitwork_page() { ?>

	<style type="text/css">
		.wpos-pro-box .hndle{background-color:#0073AA; color:#fff;}
		.wpos-pro-box.postbox{background:#dbf0fa none repeat scroll 0 0; border:1px solid #0073aa; color:#191e23;}
		.postbox-container .wpos-list li:before{font-family: dashicons; content: "\f139"; font-size:20px; color: #0073aa; vertical-align: middle;}
		.fmgc-wrap .wpos-button-full{display:block; text-align:center; box-shadow:none; border-radius:0;}
		.fmgc-shortcode-preview{background-color: #e7e7e7; font-weight: bold; padding: 2px 5px; display: inline-block; margin:0 0 2px 0;}
		.upgrade-to-pro{font-size:18px; text-align:center; margin-bottom:15px;}
	</style>

	<div id="poststuff">
		<div id="post-body" class="metabox-holder columns-2">
			<!--How it workd HTML -->
			<div id="post-body-content">
				<div class="meta-box-sortables">
					<div class="postbox">

						<h3 class="hndle">
							<span><?php _e( 'How It Works - Display and Shortcode', 'footer-mega-grid-columns' ); ?></span>
						</h3>

						<div class="inside">
							<table class="form-table">
								<tbody>
									<tr>
										<th>
											<label><?php _e('Getting Started', 'footer-mega-grid-columns'); ?></label>
										</th>
										<td>
											<ul>
												<li><?php _e('Step-1. Go to widget Tab and click on any widget.', 'footer-mega-grid-columns'); ?></li>
												<li><?php _e('Step-2. Add widgets in Footer Mega Grid Columns in widget', 'footer-mega-grid-columns'); ?></li>
											</ul>
										</td>
									</tr>

									<tr>
										<th>
											<label><?php _e('How Shortcode Works', 'footer-mega-grid-columns'); ?></label>
										</th>
										<td>
											<ul>
												<li><?php _e('Paste below template code in footer tag in footer.php file in active theme.', 'footer-mega-grid-columns'); ?></li>
												<li class="fmgc-shortcode-preview">&lt;?php if( function_exists('slbd_display_widgets') ) { echo slbd_display_widgets(); } ?&gt;</li>
											</ul>
										</td>
									</tr>

									<tr>
										<th>
											<label><?php _e('Need Support?', 'footer-mega-grid-columns'); ?></label>
										</th>
										<td>
											<p><?php _e('Check plugin document and demo.', 'footer-mega-grid-columns'); ?></p> <br/>
											<a class="button button-primary" href="https://docs.wponlinesupport.com/footer-mega-grid-columns/" target="_blank"><?php _e('Documentation', 'footer-mega-grid-columns'); ?></a>
											<a class="button button-primary" href="https://demo.wponlinesupport.com/footer-mega-grid-columns-demo/" target="_blank"><?php _e('Demo for Designs', 'footer-mega-grid-columns'); ?></a>
										</td>
									</tr>
								</tbody>
							</table>
						</div><!-- .inside -->
					</div><!-- #general -->
				</div><!-- .meta-box-sortables -->
			</div><!-- #post-body-content -->

			<!--Upgrad to Pro HTML -->
			<div id="postbox-container-1" class="postbox-container">
				<div class="meta-box-sortables">
					<div class="postbox wpos-pro-box">

						<h3 class="hndle">
							<span><?php _e( 'Upgrate to Pro', 'footer-mega-grid-columns' ); ?></span>
						</h3>
						<div class="inside">
							<ul class="wpos-list">
								<li><?php _e( '3 display output method.', 'footer-mega-grid-columns' ); ?></li>
								<li><?php _e( '1 Shortcode.', 'footer-mega-grid-columns' ); ?></li>
								<li><?php _e( 'Grid Support.', 'footer-mega-grid-columns' ); ?></li>
								<li><?php _e( 'Custom CSS editor.', 'footer-mega-grid-columns' ); ?></li>
								<li><?php _e( 'Custom CSS class support.', 'footer-mega-grid-columns' ); ?></li>
								<li><?php _e( 'Footer wrap width.', 'footer-mega-grid-columns' ); ?></li>
								<li><?php _e( 'Widget title color.', 'footer-mega-grid-columns' ); ?></li>
								<li><?php _e( 'Widget link color.', 'footer-mega-grid-columns' ); ?></li>
								<li><?php _e( 'Widget content color.', 'footer-mega-grid-columns' ); ?></li>
								<li><?php _e( '100% Multilanguage.', 'footer-mega-grid-columns' ); ?></li>
							</ul>
							<div class="upgrade-to-pro"><?php _e( 'Gain access to <strong>Footer Mega Grid Columns Pro</strong> included in <br /><strong>Essential Plugin Bundle', 'footer-mega-grid-columns'); ?></div>
							<a class="button button-primary wpos-button-full" href="https://www.wponlinesupport.com/wp-plugin/footer-mega-grid-columns/?ref=WposPratik&utm_source=WP&utm_medium=Footer-Mega-Grid&utm_campaign=Upgrade-PRO" target="_blank"><?php _e('Go Premium', 'footer-mega-grid-columns'); ?></a>
							<p><a class="button button-primary wpos-button-full" href="https://demo.wponlinesupport.com/prodemo/footer-mega-grid-columns-pro/?utm_source=WP&utm_medium=Footer-Mega-Grid&utm_campaign=PRO-Demo" target="_blank"><?php _e('View PRO Demo', 'footer-mega-grid-columns'); ?></a></p>
						</div><!-- .inside -->
					</div><!-- #general -->

					<div class="postbox">
							<h3 class="hndle">
								<span><?php _e( 'Help to improve this plugin!', 'footer-mega-grid-columns' ); ?></span>
							</h3>
							<div class="inside">
								<p><?php _e('Enjoyed this plugin? You can help by rate this plugin', 'footer-mega-grid-columns'); ?> <a href="https://wordpress.org/support/plugin/footer-mega-grid-columns/reviews/#new-post" target="_blank"><?php _e('5 stars!', 'footer-mega-grid-columns'); ?></a></p>
							</div><!-- .inside -->
					</div><!-- #general -->

				</div><!-- .metabox-holder -->
			</div><!-- #post-container-1 -->
		</div><!-- #post-body -->
	</div><!-- #poststuff -->
<?php }