<?php

function dd()
{
    $arrays = func_get_args();

    if (empty($arrays)) {
        $arrays = 'hi';
    }

    echo '<pre>';
    print_r($arrays);
    echo '</pre>';
    exit;
}

if (!function_exists('mb_str_replace')) {
    function mb_str_replace($needle, $text_replace, $haystack)
    {
        return implode($text_replace, explode($needle, $haystack));
    }
}
