<?php

require_once(dirname(__FILE__) . '/../utils.php');

/**
 * Handles the Zotpress in-text shortcode.
 * 7.3.10: Refined to use $_GET and Zotpress_prep_ajax_request_vars() for processing.
 *
 * Used by: Shortcodes, zotpress.php
 *
 * @param arr $atts The shortcode attributes.
 *
 * @return str $zp_output The in-text shortcode HTML.
 */
function ZotpressStatic_zotpressInTextBib ($atts)
{
    /*
    *   RELIES ON THESE GLOBAL VARIABLES:
    *
    *   $GLOBALS['zp_shortcode_instances'][$post->ID] {instantiated previously}
    *
    */

    $atts = shortcode_atts(array(
        'style' => false,
        'sortby' => "default",
        'sort' => false,
        'order' => false,

        'image' => false,
        'images' => false,
        'showimage' => "no",
        'showtags' => "no",

        'title' => "no",

        'download' => "no",
        'downloadable' => false,
        'notes' => false,
        'abstract' => false,
        'abstracts' => false,
        'cite' => false,
        'citeable' => false,

        'target' => false,
		'urlwrap' => false,

		'highlight' => false,
        'forcenumber' => false,
        'forcenumbers' => false

    ), $atts);

    global $post, $wpdb;

    // +---------------------------+
    // | FORMAT & CLEAN PARAMETERS |
    // +---------------------------+

    // 3.9.10: Use the Zotpress_prep_ajax_request_vars() function on bib, lib
    $zpr = Zotpress_prep_ajax_request_vars($wpdb, $atts);

    // FORMAT PARAMETERS
    $style = zpStatic_StripQuotes($zpr['style']);
    $sortby = strtolower(zpStatic_StripQuotes($zpr['sortby']));
    $order = strtolower(zpStatic_StripQuotes($zpr['order']));
    $showimage = zpStatic_StripQuotes($zpr['showimage']);
    $showtags = zpStatic_StripQuotes($zpr["showtags"]);
    $title = zpStatic_StripQuotes($zpr['title']);
    $downloadable = zpStatic_StripQuotes($zpr['downloadable']);
    $shownotes = zpStatic_StripQuotes($zpr['shownotes']);
    $abstracts = zpStatic_StripQuotes($zpr['showabstracts']);
    $citeable = zpStatic_StripQuotes($zpr["citeable"]);
    $target = zpStatic_StripQuotes($zpr["target"]);
    $urlwrap = zpStatic_StripQuotes($zpr['urlwrap']);
    $highlight = zpStatic_StripQuotes($zpr["highlight"]);
    $forcenumber = zpStatic_StripQuotes($zpr["request_start"]);

    // Set up request vars
    $request_start = 0;
    $request_last = 0;
    $overwrite_last_request = false;
    $update = false;

    // Set up item key
	$item_key = "";

    // Create an array to store citation keys and their numbers for sorting
    $citation_keys_order = array();
    if (isset($GLOBALS['zp_citation_keys'][$post->ID])) {
        $citation_keys_order = $GLOBALS['zp_citation_keys'][$post->ID];
    }

	// Get in-text items
	if ( isset( $GLOBALS['zp_shortcode_instances'][$post->ID] ) ) {
		foreach ( $GLOBALS['zp_shortcode_instances'][$post->ID] as $intextitem ) {
            // Remove pages from item key/s - IMPROVED REGEX PATTERNS
            $intextitem["items"] = preg_replace( "/(((,))+([\w\d-]+(})+))++/", "}", $intextitem["items"] );
            $intextitem["items"] = preg_replace("/,\([\w\d-]+,*[\w\d-]*\)}/", "}", $intextitem["items"]); // Modified to handle optional commas
            $intextitem["items"] = preg_replace("/\([\w\d-]+,*[\w\d-]*\)}/", "}", $intextitem["items"]);  // Added to catch cases without leading comma

            // Add separator if not the start
			if ( $item_key != "" )
                $item_key .= ";";

            // Add to the item key
			$item_key .= $intextitem["items"];
		}
	}

    // Generate instance id for shortcode
    $instance_id = "zotpress-".md5($item_key.$style.$sortby.$order.$title.$showimage.$showtags.$downloadable.$shownotes.$abstracts.$citeable.$target.$urlwrap.$forcenumber.$highlight.$post->ID);

    // GENERATE IN-TEXT BIB STRUCTURE
	$zp_output = "\n<div id='zp-InTextBib-".$instance_id."'";
    $zp_output .= " class='zp-Zotpress zp-Zotpress-InTextBib wp-block-group";
	if ( $forcenumber ) $zp_output .= " forcenumber";
	$zp_output .= " zp-Post-".$post->ID."'>";
	
    // Set up request parameters for static rendering
    $zpr['instance_id'] = $instance_id;
    $zpr['item_key'] = $item_key;
    $zpr['request_start'] = $request_start;
    $zpr['request_last'] = $request_last;
    $zpr['update'] = $update;
    $zpr['overwrite_last_request'] = $overwrite_last_request;
    
    // Override sortby if we have citation numbers to use
    if (!empty($citation_keys_order)) {
        // Force custom sorting by citation number
        $zpr['sort'] = 'custom';
        $zpr['citation_order'] = $citation_keys_order;
    }

    // For debugging
    // $zp_output .= '<pre>Item keys: ' . htmlspecialchars($item_key) . '</pre>';

    // Directly get the bibliography content using Zotpress_shortcode_request
    // This will render the bibliography statically through PHP
    $zp_output .= "<div class='zp-List'>";
    $bibContent = Zotpress_shortcode_request($zpr, true); // Check cache first
    
    if (empty($bibContent) || $bibContent == "\t\t\t\t") {
        // Fallback to JavaScript rendering if PHP rendering fails
        $zp_output .= '
            <span class="ZP_ITEM_KEY" style="display: none;">'.$item_key.'</span>
            <span class="ZP_STYLE" style="display: none;">'.$style.'</span>
            <span class="ZP_SORTBY" style="display: none;">'.$sortby.'</span>
            <span class="ZP_ORDER" style="display: none;">'.$order.'</span>
            <span class="ZP_TITLE" style="display: none;">'.$title.'</span>
            <span class="ZP_SHOWIMAGE" style="display: none;">'.$showimage.'</span>
            <span class="ZP_SHOWTAGS" style="display: none;">'.$showtags.'</span>
            <span class="ZP_DOWNLOADABLE" style="display: none;">'.$downloadable.'</span>
            <span class="ZP_NOTES" style="display: none;">'.$shownotes.'</span>
            <span class="ZP_ABSTRACT" style="display: none;">'.$abstracts.'</span>
            <span class="ZP_CITEABLE" style="display: none;">'.$citeable.'</span>
            <span class="ZP_TARGET" style="display: none;">'.$target.'</span>
            <span class="ZP_URLWRAP" style="display: none;">'.$urlwrap.'</span>
            <span class="ZP_FORCENUM" style="display: none;">'.$forcenumber.'</span>
            <span class="ZP_HIGHLIGHT" style="display: none;">'.$highlight.'</span>
            <span class="ZP_POSTID" style="display: none;">'.$post->ID.'</span>';
    } else {
        // Sort bibliography by citation numbers if we have citation keys
        if (!empty($citation_keys_order)) {
            $bibContent = zpStatic_sort_bibliography_by_citation($bibContent, $citation_keys_order, $post->ID);
        }
        
        $zp_output .= $bibContent;
    }
    
    $zp_output .= "</div><!-- .zp-List --></div><!--.zp-Zotpress-->\n\n";

	// Show theme scripts
	$GLOBALS['zp_is_shortcode_displayed'] = true;

	return $zp_output;
}

