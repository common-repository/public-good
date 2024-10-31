<?php
/**
 * @package Public_Good
 * @version 1.5.1
 */
/*
Plugin Name: Public Good
Plugin URI: http://wordpress.org/plugins/public-good/
Description: This plugin lets you embed widgets in your content that enable your readers to take action (donate, volunteer, call their representatives, learn more, etc.) on the causes referenced in your posts and articles.
Author: Public Good
Version: 1.5.1
Author URI: https://publicgood.com/
*/

defined('ABSPATH') or die('No script kiddies please!');

class Public_Good_Class
{
    public static $opt_name_source = "public_good_source";
    public static $opt_name_location = "public_good_location";
    public static $opt_name_size = "public_good_size";

    public function __construct()
    {
        add_shortcode('takeaction', array( $this, 'btn_short_code' ));
        add_action('wp_enqueue_scripts', array( $this, 'action_wp_enqueue_scripts' ));
        add_action('init', array( $this, 'btn_add_oembed_provider' ));
        add_filter('wp_kses_allowed_html', array( $this, 'public_good_filter_allowed_html' ) );
    }

    function public_good_filter_allowed_html($allowed){

        $allowed['div']['data-pgs-partner-id'] = true;
        $allowed['div']['data-pgs-location'] = true;
        $allowed['div']['data-pgs-target-type'] = true;
        $allowed['div']['data-pgs-target-id'] = true;
        $allowed['div']['data-pgs-align'] = true;
		$allowed['div']['data-pgs-custom-script'] = true;
		$allowed['div']['data-pgs-locale'] = true;

        return $allowed;
    }

    public function btn_add_oembed_provider($atts)
    {
        wp_oembed_add_provider('https://publicgood.com/*', 'https://api.pgs.io/oembed', false);
    }

    public function action_wp_enqueue_scripts()
    {
        wp_enqueue_script("takeactionjs", "https://assets.publicgood.com/pgm/v1/dpg.js", array(), "", true);
    }

    public function btn_short_code($atts)
    {
        $cssClass = ($this->verifyType(esc_attr($atts["type"])) ? (esc_attr($atts["type"]) === "button" ? "btn" : esc_attr($atts["type"])) : "flex");

        // Maintain old short code attributes for backward compatibility
		$partnerId = esc_attr($atts["partner-id"]) ? esc_attr($atts["partner-id"]) : esc_attr($atts["source"]);
		$targetType = esc_attr($atts["target-type"]) ? esc_attr($atts["target-type"]) : esc_attr($atts["targettype"]);
		$targetId = esc_attr($atts["target-id"]) ? esc_attr($atts["target-id"]) : esc_attr($atts["targetid"]);


        // Return HTML for the button.
        return '<div class="pgs-dpg-'
            . $cssClass
            .'" data-pgs-partner-id="'
            . $partnerId
            .'" data-pgs-target-type="'
            . $targetType
            .'" data-pgs-target-id="'
            . $targetId
            .'" data-pgs-location="'
            . esc_attr($atts["location"])
            .'" data-pgs-align="'
            . esc_attr($atts["align"])
            .'" ></div>';
    }

    function verifyType($type) {
        if ($type == 'button' || $type == 'card' || $type == 'chat') {
            return true;
        }

        return false;
    }

}

global $public_good_class;
$public_good_class = new Public_Good_Class;
?>