<?php
/*
 * Version: 1.2.0
 */



if (!class_exists('oftnPremium')) {

	class oftnPremium {

        public static $plugin_name = 'Options for Twenty Nineteen';
        public static $plugin_prefix = 'oftn';
        public static $plugin_text_domain = 'options-for-twenty-nineteen';

		public static function plugin_version() {

            return options_for_twenty_nineteen_premium_class::$version;

        }

        private static $timeout = 30;

		public static function plugin_common_class() {

            return str_replace('Premium', 'Common', get_called_class());

		}

		public static function plugin_row_meta($plugin_meta, $plugin_file, $plugin_data, $status) {

            $plugin_to_check = self::plugin_to_check();

            if ($plugin_file == $plugin_to_check['plugin'] && !get_option(call_user_func(self::plugin_common_class() . '::plugin_prefix') . '_purchased') && get_option(call_user_func(self::plugin_common_class() . '::plugin_prefix') . '_trial_date') && time() < strtotime('+1 week', get_option(call_user_func(self::plugin_common_class() . '::plugin_prefix') . '_trial_date'))) {

                $expiring_in = ceil(abs((strtotime('+1 week', get_option(call_user_func(self::plugin_common_class() . '::plugin_prefix') . '_trial_date'))) - time())/60/60/24);
                $expiring_color = ceil(abs((strtotime('+1 week', get_option(call_user_func(self::plugin_common_class() . '::plugin_prefix') . '_trial_date'))) - time()) * 255 / 604800);
                $plugin_meta[] = '<span style="font-weight: bold; color: #' . str_pad(dechex(255 - $expiring_color), 2, '0', STR_PAD_LEFT) . dechex($expiring_color) . '00">' . sprintf(_n('Trial expires in %s day!', 'Trial expires in %s days!', $expiring_in, call_user_func(self::plugin_common_class() . '::plugin_text_domain')), $expiring_in) . '</span>';

            }

            return $plugin_meta;

        }

		public static function check_plugin_update($transient) {

            $plugin_to_check = self::plugin_to_check();
            $response = wp_cache_get(call_user_func(self::plugin_common_class() . '::plugin_prefix') . '_latest_version');

            if ($response === false) {

			    $url = add_query_arg(
    				array(
    					'action' => 'plugin_version',
    					'plugin' => $plugin_to_check['slug'],
    					'version' => self::plugin_version(),
    					'purchased' => get_option(call_user_func(self::plugin_common_class() . '::plugin_prefix') . '_purchased'),
    					'trial_date' => get_option(call_user_func(self::plugin_common_class() . '::plugin_prefix') . '_trial_date'),
    					'url' => ((isset($_SERVER['HTTPS']) ? 'https' : 'http') . '://' . $_SERVER['HTTP_HOST'])
    				),
    				'https://webd.uk/wp-admin/admin-ajax.php'
    			);
    			$response = wp_remote_request($url, array('timeout' => self::$timeout));

                if (!is_wp_error($response)) {

                	wp_cache_set(call_user_func(self::plugin_common_class() . '::plugin_prefix') . '_latest_version', $response);

                }

            }

			if (!is_wp_error($response) && isset($response['response']['code']) && $response['response']['code'] == 200 && isset($response['body'])) {

				$plugin_version = json_decode($response['body']);

				if (isset($plugin_version->trial_date) && isset($plugin_version->purchased)) {

					self::premium_check($plugin_version->trial_date, $plugin_version->purchased);

				}

				if (is_object($plugin_version) && isset($plugin_version->new_version) && isset($plugin_version->tested) && isset($plugin_version->changelog) && version_compare(preg_replace("/[^0-9.]/", '', $plugin_version->new_version), $plugin_to_check['version'], '>')) {

					$plugin_update = new stdClass();
					$plugin_update->id = 'webd.uk/' . call_user_func(self::plugin_common_class() . '::plugin_text_domain') . '/';
					$plugin_update->slug = $plugin_to_check['slug'];
					$plugin_update->plugin = $plugin_to_check['plugin'];
					$plugin_update->new_version = preg_replace("/[^0-9.]/", '', $plugin_version->new_version);
					$plugin_update->url = 'https://' . $plugin_update->id;
					$plugin_update->package = 'https://webd.uk/downloads/' . $plugin_to_check['slug'] . '.' . $plugin_update->new_version . '.zip';

					if ($plugin_to_check['hosted']) {

						$plugin_update->icons = array(
							'2x' => 'https://ps.w.org/' . call_user_func(self::plugin_common_class() . '::plugin_text_domain') . '/assets/icon-256x256.jpg',
							'1x' => 'https://ps.w.org/' . call_user_func(self::plugin_common_class() . '::plugin_text_domain') . '/assets/icon-128x128.jpg'
						);
            
					}
            
					$plugin_update->tested = preg_replace("/[^0-9.]/", '', $plugin_version->tested);
					$plugin_update->upgrade_notice = sanitize_text_field($plugin_version->changelog);
					$transient->response[$plugin_to_check['plugin']] = $plugin_update;  

				}

			}

            return $transient;
        }

		public static function get_plugin_information($result, $action, $args) {

            $plugin_to_check = self::plugin_to_check();

            if ($action == 'plugin_information' && isset($args->slug) && $args->slug == $plugin_to_check['slug']) {

                $url = add_query_arg(
                    array(
                        'action' => 'plugin_information',
                        'plugin' => $plugin_to_check['slug'],
                        'version' => self::plugin_version(),
                        'purchased' => get_option(call_user_func(self::plugin_common_class() . '::plugin_prefix') . '_purchased'),
                        'trial_date' => get_option(call_user_func(self::plugin_common_class() . '::plugin_prefix') . '_trial_date'),
                        'url' => ((isset($_SERVER['HTTPS']) ? 'https' : 'http') . '://' . $_SERVER['HTTP_HOST'])
                    ),
                    'https://webd.uk/wp-admin/admin-ajax.php'
                );
                $response = wp_remote_request($url, array('timeout' => self::$timeout));

                if(!is_wp_error($response) && isset($response['response']['code']) && $response['response']['code'] == 200 && isset($response['body'])) {

                    $plugin_information = json_decode($response['body']);

                    if (isset($plugin_information->trial_date) && isset($plugin_information->purchased)) {

                        self::premium_check($plugin_information->trial_date, $plugin_information->purchased);

                    }

                    if (is_object($plugin_information) && isset($plugin_information->version) && isset($plugin_information->tested) && isset($plugin_information->requires) && isset($plugin_information->sections->changelog)) {

                        $clean_information = new stdClass();
                        $clean_information->last_updated = sanitize_text_field($plugin_information->last_updated);
                        $clean_information->slug = $plugin_to_check['slug'];
                        $clean_information->version = preg_replace("/[^0-9.]/", '', $plugin_information->version);
                        $clean_information->author = '<a href=\"https://webd.uk\">webd.uk</a>';
                        $clean_information->homepage = 'https://webd.uk/' . call_user_func(self::plugin_common_class() . '::plugin_text_domain') . '/';
                        $clean_information->download_link = 'https://webd.uk/downloads/' . $plugin_to_check['slug'] . '.' . $clean_information->version . '.zip';
                        $clean_information->tested = preg_replace("/[^0-9.]/", '', $plugin_information->tested);
                        $clean_information->requires = preg_replace("/[^0-9.]/", '', $plugin_information->requires);
                        $clean_information->name = $plugin_to_check['name'];
                        $clean_information->sections = array(
                            'changelog' => wp_kses($plugin_information->sections->changelog, array( 
                                'h4' => array(),
                                'ul' => array(),
                                'li' => array()
                            ))
                        );

                        return $clean_information;

                    }

                }

            }

            return $result;
        }

        public static function plugin_to_check() {

            $plugin_to_check = array(
                'name' => call_user_func(self::plugin_common_class() . '::plugin_name') . ' Premium',
                'slug' => call_user_func(self::plugin_common_class() . '::plugin_text_domain') . '-premium',
                'plugin' => call_user_func(self::plugin_common_class() . '::plugin_text_domain') . '-premium/' . call_user_func(self::plugin_common_class() . '::plugin_text_domain') . '-premium.php',
                'hosted' => true,
                'version' => self::plugin_version()
            );

            if (self::validate_plugin($plugin_to_check['plugin']) !== 0) {

                $plugin_to_check['name'] = call_user_func(self::plugin_common_class() . '::plugin_name');
                $plugin_to_check['slug'] = call_user_func(self::plugin_common_class() . '::plugin_text_domain');
                $plugin_to_check['plugin'] = call_user_func(self::plugin_common_class() . '::plugin_text_domain') . '/' . call_user_func(self::plugin_common_class() . '::plugin_text_domain') . '.php';
                $plugin_to_check['hosted'] = false;

            }

            return $plugin_to_check;

        }

		public static function start_trial() {

        	if (!absint(get_option(call_user_func(self::plugin_common_class() . '::plugin_prefix') . '_trial_date')) && !get_option(call_user_func(self::plugin_common_class() . '::plugin_prefix') . '_purchased')) {

                update_option(call_user_func(self::plugin_common_class() . '::plugin_prefix') . '_trial_date', time());
                add_action('admin_notices', array(get_called_class(), 'trial_started_notice'));

        	}

        }

		public static function trial_started_notice() {

?>
<div class="notice notice-info">
<p><strong><?= sprintf(__('%s Trial Started', call_user_func(self::plugin_common_class() . '::plugin_text_domain')), call_user_func(self::plugin_common_class() . '::plugin_name')); ?></strong><br />
<?= sprintf(__('Your free 7 day trial of %s has started.', call_user_func(self::plugin_common_class() . '::plugin_text_domain')), call_user_func(self::plugin_common_class() . '::plugin_name')); ?></p>
</div>
<?php

        }

        public static function activate_purchase_js($plugin_file, $plugin_data, $status) {

            if (!get_option(call_user_func(self::plugin_common_class() . '::plugin_prefix') . '_purchased')) {

?>
<script>
    jQuery('#<?= call_user_func(self::plugin_common_class() . '::plugin_prefix'); ?>_activate_upgrade').attr('onclick', 'return false;').attr('href', '#').one('click', function(event) { 
        event.preventDefault();
        jQuery(this).after(' <span id="<?= call_user_func(self::plugin_common_class() . '::plugin_prefix'); ?>_activate_purchase_loading_wrapper"><img src="<?= site_url('wp-admin/images/loading.gif', 'relative'); ?>" style="float: none; width: 13px; height: 13px; padding: 0;" /> <span class="countdown" style="color: #000;">30</span></span>');
        var timeleft = 29;
        var activateTimer = setInterval(function() {
            if (timeleft <= 0) {
                clearInterval(activateTimer);
            }
            jQuery('#<?= call_user_func(self::plugin_common_class() . '::plugin_prefix'); ?>_activate_purchase_loading_wrapper span').text(timeleft);
            timeleft -= 1;
        }, 1000);
        jQuery.ajax({
        	url: ajaxurl,
        	data: {
            	action: '<?= call_user_func(self::plugin_common_class() . '::plugin_prefix'); ?>_activate_purchase',
            	_ajax_nonce: '<?= wp_create_nonce(call_user_func(self::plugin_common_class() . '::plugin_prefix') . '-activate-purchase'); ?>'
        	},
        	success: function(result){
        	    if (result.success) {
                    jQuery('#<?= call_user_func(self::plugin_common_class() . '::plugin_prefix'); ?>_activate_upgrade').removeAttr('href').text(<?= json_encode(__('Purchase Confirmed', call_user_func(self::plugin_common_class() . '::plugin_text_domain'))); ?>).css('color', 'green').css('font-weight', 'bold');
        	    } else {
        	        if (result.data[0]) {
        	            alert(result.data[0].message);
        	        } else {
                        alert(<?= json_encode(__('Something went wrong, sorry!', call_user_func(self::plugin_common_class() . '::plugin_text_domain'))); ?>);
        	        }
                    jQuery('#<?= call_user_func(self::plugin_common_class() . '::plugin_prefix'); ?>_activate_upgrade').removeAttr('href').text(<?= json_encode(__('Activation Failed', call_user_func(self::plugin_common_class() . '::plugin_text_domain'))); ?>).css('color', 'red');
                }
                jQuery('#<?= call_user_func(self::plugin_common_class() . '::plugin_prefix'); ?>_activate_purchase_loading_wrapper').remove();
            },
            error: function(){
                alert(<?= json_encode(__('Activation failed. Please purchase an upgrade first. If you have already purchased an upgrade for this plugin, please contact us so we can investigate the issue.', call_user_func(self::plugin_common_class() . '::plugin_text_domain'))); ?>);
                jQuery('#<?= call_user_func(self::plugin_common_class() . '::plugin_prefix'); ?>_activate_purchase_loading_wrapper').remove();
                jQuery('#<?= call_user_func(self::plugin_common_class() . '::plugin_prefix'); ?>_activate_upgrade').removeAttr('href').text('Activation Failed').css('color', 'red');
            }
        });
    });
</script>
<?php

            }

        }

		public static function activate_purchase() {

            check_ajax_referer(call_user_func(self::plugin_common_class() . '::plugin_prefix') . '-activate-purchase');

            if (current_user_can('manage_options')) {

                if (self::is_ip_or_local()) {

					update_option(call_user_func(self::plugin_common_class() . '::plugin_prefix') . '_purchased', true);
                    wp_send_json_success();

                } else {

                    $plugin_to_check = self::plugin_to_check();
                    $url = add_query_arg(
                        array(
                            'action' => 'plugin_version',
                            'plugin' => $plugin_to_check['slug'],
                            'version' => self::plugin_version(),
                            'purchased' => get_option(call_user_func(self::plugin_common_class() . '::plugin_prefix') . '_purchased'),
                            'trial_date' => get_option(call_user_func(self::plugin_common_class() . '::plugin_prefix') . '_trial_date'),
                            'url' => ((isset($_SERVER['HTTPS']) ? 'https' : 'http') . '://' . $_SERVER['HTTP_HOST'])
                        ),
                        'https://webd.uk/wp-admin/admin-ajax.php'
                    );
                    $response = wp_remote_request($url, array('timeout' => self::$timeout));

                    if(!is_wp_error($response) && isset($response['response']['code']) && $response['response']['code'] == 200 && isset($response['body'])) {

                        $plugin_version = json_decode($response['body']);

                        if (isset($plugin_version->purchased) && $plugin_version->purchased) {

        					update_option(call_user_func(self::plugin_common_class() . '::plugin_prefix') . '_purchased', true);
                            wp_send_json_success();

                        } else {

        					delete_option(call_user_func(self::plugin_common_class() . '::plugin_prefix') . '_purchased');
                            wp_send_json_error(new WP_Error('failed', __('Activation failed. Please purchase an upgrade first. If you have already purchased an upgrade for this plugin, please contact us so we can investigate the issue.', call_user_func(self::plugin_common_class() . '::plugin_text_domain'))));

                        }

                    } else {

    					delete_option(call_user_func(self::plugin_common_class() . '::plugin_prefix') . '_purchased');
                        wp_send_json_error(new WP_Error('failed', __('The purchase activation has failed because your website cannot connect to webd.uk to confirm your purchase. Please contact your Service provider to ask them to unblock webd.uk and if they cannot help you please contact us so we can investigate further.', call_user_func(self::plugin_common_class() . '::plugin_text_domain'))));

                    }

                }

            } else {

                wp_send_json_error(new WP_Error('denied', __('Permission denied!', call_user_func(self::plugin_common_class() . '::plugin_text_domain'))));

            }

        	wp_die();

        }

        public static function is_ip_or_local() {

            $client_host = $_SERVER['HTTP_HOST'];
            $client_host = preg_replace('#^www.#', '', $client_host);
            $client_host = preg_replace('#^test.#', '', $client_host);
            $client_host = preg_replace('#^dev.#', '', $client_host);

            if (strpos($client_host, ':')) {

                $client_host = substr($client_host, 0, strpos($client_host, ':'));

            }

            if (filter_var($client_host, FILTER_VALIDATE_IP) || $client_host == 'localhost') {

                return true;
                
            } else {

                return false;
            }

        }

		public static function request_permission() {

            if ((get_option(call_user_func(self::plugin_common_class() . '::plugin_prefix') . '_trial_date') && time() < (strtotime('+1 week', get_option(call_user_func(self::plugin_common_class() . '::plugin_prefix') . '_trial_date')))) || get_option(call_user_func(self::plugin_common_class() . '::plugin_prefix') . '_purchased')) {

                return true;

            } else {

                return false;

            }

        }

		public static function upgrade_notice() {

            if (time() > (strtotime('+1 hour', filectime(__DIR__))) && get_option(call_user_func(self::plugin_common_class() . '::plugin_prefix') . '_purchased') == false && get_user_meta(get_current_user_id(), call_user_func(self::plugin_common_class() . '::plugin_prefix') . '-notice-dismissed', true) != call_user_func(self::plugin_common_class() . '::plugin_version')) {

                if (method_exists(self::plugin_common_class(), 'plugin_trial') && call_user_func(self::plugin_common_class() . '::plugin_trial') && get_option(call_user_func(self::plugin_common_class() . '::plugin_prefix') . '_trial_date') && time() < (strtotime('+1 week', get_option(call_user_func(self::plugin_common_class() . '::plugin_prefix') . '_trial_date'))) && time() > (strtotime('+5 days', get_option(call_user_func(self::plugin_common_class() . '::plugin_prefix') . '_trial_date')))) {

                    $expiring_in = ceil(abs((strtotime('+1 week', get_option(call_user_func(self::plugin_common_class() . '::plugin_prefix') . '_trial_date'))) - time())/60/60/24);

?>

<div class="notice notice-warning is-dismissible <?= call_user_func(self::plugin_common_class() . '::plugin_prefix'); ?>-notice">

<p><strong><?php printf(_n(call_user_func(self::plugin_common_class() . '::plugin_name') . ' Premium plugin trial expires in less than %s day!', call_user_func(self::plugin_common_class() . '::plugin_name') . ' Premium plugin trial expires in less than %s days!', $expiring_in, call_user_func(self::plugin_common_class() . '::plugin_text_domain')), $expiring_in); ?></strong><br />
<?php _e('Upgrade this plugin to retain access to premium features and help fund further development.', call_user_func(self::plugin_common_class() . '::plugin_text_domain')); ?></p>

<p><a href="<?= call_user_func(self::plugin_common_class() . '::upgrade_link'); ?>" title="<?= sprintf(__('Upgrade %s Premium', call_user_func(self::plugin_common_class() . '::plugin_text_domain')), call_user_func(self::plugin_common_class() . '::plugin_name')); ?>" class="button-primary"><?= sprintf(__('Upgrade %s Premium', call_user_func(self::plugin_common_class() . '::plugin_text_domain')), call_user_func(self::plugin_common_class() . '::plugin_name')); ?></a></p>

</div>

<script type="text/javascript">
    jQuery(document).on('click', '.<?= call_user_func(self::plugin_common_class() . '::plugin_prefix'); ?>-notice .notice-dismiss', function() {
	    jQuery.ajax({
    	    url: ajaxurl,
    	    data: {
        		action: 'dismiss_<?= call_user_func(self::plugin_common_class() . '::plugin_prefix'); ?>_notice_handler',
        		_ajax_nonce: '<?= wp_create_nonce(call_user_func(self::plugin_common_class() . '::plugin_prefix') . '-ajax-nonce'); ?>'
    	    }
    	});
    });
</script>

<?php

                } elseif (method_exists(self::plugin_common_class(), 'plugin_trial') && call_user_func(self::plugin_common_class() . '::plugin_trial') && get_option(call_user_func(self::plugin_common_class() . '::plugin_prefix') . '_trial_date') && time() > (strtotime('+1 week', get_option(call_user_func(self::plugin_common_class() . '::plugin_prefix') . '_trial_date')))) {

?>

<div class="notice notice-error is-dismissible <?= call_user_func(self::plugin_common_class() . '::plugin_prefix'); ?>-notice">

<p><strong><?= sprintf(__('%s Premium plugin free trial has expired', call_user_func(self::plugin_common_class() . '::plugin_text_domain')), call_user_func(self::plugin_common_class() . '::plugin_name')); ?></strong><br />
<?php _e('Upgrade this plugin to retain access to premium features and help fund further development.', call_user_func(self::plugin_common_class() . '::plugin_text_domain')); ?></p>

<p><a href="<?= call_user_func(self::plugin_common_class() . '::upgrade_link'); ?>" title="<?= sprintf(__('Upgrade %s Premium', call_user_func(self::plugin_common_class() . '::plugin_text_domain')), call_user_func(self::plugin_common_class() . '::plugin_name')); ?>" class="button-primary"><?= sprintf(__('Upgrade %s Premium', call_user_func(self::plugin_common_class() . '::plugin_text_domain')), call_user_func(self::plugin_common_class() . '::plugin_name')); ?></a></p>

</div>

<script type="text/javascript">
    jQuery(document).on('click', '.<?= call_user_func(self::plugin_common_class() . '::plugin_prefix'); ?>-notice .notice-dismiss', function() {
	    jQuery.ajax({
    	    url: ajaxurl,
    	    data: {
        		action: 'dismiss_<?= call_user_func(self::plugin_common_class() . '::plugin_prefix'); ?>_notice_handler',
        		_ajax_nonce: '<?= wp_create_nonce(call_user_func(self::plugin_common_class() . '::plugin_prefix') . '-ajax-nonce'); ?>'
    	    }
    	});
    });
</script>

<?php

                } elseif (method_exists(self::plugin_common_class(), 'plugin_trial') && call_user_func(self::plugin_common_class() . '::plugin_trial') && get_option(call_user_func(self::plugin_common_class() . '::plugin_prefix') . '_trial_date') && time() < strtotime('+1 week', get_option(call_user_func(self::plugin_common_class() . '::plugin_prefix') . '_trial_date'))) {

                    $expiring_in = ceil(abs((strtotime('+1 week', get_option(call_user_func(self::plugin_common_class() . '::plugin_prefix') . '_trial_date'))) - time())/60/60/24);

?>

<div class="notice notice-info is-dismissible <?= call_user_func(self::plugin_common_class() . '::plugin_prefix'); ?>-notice">

<p><strong><?= sprintf(__('Upgrade to %s Premium', call_user_func(self::plugin_common_class() . '::plugin_text_domain')), call_user_func(self::plugin_common_class() . '::plugin_name')); ?></strong><br />
<?php

                    printf(_n('Upgrade this plugin to keep access to premium features and help fund further development. Your trial expires in %s day!', 'Upgrade this plugin to keep access to premium features and help fund further development. Your trial expires in %s days!', $expiring_in, call_user_func(self::plugin_common_class() . '::plugin_text_domain')), $expiring_in);

?></p>

<p><a href="<?= call_user_func(self::plugin_common_class() . '::upgrade_link'); ?>" title="<?= sprintf(__('Upgrade to %s Premium', call_user_func(self::plugin_common_class() . '::plugin_text_domain')), call_user_func(self::plugin_common_class() . '::plugin_name')); ?>" class="button-primary"><?= sprintf(__('Upgrade to %s Premium', call_user_func(self::plugin_common_class() . '::plugin_text_domain')), call_user_func(self::plugin_common_class() . '::plugin_name')); ?></a></p>

</div>

<script type="text/javascript">
    jQuery(document).on('click', '.<?= call_user_func(self::plugin_common_class() . '::plugin_prefix'); ?>-notice .notice-dismiss', function() {
	    jQuery.ajax({
    	    url: ajaxurl,
    	    data: {
        		action: 'dismiss_<?= call_user_func(self::plugin_common_class() . '::plugin_prefix'); ?>_notice_handler',
        		_ajax_nonce: '<?= wp_create_nonce(call_user_func(self::plugin_common_class() . '::plugin_prefix') . '-ajax-nonce'); ?>'
    	    }
    	});
    });
</script>

<?php

                } else {

?>

<div class="notice notice-info is-dismissible <?= call_user_func(self::plugin_common_class() . '::plugin_prefix'); ?>-notice">

<p><strong><?= sprintf(__('Upgrade to %s Premium', call_user_func(self::plugin_common_class() . '::plugin_text_domain')), call_user_func(self::plugin_common_class() . '::plugin_name')); ?></strong><br />
<?php _e('Upgrade this plugin to gain access to premium features and help fund further development.', call_user_func(self::plugin_common_class() . '::plugin_text_domain')); ?></p>

<p><a href="<?= call_user_func(self::plugin_common_class() . '::upgrade_link'); ?>" title="<?= sprintf(__('Upgrade to %s Premium', call_user_func(self::plugin_common_class() . '::plugin_text_domain')), call_user_func(self::plugin_common_class() . '::plugin_name')); ?>" class="button-primary"><?= sprintf(__('Upgrade to %s Premium', call_user_func(self::plugin_common_class() . '::plugin_text_domain')), call_user_func(self::plugin_common_class() . '::plugin_name')); ?></a></p>

</div>

<script type="text/javascript">
    jQuery(document).on('click', '.<?= call_user_func(self::plugin_common_class() . '::plugin_prefix'); ?>-notice .notice-dismiss', function() {
	    jQuery.ajax({
    	    url: ajaxurl,
    	    data: {
        		action: 'dismiss_<?= call_user_func(self::plugin_common_class() . '::plugin_prefix'); ?>_notice_handler',
        		_ajax_nonce: '<?= wp_create_nonce(call_user_func(self::plugin_common_class() . '::plugin_prefix') . '-ajax-nonce'); ?>'
    	    }
    	});
    });
</script>

<?php

                }

            }

        }

		public static function validate_plugin($plugin) {

            if (!function_exists('validate_plugin')) {

                require_once(ABSPATH . 'wp-admin/includes/plugin.php');

            }

            return validate_plugin($plugin);

		}

        public static function free_plugin_notice() {

            $current_screen = get_current_screen();

            if (!($current_screen->base == 'update' && $current_screen->parent_base == 'plugins')) {

?>

<div class="notice notice-error <?= self::$plugin_prefix; ?>-notice">

<p><strong><?= sprintf(__('%s Premium', self::$plugin_text_domain), self::$plugin_name); ?></strong><br />
<?php


                if (self::validate_plugin(self::$plugin_text_domain . '/' . self::$plugin_text_domain . '.php') === 0) {

?>
<?php _e('In order to use this plugin, you need to activate the free version ...', self::$plugin_text_domain); ?></p>

<p><a href="<?=

                    esc_url(wp_nonce_url(add_query_arg(
                        array(
                            'action' => 'activate',
                            'plugin' => self::$plugin_text_domain . '/' . self::$plugin_text_domain . '.php',
                            'plugin_status' => 'all',
                            'paged' => '1'
                        ),
                        admin_url('plugins.php')
                    ), 'activate-plugin_' . self::$plugin_text_domain . '/' . self::$plugin_text_domain . '.php'));

?>" title="<?= sprintf(__('Activate %s', self::$plugin_text_domain), self::$plugin_name); ?>" class="button-primary"><?= sprintf(__('Activate %s', self::$plugin_text_domain), self::$plugin_name); ?></a></p>
<?php

                } else {

?>
<?php _e('In order to use this plugin, you need to install and activate the free version ...', self::$plugin_text_domain); ?></p>

<p><a href="<?=

                    esc_url(wp_nonce_url(add_query_arg(
                        array(
                            'action' => 'install-plugin',
                            'plugin' => self::$plugin_text_domain
                        ),
                        self_admin_url('update.php')
                    ), 'install-plugin_' . self::$plugin_text_domain));

?>" title="<?= sprintf(__('Install %s', self::$plugin_text_domain), self::$plugin_name); ?>" class="button-primary"><?= sprintf(__('Install %s', self::$plugin_text_domain), self::$plugin_name); ?></a></p>
<?php

                }

            }

?>
</div>

<?php

        }

        public static function premium_check($trial_date, $purchased) {

            if (get_option(call_user_func(self::plugin_common_class() . '::plugin_prefix') . '_purchased') && !$purchased) {

                delete_option(call_user_func(self::plugin_common_class() . '::plugin_prefix') . '_purchased');

                if (!get_option(call_user_func(self::plugin_common_class() . '::plugin_prefix') . '_trial_date')) {

                    if (absint($trial_date)) {

                        update_option(call_user_func(self::plugin_common_class() . '::plugin_prefix') . '_trial_date', absint($trial_date));

                    } else {

                        update_option(call_user_func(self::plugin_common_class() . '::plugin_prefix') . '_trial_date', time()-604800);

                    }

                }

            }

            if (absint($trial_date)) {

                if (!get_option(call_user_func(self::plugin_common_class() . '::plugin_prefix') . '_trial_date') || (absint($trial_date) !== absint(get_option(call_user_func(self::plugin_common_class() . '::plugin_prefix') . '_trial_date')))) {

                    update_option(call_user_func(self::plugin_common_class() . '::plugin_prefix') . '_trial_date', absint($trial_date));

                }

            }

        }

        public static function upgrader_pre_download($reply, $package, $updater) {

            $plugin_to_check = self::plugin_to_check();

            if ($plugin_to_check['hosted'] && isset($updater->skin->plugin_info['TextDomain']) && $updater->skin->plugin_info['TextDomain'] == $plugin_to_check['slug'] && !get_option(call_user_func(self::plugin_common_class() . '::plugin_prefix') . '_purchased')) {

                return new WP_Error(
                    'no_credentials',
                    sprintf(
                        wp_kses(
                            __(
                                'To receive automatic updates, purchase a license for the plugin ...<br /><a href="%s" title="' . 'Upgrade %s" class="button-primary">' . 'Upgrade %s</a>',
                                call_user_func(self::plugin_common_class() . '::plugin_text_domain')
                            ),
                            array(
                                'a' => array('href' => array(), 'title' => array()),
                                'br' => array()
                            )
                        ),
                        esc_url(call_user_func(self::plugin_common_class() . '::upgrade_link')),
                        $plugin_to_check['name'],
                        $plugin_to_check['name']
                    )
                );

            } else {

                return $reply;

            }

        }

	}

}

?>
