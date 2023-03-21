<?php
/*
Plugin Name: WapuuGotchi Collection Preview
Plugin URI: https://coachbirgit.com/wapuugotchi-collection-preview
Description: Fetches data and displays items from the WapuuGotchi API using Flexbox CSS in a card style. Use the shortcode <pre>[wapuugotchi_collection_preview]</pre>
Version: 1.0
Author: CoachBirgit
Author URI: https://coachbirgit.com
License: GPLv2 or later
Text Domain: wapuugotchi-collection-preview
*/

// Enqueue styles
function wapuugotchi_collection_preview_enqueue_styles() {
    wp_enqueue_style('wapuugotchi-collection-preview-styles', plugin_dir_url(__FILE__) . 'style.css');
}
add_action('wp_enqueue_scripts', 'wapuugotchi_collection_preview_enqueue_styles');

// Add the shortcode [wapuugotchi_collection_preview]
function wapuugotchi_collection_preview_shortcode($atts) {
    // Fetch the JSON data from the API
    $api_url = 'https://api.wapuugotchi.com/collection';
    $response = wp_remote_get($api_url);

    if (is_wp_error($response)) {
        return 'Error: ' . $response->get_error_message();
    }

    $collections = json_decode(wp_remote_retrieve_body($response), true);

    if (!$collections) {
        return 'Error: Invalid JSON data';
    }

    // Start building the output HTML
    $output = '<div class="wapuugotchi-collection-preview">';

    foreach ($collections['collections'] as $collection) {
        $output .= '<div class="collection">';
        $output .= '<h3>' . esc_html($collection['caption']) . '</h3>';
        $output .= '<div class="items">';

        foreach ($collection['items'] as $item) {
            $output .= '<div class="item">';
            $output .= '<img src="' . esc_url($item['preview']) . '" alt="' . esc_attr($item['meta']['name']) . '">';
            $output .= '<h4>' . esc_html($item['meta']['name']) . '</h4>';
            $output .= '<p>' . esc_html($item['meta']['description']) . '</p>';
            $output .= '</div>';
        }

        $output .= '</div>'; // Close items
        $output .= '</div>'; // Close collection
    }

    $output .= '</div>'; // Close wapuugotchi-collection-preview

    return $output;
}
add_shortcode('wapuugotchi_collection_preview', 'wapuugotchi_collection_preview_shortcode');
