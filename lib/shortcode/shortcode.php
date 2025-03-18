<?php

require(__DIR__ . '/shortcode.static.functions.php');
require(__DIR__ . '/shortcode.request.php');
require(__DIR__ . '/../cache.php'); // Include the cache management file

function Zotpress_Static_Bibliography_func($atts)
{
    global $wpdb;

    $zp_atts = shortcode_atts(array(
        'userid' => false,
        'nickname' => false,
        'author' => false,
        'year' => false,
        'item_type' => 'items',
        'collection_id' => false,
        'item_key' => false,
        'tag_name' => false,
        'style' => false,
        'limit' => false,
        'sortby' => 'default',
        'order' => false,
        'title' => 'no',
        'showimage' => 'no',
        'showtags' => 'no',
        'downloadable' => 'no',
        'shownotes' => false,
        'abstracts' => 'no',
        'citeable' => false,
        'target' => false,
        'urlwrap' => false,
        'highlight' => false,
        'forcenumber' => false
    ), $atts);

    $zpr = Zotpress_prep_ajax_request_vars($wpdb, $zp_atts);

    $zp_output = '<div class="zp-Zotpress">';
    $zp_output .= Zotpress_Static_Bibliography_shortcode_request($zpr, true);
    $zp_output .= '</div>';

    return $zp_output;
}
add_shortcode('zotpress_static', 'Zotpress_Static_Bibliography_func');