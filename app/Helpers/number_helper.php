<?php

/**
 * Converts a shorthand byte value to an integer byte value.
 * Accepts values that might be in an ini file, like 2M, 1G, etc.
 *
 * If no alpha character is given, the value is returned unchanged.
 */
function string_to_bytes(string $val): int|float|string
{
    $val = trim((string) $val);
    $last = strtolower($val[strlen($val)-1]);

    if (empty($last) || is_numeric($last)) {
        return (int)$val;
    }

    $val = substr($val, 0, strlen($val)-1);

    return match($last) {
        'g' => (int)($val * 1024 * 1024 * 1024),
        'm' => (int)($val * 1024 * 1024),
        'k' => (int)($val * 1024),
        default => (int)$val,
    };
}
