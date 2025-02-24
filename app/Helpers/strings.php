<?php

if (!function_exists('onlyNumbers')) {
    function onlyNumbers(string $value): string
    {
        return preg_replace('/[^0-9]/', '', $value);
    }
}

if (!function_exists('alphaNumeric')) {
    function alphaNumeric(string $value): string
    {
        return preg_replace('/[^a-zA-Z0-9]/', '', $value);
    }
}
