<?php

function get_zotpress_cache($key)
{
    global $wp_object_cache;
    return $wp_object_cache->get($key, 'zotpress_static');
}

function set_zotpress_cache($key, $data, $expiration = 3600)
{
    global $wp_object_cache;
    return $wp_object_cache->set($key, $data, 'zotpress_static', $expiration);
}