<?php

if (!function_exists('normalize_boolean')) {
    function normalize_boolean($value)
    {
        return filter_var($value, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE);
    }
}
