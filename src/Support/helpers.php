<?php
/**
 * helpers.php
 */

if (!function_exists('dd')) {
    function dd($load)
    {
        var_dump($load);
        die();
    }
}
