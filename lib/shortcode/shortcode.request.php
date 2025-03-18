<?php

function Zotpress_Static_Bibliography_shortcode_request($zpr = false, $checkcache = false)
{
    global $wpdb;

    // Check cache
    $cache_key = 'zotpress_static_' . md5(serialize($zpr));
    $cached_data = get_zotpress_cache($cache_key);
    if ($cached_data) {
        return $cached_data;
    }

    // Prep request vars
    if ($zpr === false || $zpr == '') {
        $zpr = Zotpress_prep_ajax_request_vars($wpdb);
    }

    // Set up request queue
    $zp_request_queue = array();

    // Set up Zotpress request
    $zp_import_contents = new ZotpressRequest();

    // Set up request meta
    $zp_request_meta = array("request_last" => (int)$zpr["request_last"], "request_next" => 0);

    // Set up data variable
    $zp_all_the_data = array();

    // Format Zotero request URL
    // Account for items + collection_id
    if ($zpr["item_type"] == "items" && $zpr["collection_id"] !== false) {
        // ...existing code...
    }

    // Account for items + zp_tag_id
    if ($zpr["item_type"] == "items" && $zpr["zp_tag_id"] !== false) {
        // ...existing code...
    }

    // Account for collection_id + get_top
    if ($zpr["get_top"] && $zpr["collection_id"] !== false) {
        // ...existing code...
    }

    // Account for tag display - let's limit it
    if ($zpr["is_dropdown"] === true && $zpr["item_type"] == "tags") {
        // ...existing code...
    }

    // Account for $zpr["maxresults"]
    if ($zpr["maxresults"] !== false) {
        // ...existing code...
    }

    // Build request URLs
    if ($zp_request_queue !== []) {
        foreach ($zp_request_queue as $api_user_id => $zp_request_data) {
            $zp_request_queue = Zotpress_prep_request_URL($wpdb, $zpr, $zp_request_queue, $api_user_id, $zp_request_data);
        }
    } else {
        $zp_request_queue = Zotpress_prep_request_URL($wpdb, $zpr, $zp_request_queue);
    }

    // Request the data
    $temp_data = $zp_import_contents->get_request_contents($zp_request_queue);

    // Format the data
    if (count($temp_data) > 0) {
        foreach ($temp_data as $item) {
            // Process each item and generate HTML output
            $zp_all_the_data[] = $item;
        }
    }

    // Finish and output the data
    $zp_output = '';
    if (count($zp_all_the_data) > 0 && $zp_all_the_data != "") {
        $zp_output .= '<div class="zp-List">';
        foreach ($zp_all_the_data as $zp_citation) {
            // Generate HTML for each citation
            $zp_output .= '<div class="zp-Citation">' . $zp_citation->data->title . '</div>';
        }
        $zp_output .= '</div>';
    }

    // Set cache
    set_zotpress_cache($cache_key, $zp_output);

    return $zp_output;
}
