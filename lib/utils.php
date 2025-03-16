<?php
/**
 * Utility functions for Zotpress Static Bibliography
 */

/**
 * Strip quotes and decode HTML entities from a string
 * 
 * @param string $string The string to process
 * @return string The processed string
 */
function zpStatic_StripQuotes($string)
{
    // Strip quotes and decode
    $string = html_entity_decode($string, ENT_QUOTES);
    $string = str_replace(chr(0x27), '', $string); // single quote
    $string = str_replace(chr(0x22), '', $string); // double quote
    return $string;
}
