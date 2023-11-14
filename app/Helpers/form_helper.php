<?php

use CodeIgniter\Validation\ValidationInterface;

function form_error(ValidationInterface $validator, string $field): string
{
    if ($validator->hasError($field)) {
        return '<label class="label error"><span class="label-text-all">' . $validator->getError($field) . '</span></label>';
    }

    return '';
}

/**
 * Returns the maximum upload size in human-readable format.
 */
function max_upload_size(): string
{
    helper('number');

    $max_upload   = string_to_bytes(ini_get('upload_max_filesize'));
    $max_post     = string_to_bytes(ini_get('post_max_size'));
    $memory_limit = string_to_bytes(ini_get('memory_limit'));

    // find the smallest of them, this defines the real limit
    $size = min($max_upload, $max_post, $memory_limit);

    return number_to_size($size);
}