/**
 * Sort bibliography entries by citation numbers
 * 
 * @param string $bibContent The HTML content of the bibliography
 * @param array $citation_keys_order Array of citation keys and their numbers
 * @param int $post_id The post ID
 * 
 * @return string Sorted bibliography HTML
 */
function zpStatic_sort_bibliography_by_citation($bibContent, $citation_keys_order, $post_id) {
    // If there are no citation keys, return the original content
    if (empty($citation_keys_order)) {
        return $bibContent;
    }
    
    // Parse the bibliography HTML
    $dom = new DOMDocument();
    
    // Suppress warnings for HTML5 tags
    libxml_use_internal_errors(true);
    $dom->loadHTML(mb_convert_encoding($bibContent, 'HTML-ENTITIES', 'UTF-8'));
    libxml_clear_errors();
    
    // Get all bibliography entries
    $entries = $dom->getElementsByTagName('div');
    $bibEntries = array();
    
    // Extract entries and their citation keys
    foreach ($entries as $entry) {
        if ($entry->hasAttribute('class') && strpos($entry->getAttribute('class'), 'zp-Entry') !== false) {
            $id = $entry->getAttribute('id');
            if ($id) {
                // Extract citation key from the ID (format: zp-ID-{post_id}-{api_user_id}-{citation_key})
                $idParts = explode('-', $id);
                if (count($idParts) >= 5) {
                    $citationKey = end($idParts);
                    
                    // Store the entry with its citation key
                    $bibEntries[] = array(
                        'key' => $citationKey,
                        'html' => $dom->saveHTML($entry),
                        'order' => isset($citation_keys_order[$citationKey]) ? $citation_keys_order[$citationKey] : 999
                    );
                }
            }
        }
    }
    
    // Sort entries by citation number
    usort($bibEntries, function($a, $b) {
        return $a['order'] - $b['order'];
    });
    
    // Rebuild the bibliography HTML
    $sortedHTML = '';
    foreach ($bibEntries as $entry) {
        $sortedHTML .= $entry['html'];
    }
    
    return $sortedHTML;
}

?>
