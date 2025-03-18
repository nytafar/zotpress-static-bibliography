<?php

if (!function_exists('zp_clean_param')) {
    /**
     * Removes all extra quotations.
     *
     * Used by: Shortcodes
     *
     * @param str $param The shortcode parameter to clean.
     *
     * @return str $clean_param The clean parameter.
     */
    function zp_clean_param($param)
    {
        // Thanks to Emerson@StackOverflow
        $search = array(
            '&#8220;', // 1. Left Double Quotation Mark “
            '“',
            '&#8221;', // 2. Right Double Quotation Mark ”
            '”',
            '&#8216;', // 3. Left Single Quotation Mark ‘
            '‘',
            '&#8217;', // 4. Right Single Quotation Mark ’
            '’',
            // NOTE: We need apostrophes (single quotes) for tags and ...?
            // '&#039;',  // 5. Normal Single Quotation Mark '
            '&amp;',   // 6. Ampersand &
            '&quot;',  // 7. Normal Double Qoute
            '&lt;',    // 8. Less Than <
            '&gt;'     // 9. Greater Than >
        );

        // Fix the String
        $clean_param = htmlspecialchars($param, ENT_QUOTES);

        return str_replace($search, "", $clean_param);
    }
}

if (!function_exists('zp_get_year')) {
    /**
     * Gets the year from a date.
     *
     * Used by: In-Text Shortcode, In-Text Bibliography Shortcode
     *
     * @param str $date The date to search in.
     * @param bol $yesnd Return with a "n.d." if no year found.
     *
     * @return str $date_return The year found or blank/n.d. if not found.
     */
    function zp_get_year($date, $yesnd = false)
    {
        $date_return = false;

        preg_match_all('/(\d{4})/', $date, $matches);

        if (is_null($matches[0][0]))
            $date_return = $yesnd === true ? "n.d." : "";
        else
            $date_return = $matches[0][0];

        return $date_return;
    }
}

if (!function_exists('subval_sort')) {
    /**
     * Sorts by a secondary value.
     *
     * Used by: Bibliography Shortcode, In-Text Bibliography Shortcode
     *
     * @param arr $item_arr The date to format.
     * @param str $sortby What attribute to sort by.
     * @param str $order What is the order (direction of the sort): ASC, DESC.
     *
     * @return arr $item_arr The newly sorted array of items.
     */
    function subval_sort($item_arr, $sortby, $order)
    {
        // Format sort order
        $order = strtolower($order) == "desc" ? SORT_DESC : SORT_ASC;

        // Author or date
        if ($sortby == "author"
            || $sortby == "date") {

            foreach ($item_arr as $key => $val) {

                $author[$key] = $val["author"];

                $zpdate = "";
                $zpdate = isset($val["zpdate"]) ? $val["zpdate"] : $val["date"];

                $date[$key] = zp_date_format($zpdate);
            }

        } elseif ($sortby == "title") {

            foreach ($item_arr as $key => $val) {

                $title[$key] = $val["title"];
                $author[$key] = $val["author"];
            }
        }

        // NOTE: array_multisort seems to be ignoring second sort for date->author
        if ($sortby == "author" && isset($author) && is_array($author))
            array_multisort($author, $order, $date, $order, $item_arr);
        elseif ($sortby == "date" && isset($date) && is_array($date))
            array_multisort($date, $order, $author, SORT_ASC, $item_arr);
        elseif ($sortby == "title" && isset($title) && is_array($title))
            array_multisort($title, $order, $author, $order, $item_arr);

        return $item_arr;
    }
}

