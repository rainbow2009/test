<?php

function dd()
{
    $arrays = func_get_args();
    print "<pre>";
    print_r($arrays);
    print "</pre>";
    exit;
}

if (!function_exists('mb_str_replace')) {
    function mb_str_replace($needle, $text_replace, $haystack)
    {
        return implode($text_replace, explode($needle, $haystack));
    }
}
