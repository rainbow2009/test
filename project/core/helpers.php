<?php

function dd()
{
    $arrays = func_get_args();
    print "<pre>";
    print_r($arrays);
    print "</pre>";
    exit;
}