if (!function_exists('zp_date_format')) {
    /**
     * Reformats the date in a standard format: yyyy-mm-dd.
     *
     * Can read the following:
     *  - yyyy/mm/dd, mm/dd/yyyy
     *  - the dash equivalents of the above
     *  - mmmm dd, yyyy
     *  - yyyy mmmm, yyyy mmm (and the reverse)
     *  - mm-mm yyyy
     *
     * Used by: subval_sort
     *
     * @param str $date The date to format.
     *
     * @return str The formatted date, or the original if formatting fails.
     */
    function zp_date_format($date)
    {
        // Set up search lists
        $list_month_long = array("01" => "January", "02" => "February", "03" => "March", "04" => "April", "05" => "May", "06" => "June", "07" => "July", "08" => "August", "09" => "September", "10" => "October", "11" => "November", "12" => "December");
        $list_month_short = array("01" => "Jan", "02" => "Feb", "03" => "Mar", "04" => "Apr", "05" => "May", "06" => "Jun", "07" => "Jul", "08" => "Aug", "09" => "Sept", "10" => "Oct", "11" => "Nov", "12" => "Dec");

        // Check if it's a mm-mm dash
        if (preg_match("/^[a-zA-Z]+[-][a-zA-Z]+[ ]\\d+\$/", $date) == 1) {

            $temp1 = preg_split("/-|\//", $date);
            $temp2 = preg_split("[\s]", $temp1[1]);

            $date = $temp1[0] . " " . $temp2[1];
        }

        // If it's already formatted with a dash or forward slash
        if (strpos($date, "-") !== false
            || strpos($date, "/") !== false) {

            // Break it up
            $temp = preg_split("/-|\//", $date);

            // If year is last, switch it with first
            if (strlen($temp[0]) != 4) {

                // Just month and year
                if (count($temp) == 2)
                    $date_formatted = array(
                        "year" => $temp[1],
                        "month" => $temp[0],
                        "day" => false
                    );
                // Assuming mm dd yyyy
                else
                    $date_formatted = array(
                        "year" => $temp[2],
                        "month" => $temp[0],
                        "day" => $temp[1]
                    );

            } elseif (isset($temp[2])) {

                // day is set
                $date_formatted = array(
                    "year" => $temp[0],
                    "month" => $temp[1],
                    "day" => $temp[2]
                );

            } else {

                $date_formatted = array(
                    "year" => $temp[0],
                    "month" => $temp[1],
                    "day" => false
                );
            }

        } elseif (strpos($date, ",")) {

            $date = trim(str_replace(", ", ",", $date));
            $temp = preg_split("/,| /", $date);

            // Convert month
            $month = array_search($temp[0], $list_month_long);
            if (!$month)
                $month = array_search($temp[0], $list_month_short);

            $date_formatted = array(
                "year" => $temp[2],
                "month" => $month,
                "day" => $temp[1]
            );

        } else {

            $date = trim(str_replace("  ", "-", $date));
            $temp = explode(" ", $date);

            // If there's at least two parts to the date
            if ($temp !== []) {

                // Check if name is first
                if (!is_numeric($temp[0])) {

                    if (in_array($temp[0], $list_month_long)) {

                        $date_formatted = array(
                            "year" => $temp[1],
                            "month" => array_search($temp[0], $list_month_long),
                            "day" => false
                        );

                    } elseif (in_array($temp[0], $list_month_short)) {

                        $date_formatted = array(
                            "year" => $temp[1],
                            "month" => array_search($temp[0], $list_month_short),
                            "day" => false
                        );

                    } else // Not a recognizable month word

                        $date_formatted = array(
                            "year" => $temp[0], // $temp[1]
                            "month" => false,
                            "day" => false
                        );

                } elseif (count($temp) > 1) {

                    if (in_array($temp[1], $list_month_long)) {

                        $date_formatted = array(
                            "year" => $temp[0],
                            "month" => array_search($temp[1], $list_month_long),
                            "day" => false
                        );

                    } elseif (in_array($temp[1], $list_month_short)) {

                        $date_formatted = array(
                            "year" => $temp[0],
                            "month" => array_search($temp[1], $list_month_short),
                            "day" => false
                        );

                    } else // Not a recognizable month word

                        $date_formatted = array(
                            "year" => $temp[0],
                            "month" => false,
                            "day" => false
                        );

                } else { // Only one part in the array

                    $date_formatted = array(
                        "year" => $temp[0],
                        "month" => false,
                        "day" => false
                    );
                }
            }

            // Otherwise, assume year
            else {
                $date_formatted = array(
                    "year" => $temp[0],
                    "month" => false,
                    "day" => false
                );
            }
        }

        // Format date in standard form: yyyy-mm-dd
        $date_formatted = implode("-", array_filter($date_formatted));

        if (!isset($date_formatted))
            $date_formatted = $date;

        return $date_formatted;
    }
}

if (!function_exists('Zotpress_prep_ajax_request_vars')) {
    /**
     * Processes the WP AJAX Zotero request variables.
     * We need to make sure user input is checked.
     * For complex strings inc. non-English chars: strip_tags()
     * For all else: sanitize_text_field()
     *
     * Used by: shortcode.php, shortcode.intextbib.php
     *
     * @param obj $wpdb WP DB object.
     * @param arr Shortcode attributes.
     * @param bool Whether library shortcode or not.
     * 
     * @return string Array with the processed variables.
     */
    function Zotpress_prep_ajax_request_vars($wpdb, $atts = false, $is_zplib = false)
    {
        // Original implementation
    }
}

if (!function_exists('Zotpress_prep_request_URL')) {
    /**
     * Preps and formats the Zotero API request URL.
     *
     * Handles all possible Zotpress parameters for bibliography
     * shortcodes. Per user account.
     *
     * @param obj $wpdb WP DB object.
     * @param arr $zpr Holds all params for request.
     * @param arr $zp_request_queue Holds all requests for all accounts.
     * @param str $api_user_id Optional. API user ID.
     *
     * @return arr $zp_request_queue The new request queue of formatted URLs.
     */
    function Zotpress_prep_request_URL($wpdb, $zpr, $zp_request_queue, $api_user_id = false, $zp_request_data = false)
    {
        // Original implementation
    }
}

?>