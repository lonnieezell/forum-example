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
function max_upload_size(bool $returnRaw = false): string
{
    helper('number');

    // If a value has been set in the .env file, use that
    $maxSize = config('ImageUpload')->fileSize;
    if ($maxSize > 0) {
        return $returnRaw
            ? $maxSize
            : number_to_size($maxSize);
    }

    $maxUpload   = string_to_bytes(ini_get('upload_max_filesize'));
    $maxPost     = string_to_bytes(ini_get('post_max_size'));
    $memoryLimit = string_to_bytes(ini_get('memory_limit'));

    // find the smallest of them, this defines the real limit
    $size = min($maxUpload, $maxPost, $memoryLimit);

    return $returnRaw ? $size : number_to_size($size);
}
