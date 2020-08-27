<?php
/*
 * Plugin Name: Options for Twenty Nineteen Premium
 * Version: 1.1.5
 * Plugin URI: https://webd.uk/options-for-twenty-nineteen/
 * Description: Adds even more options to modify the default Wordpress theme Twenty Nineteen
 * Author: webd.uk
 * Author URI: https://webd.uk
 */



if (!defined('ABSPATH')) {
    exit('This isn\'t the page you\'re looking for. Move along, move along.');
}



if (!class_exists('options_for_twenty_nineteen_premium_class')) {

	class options_for_twenty_nineteen_premium_class {

        public static $version = '1.1.5';

		function __construct() {

            add_action('customize_register', array($this, 'oftn_customize_register'), 999);
            add_action('wp_head' , array($this, 'oftn_header_output'));

            if (oftnPremium::request_permission()) {

                add_filter('twentynineteen_can_show_post_thumbnail', array($this, 'oftn_hide_featured_image'));
                add_action('wp_enqueue_scripts', array($this, 'oftn_enqueue_dashicons'));

            }

            add_action('load-post.php', array($this, 'oftn_theme_options_metabox_setup'));
            add_action('load-post-new.php', array($this, 'oftn_theme_options_metabox_setup'));
            add_action('after_setup_theme', array($this, 'oftn_add_theme_support'), 11);

            if (is_admin()) {

                add_filter('pre_set_site_transient_update_plugins', 'oftnPremium::check_plugin_update');
                add_filter('plugins_api', 'oftnPremium::get_plugin_information', 10, 3);
                add_filter('after_plugin_row_' . plugin_basename(__FILE__), 'oftnPremium::activate_purchase_js', 10, 3);
                add_action('wp_ajax_oftn_activate_purchase', 'oftnPremium::activate_purchase');
                add_action('admin_notices', 'oftnPremium::upgrade_notice');
                add_filter('upgrader_pre_download', 'oftnPremium::upgrader_pre_download', 10, 3);
                add_filter('plugin_action_links_' . plugin_basename(__FILE__), array($this, 'oftn_add_plugin_action_links'));
                add_filter('plugin_row_meta', 'oftnPremium::plugin_row_meta', 10, 4);

            }

		}

		function oftn_add_plugin_action_links($links) {

			$settings_links = oftnCommon::plugin_action_links(admin_url('customize.php'), true);

			return array_merge($settings_links, $links);

		}

        function oftn_customize_register($wp_customize) {

            $upgrade_link = '<a href="' . oftnCommon::upgrade_link() . '" title="' . __('Upgrade to Options for Twenty Nineteen Premium', 'options-for-twenty-nineteen') . '">' . __('Upgrade to Options for Twenty Nineteen Premium', 'options-for-twenty-nineteen') . '</a>';
            $upgrade_nag = $upgrade_link . __(' to activate this option.', 'options-for-twenty-nineteen');

            $control_label = __('Show Featured Image on Posts Page', 'options-for-twenty-nineteen');
            $control_description = __('Show the featured image selected on the page chosen to be the posts page.', 'options-for-twenty-nineteen');

if (oftnPremium::request_permission()) {

            $wp_customize->add_setting('show_featured_image_on_posts_page', array(
                'default'           => false,
                'type'              => 'theme_mod',
                'transport'         => 'refresh',
                'sanitize_callback' => 'oftnCommon::sanitize_boolean'
            ));
            $wp_customize->add_control('show_featured_image_on_posts_page', array(
                'label'         => $control_label,
                'description'   => $control_description,
                'section'       => 'oftn_header',
                'settings'      => 'show_featured_image_on_posts_page',
                'type'          => 'checkbox'
            ));

} else {

            oftnCommon::add_hidden_control($wp_customize, 'show_featured_image_on_posts_page', 'oftn_header', $control_label, $control_description . ' ' . $upgrade_nag);

}

            $control_label = __('Show Title on Posts Page', 'options-for-twenty-nineteen');
            $control_description = __('Show the page title on the page chosen to be the posts page.', 'options-for-twenty-nineteen');

if (oftnPremium::request_permission()) {

            $wp_customize->add_setting('show_title_on_pasts_page', array(
                'default'           => false,
                'type'              => 'theme_mod',
                'transport'         => 'refresh',
                'sanitize_callback' => 'oftnCommon::sanitize_boolean'
            ));
            $wp_customize->add_control('show_title_on_pasts_page', array(
                'label'         => $control_label,
                'description'   => $control_description,
                'section'       => 'oftn_header',
                'settings'      => 'show_title_on_pasts_page',
                'type'          => 'checkbox'
            ));

} else {

            oftnCommon::add_hidden_control($wp_customize, 'show_title_on_pasts_page', 'oftn_header', $control_label, $control_description . ' ' . $upgrade_nag);

}

            $control_label = __('Move Logo Above Title', 'options-for-twenty-nineteen');
            $control_description = __('Repositions the logo above the site title. This option will override the above setting and show the full size logo.', 'options-for-twenty-nineteen');

if (oftnPremium::request_permission()) {

            $wp_customize->add_setting('reposition_logo', array(
                'default'           => false,
                'type'              => 'theme_mod',
                'transport'         => 'refresh',
                'sanitize_callback' => 'oftnCommon::sanitize_boolean'
            ));
            $wp_customize->add_control('reposition_logo', array(
                'label'         => $control_label,
                'description'   => $control_description,
                'section'       => 'oftn_header',
                'settings'      => 'reposition_logo',
                'type'          => 'checkbox'
            ));

} else {

            oftnCommon::add_hidden_control($wp_customize, 'reposition_logo', 'oftn_header', $control_label, $control_description . ' ' . $upgrade_nag);

}

            $control_label = __('Logo Alignment', 'options-for-twenty-nineteen');
            $control_description = __('Align the logo to the left, center or right. For use with the above option only.', 'options-for-twenty-nineteen');

if (oftnPremium::request_permission()) {

            $wp_customize->add_setting('logo_align', array(
                'default'       => '',
                'type'          => 'theme_mod',
                'transport'     => 'postMessage',
                'sanitize_callback' => 'oftnCommon::sanitize_options'
            ));
            $wp_customize->add_control('logo_align', array(
                'label'         => $control_label,
                'description'   => $control_description,
                'section'       => 'oftn_header',
                'settings'      => 'logo_align',
                'type'          => 'select',
                'choices'       => array(
                    '' => __('Left', 'options-for-twenty-nineteen'),
                    'center' => __('Center', 'options-for-twenty-nineteen'),
                    'right' => __('Right', 'options-for-twenty-nineteen')
                )
            ));

} else {

            oftnCommon::add_hidden_control($wp_customize, 'logo_align', 'oftn_header', $control_label, $control_description . ' ' . $upgrade_nag);

}

            $control_label = __('Remove Logo Circle Mask', 'options-for-twenty-nineteen');
            $control_description = __('Removes the circle effect and allows the logo to be its own shape.', 'options-for-twenty-nineteen');

if (oftnPremium::request_permission()) {

            $wp_customize->add_setting('remove_logo_border_radius', array(
                'default'           => false,
                'type'              => 'theme_mod',
                'transport'         => 'refresh',
                'sanitize_callback' => 'oftnCommon::sanitize_boolean'
            ));
            $wp_customize->add_control('remove_logo_border_radius', array(
                'label'         => $control_label,
                'description'   => $control_description,
                'section'       => 'oftn_header',
                'settings'      => 'remove_logo_border_radius',
                'type'          => 'checkbox'
            ));

} else {

            oftnCommon::add_hidden_control($wp_customize, 'remove_logo_border_radius', 'oftn_header', $control_label, $control_description . ' ' . $upgrade_nag);

}

            $control_label = __('Hide Site Title', 'options-for-twenty-nineteen');
            $control_description = __('Hides the site title.', 'options-for-twenty-nineteen');

if (oftnPremium::request_permission()) {

            $wp_customize->add_setting('hide_site_title', array(
                'default'           => false,
                'type'              => 'theme_mod',
                'transport'         => 'refresh',
                'sanitize_callback' => 'oftnCommon::sanitize_boolean'
            ));
            $wp_customize->add_control('hide_site_title', array(
                'label'         => $control_label,
                'description'   => $control_description,
                'section'       => 'oftn_header',
                'settings'      => 'hide_site_title',
                'type'          => 'checkbox'
            ));

} else {

            oftnCommon::add_hidden_control($wp_customize, 'hide_site_title', 'oftn_header', $control_label, $control_description . ' ' . $upgrade_nag);

}

            $control_label = __('Hide Site Description', 'options-for-twenty-nineteen');
            $control_description = __('Hides the site description.', 'options-for-twenty-nineteen');

if (oftnPremium::request_permission()) {

            $wp_customize->add_setting('hide_site_description', array(
                'default'           => false,
                'type'              => 'theme_mod',
                'transport'         => 'refresh',
                'sanitize_callback' => 'oftnCommon::sanitize_boolean'
            ));
            $wp_customize->add_control('hide_site_description', array(
                'label'         => $control_label,
                'description'   => $control_description,
                'section'       => 'oftn_header',
                'settings'      => 'hide_site_description',
                'type'          => 'checkbox'
            ));

} else {

            oftnCommon::add_hidden_control($wp_customize, 'hide_site_description', 'oftn_header', $control_label, $control_description . ' ' . $upgrade_nag);

}

            $control_label = __('Scroll to Content Arrow', 'options-for-twenty-nineteen');
            $control_description = __('Show an arrow to scroll to the main content in the header.', 'options-for-twenty-nineteen');

if (oftnPremium::request_permission()) {

            $wp_customize->add_setting('scroll_to_content', array(
                'default'           => false,
                'type'              => 'theme_mod',
                'transport'         => 'refresh',
                'sanitize_callback' => 'oftnCommon::sanitize_boolean'
            ));
            $wp_customize->add_control('scroll_to_content', array(
                'label'         => $control_label,
                'description'   => $control_description,
                'section'       => 'oftn_header',
                'settings'      => 'scroll_to_content',
                'type'          => 'checkbox'
            ));

} else {

            oftnCommon::add_hidden_control($wp_customize, 'scroll_to_content', 'oftn_header', $control_label, $control_description . ' ' . $upgrade_nag);

}

            $control_label = __('Bounce Scroll to Content Arrow', 'options-for-twenty-nineteen');
            $control_description = __('Animates the scroll down arrow in the header.', 'options-for-twenty-nineteen');

if (oftnPremium::request_permission() == true) {

            $wp_customize->add_setting('bounce_scroll_to_content_arrow', array(
                'default'       => false,
                'type'          => 'theme_mod',
                'transport'     => 'refresh',
                'sanitize_callback' => 'oftnCommon::sanitize_boolean'
            ));
            $wp_customize->add_control('bounce_scroll_to_content_arrow', array(
                'label'         => $control_label,
                'description'   => $control_description,
                'section'       => 'oftn_header',
                'settings'      => 'bounce_scroll_to_content_arrow',
                'type'          => 'checkbox'
            ));

} else {

            oftnCommon::add_hidden_control($wp_customize, 'bounce_scroll_to_content_arrow', 'oftn_header', $control_label, $control_description . ' ' . $upgrade_nag);

}

            if (file_exists(ABSPATH . WPINC . '/css/dashicons.css')) {

                $dashicons_css = explode("\n", implode('', file(ABSPATH . WPINC . '/css/dashicons.css')));
                $dashicons = array('' => __('Default Theme Arrow', 'options-for-twenty-nineteen'));

                foreach ($dashicons_css as $css_entry) {

                    if (strlen($css_entry) > 10 && substr($css_entry, 0, 11) === '.dashicons-') {

                        $dashicon_class = sanitize_key(preg_replace(array('/^.dashicons-/', '/:before {$/'), '', $css_entry));
                        $dashicons[$dashicon_class] = __(ucwords(str_replace('-', ' ', $dashicon_class)), 'options-for-twenty-nineteen');

                    }

                }

                asort($dashicons);

                $control_label = __('Scroll to Content Dashicon', 'options-for-twenty-nineteen');
                $control_description = sprintf(wp_kses(__('Choose your own <a href="%s">dashicon</a> for the arrow that scrolls to the main content.', 'options-for-twenty-nineteen'), array('a' => array('href' => array()))), esc_url('https://developer.wordpress.org/resource/dashicons/'));

                if (oftnPremium::request_permission()) {

                    $wp_customize->add_setting('scroll_to_content_dashicon', array(
                        'default'       => '',
                        'type'          => 'theme_mod',
                        'transport'     => 'refresh',
                        'sanitize_callback' => 'oftnCommon::sanitize_options'
                    ));
                    $wp_customize->add_control('scroll_to_content_dashicon', array(
                        'label'         => $control_label,
                        'description'   => $control_description,
                        'section'       => 'oftn_header',
                        'settings'      => 'scroll_to_content_dashicon',
                        'type'          => 'select',
                        'choices'       => $dashicons
                    ));

                } else {

                    oftnCommon::add_hidden_control($wp_customize, 'scroll_to_content_dashicon', 'oftn_header', $control_label, $control_description . ' ' . $upgrade_nag);

                }

            }

            $control_label = __('Implement Yoast SEO Breadcrumbs', 'options-for-twenty-nineteen');

            $query_args = array(
                's' => 'wordpress-seo',
                'tab' => 'search',
                'type' => 'term'
            );

            $control_description = sprintf(wp_kses(__('Inject <a href="%s">Yoast SEO</a> breadcrumbs above and / or below single post and page content.', 'options-for-twenty-nineteen'), array('a' => array('href' => array()))), esc_url(add_query_arg($query_args, admin_url('plugin-install.php'))));

if (oftnPremium::request_permission()) {

            $wp_customize->add_setting('implement_yoast_breadcrumbs', array(
                'default'       => '',
                'type'          => 'theme_mod',
                'transport'     => 'refresh',
                'sanitize_callback' => 'oftnCommon::sanitize_options'
            ));
            $wp_customize->add_control('implement_yoast_breadcrumbs', array(
                'label'         => $control_label,
                'description'   => $control_description,
                'section'       => 'oftn_content',
                'settings'      => 'implement_yoast_breadcrumbs',
                'type'          => 'select',
                'choices'       => array(
                    '' => __('Disable Breadcrumbs', 'options-for-twenty-nineteen'),
                    'top' => __('Above Content', 'options-for-twenty-nineteen'),
                    'bottom' => __('Below Content', 'options-for-twenty-nineteen'),
                    'both' => __('Above and Below Content', 'options-for-twenty-nineteen')
                )
            ));

} else {

            oftnCommon::add_hidden_control($wp_customize, 'implement_yoast_breadcrumbs', 'oftn_content', $control_label, $control_description . ' ' . $upgrade_nag);

}

            $control_label = __('Show Archive Description', 'options-for-twenty-nineteen');
            $control_description = __('Show the tag or category description on archive pages.', 'options-for-twenty-nineteen');

if (oftnPremium::request_permission()) {

            $wp_customize->add_setting('show_archive_description', array(
                'default'           => false,
                'type'              => 'theme_mod',
                'transport'         => 'refresh',
                'sanitize_callback' => 'oftnCommon::sanitize_boolean'
            ));
            $wp_customize->add_control('show_archive_description', array(
                'label'         => $control_label,
                'description'   => $control_description,
                'section'       => 'oftn_content',
                'settings'      => 'show_archive_description',
                'type'          => 'checkbox'
            ));

} else {

            oftnCommon::add_hidden_control($wp_customize, 'show_archive_description', 'oftn_content', $control_label, $control_description . ' ' . $upgrade_nag);

}

            $control_label = __('Show Full Content in Archive', 'options-for-twenty-nineteen');
            $control_description = __('Show the full post content rather than an excerpt in archive pages.', 'options-for-twenty-nineteen');

if (oftnPremium::request_permission()) {

            $wp_customize->add_setting('show_full_content_in_archive', array(
                'default'           => false,
                'type'              => 'theme_mod',
                'transport'         => 'refresh',
                'sanitize_callback' => 'oftnCommon::sanitize_boolean'
            ));
            $wp_customize->add_control('show_full_content_in_archive', array(
                'label'         => $control_label,
                'description'   => $control_description,
                'section'       => 'oftn_content',
                'settings'      => 'show_full_content_in_archive',
                'type'          => 'checkbox'
            ));

} else {

            oftnCommon::add_hidden_control($wp_customize, 'show_full_content_in_archive', 'oftn_content', $control_label, $control_description . ' ' . $upgrade_nag);

}

            $control_label = __('Activate Jetpack Infinite Scroll', 'options-for-twenty-nineteen');
            $control_description = __('Turns on infinite scroll when using Jetpack, remember not to use footer widgets as they won\'t be accessible.', 'options-for-twenty-nineteen');

if (oftnPremium::request_permission()) {

            $wp_customize->add_setting('infinite_scroll', array(
                'default'           => false,
                'type'              => 'theme_mod',
                'transport'         => 'refresh',
                'sanitize_callback' => 'oftnCommon::sanitize_boolean'
            ));
            $wp_customize->add_control('infinite_scroll', array(
                'label'         => $control_label,
                'description'   => $control_description,
                'section'       => 'oftn_content',
                'settings'      => 'infinite_scroll',
                'type'          => 'checkbox'
            ));

} else {

            oftnCommon::add_hidden_control($wp_customize, 'infinite_scroll', 'oftn_content', $control_label, $control_description . ' ' . $upgrade_nag);

}

            $control_label = __('Replace "Powered by" Text', 'options-for-twenty-nineteen');
            $control_description = __('Provide alternate text to replace "Proudly powered by Wordpress".', 'options-for-twenty-nineteen');

if (oftnPremium::request_permission()) {

            $wp_customize->add_setting('replace_powered_by_wordpress', array(
                'default'           => '',
                'type'              => 'theme_mod',
                'transport'         => 'refresh',
                'sanitize_callback' => 'wp_kses_post'
            ));
            $wp_customize->add_control('replace_powered_by_wordpress', array(
                'label'         => $control_label,
                'description'   => $control_description,
                'section'       => 'oftn_footer',
                'settings'      => 'replace_powered_by_wordpress',
                'type'          => 'text'
            ));

} else {

            oftnCommon::add_hidden_control($wp_customize, 'replace_powered_by_wordpress', 'oftn_footer', $control_label, $control_description . ' ' . $upgrade_nag);

}

            $control_label = __('Featured Content Layout', 'options-for-twenty-nineteen');
            $control_description = __('Show the Featured Content as posts or a grid of featured images.', 'options-for-twenty-nineteen');

if (oftnPremium::request_permission()) {

            $wp_customize->add_setting('featured_content_layout', array(
                'default'           => 'posts',
                'type'              => 'theme_mod',
                'transport'         => 'refresh',
                'sanitize_callback' => 'oftnCommon::sanitize_boolean'
            ));
            $wp_customize->add_control('featured_content_layout', array(
                'label'         => $control_label,
                'description'   => $control_description,
                'section'       => 'featured_content',
                'settings'      => 'featured_content_layout',
                'type'          => 'select',
                'choices'       => array(
                    'grid' => 'Grid',
                    'posts' => 'Posts'
                ),
            ));

} else {

            oftnCommon::add_hidden_control($wp_customize, 'featured_content_layout', 'featured_content', $control_label, $control_description . ' ' . $upgrade_nag);

}

        }

        function oftn_header_output() {

?>
<!--Customizer CSS-->
<style type="text/css">
<?php

if (oftnPremium::request_permission()) {

            if ((is_page() || is_single()) && get_post_meta(get_the_ID(), 'oftn_hide_title', true) == '1') {

?>
#main article:first-of-type .entry-title {
    display: none;
}
<?php

            }

            if (get_theme_mod('show_featured_image_on_posts_page') && is_home() && get_option('page_for_posts')) {

                add_action('get_template_part_template-parts/header/site', array($this, 'oftn_show_featured_image_on_posts_page'));

            }

            if (!get_theme_mod('show_featured_image_on_posts_page') && get_theme_mod('show_title_on_pasts_page') && is_home() && get_option('page_for_posts')) {

                add_action('get_template_part_template-parts/header/site', array($this, 'oftn_show_title_on_pasts_page'));

            }

            if (get_theme_mod('reposition_logo')) {

?>
.site-logo {
	position: relative;
	margin-bottom: calc(.66 * 1rem);
	right: auto;
}
.site-logo .custom-logo-link {
	width: auto;
	display: inline-block;
}
.site-logo .custom-logo-link .custom-logo {
	display: block;
}
<?php

                $mod = get_theme_mod('logo_align');

                if ($mod == 'center') {

?>
.site-logo {
	text-align: center;
}
<?php

                } elseif ($mod == 'right') {

?>
.site-logo {
	text-align: right;
}
<?php

                }

            }

            oftnCommon::generate_css('.site-logo .custom-logo-link', 'border-radius', 'remove_logo_border_radius', '', '', '0');

            if (get_theme_mod('scroll_to_content')) {

                add_action('wp_footer', array($this, 'oftn_inject_scroll_down'));

?>
.site-header.featured-image .entry-title {
padding-right: 1em;
}
.menu-scroll-down {
    position: absolute;
	right: 0;
	bottom: 1em;
	text-align: right;
	margin-right: 1em;
	z-index: 9;
}
.menu-scroll-down svg.svg-icon, .menu-scroll-down .dashicons {
	width: 37.125px;
	height: 37.125px;
}
.menu-scroll-down .dashicons {
    font-size: 1.6875em;
	display: block;
	margin-bottom: 0.6em;
}
@media only screen and (min-width: 768px) {
    .menu-scroll-down {
    	bottom: 3em;
	    margin-right: calc(10% + 60px);
    }
    .menu-scroll-down svg.svg-icon, .menu-scroll-down .dashicons {
        font-size: 2.25em;
	    width: 49.5px;
	    height: 49.5px;
    }
}
<?php

                if (get_theme_mod('bounce_scroll_to_content_arrow')) {

?>
.menu-scroll-down {
  animation: bounce 2s infinite;
}
@keyframes bounce {
  0%, 20%, 50%, 80%, 100% {
    transform: translateY(0);
  }
  40% {
    transform: translateY(-30px);
  }
  60% {
    transform: translateY(-15px);
  }
}
<?php
                }

            }

            if (get_theme_mod('implement_yoast_breadcrumbs') && !is_front_page() && (is_single() || is_page())) {

                add_action('get_template_part_template-parts/footer/footer', array($this, 'oftn_implement_yoast_breadcrumbs'));

            }

            if (get_theme_mod('show_archive_description') && (is_category() || is_tag())) {

                add_action('get_template_part_template-parts/content/content', array($this, 'oftn_show_archive_description'));

            }

            if (get_theme_mod('show_full_content_in_archive') && is_archive()) {

                add_filter('get_the_excerpt', array($this, 'oftn_show_full_content_in_archive'), 10, 2);

            }

            if (get_theme_mod('replace_powered_by_wordpress')) {

                add_action('wp_footer', array($this, 'oftn_replace_imprint'));

            }

}

?>
</style> 
<!--/Customizer CSS-->
<?php

        }

        function oftn_show_featured_image_on_posts_page() {

?>
<div class="site-featured-image">
<figure class="post-thumbnail">
<?= get_the_post_thumbnail(get_option('page_for_posts')); ?>
</figure><!-- .post-thumbnail --><?php

            if (get_theme_mod('show_title_on_pasts_page')) {

?>
<div class="entry-header">
<h1 class="entry-title"><?= get_the_title(get_option('page_for_posts')); ?></h1>
</div><!-- .entry-header --><?php

            }

?>
</div>
<script type="text/javascript">
    (function() {
        document.getElementsByClassName('site-header')[0].appendChild(document.getElementsByClassName('site-featured-image')[0]);
        document.getElementsByClassName('site-header')[0].classList.add('featured-image');
    })();
</script>
<?php

        }


        function oftn_hide_featured_image($permission) {

            if (get_post_meta(get_the_ID(), 'oftn_hide_featured_image', true) == '1' && (is_page() || is_single())) {

                return false;

            } else {

                return $permission;

            }

        }

        function oftn_show_title_on_pasts_page() {

?>
<header class="blog-title entry">
<div class="entry-header">
<h1 class="entry-title"><?= get_the_title(get_option('page_for_posts')); ?></h1>
</div><!-- .entry-header -->
</header>
<script type="text/javascript">
    document.addEventListener("DOMContentLoaded", () => {
        document.getElementsByClassName('site-main')[0].insertBefore(document.getElementsByClassName('blog-title')[0], document.getElementsByClassName('site-main')[0].firstChild);
    });
</script>
<?php

        }

        function oftn_theme_options_metabox_setup() {

            add_action('add_meta_boxes', array($this, 'oftn_add_theme_options_metabox'));

            if (oftnPremium::request_permission()) {

                add_action('save_post', array($this, 'oftn_save_theme_options_meta'), 10, 2);

            }

        }

        function oftn_add_theme_options_metabox() {

            add_meta_box('oftn_meta_box', __('Theme Options', 'options-for-twenty-nineteen'), array($this, 'oftn_render_theme_options_metabox'), array('post', 'page'), 'side');

        }

        function oftn_render_theme_options_metabox($post) {

            $upgrade_link = '<a href="' . oftnCommon::upgrade_link() . '" title="' . __('Upgrade Options for Twenty Nineteen', 'options-for-twenty-nineteen') . '">' . __('Upgrade Options for Twenty Nineteen', 'options-for-twenty-nineteen') . '</a>';

            if (get_option('oftn_purchased') == false) {

                if (get_option('oftn_trial_date') && time() < (strtotime('+1 week', get_option('oftn_trial_date')))) {

                    $expiring_in = ceil(abs((strtotime('+1 week', get_option('oftn_trial_date'))) - time())/60/60/24);
                    $expiring_text = '<p><span class="attention">' . sprintf(_n('Options for Twenty Nineteen plugin trial expires in less than %s day!', 'Options for Twenty Nineteen plugin trial expires in less than %s days!', $expiring_in, 'options-for-twenty-nineteen'), $expiring_in) . '</span>';
                    echo('<strong>' . $expiring_text . '</strong>' . ' ' . $upgrade_link . ' ' . __('to keep using all the options after that time.', 'options-for-twenty-nineteen') . '</p>');

                }

            }

            wp_nonce_field('options-for-twenty-nineteen', 'oftn-meta-nonce');

            if (oftnPremium::request_permission()) {

?>
<input type="checkbox" name="oftn-hide-featured-image" id="oftn-hide-featured-image" value="1" <?php checked(get_post_meta($post->ID, 'oftn_hide_featured_image', true), '1' ); ?> />
<label for="oftn-hide-featured-image"><?= __('Hide featured image', 'options-for-twenty-nineteen'); ?></label>
<br />
<input type="checkbox" name="oftn-hide-title" id="oftn-hide-title" value="1" <?php checked(get_post_meta($post->ID, 'oftn_hide_title', true), '1' ); ?> />
<label for="oftn-hide-title"><?= __('Hide title', 'options-for-twenty-nineteen'); ?></label>
<?php

            } else {

?>

<p><?= $upgrade_link; ?> <?= __('to hide featured images or titles.', 'options-for-twenty-nineteen'); ?></p>

<?php
            }

        }

        function oftn_save_theme_options_meta($post_id, $post) {

            if (!isset( $_POST['oftn-meta-nonce'] ) || !wp_verify_nonce($_POST['oftn-meta-nonce'], 'options-for-twenty-nineteen')) {

                return;

            }

            $post_type = get_post_type_object( $post->post_type );

            if (!current_user_can($post_type->cap->edit_post, $post_id)) {

                return;

            }

            if (isset($_POST['oftn-hide-featured-image']) && $_POST['oftn-hide-featured-image'] == '1') {

                update_post_meta($post_id, 'oftn_hide_featured_image', 1);

            } else {

                delete_post_meta($post_id, 'oftn_hide_featured_image');

            }

            if (isset($_POST['oftn-hide-title']) && $_POST['oftn-hide-title'] == '1') {

                update_post_meta($post_id, 'oftn_hide_title', 1);

            } else {

                delete_post_meta($post_id, 'oftn_hide_title');

            }

        }

        function oftn_add_theme_support() {

if (oftnPremium::request_permission()) {

            if (get_theme_mod('infinite_scroll')) {

                add_theme_support('infinite-scroll', array(
                    'container' => 'main',
                    'render'    => array($this, 'oftn_infinite_scroll_render')
                ));

            }

            add_theme_support('featured-content', array(
                'featured_content_filter'  => 'twentynineteen_get_featured_posts',
                'max_posts'                => 20,
                'post_types'               => array('post', 'page')
            ));

            add_action('get_template_part_template-parts/footer/footer', array($this, 'oftn_show_featured_posts'));

}

        }

        function oftn_infinite_scroll_render() {

            while(have_posts()) {

                the_post();

                if (is_archive()) {

                    get_template_part('template-parts/content/content', 'excerpt');

                } else {

                    get_template_part('template-parts/content/content');

                }

            }

        }

        function oftn_show_featured_posts() {

            if (is_front_page()) {

                $featured_posts = $this->oftn_get_featured_posts();

                if ($featured_posts) {

                    global $post;

?>
<div id="featured-content" class="featured-content">
	<div class="featured-content-inner">
<?php

                    if (get_theme_mod('featured_content_layout') == 'grid') { echo '<ul class="wp-block-gallery columns-3 is-cropped">'; }

                	foreach ((array) $featured_posts as $order => $post) {

                		setup_postdata($post);

                        if (get_theme_mod('featured_content_layout') == 'grid') {

?>
    <li class="blocks-gallery-item">
        <figure>
<?php

                            if (twentynineteen_can_show_post_thumbnail()) {

?>
            <a href="<?php the_permalink(); ?>" aria-hidden="true" tabindex="-1"><?php the_post_thumbnail('large'); ?></a>
<?php

                            }

                            the_title( '<figcaption>', '</figcaption>' );

?>
        </figure>
    </li>
<?php

                        } else {

                            get_template_part('template-parts/content/content');

                        }

                    }

                    if (get_theme_mod('featured_content_layout') == 'grid') { echo '</ul>'; }

?>
	</div><!-- .featured-content-inner -->
</div><!-- #featured-content .featured-content -->
<script type="text/javascript">
    (function() {
        document.getElementsByClassName('site-main')[0].insertBefore(document.getElementsByClassName('featured-content')[0], document.getElementsByClassName('site-main')[0].firstChild);
    })();
</script>
<?php

            		wp_reset_postdata();

                }

            }

        }

        function oftn_get_featured_posts() {

            return apply_filters('twentynineteen_get_featured_posts', array());

        }

        function oftn_inject_scroll_down() {

?>

<a href="#content" class="menu-scroll-down"><span class="screen-reader-text"><?= __('Scroll down to content', 'options-for-twenty-nineteen'); ?></span><?php

                if (get_theme_mod('scroll_to_content_dashicon')) {

?>
<span alt="Menu" class="dashicons dashicons-dashicons dashicons-<?= get_theme_mod('scroll_to_content_dashicon'); ?>"></span><?php

                } else {

                    echo twentynineteen_get_icon_svg('keyboard_arrow_down');

                }

?>
</a>
<script type="text/javascript">
    (function () {
        var scrollArrow = document.getElementsByClassName('menu-scroll-down')[0];
        if (typeof document.getElementsByClassName('site-featured-image')[0] !== 'undefined') {
            var featuredImage = document.getElementsByClassName('site-featured-image')[0];
            featuredImage.appendChild(scrollArrow);
            scrollArrow.onclick = function(event) {
                event.preventDefault()
                scrollTo(document.documentElement, document.getElementById('content').offsetTop, 300);
            }
            function scrollTo(element, to, duration) {
                if (duration <= 0) return;
                var difference = to - element.scrollTop;
                var perTick = difference / duration * 10;
                setTimeout(function() {
                    element.scrollTop = element.scrollTop + perTick;
                    if (element.scrollTop === to) return;
                    scrollTo(element, to, duration - 10);
                }, 10);
            }
        } else {
            scrollArrow.parentNode.removeChild(scrollArrow);
        }
    }());
</script>
<?php

        }

        function oftn_enqueue_dashicons() {

            if (!is_admin() && get_theme_mod('scroll_to_content_dashicon')) {

                wp_enqueue_style('dashicons');

            }

        }

        function oftn_implement_yoast_breadcrumbs() {

            if (function_exists('yoast_breadcrumb')) {

                yoast_breadcrumb('<p id="breadcrumbs">','</p>');

?>
<script type="text/javascript">
    var oftnYoast = document.getElementById("breadcrumbs");<?php

                if (get_theme_mod('implement_yoast_breadcrumbs') == 'top' || get_theme_mod('implement_yoast_breadcrumbs') == 'both') {

?>
    var oftnYoastTop = oftnYoast.cloneNode(true);
    document.getElementsByClassName('site-main')[0].insertBefore(oftnYoastTop, document.getElementsByClassName('site-main')[0].firstChild);<?php

                }

                if (get_theme_mod('implement_yoast_breadcrumbs') == 'bottom' || get_theme_mod('implement_yoast_breadcrumbs') == 'both') {

?>
    var oftnYoastBottom = oftnYoast.cloneNode(true);
    document.getElementsByClassName('site-main')[0].appendChild(oftnYoastBottom);<?php

                }

?>
    oftnYoast.parentNode.removeChild(oftnYoast);
</script>
<?php

            }

        }

        function oftn_show_archive_description() {

            if (!$this->oftn_archive_description_shown) {

                the_archive_description( '<div class="taxonomy-description">', '</div>' );

?>
<script type="text/javascript">
    (function() {
        document.getElementsByClassName('page-header')[0].appendChild(document.getElementsByClassName('taxonomy-description')[0]);
    })();
</script>
<?php

                $this->oftn_archive_description_shown = true;

            }

        }

        function oftn_show_full_content_in_archive($post_excerpt, $post) {

            return $post->post_content;

        }

        function oftn_replace_imprint() {

?>
<script type="text/javascript">
    (function() {
        var el = document.getElementsByClassName('imprint')[0];
        var newEl = document.createElement('span');
        newEl.innerHTML = '<?= addslashes(get_theme_mod('replace_powered_by_wordpress')); ?>.';
        newEl.classList.add("imprint");
        el.parentNode.replaceChild(newEl, el);
    })();
</script>
<?php

        }

	}

    if (!class_exists('oftnPremium')) {

        require_once(dirname(__FILE__) . '/includes/class-oftn-premium.php');

    }

    function options_for_twenty_nineteen_premium_init() {

        if (class_exists('options_for_twenty_nineteen_class') && class_exists('oftnCommon')) {

            oftnPremium::start_trial();
            $options_for_twenty_nineteen_premium_object = new options_for_twenty_nineteen_premium_class();

        } else {

            add_action('admin_notices', 'oftnPremium::free_plugin_notice');

        }

    }

    add_action('plugins_loaded', 'options_for_twenty_nineteen_premium_init');

}

?>
