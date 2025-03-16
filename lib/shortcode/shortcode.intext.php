<?php

require_once(dirname(__FILE__) . '/../utils.php');

function ZotpressStatic_zotpressInText ($atts)
{
    /*
    *   GLOBAL VARIABLES
    *
    *   $GLOBALS['zp_shortcode_instances'] {instantiated in zotpress.php}
    *
    */

    extract(shortcode_atts(array(

        'item' => false,
        'items' => false,

        'pages' => false,
        'format' => "(%a%, %d%, %p%)",
		'brackets' => false,
        'etal' => false, // default (false), yes, no
        'separator' => false, // default (comma), semicolon
        'and' => false, // ampersand [default], and, comma, comma-amp, comma-and

        'userid' => false,
        'api_user_id' => false,
        'nickname' => false,
        'nick' => false

    ), $atts));
    
    global $wpdb, $post;
    
    // Check for item or items
    if (!$item && !$items) return;
    
    // Set up item/s
    if ($item && !$items) $items = $item;
    
    // PREPARE ATTRIBUTES
    if ( $items )
        $items = zpStatic_StripQuotes( str_replace(" ", "", $items ));
    elseif ( $item )
        $items = zpStatic_StripQuotes( str_replace(" ", "", $item ));

    $pages = zpStatic_StripQuotes( $pages );
    $format = zpStatic_StripQuotes( $format );
    $brackets = zpStatic_StripQuotes( $brackets );

    $etal = zpStatic_StripQuotes( $etal );
    if ( $etal == "default" ) $etal = false;

    $separator = zpStatic_StripQuotes( $separator );
    if ( $separator == "default" ) $separator = false;

    $and = zpStatic_StripQuotes( $and );
    if ( $and == "default" ) $and = false;

    if ( $userid ) $api_user_id = zpStatic_StripQuotes( $userid );
    if ( $nickname ) $nickname = zpStatic_StripQuotes( $nickname );
    if ( $nick ) $nickname = zpStatic_StripQuotes( $nick );
    
    // Set up api_user_id
    if ($nickname && !$api_user_id) {
        $api_user_id = $wpdb->get_var(
            $wpdb->prepare(
                "SELECT api_user_id FROM ".$wpdb->prefix."zotpress WHERE nickname = %s",
                $nickname
            )
        );
    } else if (!$api_user_id) {
        $api_user_id = $wpdb->get_var(
            "SELECT api_user_id FROM ".$wpdb->prefix."zotpress ORDER BY id DESC LIMIT 1"
        );
    }
    
    // Set up all_page_instances
    $all_page_instances = array();
    $all_page_instances_str = "";
    
    // Add api_user_id if not there
    if (strpos($items, ":") === false) {
        if (strpos($items, "{") !== false) {
            $items = str_replace("{", "{".$api_user_id.":", $items);
        } elseif (strpos($items, ",") !== false) {
            $items = "{".$api_user_id.":" . str_replace(",", "},{".$api_user_id.":", $items)."}";
        } else // assume unformatted and single, so place at front
        {
            $items = "{".$api_user_id.":".$items."}";
        }
    }
    
    // Determine page instances and where
    $temp_items = explode("},{", $items);
    $all_np = true;
    
    foreach ($temp_items as $id => $item) {
        if (preg_match('/,(.)+/', $item, $match) == 1) {
            $temp_arr_page_ins = [];
            
            // First, check for multiple, non-contiguous page numbers in parentheses
            if (preg_match('/(.)+/', $match[0], $matchm) == 1)
                $temp_arr_page_ins = array_filter(explode(',', str_replace('(', '', str_replace(')', '', $match[0]))));
            else
                $temp_arr_page_ins = $match[0];
            
            // Then go through all and format
            foreach ($temp_arr_page_ins as $pid => $page_ins) {
                if ($page_ins == '')
                    continue;
                
                $temp_arr_page_ins[$pid] = str_replace("}", "", str_replace(",", "", $page_ins));
            }
            $all_page_instances[$id] = $temp_arr_page_ins;
            
            if (strlen($all_page_instances_str) > 0)
                $all_page_instances_str = $all_page_instances_str . '--';
            $all_page_instances_str = $all_page_instances_str . join('++', $temp_arr_page_ins);
            
            $all_np = false;
        } else {
            $all_page_instances[$id] = "np";
            
            if (strlen($all_page_instances_str) > 0)
                $all_page_instances_str = $all_page_instances_str . '--';
            $all_page_instances_str = $all_page_instances_str . 'np';
        }
    }
    
    // Replace ndashes and mdashes with dashes
    $items = str_replace("–", "-", str_replace("–", "-", $items));
    
    // Remove pages from item key/s
    $items = preg_replace("/(((,))+([\w\d-]+(})+))++/", "}", $items);
    $items = preg_replace("/,\([\w\d-]+,+[\w\d-]+\)}/", "}", $items);
    unset($temp_items);
    
    // Generate instance id for shortcode
    $instance_id = "zp-InText-zp-ID-" . str_replace(" ", "_", str_replace("&", "_", str_replace("+", "_", str_replace("/", "_", str_replace("{", "-", str_replace("}", "-", str_replace(":", "-", str_replace(",", "_", $items)))))))) ."-wp".$post->ID;
    
    // Set up array for this post, if it doesn't exist
    if (!isset($GLOBALS['zp_shortcode_instances'][$post->ID]))
        $GLOBALS['zp_shortcode_instances'][$post->ID] = array();
    
    // Determine if all items are np
    if ($all_np) {
        $all_page_instances_str = "np";
    }
    
    // Then, add the instance to the array
    $GLOBALS['zp_shortcode_instances'][$post->ID][] = array(
        "instance_id" => $instance_id,
        "items" => $items,
        "page_instances" => $all_page_instances
    );
    
    // Show theme scripts
    $GLOBALS['zp_is_shortcode_displayed'] = true;
    
    // NEW: Server-side rendering for static citations
    // Only process if format contains %num% and we have a specific item format
    if (strpos($format, "%num%") !== false) {
        // Initialize global citation counter if not exists
        if (!isset($GLOBALS['zp_citation_count'][$post->ID])) {
            $GLOBALS['zp_citation_count'][$post->ID] = 0;
            
            // Also create a map of citation keys to numbers
            $GLOBALS['zp_citation_keys'][$post->ID] = array();
        }
        
        // Parse items to get citation keys
        $intext_citations = array();
        $intext_citation_split = explode("},{", $items);
        
        foreach ($intext_citation_split as $id => $item) {
            $item_parts = explode(":", $item);
            $api_user_id = str_replace("{", "", $item_parts[0]);
            $item_key = str_replace("}", "", $item_parts[1]);
            
            // Get page instances for this item
            $item_pages = false;
            if ($all_page_instances_str != "np") {
                $pages_array = explode("--", $all_page_instances_str);
                $item_pages = $pages_array[$id];
                if ($item_pages == "np") {
                    $item_pages = false;
                } else {
                    $item_pages = str_replace("++", ", ", $item_pages);
                }
            }
            
            $intext_citations[] = array(
                "api_user_id" => $api_user_id,
                "key" => $item_key,
                "pages" => $item_pages
            );
        }
        
        // Include request class and functions
        include_once(dirname(__FILE__) . '/../request/request.class.php');
        include_once(dirname(__FILE__) . '/../request/request.functions.php');
        
        // Build citation output
        $output = '';
        
        // Process each citation
        foreach ($intext_citations as $index => $citation) {
            // Check if this citation key has already been used
            if (!isset($GLOBALS['zp_citation_keys'][$post->ID][$citation['key']])) {
                // Increment the global citation counter
                $GLOBALS['zp_citation_count'][$post->ID]++;
                
                // Store the citation number for this key
                $GLOBALS['zp_citation_keys'][$post->ID][$citation['key']] = $GLOBALS['zp_citation_count'][$post->ID];
            }
            
            // Get the citation number for this key
            $citation_number = $GLOBALS['zp_citation_keys'][$post->ID][$citation['key']];
            
            // Format the citation
            $item_citation = $format;
            $item_citation = str_replace("%num%", $citation_number, $item_citation);
            
            // Handle pages
            if ($citation['pages'] !== false) {
                $multip = "p. ";
                if (strpos($citation['pages'], "-") !== false) {
                    $multip = "pp. ";
                }
                $item_citation = str_replace("%p%", $multip . $citation['pages'], $item_citation);
            } else {
                $item_citation = str_replace(", %p%", "", $item_citation);
                $item_citation = str_replace("%p%", "", $item_citation);
            }
            
            // Handle brackets
            if ($brackets == "yes") {
                $item_citation = str_replace("(", "", $item_citation);
                $item_citation = str_replace(")", "", $item_citation);
            }
            
            // Add to output with appropriate separator
            if ($index > 0) {
                if ($separator == "comma") {
                    $output .= ", ";
                } else {
                    $output .= "; ";
                }
            }
            
            // Add citation to output
            $output .= '<a rel="' . $citation['key'] . '" class="zp-ZotpressInText" href="#zp-ID-' . $post->ID . '-' . $citation['api_user_id'] . '-' . $citation['key'] . '">' . $item_citation . '</a>';
        }
        
        // Add brackets if needed
        if ($brackets == "yes") {
            $output = '[' . $output . ']';
        }
        
        return '<span class="' . $instance_id . ' zp-InText-Citation">' . $output . '</span>';
    }
    
    // If not using the static rendering, fall back to the original JavaScript method
    $output = '<span class="'.$instance_id.' zp-InText-Citation loading" rel="{ \'pages\': \''.$all_page_instances_str.'\', \'items\': \''.$items.'\', \'format\': \''.$format.'\', \'brackets\': \''.$brackets.'\', \'etal\': \''.$etal.'\', \'separator\': \''.$separator.'\', \'and\': \''.$and.'\' }"></span>';
    
    return $output;
}


?>
